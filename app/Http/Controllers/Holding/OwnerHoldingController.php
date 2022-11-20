<?php

namespace App\Http\Controllers\Holding;

use App\Enums\DiskEnum;
use App\Http\Controllers\Controller;
use App\Models\Holding;
use App\Models\Umkm;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Termwind\Components\Raw;

class OwnerHoldingController extends Controller
{
    use ApiResponser;

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'nama' => 'required'
            ]);

            $data = [
                'user_id' => Auth::id(),
                'nama' => $request->nama,
            ];

            if ($request->has('foto')) {
                $data['foto'] = $request->file('foto')->store('holding', DiskEnum::IMAGE);
            }

            $holding = Holding::create($data);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse($holding);
    }

    public function get($id)
    {
        $holding = Holding::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$holding)
            return $this->errorResponse($holding);

        return $this->successResponse($holding);
    }

    public function list()
    {
        $holdings = Holding::where('user_id', Auth::id())->orderByDesc('id')->withCount('umkms')->get();

        return $this->successResponse($holdings);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $holding = Holding::where('id', $id)->where('user_id', Auth::id())->first();

            if (!$holding)
                return $this->errorResponse('Holding not found', 404);

            if ($request->filled('nama'))
                $holding->nama = $request->nama;

            if ($request->has('foto')) {
                $path = $request->file('foto')->store('holding', DiskEnum::IMAGE);
                $holding->foto = $path;
            }

            $holding->save();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse($holding);
    }

    public function addUmkmToHolding(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'umkm_id' => ['required', 'exists:umkm,id']
            ]);

            $holding = Holding::where('id', $id)->where('user_id', Auth::id())->first();
            $umkm = Umkm::where('id', $request->umkm_id)->where('user_id', Auth::id())->first();

            if (!$holding)
                return $this->errorResponse('Holding not found', 404);

            if (!$umkm)
                return $this->errorResponse('Umkm not found', 404);

            $holding->umkms()->attach($request->umkm_id);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(true);
    }

    public function removeUmkmFromHolding(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'umkm_id' => ['required', 'exists:umkm,id']
            ]);

            $holding = Holding::where('id', $id)->where('user_id', Auth::id())->with('umkms')->first();
            $umkm = Umkm::where('id', $request->umkm_id)->where('user_id', Auth::id())->first();

            if (!$holding)
                return $this->errorResponse('Holding not found', 404);

            if (!$umkm)
                return $this->errorResponse('Umkm not found', 404);

            if (!$holding->umkms()->where('umkm_id', $request->umkm_id)->first())
                return $this->errorResponse('Umkm not connected to this holding', 422);

            $holding->umkms()->detach($request->umkm_id);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(true);
    }
}
