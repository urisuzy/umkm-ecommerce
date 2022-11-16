<?php

namespace App\Http\Controllers\Foto;

use App\Enums\DiskEnum;
use App\Http\Controllers\Controller;
use App\Models\ProdukFoto;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FotoProdukController extends Controller
{
    use ApiResponser;

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'foto' => ['required', 'file']
            ]);

            DB::beginTransaction();
            try {
                $path = $request->file('foto')->store('produk', DiskEnum::IMAGE);

                $fotoProduk = ProdukFoto::create([
                    'path_foto' => $path
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->errorResponse($e->getMessage());
            }
            DB::commit();
            return $this->successResponse($fotoProduk->id);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}
