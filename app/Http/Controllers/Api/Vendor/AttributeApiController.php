<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Variant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\FuncCall;

class AttributeApiController extends Controller
{
    public function get_attributes()
    {
        try {
            $attributes = Attribute::with(['categories', 'attributeValues'])->where('created_by', auth('api')->user()->id)->get();
            if ($attributes) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $attributes],
                    'timestamp' => Carbon::now(),
                    'message' =>  'Attribute fetched successfully',
                ]);
            } else {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $attributes],
                    'timestamp' => Carbon::now(),
                    'message' => 'Data not fetched',
                ]);
            }
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
            );
            // return response()->json(['status' => false, 'msg' => $e->getMessage()]);
        }
    }


    public function get_attributes_by_id($id)
    {
        try {

            $attributes = Attribute::where('id', $id)->with(['categories', 'attributeValues'])->get();
            if ($attributes) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $attributes],
                    'timestamp' => Carbon::now(),
                    'message' => 'Attribute by Id fetched successfully',
                ]);
            } else {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $attributes],
                    'timestamp' => Carbon::now(),
                    'message' => 'Data not fetched',
                ]);
            }
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
            );
        }
    }

    public function store_attributes(Request $request)
    {
        try {
            
            $validator = Validator::make($request->all(), [
                'attribute_type' => 'required',
                'name' => 'required',
                'order' => 'nullable|numeric',
            ]);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'http_status_code' => 422,
                        'status' => false,
                        'context' => ['error' => 'Validation failed'],
                        'timestamp' => Carbon::now(),
                        'message' =>  $validator->errors()->first(),
                    ],
                    422
                );
            }
            $user = Auth::user();
            $attribute = new Attribute();
            $attribute->attribute_type = $request->attribute_type;
            $attribute->attribute_name = $request->name;
            $attribute->list_order = $request->order;
            $attribute->created_by = $user->id;
            $attribute->updated_by = $user->id;
            $attribute->save();


            $categoryIds = $request->input('categories');
            $attribute->categories()->attach($categoryIds);

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $attribute],
                'timestamp' => Carbon::now(),
                'message' => 'Attribute Store Successfully',
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
            );
        }
    }


    public function update_attributes(Request $request)
    {
        try {
          
            $validator = Validator::make($request->all(), [
                'attr_id' => 'required',
                'attribute_type' => 'required',
                'name' => 'required',
                'order' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'http_status_code' => 422,
                        'status' => false,
                        'context' => ['error' => 'Validation failed'],
                        'timestamp' => Carbon::now(),
                        'message' => $validator->errors()->first(),
                    ],
                    422
                );
            }

            $id = $request->attr_id;
            $user = Auth::user();
            $attribute = Attribute::find($id);
            $attribute->attribute_type = $request->attribute_type;
            $attribute->attribute_name = $request->name;
            $attribute->list_order = $request->order;
            $attribute->updated_by = $user->id;
            $attribute->save();
            $categoryIds = $request->input('category');
            $attribute->categories()->sync($categoryIds);
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $attribute],
                'timestamp' => Carbon::now(),
                'message' => 'Attribute Updated Successfully',
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



    public function delete_attribute($id){
        try {

            $attribute = Attribute::where('id', $id)->delete();
            $attr_value =AttributeValue::where('attribute_id', $id)->delete();
    
            $variants_by_attr = Variant::whereRaw("FIND_IN_SET(?,attr_id)",[$id])->delete();
    
              Attribute::where('id', $id)->forceDelete();
             AttributeValue::where('attribute_id', $id)->forceDelete();

             return response()->json(
                [
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' =>[]],
                    'timestamp' => Carbon::now(),
                    'message' => 'Attribute Deleted Successfully',
                ],
                200
            ); 

        }
        catch (\Exception $e) {
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


    public function get_attributes_values()
    {
        try {
            $attribute_values = AttributeValue::with('attribute.categories')->get();
            if ($attribute_values) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $attribute_values],
                    'timestamp' => Carbon::now(),
                    'message' => 'Attribute Value fetched successfully',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Data Not Found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Data Not fetched',
                    ],
                    404
                );
            }
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
            );
        }
    }

    public function get_attributes_values_by_id($id)
    {
        try {
            $attribute_values = AttributeValue::where('id', $id)->first();
            if (!$attribute_values) {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Attributes Not Found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Data Not Found',
                    ],
                    404
                );
            } else {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => [$attribute_values]],
                    'timestamp' => Carbon::now(),
                    'message' => 'Attribute Value by Id fetched successfully',
                ]);
            }
            
            
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

    public function get_attributes_values_by_attr_id($attr_id)
    {
        try {
            $attribute_values = AttributeValue::where('attribute_id', $attr_id)->get();
            if ($attribute_values->isEmpty()) {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Data Not Found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Data Not Found',
                    ],
                    404
                );
            } else {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $attribute_values],
                    'timestamp' => Carbon::now(),
                    'message' => 'Attribute Value by Attr Id fetched successfully',
                ]);
            }
            
        
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
            );          }
    }

    public function add_attribute_value(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'attribute_id' => 'required',
                'attribute_value' => 'required',
                'value_list_order' => 'nullable|numeric',
            ]);
            $user = Auth::user();
            $value_attr = new AttributeValue();
            $value_attr->attribute_id = $request->attribute_id;
            $value_attr->attribute_value = $request->attribute_value;
            $value_attr->value_list_order = $request->value_list_order;
            $value_attr->color_attribute = $request->color_attribute  ;
            $value_attr->created_by = $user->id;
            $value_attr->updated_by = $user->id;
            $value_attr->save();

            if ($value_attr) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => [$value_attr]],
                    'timestamp' => Carbon::now(),
                    'message' => 'Attribute Value stored successfully',
                ]);
            
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Data Not Found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Data Not Found',
                    ],
                    404
                );
            }
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
            );     
        }
    }

    
    public function update_attribute_value(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'value_id'=> 'required',
                'attribute_value' => 'required',
                'value_list_order' => 'nullable|numeric',
            ]);
            $user = Auth::user();
            $value_attr = AttributeValue::find($request->value_id);
            $value_attr->attribute_value = $request->attribute_value;
            $value_attr->value_list_order = $request->value_list_order;
            $value_attr->color_attribute = $request->color_attribute  ;
            $value_attr->updated_by = $user->id;
            $value_attr->save();

            if ($value_attr) {
                return response()->json(['status' => true,  'data' => $value_attr, 'message' => 'Attribute Value updated successfully']);
            } else {
                return response()->json(['status' => false, 'message' => 'Data not updated'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 404);
        }
    }

    public function delete_attribute_value($id){
        try {

            $attr_value = AttributeValue::where('id', $id)->first();
            $variants_by_attr = Variant::whereRaw("FIND_IN_SET(?,attr_id)",[$id])->whereRaw("FIND_IN_SET(?,attr_value_id)",[$id])->delete();
            $attr_value->delete();
    
            AttributeValue::where('id', $id)->forceDelete();

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => []],
                'timestamp' => Carbon::now(),
                'message' => 'Attribute Value Deleted Successfully',
            ]);
        }
        catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 404);
        }
    }


    public function get_vendor_attributes(){
        try {
            $attributes = Attribute::where('status', 'active')->where('created_by', Auth::user()->id)->get();
        
            foreach($attributes as $attribute){
                $attribute->categories_count = $attribute->categories()->count();
                $categories = $attribute->categories()->pluck('category_name')->toArray();
                $attribute->categories_name = implode(',',$categories);
                $attribute_data = Attribute::find($attribute->id);
                $attributeValues = $attribute_data->attributeValues;
                $attribute->attribute_values_count = $attributeValues->count();
    
            }

                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $attributes],
                    'timestamp' => Carbon::now(),
                    'message' =>  'Attribute fetched successfully',
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
            );
        }

    }
}
