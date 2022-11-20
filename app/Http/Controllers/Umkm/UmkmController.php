<?php

namespace App\Http\Controllers\Umkm;

use App\Http\Controllers\Controller;
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
        try {
            $request->validate([
                'nama_umkm' => ['required', 'max:255', 'string'],
                'alamat' => ['required', 'string', 'max:255'],
                'no_telp_umkm' => ['required', 'numeric']
            ]);

            $user = Auth::user();

            $umkm = Umkm::create([
                'user_id' => $user->id,
                ...$request->toArray()
            ]);

            return $this->successResponse($umkm);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    public function list()
    {
        $umkms = Umkm::where('user_id', Auth::id())->get();
        return $this->successResponse($umkms);
    }

    public function get($umkmId)
    {
        try {
            $umkm = Umkm::where('user_id', Auth::id())->where('id', $umkmId)->first();

            if (!$umkm)
                return $this->errorResponse('Umkm not found', 404);

            return $this->successResponse($umkm);
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
        return $this->successResponse($umkm);
    }
}
