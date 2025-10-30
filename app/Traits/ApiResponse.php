<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

trait ApiResponse
{

    protected function successResponse($data, $code, $message = 'success')
    {
        return response()->json(['status_code' => $code, "message" => $message, 'result' => $data]);
    }

    protected function errorResponse($code, $error, $message = 'false')
    {
        return response()->json(['status_code' => $code, 'error' => $error, 'messsage' => $message]);
    }

    protected function showMessage($message, $code)
    {
        return response()->json(['message' => $message, 'code' => $code]);
    }

    protected function showAll(LengthAwarePaginator $paginator, $code = 200)
    {
        $collection = $this->paginateResponse($paginator);
        return $this->successResponse($collection, $code);
    }


    protected function showOne(Model $model, $code = 200)
    {
        return $this->successResponse($model, $code);
    }

    protected function paginateResponse(LengthAwarePaginator $paginator)
    {
        return [
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ];
    }
}
