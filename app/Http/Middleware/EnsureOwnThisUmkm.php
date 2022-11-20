<?php

namespace App\Http\Middleware;

use App\Models\Umkm;
use App\Traits\ApiResponser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureOwnThisUmkm
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
        $umkm = Umkm::where('id', $request->route('umkmId'))->first();
        if (!$umkm)
            return $this->errorResponse('Umkm not found', 404);

        if ($umkm->user_id != Auth::id())
            return $this->errorResponse('Access denied', 403);

        return $next($request);
    }
}
