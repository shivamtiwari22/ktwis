<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //

    public function getAllCategory()
    {
        try {
            $data = Category::all();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $data],
                'timestamp' => Carbon::now(),
                'message' => 'Show all Category data successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'http_status_code' => 500,
                    'status' => false,
                    'context' => ['error' => $e->getMessage()],
                    'timestamp' => Carbon::now(),
                    'message' => 'An unexpected error occurred',
                ],
                500
            );        }

    }
}
