<?php

namespace App\Http\Controllers\Produk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SellerProdukController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string'],
            'harga' => ['required', 'numeric'],
            'diskon' => ['required', 'numeric', 'min:0', 'max:99'],
            'foto' => ['required', 'array']
        ]);

        foreach ($request->foto as $foto) {
            echo $foto;
        }
    }

    public function get($id)
    {
        //
    }

    public function list(Request $request)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function delete($id)
    {
        //
    }
}
