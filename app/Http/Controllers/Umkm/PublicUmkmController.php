<?php

namespace App\Http\Controllers\Umkm;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class PublicUmkmController extends Controller
{
    use ApiResponser;

    public function get($id)
    {
        $umkm = Umkm::where('id', $id)->with(['holdings'])->first();

        if (!$umkm)
            return $this->errorResponse('Umkm not found', 404);

        return $this->successResponse($umkm);
    }
}
