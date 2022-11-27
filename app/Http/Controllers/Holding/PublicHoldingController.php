<?php

namespace App\Http\Controllers\Holding;

use App\Http\Controllers\Controller;
use App\Http\Resources\HoldingResource;
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
            return $this->errorResponse('Holding not found', 404);

        return $this->successResponse(new HoldingResource($holding));
    }
}
