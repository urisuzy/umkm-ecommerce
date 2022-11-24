<?php

namespace App\Http\Controllers\Pembelian;

use App\Enums\OrderEnum;
use App\Http\Controllers\Controller;
use App\Models\DetailPembelian;
use App\Models\Pembelian;
use App\Models\Produk;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BuyerPembelianController extends Controller
{
    use ApiResponser;

    public function list(Request $request)
    {
        try {
            $request->validate([
                'perpage' => ['required'],
                'order' => ['required', 'in:asc,desc'],
                'orderby' => ['required', 'in:id,paid_at,sent_at'],
                'status' => ''
            ]);

            $pembelians = Pembelian::with(['details', 'buyer', 'seller'])->where('buyer_id', Auth::id());

            $orderStatus = OrderEnum::getConstants();
            if ($request->filled('status') && in_array($request->status, $orderStatus))
                $pembelians = $pembelians->where('status', $request->status);

            $pembelians = $pembelians->orderBy($request->orderby, $request->order);

            $pembelians = $pembelians->paginate($request->perpage);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }

        return $this->paginateSuccessResponse($pembelians);
    }

    public function get($id)
    {
        $pembelians = Pembelian::where('id', $id)->where('buyer_id', Auth::id())->with(['details', 'buyer', 'seller'])->first();

        if (!$pembelians)
            return $this->errorResponse('Pembelian not found', 404);

        return $this->successResponse($pembelians);
    }

    public function create(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'orders' => ['required', 'array'],
                'orders.*.produk_id' => ['required', 'exists:produk,id'],
                'orders.*.jumlah' => ['required', 'numeric']
            ]);

            $orders = $request->toArray()['orders'];
            $umkmId = null;
            $sellerId = null;
            $time = time();
            $noInvoice = $time;
            $details = [];
            $paymentData = [];

            foreach ($orders as $order) {
                $produk = Produk::where('id', $order['produk_id'])->with('umkm')->first();

                if ($produk->umkm->user_id == Auth::id())
                    throw new \Exception("Dilarang membeli produk sendiri");

                if ($umkmId && $umkmId != $produk->umkm->id)
                    throw new \Exception("Harus membeli produk di umkm yang sama");

                $umkmId = $produk->umkm->id;
                $sellerId = $produk->umkm->user_id;
                $totalHarga = $produk->harga - ($produk->diskon * $produk->harga / 100);
                $details[] = [
                    'produk_id' => $produk->id,
                    'diskon' => $produk->diskon,
                    'jumlah_barang' => $order['jumlah'],
                    'total_harga' => $totalHarga
                ];
                $paymentData['product'][] = $produk->nama;
                $paymentData['qty'][] = $order['jumlah'];
                $paymentData['total'][] = $totalHarga;
            }

            // GENERATE SESSION
            $service = new \App\Services\IPaymu;
            $makePayment = $service->makeRedirectPayment($paymentData['product'], $paymentData['qty'], $paymentData['total'], $noInvoice);

            $pembelian = Pembelian::create([
                'buyer_id' => Auth::id(),
                'seller_id' => $sellerId,
                'no_kuitansi' => $noInvoice,
                'status' => OrderEnum::UNPAID,
                'payment_code' => $makePayment['session_id']
            ]);

            for ($i = 0; $i < count($details); $i++) {
                $details[$i]['pembelian_id'] = $pembelian->id;
                DetailPembelian::create($details[$i]);
            }

            $get = Pembelian::with(['details', 'buyer', 'seller'])->where('id', $pembelian->id)->first();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse($get);
    }

    public function redirectPay($id)
    {
        $pembelian = Pembelian::where('id', $id)->first();

        if (!$pembelian)
            return redirect(config('app.frontend_url') . '/404');

        if ($pembelian->status != OrderEnum::UNPAID)
            return redirect(config('app.frontend_url') . '/404');

        return redirect("https://sandbox.ipaymu.com/payment/{$pembelian->payment_code}");
    }
}
