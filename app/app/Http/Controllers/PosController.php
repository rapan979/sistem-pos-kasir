<?php

namespace App\Http\Controllers;

use App\Models\CashierShift;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Topping;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\TransactionDetailTopping;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Controller khusus sistem kasir (Point of Sale).
 * Fokus murni pada alur kasir: pilih menu, keranjang, buka/tutup shift kasir,
 * transaksi tunai/QRIS, hitung kembalian, cetak nota, dan riwayat shift.
 */
class PosController extends Controller
{
    /**
     * Tampilan utama layar kasir POS.
     */
    public function index(): View
    {
        $user = auth()->user();
        $business = $user->business;

        // Ambil gerai aktif (atau gerai pertama jika owner belum terikat outlet khusus)
        $outlet = $user->outlet ?? $business->outlets()->first();

        // Cari shift kasir yang sedang aktif/terbuka untuk kasir/outlet ini
        $activeShift = null;
        if ($outlet) {
            $activeShift = CashierShift::where('outlet_id', $outlet->id)
                ->where('user_id', $user->id)
                ->where('status', 'open')
                ->latest('opened_at')
                ->first();
        }

        // Ambil daftar kategori produk milik usaha ini beserta produk aktif & variasinya
        $categories = Category::where('business_id', $business->id)
            ->with(['products' => function ($query) {
                $query->where('is_available', true)
                    ->with('variations');
            }])
            ->get();

        // Ambil topping berbayar yang tersedia
        $toppings = Topping::where('business_id', $business->id)
            ->where('is_available', true)
            ->get();

        // Ambil 5 transaksi terakhir pada shift ini untuk riwayat cepat kasir
        $recentTransactions = collect();
        if ($activeShift) {
            $recentTransactions = Transaction::where('cashier_shift_id', $activeShift->id)
                ->latest()
                ->take(5)
                ->get();
        }

        return view('pos.index', [
            'user' => $user,
            'business' => $business,
            'outlet' => $outlet,
            'activeShift' => $activeShift,
            'categories' => $categories,
            'toppings' => $toppings,
            'recentTransactions' => $recentTransactions,
        ]);
    }

    /**
     * Buka Shift Kasir (Input modal awal laci).
     */
    public function openShift(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'opening_cash' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $user = auth()->user();
        $outlet = $user->outlet ?? $user->business->outlets()->first();

        if (! $outlet) {
            return response()->json(['success' => false, 'message' => 'Gerai kasir tidak ditemukan.'], 422);
        }

        // Cek apakah sudah ada shift yang terbuka
        $existingShift = CashierShift::where('outlet_id', $outlet->id)
            ->where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        if ($existingShift) {
            return response()->json([
                'success' => true,
                'message' => 'Shift kasir sudah aktif.',
                'shift' => $existingShift,
            ]);
        }

        $shift = CashierShift::create([
            'outlet_id' => $outlet->id,
            'user_id' => $user->id,
            'opened_at' => now(),
            'opening_cash' => $validated['opening_cash'],
            'expected_cash' => $validated['opening_cash'],
            'status' => 'open',
            'notes' => $validated['notes'] ?? 'Buka kasir operasional.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shift kasir berhasil dibuka.',
            'shift' => $shift,
        ]);
    }

    /**
     * Tutup Shift Kasir (Hitung uang fisik & rekap).
     */
    public function closeShift(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'actual_cash' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $user = auth()->user();
        $outlet = $user->outlet ?? $user->business->outlets()->first();

        $shift = CashierShift::where('outlet_id', $outlet->id)
            ->where('user_id', $user->id)
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        if (! $shift) {
            return response()->json(['success' => false, 'message' => 'Tidak ada shift kasir yang aktif.'], 422);
        }

        // Hitung total uang tunai yang masuk dari transaksi shift ini
        $totalCashSales = Transaction::where('cashier_shift_id', $shift->id)
            ->where('payment_method', 'cash')
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $expectedCash = $shift->opening_cash + $totalCashSales;
        $actualCash = $validated['actual_cash'];
        $difference = $actualCash - $expectedCash;

        $shift->update([
            'closed_at' => now(),
            'expected_cash' => $expectedCash,
            'actual_cash' => $actualCash,
            'difference' => $difference,
            'status' => 'closed',
            'notes' => $validated['notes'] ?? $shift->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shift kasir berhasil ditutup.',
            'summary' => [
                'opening_cash' => $shift->opening_cash,
                'total_cash_sales' => $totalCashSales,
                'expected_cash' => $expectedCash,
                'actual_cash' => $actualCash,
                'difference' => $difference,
            ],
        ]);
    }

    /**
     * Proses Checkout Transaksi Kasir.
     */
    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.variation_id' => ['required', 'integer', 'exists:product_variations,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.sugar_level' => ['nullable', 'string'],
            'items.*.ice_level' => ['nullable', 'string'],
            'items.*.topping_ids' => ['nullable', 'array'],
            'items.*.topping_ids.*' => ['integer', 'exists:toppings,id'],
            'payment_method' => ['required', 'string', 'in:cash,qris'],
            'paid_amount' => ['required', 'integer', 'min:0'],
        ]);

        $user = auth()->user();
        $business = $user->business;
        $outlet = $user->outlet ?? $business->outlets()->first();

        // Pastikan shift kasir terbuka
        $activeShift = CashierShift::where('outlet_id', $outlet->id)
            ->where('user_id', $user->id)
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        if (! $activeShift) {
            return response()->json([
                'success' => false,
                'message' => 'Laci kasir belum dibuka. Silakan masukkan modal awal terlebih dahulu.',
            ], 422);
        }

        return DB::transaction(function () use ($validated, $user, $business, $outlet, $activeShift) {
            // Hitung total belanja dari database untuk menjamin integritas harga
            $calculatedTotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $variation = ProductVariation::with('product')->findOrFail($item['variation_id']);
                $qty = $item['quantity'];

                $toppingSubtotal = 0;
                $toppingsList = [];

                if (! empty($item['topping_ids'])) {
                    $selectedToppings = Topping::whereIn('id', $item['topping_ids'])
                        ->where('business_id', $business->id)
                        ->get();

                    foreach ($selectedToppings as $top) {
                        $toppingSubtotal += $top->price * $qty;
                        $toppingsList[] = [
                            'topping_id' => $top->id,
                            'name' => $top->name,
                            'price' => $top->price,
                            'subtotal' => $top->price * $qty,
                        ];
                    }
                }

                $itemSubtotal = ($variation->price * $qty) + $toppingSubtotal;
                $calculatedTotal += $itemSubtotal;

                $sugar = $item['sugar_level'] ?? 'Normal';
                $ice = $item['ice_level'] ?? 'Normal';
                $customNotes = "Gula: {$sugar}, Es: {$ice}";

                $itemsData[] = [
                    'variation' => $variation,
                    'quantity' => $qty,
                    'price' => $variation->price,
                    'subtotal' => $itemSubtotal,
                    'notes' => $customNotes,
                    'toppings' => $toppingsList,
                ];
            }

            $paidAmount = $validated['paid_amount'];

            // Validasi uang bayar jika tunai
            if ($validated['payment_method'] === 'cash' && $paidAmount < $calculatedTotal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nominal uang yang diterima kurang dari total belanja.',
                ], 422);
            }

            // Generate nomor nota unik: INV-YYYYMMDD-XXXX
            $datePrefix = date('Ymd');
            $countToday = Transaction::where('business_id', $business->id)
                ->whereDate('created_at', today())
                ->count() + 1;
            $invoiceNumber = 'INV-' . $datePrefix . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            $changeAmount = ($validated['payment_method'] === 'cash')
                ? max(0, $paidAmount - $calculatedTotal)
                : 0;

            // Simpan transaksi
            $transaction = Transaction::create([
                'invoice_number' => $invoiceNumber,
                'business_id' => $business->id,
                'outlet_id' => $outlet->id,
                'cashier_shift_id' => $activeShift->id,
                'user_id' => $user->id,
                'total_amount' => $calculatedTotal,
                'paid_amount' => ($validated['payment_method'] === 'cash') ? $paidAmount : $calculatedTotal,
                'change_amount' => $changeAmount,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            // Simpan rincian barang & topping
            foreach ($itemsData as $entry) {
                $detail = TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_variation_id' => $entry['variation']->id,
                    'product_name' => $entry['variation']->product->name,
                    'variation_name' => $entry['variation']->variation_name,
                    'quantity' => $entry['quantity'],
                    'price_at_transaction' => $entry['price'],
                    'subtotal' => $entry['subtotal'],
                    'notes' => $entry['notes'],
                ]);

                foreach ($entry['toppings'] as $t) {
                    TransactionDetailTopping::create([
                        'transaction_detail_id' => $detail->id,
                        'topping_id' => $t['topping_id'],
                        'topping_name' => $t['name'],
                        'quantity' => $entry['quantity'],
                        'price_at_transaction' => $t['price'],
                        'subtotal' => $t['subtotal'],
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diselesaikan.',
                'invoice_number' => $transaction->invoice_number,
                'total_amount' => $transaction->total_amount,
                'paid_amount' => $transaction->paid_amount,
                'change_amount' => $transaction->change_amount,
                'payment_method' => $transaction->payment_method,
                'created_at' => $transaction->created_at->format('d/m/Y H:i'),
                'outlet_name' => $outlet->name,
                'cashier_name' => $user->name,
            ]);
        });
    }

    /**
     * Batalkan transaksi (Void) dengan alasan jelas.
     */
    public function voidTransaction(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $user = auth()->user();
        $transaction = Transaction::where('business_id', $user->business_id)
            ->findOrFail($id);

        if ($transaction->payment_status === 'void') {
            return response()->json(['success' => false, 'message' => 'Transaksi ini sudah pernah dibatalkan.'], 422);
        }

        $transaction->update([
            'payment_status' => 'void',
            'voided_by' => $user->id,
            'void_reason' => $validated['reason'],
            'voided_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dibatalkan (void).',
        ]);
    }
}
