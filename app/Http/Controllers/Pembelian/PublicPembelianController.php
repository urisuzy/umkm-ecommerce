<?php

namespace App\Http\Controllers\Pembelian;

use App\Enums\OrderEnum;
use App\Http\Controllers\Controller;
use App\Mail\OrderReceived;
use App\Models\Pembelian;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PublicPembelianController extends Controller
{
    use ApiResponser;

    public function updateReceived($id)
    {
        DB::beginTransaction();
        try {
            $pembelian = Pembelian::where('id', $id)->first();

            if (!$pembelian)
                return $this->errorResponse('Pembelian not found', 404);

            if ($pembelian->status != OrderEnum::SENT)
                return $this->errorResponse('Pembelian tidak sedang dikirim', 422);

            $pembelian->status = OrderEnum::RECEIVED;
            $pembelian->save();

            // SEND MAIL
            Mail::to($pembelian->buyer->email)->send(new OrderReceived());
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e, 400);
        }
        DB::commit();
        return $this->successResponse($pembelian);
    }
}
