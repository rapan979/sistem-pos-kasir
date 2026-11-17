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
use Illuminate\Support\Facades\Http;
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

        // Ambil riwayat transaksi pada shift ini lengkap dengan rincian menu yang dipesan
        $recentTransactions = collect();
        $shiftCashSales = 0;
        $shiftQrisSales = 0;
        $expectedCash = 0;

        if ($activeShift) {
            $recentTransactions = Transaction::where('cashier_shift_id', $activeShift->id)
                ->with('details')
                ->latest()
                ->take(30)
                ->get();

            $shiftCashSales = (int) Transaction::where('cashier_shift_id', $activeShift->id)
                ->whereIn('payment_method', ['tunai', 'cash'])
                ->whereIn('payment_status', ['lunas', 'paid'])
                ->sum('total_amount');

            $shiftQrisSales = (int) Transaction::where('cashier_shift_id', $activeShift->id)
                ->where('payment_method', 'qris')
                ->whereIn('payment_status', ['lunas', 'paid'])
                ->sum('total_amount');

            $expectedCash = $activeShift->opening_cash + $shiftCashSales;
        }

        return view('pos.index', [
            'user' => $user,
            'business' => $business,
            'outlet' => $outlet,
            'activeShift' => $activeShift,
            'categories' => $categories,
            'toppings' => $toppings,
            'recentTransactions' => $recentTransactions,
            'shiftCashSales' => $shiftCashSales,
            'shiftQrisSales' => $shiftQrisSales,
            'expectedCash' => $expectedCash,
        ]);
    }

    /**
     * Ringkasan Realtime Shift Kasir (Pendapatan Tunai, QRIS, & Uang Fisik Harusnya).
     */
    public function shiftSummary(Request $request): JsonResponse
    {
        $user = auth()->user();
        $outlet = $user->outlet ?? $user->business->outlets()->first();

        $shift = CashierShift::where('outlet_id', $outlet->id)
            ->where('user_id', $user->id)
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        if (! $shift) {
            return response()->json(['success' => false, 'message' => 'Tidak ada shift kasir yang aktif.'], 404);
        }

        $cashSales = (int) Transaction::where('cashier_shift_id', $shift->id)
            ->whereIn('payment_method', ['tunai', 'cash'])
            ->whereIn('payment_status', ['lunas', 'paid'])
            ->sum('total_amount');

        $qrisSales = (int) Transaction::where('cashier_shift_id', $shift->id)
            ->where('payment_method', 'qris')
            ->whereIn('payment_status', ['lunas', 'paid'])
            ->sum('total_amount');

        $totalSales = $cashSales + $qrisSales;
        $expectedCash = $shift->opening_cash + $cashSales;
        $txCount = Transaction::where('cashier_shift_id', $shift->id)
            ->whereIn('payment_status', ['lunas', 'paid'])
            ->count();

        return response()->json([
            'success' => true,
            'opening_cash' => $shift->opening_cash,
            'cash_sales' => $cashSales,
            'qris_sales' => $qrisSales,
            'total_sales' => $totalSales,
            'expected_cash' => $expectedCash,
            'total_transactions' => $txCount,
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
            ->whereIn('payment_method', ['tunai', 'cash'])
            ->whereIn('payment_status', ['lunas', 'paid'])
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
            'payment_method' => ['required', 'string', 'in:tunai,qris,cash'],
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

            $isCash = in_array(strtolower($validated['payment_method']), ['cash', 'tunai']);
            $dbPaymentMethod = $isCash ? 'tunai' : 'qris';

            // Validasi uang bayar jika tunai
            if ($isCash && $paidAmount < $calculatedTotal) {
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

            $changeAmount = $isCash
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
                'paid_amount' => $isCash ? $paidAmount : $calculatedTotal,
                'change_amount' => $changeAmount,
                'payment_method' => $dbPaymentMethod,
                'payment_status' => 'lunas',
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
                'created_at' => $transaction->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') . ' WIB',
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

        if (in_array($transaction->payment_status, ['batal', 'void'])) {
            return response()->json(['success' => false, 'message' => 'Transaksi ini sudah pernah dibatalkan.'], 422);
        }

        $transaction->update([
            'payment_status' => 'batal',
            'voided_by' => $user->id,
            'void_reason' => $validated['reason'],
            'voided_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dibatalkan (void).',
        ]);
    }

    protected function getMidtransBaseUrl(): string
    {
        return config('services.midtrans.is_production', false)
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';
    }

    protected function getMidtransServerKey(): string
    {
        return config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-vW7qdP51IaneD6x-rRQEW_fV'));
    }

    protected function midtransClient(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withBasicAuth($this->getMidtransServerKey(), '')
            ->withOptions([
                'verify' => PHP_OS_FAMILY === 'Windows' ? false : true,
            ])
            ->withHeaders([
                'Accept' => 'application/json',
            ]);
    }

    /**
     * Generate QRIS Dinamis via Midtrans Core API.
     */
    public function generateMidtransQris(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.variation_id' => ['required', 'integer', 'exists:product_variations,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.sugar_level' => ['nullable', 'string'],
            'items.*.ice_level' => ['nullable', 'string'],
            'items.*.topping_ids' => ['nullable', 'array'],
            'items.*.topping_ids.*' => ['integer', 'exists:toppings,id'],
        ]);

        $user = auth()->user();
        $business = $user->business;
        $activeShift = CashierShift::where('outlet_id', $user->outlet_id ?? $business->outlets()->first()->id)
            ->where('user_id', $user->id)
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        if (! $activeShift) {
            return response()->json(['success' => false, 'message' => 'Silakan buka shift kasir terlebih dahulu.'], 422);
        }

        $calculatedTotal = 0;
        $itemsData = [];

        foreach ($validated['items'] as $item) {
            $variation = ProductVariation::with('product')->findOrFail($item['variation_id']);
            $unitPrice = $variation->price;
            $toppingSum = 0;
            $selectedToppings = [];

            if (! empty($item['topping_ids'])) {
                $toppings = Topping::whereIn('id', $item['topping_ids'])
                    ->where('business_id', $business->id)
                    ->get();

                foreach ($toppings as $t) {
                    $toppingSum += $t->price;
                    $selectedToppings[] = [
                        'topping_id' => $t->id,
                        'name' => $t->name,
                        'price' => $t->price,
                        'subtotal' => $t->price * $item['quantity'],
                    ];
                }
            }

            $itemSubtotal = ($unitPrice + $toppingSum) * $item['quantity'];
            $calculatedTotal += $itemSubtotal;

            $notesArr = [];
            if (! empty($item['sugar_level'])) $notesArr[] = 'Gula: ' . $item['sugar_level'];
            if (! empty($item['ice_level'])) $notesArr[] = 'Es: ' . $item['ice_level'];

            $itemsData[] = [
                'variation_id' => $variation->id,
                'quantity' => $item['quantity'],
                'price' => $unitPrice,
                'subtotal' => $itemSubtotal,
                'notes' => implode(', ', $notesArr),
                'toppings' => $selectedToppings,
                'product_name' => $variation->product->name,
                'variation_name' => $variation->variation_name,
            ];
        }

        $orderId = 'QRIS-' . date('YmdHis') . '-' . rand(100, 999);

        try {
            $response = $this->midtransClient()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->getMidtransBaseUrl() . '/charge', [
                    'payment_type' => 'qris',
                    'transaction_details' => [
                        'order_id' => $orderId,
                        'gross_amount' => $calculatedTotal,
                    ],
                ]);

            if (! $response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghubungi server Midtrans: ' . ($response->json('status_message') ?? $response->body()),
                ], 500);
            }

            $data = $response->json();
            $qrUrl = null;
            if (isset($data['actions']) && is_array($data['actions'])) {
                foreach ($data['actions'] as $act) {
                    if ($act['name'] === 'generate-qr-code') {
                        $qrUrl = $act['url'];
                        break;
                    }
                }
            }

            // Simpan detail pesanan di cache sementara (berlaku 30 menit)
            cache()->put('midtrans_order_' . $orderId, [
                'itemsData' => $itemsData,
                'calculatedTotal' => $calculatedTotal,
                'shift_id' => $activeShift->id,
                'user_id' => $user->id,
                'outlet_id' => $user->outlet_id ?? $business->outlets()->first()->id,
                'business_id' => $business->id,
            ], now()->addMinutes(30));

            return response()->json([
                'success' => true,
                'order_id' => $orderId,
                'gross_amount' => $calculatedTotal,
                'qr_url' => $qrUrl,
                'qr_string' => $data['qr_string'] ?? null,
                'expiry_time' => $data['expiry_time'] ?? null,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses Midtrans: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cek status pembayaran QRIS Midtrans secara realtime.
     */
    public function checkMidtransStatus(Request $request, string $orderId): JsonResponse
    {
        try {
            $response = $this->midtransClient()
                ->get($this->getMidtransBaseUrl() . '/' . $orderId . '/status');

            if (! $response->successful()) {
                return response()->json(['success' => false, 'status' => 'pending'], 200);
            }

            $data = $response->json();
            $status = $data['transaction_status'] ?? 'pending';

            // Jika status lunas (settlement)
            if ($status === 'settlement') {
                $cached = cache()->get('midtrans_order_' . $orderId);

                // Cek apakah transaksi sudah tersimpan di database
                $existingTx = Transaction::where('invoice_number', $orderId)->first();
                if ($existingTx) {
                    return response()->json([
                        'success' => true,
                        'status' => 'settlement',
                        'transaction' => [
                            'invoice_number' => $existingTx->invoice_number,
                            'total_amount' => $existingTx->total_amount,
                            'paid_amount' => $existingTx->paid_amount,
                            'change_amount' => $existingTx->change_amount,
                            'payment_method' => $existingTx->payment_method,
                            'created_at' => $existingTx->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') . ' WIB',
                            'items' => $existingTx->details->map(fn($d) => [
                                'product_name' => $d->product_name,
                                'variation_name' => $d->variation_name,
                                'quantity' => $d->quantity,
                                'unitPrice' => $d->price_at_transaction,
                                'subtotal' => $d->subtotal,
                                'toppings' => [],
                            ]),
                        ],
                    ]);
                }

                if (! $cached) {
                    return response()->json(['success' => false, 'message' => 'Sesi transaksi kedaluwarsa.'], 422);
                }

                // Simpan transaksi lunas ke DB
                $user = auth()->user();
                $tx = DB::transaction(function () use ($orderId, $data, $cached, $user) {
                    $transaction = Transaction::create([
                        'invoice_number' => $orderId,
                        'business_id' => $cached['business_id'],
                        'outlet_id' => $cached['outlet_id'],
                        'cashier_shift_id' => $cached['shift_id'],
                        'user_id' => $user->id,
                        'total_amount' => (int) $data['gross_amount'],
                        'paid_amount' => (int) $data['gross_amount'],
                        'change_amount' => 0,
                        'payment_method' => 'qris',
                        'payment_status' => 'lunas',
                        'paid_at' => now(),
                    ]);

                    foreach ($cached['itemsData'] as $entry) {
                        $detail = TransactionDetail::create([
                            'transaction_id' => $transaction->id,
                            'product_variation_id' => $entry['variation_id'],
                            'product_name' => $entry['product_name'],
                            'variation_name' => $entry['variation_name'],
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

                    return $transaction;
                });

                cache()->forget('midtrans_order_' . $orderId);

                return response()->json([
                    'success' => true,
                    'status' => 'settlement',
                    'message' => 'Pembayaran QRIS Midtrans Berhasil!',
                    'transaction' => [
                        'invoice_number' => $tx->invoice_number,
                        'total_amount' => $tx->total_amount,
                        'paid_amount' => $tx->paid_amount,
                        'change_amount' => 0,
                        'payment_method' => 'qris',
                        'created_at' => $tx->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') . ' WIB',
                        'items' => collect($cached['itemsData'])->map(fn($i) => [
                            'product_name' => $i['product_name'],
                            'variation_name' => $i['variation_name'],
                            'quantity' => $i['quantity'],
                            'unitPrice' => $i['price'],
                            'subtotal' => $i['subtotal'],
                            'toppings' => $i['toppings'],
                        ]),
                    ],
                ]);
            }

            return response()->json([
                'success' => true,
                'status' => $status,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'status' => 'pending'], 200);
        }
    }

    /**
     * Batalkan QRIS Midtrans jika kasir menutup popup.
     */
    public function cancelMidtransQris(Request $request, string $orderId): JsonResponse
    {
        try {
            $this->midtransClient()
                ->post($this->getMidtransBaseUrl() . '/' . $orderId . '/cancel');

            cache()->forget('midtrans_order_' . $orderId);

            return response()->json(['success' => true, 'message' => 'QRIS dibatalkan.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => true]);
        }
    }
}
