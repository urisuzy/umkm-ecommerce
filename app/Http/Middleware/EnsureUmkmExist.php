<?php

namespace App\Http\Middleware;

use App\Models\Umkm;
use App\Traits\ApiResponser;
use Closure;
use Illuminate\Http\Request;

class EnsureUmkmExist
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
        if ($request->route('umkmId') == 'all')
            return $next($request);

        $umkm = Umkm::where('id', $request->route('umkmId'))->first();
        if (!$umkm)
            return $this->errorResponse('Umkm not found', 404);

        return $next($request);
    }
}
