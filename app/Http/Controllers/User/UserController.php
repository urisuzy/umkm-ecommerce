<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use ApiResponser;

    public function me()
    {
        $user = User::where('id', Auth::id())->with(['profile', 'umkms', 'holdings'])->first();
        return $this->successResponse($user);
    }
}
