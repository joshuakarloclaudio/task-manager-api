<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class RequestHelper
{
    public static function formatPagination(Request $request): array
    {
        $page = $request->integer('page');
        $perPage = $request->integer('per_page', 10);
        $orderColumn = $request->query('order_column', 'id');
        $orderType = $request->query('order_type', 'asc');

        return [
            'page' => $page,
            'per_page' => $perPage,
            'order_column' => $orderColumn,
            'order_type' => $orderType,
        ];
    }
}
