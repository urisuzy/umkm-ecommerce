<?php

namespace App\Http\Controllers\Umkm;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

            if ($user->umkm)
                $this->errorResponse('User has umkm', 403);

            $umkm = Umkm::create([
                'user_id' => $user->id,
                ...$request->toArray()
            ]);

            return $this->successResponse($umkm);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}
