<?php

namespace App\Http\Controllers\Holding;

use App\Http\Controllers\Controller;
use App\Models\Holding;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class PublicHoldingController extends Controller
{
    use ApiResponser;

    public function get($id)
    {
        $holding = Holding::where('id', $id)->with(['umkms'])->first();

        if (!$holding)
            return $this->errorResponse($holding);

        return $this->successResponse($holding);
    }
}
