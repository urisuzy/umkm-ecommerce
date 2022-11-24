<?php

namespace App\Http\Controllers\PaymentGateway;

use App\Enums\OrderEnum;
use App\Http\Controllers\Controller;
use App\Mail\PaymentReceived;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class IpaymuController extends Controller
{
    public function webhook(Request $request)
    {
        $array = $request->toArray();

        $pembelian = Pembelian::where('payment_code', $array['sid'])->with('buyer')->first();

        if ($pembelian && $pembelian->status == OrderEnum::UNPAID) {
            $pembelian->status = OrderEnum::PAID;
            $pembelian->paid_at = date("Y-m-d H:i:s");
            $pembelian->save();

            Mail::to($pembelian->buyer->email)->send(new PaymentReceived());
        }

        return response('sukses masuk');
    }
}
