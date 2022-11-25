<?php

namespace App\Traits;

trait ApiResponser
{
  public function successResponse($data = [], $code = 200)
  {
    return response()->json([
      'message' => 'success',
      'data' => $data
    ], $code);
  }

  public function errorResponse($message, $code = 400)
  {
    return response()->json([
      'message' => $message,
    ], $code);
  }

  public function paginateSuccessResponse($paginated, $code = 200)
  {
      return $this->successResponse([
          'current_page' => $paginated->currentPage(),
          'per_page' => (int) $paginated->perPage(),
          'total' => $paginated->total(),
          'data' => $paginated->items()
      ], $code);
  }
}
