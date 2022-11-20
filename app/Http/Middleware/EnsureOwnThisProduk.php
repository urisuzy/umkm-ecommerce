<?php

namespace App\Http\Middleware;

use App\Models\Produk;
use App\Traits\ApiResponser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureOwnThisProduk
{
    use ApiResponser;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $produk = Produk::with('umkm')->where('id', $request->route('id'))->first();
        if (!$produk)
            return $this->errorResponse('Produk not found', 404);

        if ($produk->umkm->user_id != Auth::id())
            return $this->errorResponse('Access denied', 403);

        return $next($request);
    }
}
