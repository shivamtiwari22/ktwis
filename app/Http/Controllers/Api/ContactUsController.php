<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminContactUs;
use App\Models\Contactus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;


class ContactUsController extends Controller
{
    //
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
            'subject' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(['http_status_code' => 422 ,'status' => false, 'context' =>  ['error' => $validator->errors()->first()] ,  'timestamp'=> Carbon::now() , 'message' => 'Validation failed'], 422);
        } 
        $user = Contactus::create($request->all());
    
        if($user){
            return response()->json([ 'http_status_code' => 200 ,'status' => true, 'context' =>  ['data' => [$user]] ,  'timestamp'=> Carbon::now() , 'message' => "Successfully Sent"]);
        }else{
            return response()->json([ 'http_status_code' => 500 ,'status' => false, 'context' =>  ['error' => 'An unexpected error occurred' ] ,  'timestamp'=> Carbon::now() , 'message' => "Something Went Wrong" ],500); 
        }
    }



    public function contact_us(){
        $user = AdminContactUs::take(1)->first() ?? null;
        if($user){
            return response()->json([ 'http_status_code' => 200 ,'status' => true, 'context' =>  ['data' => [$user]] ,  'timestamp'=> Carbon::now() , 'message' => "fetched successfully"]);
        }else{
            return response()->json([ 'http_status_code' => 404 ,'status' => false, 'context' =>  ['error' => 'data not found' ] ,  'timestamp'=> Carbon::now() , 'message' => "data not found" ],404); 
        }
    }
}


 
