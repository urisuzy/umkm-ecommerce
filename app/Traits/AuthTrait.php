<?php

namespace App\Traits;

trait AuthTrait
{
  public function urlSuccessVerified()
  {
    return config('app.frontend_url') . '/auth/success-verified';
  }

  public function urlFailVerified()
  {
    return config('app.frontend_url') . '/auth/failed-verified';
  }
}
