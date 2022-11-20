<?php

namespace App\Http\Controllers\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IpaymuController extends Controller
{
    public function webhook(Request $request)
    {
        //
        info($request->all());
        return response('sukses masuk');
    }
}
