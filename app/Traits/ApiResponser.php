<?php

namespace App\Traits;

trait ApiResponser
{
  public function successResponse($data = [], $code = 200)
  {
    return response()->json([
      'code' => $code,
      'message' => 'success',
      'data' => $data
    ], $code);
  }

  public function errorResponse($message, $code = 400)
  {
    return response()->json([
      'code' => $code,
      'message' => $message,
    ], $code);
  }
}
