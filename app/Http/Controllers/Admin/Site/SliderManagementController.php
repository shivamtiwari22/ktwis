<?php

namespace App\Http\Controllers\Admin\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SliderManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SliderManagementController extends Controller
{
    //

    public function create()
    {
        $categories = Category::all();
        return view(
            'admin.site.slider_management.create',
            compact('categories')
        );
    }

    public function store(Request $request)
    {
        $rules = [
            'slider_image' => 'mimes:jpeg,jpg,png,gif|max:10000|required',
            'mobile_image' => 'mimes:jpeg,jpg,png,gif|max:10000',
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $val->errors()->first(),
            ]);
            exit();
        } else {
            if ($request->mobile_image) {
                $mobile_image =
                    'mobile_image' .
                    '_' .
                    time() .
                    '.' .
                    request()->mobile_image->getClientOriginalExtension();
                request()->mobile_image->move(
                    public_path('admin/site/sliderManagement/mobile/'),
                    $mobile_image
                );
            } else {
                $mobile_image = null;
            }

            $slider_image =
                'slider_image' .
                '_' .
                time() .
                '.' .
                request()->slider_image->getClientOriginalExtension();
            request()->slider_image->move(
                public_path('admin/site/sliderManagement/slider/'),
                $slider_image
            );

            $slider = new SliderManagement();
            $data = [
                'title' => $request->title,
                'title_color' => $request->title_color,
                'subtitle' => $request->subtitle,
                'subtitle_color' => $request->subtitle_color,
                'description' => $request->description,
                'description_color' => $request->description_color,
                'link' => $request->link,
                'order' => $request->order,
                'text_position' => $request->text_position,
                'slider_image' => $slider_image,
                'mobile_image' => $mobile_image,
                'status' => $request->status,
                'has_category_slider' => $request->has_category_slider ? 1 : 0,
                'category_id' => $request->category_id,
            ];
            $slider = $slider->insert($data);

            if ($slider) {
                return response()->json([
                    'status' => true,
                    'location' => route('slider.list'),
                    'msg' => 'Slider Added Successfully',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'msg' => 'Something Went Wrong!',
                ]);
            }
        }
    }

    public function list()
    {
        return view('admin.site.slider_management.list');
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

        $total = SliderManagement::select('slider_management.*')->Where(
            function ($query) use ($search) {
                $query->orWhere(
                    'slider_management.title',
                    'like',
                    '%' . $search . '%'
                );
                $query->orWhere(
                    'slider_management.title_color',
                    'like',
                    '%' . $search . '%'
                );
                $query->orWhere(
                    'slider_management.subtitle',
                    'like',
                    '%' . $search . '%'
                );
                $query->orWhere(
                    'slider_management.subtitle_color',
                    'like',
                    '%' . $search . '%'
                );
                $query->orWhere(
                    'slider_management.description',
                    'like',
                    '%' . $search . '%'
                );
                $query->orWhere(
                    'slider_management.description_color',
                    'like',
                    '%' . $search . '%'
                );
                $query->orWhere(
                    'slider_management.link',
                    'like',
                    '%' . $search . '%'
                );
                $query->orWhere(
                    'slider_management.order',
                    'like',
                    '%' . $search . '%'
                );
                $query->orWhere(
                    'slider_management.text_position',
                    'like',
                    '%' . $search . '%'
                );
            }
        );
        $total = $total->count();

        $slider = SliderManagement::select('slider_management.*')->Where(
            function ($query) use ($search) {
                $query->orWhere(
                    'slider_management.title',
                    'like',
                    '%' . $search . '%'
                );
                $query->orWhere(
                    'slider_management.title_color',
                    'like',
                    '%' . $search . '%'
                );
                $query->orWhere(
                    'slider_management.subtitle',
                    'like',
                    '%' . $search . '%'
                );
                $query->orWhere(
                    'slider_management.subtitle_color',
                    'like',
                    '%' . $search . '%'
                );
                $query->orWhere(
                    'slider_management.description',
                    'like',
                    '%' . $search . '%'
                );
                $query->orWhere(
                    'slider_management.description_color',
                    'like',
                    '%' . $search . '%'
                );
                $query->orWhere(
                    'slider_management.link',
                    'like',
                    '%' . $search . '%'
                );
                $query->orWhere(
                    'slider_management.order',
                    'like',
                    '%' . $search . '%'
                );
                $query->orWhere(
                    'slider_management.text_position',
                    'like',
                    '%' . $search . '%'
                );
            }
        );

        $slider = $slider
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($ofset)
            ->get();

        $i = 1 + $ofset;
        $data = [];
        foreach ($slider as $key => $slider) {
            $action =
                '<a href="' .
                route('slider.edit', $slider->id) .
                '"class="px-2 btn btn-warning text-white" id="editClient"><i class="dripicons-document-edit"></i></i></a>
             <button class="  px-2 btn btn-danger deleteType " id="DeleteClient" data-id="' .
                $slider->id .
                '" data-name="' .
                $slider->title .
                '"><i class="dripicons-trash"></i></button>';

            $options =
                '<pre>
<b>Title text color:</b>' .
                $slider->title_color .
                '
<b>Sub-title text color:</b>' .
                $slider->subtitle_color .
                '
<b>Description Color:</b>' .
                $slider->description_color .
                '
<b>Order:</b>' .
                $slider->order .
                '
<b>Text Position:</b>' .
                $slider->text_position .
                '
<b>Link:</b>' .
                $slider->link .
                '
                        </pre>';

            if ($slider->mobile_image) {
                $mobile =
                    '<img src="' .
                    asset(
                        'public/admin/site/sliderManagement/mobile/' .
                            $slider->mobile_image
                    ) .
                    '" alt="' .
                    $slider->mobile_image .
                    '" width="40px">';
            } else {
                $mobile = '-';            }
            $sliderImg =
                '<img src="' .
                asset(
                    'public/admin/site/sliderManagement/slider/' .
                        $slider->slider_image
                ) .
                '" alt="' .
                $slider->slider_image .
                '" width="40px">';

            $data[] = [
                $i + $key,
                $mobile,
                mb_substr($slider->description,0,60),
                $sliderImg,
                $options,
                $slider->title,
                $slider->subtitle,
                $action,
            ];
        }
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;
        echo json_encode($records);
    }

    public function delete(Request $request)
    {
        $category = SliderManagement::find($request->id);
        if ($category->delete()) {
            return response()->json([
                'status' => true,
                'location' => route('slider.list'),
                'msg' => 'Slider Deleted Successfully',
            ]);
            exit();
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Error Occurred, Please try again',
            ]);
        }
    }

    public function edit($id)
    {
        $slider = SliderManagement::find($id);
        $categories = Category::all();

        return view('admin.site.slider_management.edit', compact('slider','categories'));
    }

    public function update(Request $request)
    {
        $rules = [];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json([
                'status' => false,
                'message' => $val->errors()->first(),
            ]);
        } else {
            $slider = SliderManagement::find($request->id);
            if ($request->slider_image) {
                $slider_image =
                    'slider_image' .
                    '_' .
                    time() .
                    '.' .
                    request()->slider_image->getClientOriginalExtension();
                request()->slider_image->move(
                    public_path('admin/site/sliderManagement/slider/'),
                    $slider_image
                );
            } else {
                $slider_image = $slider->slider_image;
            }

            if ($request->delete_mobile) {
                $mobile_image = null;
            } else {
                if ($request->mobile_image) {
                    $mobile_image =
                        'mobile_image' .
                        '_' .
                        time() .
                        '.' .
                        request()->mobile_image->getClientOriginalExtension();
                    request()->mobile_image->move(
                        public_path('admin/site/sliderManagement/mobile/'),
                        $mobile_image
                    );
                } else {
                    $mobile_image = $slider->mobile_image;
                }
            }

            $slider->title = $request->title;
            $slider->title_color = $request->title_color;
            $slider->subtitle = $request->subtitle;
            $slider->subtitle_color = $request->subtitle_color;
            $slider->description = $request->description;
            $slider->description_color = $request->description_color;
            $slider->link = $request->link;
            $slider->order = $request->order;
            $slider->text_position = $request->text_position;
            $slider->slider_image = $slider_image; // Assign the variable $slider_image instead of $request->slider_image
            $slider->mobile_image = $mobile_image; // Assign the variable $mobile_image instead of $request->mobile_image
            $slider->status = $request->status;
            $slider->has_category_slider = $request->has_category_slider ? 1 : 0 ;
            $slider->category_id = $request->category_id;

            if ($slider->save()) {
                return response()->json([
                    'status' => true,
                    'location' => route('slider.list'),
                    'message' => 'Slider Updated Successfully',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Something Went Wrong!',
                ]);
            }
        }
    }
}
