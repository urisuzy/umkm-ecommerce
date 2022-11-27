<?php

namespace App\Http\Controllers\Pembelian;

use App\Enums\OrderEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\PembelianResource;
use App\Mail\OrderShipped;
use App\Models\Pembelian;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SellerPembelianController extends Controller
{
    use ApiResponser;

    public function list(Request $request, $umkmId)
    {
        try {
            $request->validate([
                'perpage' => ['required'],
                'order' => ['required', 'in:asc,desc'],
                'orderby' => ['required', 'in:id,paid_at,sent_at'],
                'status' => ''
            ]);

            $pembelians = Pembelian::with(['details', 'buyer', 'seller'])->where('umkm_id', $umkmId);

            $orderStatus = OrderEnum::getConstants();
            if ($request->filled('status') && in_array($request->status, $orderStatus))
                $pembelians = $pembelians->where('status', $request->status);

            $pembelians = $pembelians->orderBy($request->orderby, $request->order);

            $pembelians = $pembelians->paginate($request->perpage);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }

        return $this->paginateSuccessResponse(PembelianResource::collection($pembelians));
    }

    public function get($umkmId, $id)
    {
        $pembelian = Pembelian::where('id', $id)->where('umkm_id', $umkmId)->with(['details', 'buyer', 'seller'])->first();

        if (!$pembelian)
            return $this->errorResponse('Pembelian not found', 404);

        return $this->successResponse(new PembelianResource($pembelian));
    }

    public function updateResi(Request $request, $umkmId, $id)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'no_resi' => 'required'
            ]);

            $pembelian = Pembelian::where('umkm_id', $umkmId)->where('id', $id)->first();

            if (!$pembelian)
                return $this->errorResponse('Pembelian not found', 404);

            if ($pembelian->status != OrderEnum::PAID)
                return $this->errorResponse('Pembelian belum dibayar', 422);

            $pembelian->status = OrderEnum::SENT;
            $pembelian->no_resi = $request->no_resi;
            $pembelian->sent_at = date("Y-m-d H:i:s");
            $pembelian->save();

            // SEND MAIL
            Mail::to($pembelian->buyer->email)->send(new OrderShipped());
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(new PembelianResource($pembelian));
    }
}
