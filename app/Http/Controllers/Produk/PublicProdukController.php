<?php

namespace App\Http\Controllers\Produk;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProdukResource;
use App\Http\Resources\ReviewResource;
use App\Models\Pembelian;
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
                'search' => '',
                'umkm_id' => '',
                'perpage' => 'required',
                'order' => ['required', 'in:asc,desc'],
                'orderby' => ['required', 'in:id,nama,harga,diskon']
            ]);

            $produks = Produk::with(['umkm', 'fotos']);

            if ($request->filled('search'))
                $produks = $produks->where('nama', 'like', "%{$request->search}%");

            if ($request->filled('umkm_id'))
                $produks = $produks->where('umkm_id', $request->umkm_id);

            $produks = $produks->orderBy($request->orderby, $request->order);

            $produks = $produks->paginate($request->perpage);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }

        return $this->paginateSuccessResponse(ProdukResource::collection($produks));
    }

    public function get($id)
    {
        $produk = Produk::where('id', $id)->with('umkm')->first();

        if (!$produk)
            return $this->errorResponse('Produk not found', 404);

        return $this->successResponse(new ProdukResource($produk));
    }

    public function listReviews($id)
    {
        $pembelians = Pembelian::query()
            ->whereNotNull('review')
            ->whereRelation('details', 'produk_id', $id)
            ->with('buyer')
            ->get();

        if (!$pembelians)
            return $this->errorResponse('Produk tidak ditemukan atau produk tidak memiliki review', 404);

        return $this->successResponse(ReviewResource::collection($pembelians));
    }
}
