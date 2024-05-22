<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TestimonialApiController extends Controller
{
    public function gettestimonial()
    {
        try {
            $data = Testimonial::all();

            $visibleTestimonials = [];

            foreach ($data as $testimonial) {
                if ($testimonial->status == 1) {
                    $visibleTestimonials[] = $testimonial;
                }
            }
        
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => [$visibleTestimonials]],
                'timestamp' => Carbon::now(),
                'message' => 'Data Fetch Successfully ',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'http_status_code' =>500,
                    'status' => false,
                    'context' => ['error' => $e->getMessage()],
                    'timestamp' => Carbon::now(),
                    'message' => 'An unexpected error occurred',
                ],
                500
            );
        }
    }
    public function add_testimonial(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required',
                'rating' => 'required',
                'file' => 'required',
                'status' => 'required',
                'content' => 'required',
    
            ]);
    
            if (!$validatedData) {
                return response()->json(['status' => false, 'message' => 'Dispute not found.'], 404);
            }
            $dispute = new Testimonial();
            $dispute->name = $validatedData['name'];
            $dispute->status = $validatedData['status'];
            $dispute->testimonial = $validatedData['content'];
            $dispute->rating = $validatedData['rating'];
    
            if ($request->hasFile('file')) {
                $image = $request->file('file');
                $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('admin/testimonial');
                $image->move($destinationPath, $image_name);
                $dispute->profile_pics = $image_name;
            }
         else {
                $dispute->profile_pics  = null;
            }
    
    
            $dispute->save();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => [$dispute]],
                'timestamp' => Carbon::now(),
                'message' => 'Data Add Testimonial Successfully ',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'http_status_code' =>500,
                    'status' => false,
                    'context' => ['error' => $e->getMessage()],
                    'timestamp' => Carbon::now(),
                    'message' => 'An unexpected error occurred',
                ],
                500
            );
        }
    }
    public function edit_testimonial(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id' => 'required',
              
    
            ]);
    
            if (!$validatedData) {
                return response()->json(['status' => false, 'message' => 'Dispute not found.'], 404);
            }
            $dispute = Testimonial::find($request->id);
            $dispute->testimonial = $request->content;
            $dispute->rating = $request->rating;
            $dispute->status = $request->status;
         
            if ($request->hasFile('name_file')) {
                $image = $request->file('name_file');
                $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('admin/testimonial');
                $image->move($destinationPath, $image_name);
                $dispute->profile_pics = $image_name;
            }
            $dispute->save();
    
    
            $dispute->save();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => [$dispute]],
                'timestamp' => Carbon::now(),
                'message' => 'Data update Testimonial Successfully ',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'http_status_code' =>500,
                    'status' => false,
                    'context' => ['error' => $e->getMessage()],
                    'timestamp' => Carbon::now(),
                    'message' => 'An unexpected error occurred',
                ],
                500
            );
        }
    }
    public function delete_testimonial(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id' => 'required',
              
    
            ]);
    
            if (!$validatedData) {
                return response()->json(['status' => false, 'message' => 'Dispute not found.'], 404);
            }
          
            $data = Testimonial::find($request->id);
            $data->delete();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => [$data]],
                'timestamp' => Carbon::now(),
                'message' => 'Data delete  Successfully ',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'http_status_code' =>500,
                    'status' => false,
                    'context' => ['error' => $e->getMessage()],
                    'timestamp' => Carbon::now(),
                    'message' => 'An unexpected error occurred',
                ],
                500
            );
        }
    }
}
