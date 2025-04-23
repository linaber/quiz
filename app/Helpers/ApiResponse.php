<?php


namespace App\Helpers;


class ApiResponse
{
    public static function error(string $key, array $data = [])
    {
        return response()->json([
            'error' => [
                'code' => __('api/errors.codes.'.$key),
                'message' => __('api/errors.messages.'.$key, $data),
            ]
        ], __('api/errors.codes.'.$key));
    }
}
