<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SliderManagement;
use Illuminate\Http\Request;

class SliderApiController extends Controller
{
    public function index_slider()
    {
        try {
            $slider = SliderManagement::where('status', '1')->get();
            return response()->json(['status' => true,  'data' => $slider]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()]);
        }
    }
}
