<?php

namespace App\Http\Controllers\Umkm;

use App\Http\Controllers\Controller;
use App\Http\Resources\UmkmResource;
use App\Models\Holding;
use App\Models\Umkm;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UmkmController extends Controller
{
    use ApiResponser;
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'nama_umkm' => ['required', 'max:255', 'string'],
                'alamat' => ['required', 'string', 'max:255'],
                'no_telp_umkm' => ['required', 'numeric'],
                'holding_id' => []
            ]);

            $user = Auth::user();

            if ($request->filled('holding_id')) {
                $holding = Holding::where('user_id', Auth::id())->where('id', $request->holding_id)->first();

                if (!$holding)
                    return $this->errorResponse('Holding not found', 404);
            }

            $umkm = Umkm::create([
                'user_id' => $user->id,
                ...$request->toArray()
            ]);

            if ($request->filled('holding_id'))
                $holding->umkms()->attach($umkm->id);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 400);
        }
        DB::commit();
        return $this->successResponse(new UmkmResource($umkm));
    }

    public function list(Request $request)
    {
        $umkms = Umkm::where('user_id', Auth::id());

        if ($request->filled('holding_id'))
            $umkms = $umkms->whereRelation('holdings', 'holding_id', $request->holding_id);

        $umkms = $umkms->get();
        return $this->successResponse(UmkmResource::collection($umkms));
    }

    public function get($umkmId)
    {
        try {
            $umkm = Umkm::where('user_id', Auth::id())->where('id', $umkmId)->first();

            if (!$umkm)
                return $this->errorResponse('Umkm not found', 404);

            return $this->successResponse(new UmkmResource($umkm));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function update(Request $request, $umkmId)
    {
        DB::beginTransaction();
        try {
            $umkm = Umkm::where('user_id', Auth::id())->where('id', $umkmId)->first();

            if (!$umkm)
                throw new \Exception('Umkm not found');

            if ($request->filled('nama_umkm'))
                $umkm->nama_umkm = $request->nama_umkm;

            if ($request->filled('alamat'))
                $umkm->alamat = $request->alamat;

            if ($request->filled('no_telp_umkm'))
                $umkm->no_telp_umkm = $request->no_telp_umkm;

            $umkm->save();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(new UmkmResource($umkm));
    }
}
