<?php

namespace App\Http\Controllers\Produk;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProdukResource;
use App\Models\Produk;
use App\Models\ProdukFoto;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SellerProdukController extends Controller
{
    use ApiResponser;

    public function store(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string'],
            'harga' => ['required', 'numeric'],
            'diskon' => ['required', 'numeric', 'min:0', 'max:99'],
            'foto' => ['required', 'array']
        ]);

        DB::beginTransaction();
        try {
            $produk = Produk::create([
                'umkm_id' => (int) $request->route('umkmId'),
                'nama' => $request->nama,
                'harga' => $request->harga,
                'diskon' => $request->diskon
            ]);
            foreach ($request->foto as $foto) {
                ProdukFoto::where('id', $foto)->update(['produk_id' => $produk->id]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(new ProdukResource($produk));
    }

    public function get($umkmId, $id)
    {
        try {
            $produk = Produk::where('id', $id)->with(['fotos'])->first();

            if (!$produk)
                return $this->errorResponse('Produk not found', 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
        return $this->successResponse(new ProdukResource($produk));
    }

    public function list(Request $request)
    {
        $umkmId = $request->route('umkmId');
        $produks = Produk::where('umkm_id', $umkmId)->orderByDesc('id')->get();
        return $this->successResponse(ProdukResource::collection($produks));
    }

    public function update(Request $request, $umkmId,  $id)
    {
        DB::beginTransaction();
        try {
            $produk = Produk::where('id', $id)->first();

            if (!$produk)
                throw new \Exception('Produk not found', 404);

            if ($request->filled('nama'))
                $produk->nama = $request->nama;

            if ($request->filled('harga'))
                $produk->harga = $request->harga;

            if ($request->filled('diskon'))
                $produk->diskon = $request->diskon;

            $produk->save();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(new ProdukResource($produk));
    }

    public function delete($umkmId, $id)
    {
        DB::beginTransaction();
        try {
            $produk = Produk::where('id', $id)->first();

            if (!$produk)
                throw new \Exception('Produk not found', 404);

            $produk->delete();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(true);
    }
}
