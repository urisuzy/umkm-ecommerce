<?php

namespace App\Http\Controllers\Produk;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class PublicProdukController extends Controller
{
    use ApiResponser;

    public function list(Request $request)
    {
        try {
            $request->validate([
                'nama' => '',
                'umkm_id' => '',
                'perpage' => 'required',
                'order' => ['required', 'in:asc,desc'],
                'orderby' => ['required', 'in:id,nama,harga,diskon']
            ]);

            $produks = Produk::with(['umkm']);

            if ($request->filled('nama'))
                $produks = $produks->where('nama', 'like', "%{$request->nama}%");

            if ($request->filled('umkm_id'))
                $produks = $produks->where('umkm_id', $request->umkm_id);

            $produks = $produks->orderBy($request->orderby, $request->order);

            $produks = $produks->paginate($request->perpage);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }

        return $this->paginateSuccessResponse($produks);
    }

    public function get($id)
    {
        $produk = Produk::where('id', $id)->with('umkm')->first();

        if (!$produk)
            return $this->errorResponse('Produk not found', 404);

        return $this->successResponse($produk);
    }
}