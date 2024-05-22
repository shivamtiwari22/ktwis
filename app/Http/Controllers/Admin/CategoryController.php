<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ReturnPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function view($id){
        $category = Category::where('id', $id)->first();
        $categoryNames = [];
        $category_list = Category::where('id', $id)->with('parent')->get();
        $categories = json_decode($category_list, true);
        
        $extractCategoryNames = function ($category_list) use (&$extractCategoryNames, &$categoryNames) {
            foreach ($category_list as $category) {
                $categoryNames[] = $category['category_name'];
                if (isset($category['parent'])) {
                    $extractCategoryNames([$category['parent']]);
                }
            }
        };
        $extractCategoryNames($categories);
        $categoryNames = array_reverse($categoryNames);
        return view('admin.category.view', compact('category', 'categoryNames'));
    }
    public function update(Request $request)
    {
    
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|max:26',
            'parent_category_id ' => 'nullable|exists:categories,id',
            'category_img' => 'mimes:jpeg,jpg,png|max:2000'
        ]);

        $customMessages = [
            'category_img.max' => 'The profile picture must not be larger than 2 MB.',
        ];
        $validator->setCustomMessages($customMessages);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $validator->errors()->first(),
                ],
            );
        }

            $category = Category::find($request->cat_id);
            if($request->category_img){
                $category_img = 'image'.'_'.$request->category_name.time().'.'.request()->category_img->getClientOriginalExtension();
                request()->category_img->move(public_path('admin/category/images/'), $category_img);
            }else{
                $category_img = $category->image;
            }

            $category->category_name = $request->category_name;
            $category->image = $category_img;
            $category->parent_category_id = $request->parent_category_id;
            $category->ogtag = $request->ogtag ;
            $category->schema_markup = $request->schema_markup;
            $category->meta_title   = $request->meta_title ;
            $category->meta_description   = $request->meta_description ;
            $category->keywords   = $request->keywords ;
            $category->slug = $request->slug;
            if ($category->save()) {
                return response()->json(array('status' => true, 'location' =>  route('admin.categories.list'), 'message' => 'Category Updated Successfully'));
            } else {
                return response()->json(array('status' => false, 'message' => 'Something Went Wrong!'));
            }
        
    }
    public function edit($id){
        $category_list = Category::whereNull('parent_category_id')->with('children')->get();
        $category = Category::where('id', $id)->first();
        $parent_category = Category::where('id', $category->parent_category_id)->first();
        return view('admin.category.edit', compact('category', 'parent_category', 'category_list', ));
        
    }
    public function delete_list(Request $request)
    {
        // $delete = Category::where('id', $request->id)->delete();
        // $deleteChild = Category::where('parent_category_id', $request->id)->delete();
        $category = Category::find($request->id);
        $category->deleteCategoryHierarchy($request->id);
        ReturnPolicy::where('category_id',$request->id)->delete();
        if ($category) {
            return response()->json(['status' => true, 'location' => route('admin.categories.list'), 'msg' => "Deleted Successfully"]);
            exit;
        } else {
            return response()->json(['status' => false, 'msg' => "Error Occurred, Please try again"]);
        }
    }
    public function list(){
      return view('admin.category.list');
    }
    public function list_render(Request $request)
    {
        if (isset($request->search['value'])) {
            $search = $request->search['value'];
        } else {
            $search = '';
        }
        if (isset($request->length)) {
            $limit = $request->length;
        } else {
            $limit = 10;
        }
        if (isset($request->start)) {
            $ofset = $request->start;
        } else {
            $ofset = 0;
        }
        $orderRecord = $request->order[0]['dir'];
        $nameOrder = $request->columns[$request->order[0]['column']]['name'];
       
        $total =  Category::select('categories.*')
                ->Where(function ($query) use ($search) {
                        $query->orWhere('categories.category_name', 'like', '%'.$search . '%');
                    });
                    $total = $total->count();

        $category =  Category::select('categories.*')
                ->Where(function ($query) use ($search) {
                    $query->orWhere('categories.category_name', 'like', '%'.$search . '%');
                    });
             
        $category = $category->orderBy('id',$orderRecord)->limit($limit)->offset($ofset)->get();

        $i = 1 + $ofset;
        $data = [];
        foreach ($category as $key => $category) {
            $action = 
            '<a href="' . route('admin.categories.view', $category->id) . '"class="px-2 btn btn-primary text-white" id="showClient"><i class="dripicons-preview"></i></a>
            <a href="' . route('admin.categories.edit', $category->id) . '"class="px-2 btn btn-warning text-white" id="editClient"><i class="dripicons-document-edit"></i></i></a>
             <button class="  px-2 btn btn-danger deleteType " id="DeleteClient" data-id="' . $category->id . '" data-name="'. $category->category_name .'"><i class="dripicons-trash"></i></button>';
             $categoryNames = [];
             $category_list = Category::where('id', $category->id)->with('parent')->get();
             $categories = json_decode($category_list, true);
             
             $extractCategoryNames = function ($category_list) use (&$extractCategoryNames, &$categoryNames) {
                 foreach ($category_list as $category) {
                     $categoryNames[] = $category['category_name'];
                     if (isset($category['parent'])) {
                         $extractCategoryNames([$category['parent']]);
                     }
                 }
             };
             
             $extractCategoryNames($categories);
             $categoryNames = array_reverse($categoryNames);
             $categoryNames = implode(' -> ', $categoryNames);

             if($category->image){
                $image = '<img src="' . asset('public/admin/category/images/' . $category->image) . '" alt="'.$category->category_name.'" width="40px">';
                }else{
                   $image = null;
                }

            $data[] = array(
                $i + $key,
                $category->category_name,
                $image,
                $categoryNames,
                $action,
            );
        }
        $records['recordsTotal'] =$total;
        $records['recordsFiltered'] =$total;
        $records['data'] = $data;
        echo json_encode($records);
    }
    public function searchCategory(Request $request)
    {
        if (!is_null($request->search_by_category)) {
        $category_name = Category::where('category_name', 'like', '%' . $request->search_by_category . '%')->get();
        }else{
        $category_name = [];
        }
    return response()->json(array('status' => true, 'msg' => 'Successfully retrieved', 'data' => $category_name));
    }

    public function create()
    {
        $category_list = Category::whereNull('parent_category_id')->with('children')->get();
        return view('admin.category.create', compact('category_list'));
    }

    public function get_child_category(Request $request)
    {
        $category_list = Category::where('parent_category_id', $request->selectedValue)->with('children')->get();
        return response()->json(array('status' => true, 'msg' => 'Successfully retrieved', 'data' => $category_list));
    }

    public function store(Request $request){
        // return $request->all();
        $rules = [
            'category_name' => 'required|max:255',
            'parent_category_id ' => 'nullable|exists:categories,id',
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json(['status' => false, 'msg' => $val->errors()->first()]);
            exit;
        } else {
            if($request->category_img){
                $category_img = 'image'.'_'.$request->category_name.time().'.'.request()->category_img->getClientOriginalExtension();
                request()->category_img->move(public_path('admin/category/images/'), $category_img);
            }else{
                $category_img = null;
            }
            $new_Content_type = new Category();
            $data = [
                'category_name'            => $request->category_name,
                'parent_category_id'       => $request->parent_category_id,
                'image'                   => $category_img,
                'slug'                    => $request->slug,
                'ogtag'      => $request->ogtag ,
                'schema_markup'  => $request->schema_markup ,
                'meta_title'   => $request->meta_title ,
                'meta_description'   => $request->meta_description ,
                'keywords'  => $request->keywords
            ];
            $new_Content_type = $new_Content_type->insert($data);
            
            if ($new_Content_type) {
                return response()->json(array('status' => true,  'location' => route('admin.categories.list'),   'msg' => 'Category Created Successfully!!'));
            } else {
                return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));
            }
        }
    }

    public function get_parent_category(Request $request){
        $categoryNames = [];
        $category_list = Category::where('id', $request->selectedValue)->with('parent')->get();
        $categories = json_decode($category_list, true);
        
        $extractCategoryNames = function ($category_list) use (&$extractCategoryNames, &$categoryNames) {
            foreach ($category_list as $category) {
                $categoryNames[] = $category['category_name'];
                if (isset($category['parent'])) {
                    $extractCategoryNames([$category['parent']]);
                }
            }
        };
        
        $extractCategoryNames($categories);
        $categoryNames = array_reverse($categoryNames);
        return response()->json(array('status' => true, 'msg' => 'Successfully retrieved', 'data' => $categoryNames));


    
    }

}
