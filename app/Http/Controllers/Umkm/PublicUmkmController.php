<?php

namespace App\Http\Controllers\Umkm;

use App\Http\Controllers\Controller;
use App\Http\Resources\UmkmResource;
use App\Models\Umkm;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicUmkmController extends Controller
{
    use ApiResponser;

    public function get($id)
    {
        $umkm = Umkm::where('id', $id)->with(['holdings'])->first();

        if (!$umkm)
            return $this->errorResponse('Umkm not found', 404);

        return $this->successResponse(new UmkmResource($umkm));
    }

    public function list(Request $request)
    {
        try {
            $request->validate([
                'perpage' => ['required'],
                'orderby' => ['required', 'in:id,nama'],
                'order' => ['required', 'in:desc,asc']
            ]);

            $umkms = Umkm::query();

            if ($request->filled('holding_id')) {
                $holdingId = $request->holding_id;
                $umkms = $umkms->whereHas('holdings', function ($query) use ($holdingId) {
                    $query->where('holding_id', $holdingId);
                });
            }

            if ($request->filled('user_id'))
                $umkms = $umkms->where('user_id', $request->user_id);

            $umkms = $umkms->orderBy($request->orderby, $request->order);

            $umkms = $umkms->paginate($request->perpage);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }

        return $this->paginateSuccessResponse(UmkmResource::collection($umkms));
    }
}
