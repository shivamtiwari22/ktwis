<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{
    public function create()
    {
        return view('admin.settings.languages.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'language'            => 'required',
            'code'                => 'required',        
            'php_locale_code'     => 'required',        
            'status'              => 'required',    
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json(['status' => false, 'msg' => $val->errors()->first()]);
            exit;
        } else {
            if($request->flag)
            {
            $flag = 'currency_flag'.'_'.$request->language.time().'.'.request()->flag->getClientOriginalExtension();
            request()->flag->move(public_path('admin/setting/language/flag'), $flag);
            }else{
                $flag = null;
            }
            $language = new Language();
            $data = [
                'language'           => $request->language,
                'order'              => $request->order,
                'code'               => $request->code,
                'flag'               => $flag,
                'php_locale_code'    => $request->php_locale_code,
                'status'             => $request->status,
            ];
            $language = $language->insert($data);

            if ($language) {
                return response()->json(array('status' => true, 'location' => route('languages.list')   ,'msg' => 'Currency created successfully!!'));
            } else {
                return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));
            }
        }
    }

    public function list()
    {
        return view('admin.settings.languages.list');
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
        
            $total =  Language::select('languages.*')
                    ->Where(function ($query) use ($search) {
                            $query->orWhere('languages.language', 'like', '%'.$search . '%');
                            $query->orWhere('languages.order', 'like', '%'.$search . '%');
                            $query->orWhere('languages.code', 'like', '%'.$search . '%');
                            $query->orWhere('languages.php_locale_code', 'like', '%'.$search . '%');

                            $searchLower = strtolower($search);
                            if ($searchLower === 'on' || $searchLower === 'o') {
                                $query->orWhere('languages.status', 1);
                            }
                            $searchLower = strtolower($search);
                            if ($searchLower === 'off' || $searchLower === 'of' || $searchLower ==='o') {
                                $query->orWhere('languages.status', 0);
                            }
                        });
                        $total = $total->count();

            $language =  Language::select('languages.*')
                    ->Where(function ($query) use ($search) {
                        $query->orWhere('languages.language', 'like', '%'.$search . '%');
                        $query->orWhere('languages.order', 'like', '%'.$search . '%');
                        $query->orWhere('languages.code', 'like', '%'.$search . '%');
                        $query->orWhere('languages.php_locale_code', 'like', '%'.$search . '%');

                        $searchLower = strtolower($search);
                        if ($searchLower === 'on' || $searchLower === 'o') {
                            $query->orWhere('languages.status', 1);
                        }
                        $searchLower = strtolower($search);
                        if ($searchLower === 'off' || $searchLower === 'of' || $searchLower ==='o') {
                            $query->orWhere('languages.status', 0);
                        }
                        });
                
            $language = $language->orderBy('id',$orderRecord)->limit($limit)->offset($ofset)->get();

            $i = 1 + $ofset;
        $data = [];
        foreach ($language as $key => $language) {
            $action = '
            <a href="' . route('languages.view', $language->id) . '"class="px-2 btn btn-primary text-white" id="showClient"><i class="dripicons-preview"></i></a>
            <a href="' . route('languages.edit', $language->id) . '"class="px-2 btn btn-warning text-white" id="editClient"><i class="dripicons-document-edit"></i></i></a>
             <button class="  px-2 btn btn-danger deleteType " id="DeleteClient" data-id="' . $language->id . '" data-name="'. $language->language .'"><i class="dripicons-trash"></i></button>';

             if ($language->status == 0) {
      
            $status = '
            <input type="checkbox" id="switch2_' . $language->id . '" data-switch="primary" name="status" value="1" onclick="updateCheckboxValue(this)">
            <label for="switch2_' . $language->id . '" data-on-label="On" data-off-label="Off" class="ChangeStatus"  data-id="' . $language->id . '" my-value="1"></label>
            ';
            
} else {
    $status = '
            <input type="checkbox" id="switch2_' . $language->id . '" checked data-switch="primary" name="status" value="1" onclick="updateCheckboxValue(this)">
            <label for="switch2_' . $language->id . '" data-on-label="On" data-off-label="Off" class="ChangeStatus"  data-id="' . $language->id . '" my-value="0"></label>
            ';
}
            if($language->flag){
                $flag = '<img src="' . asset('public/admin/setting/language/flag/' . $language->flag) . '" alt="'.$language->name.'" width="40px">';
                }else{
                   $flag = null;
                }
   

            $data[] = array(
                $i + $key,
                $language->language,
                $language->order,
                $language->code,
                $flag,
                $language->php_locale_code,
                $status,
                $action,
            );
        }
        $records['recordsTotal'] =$total;
        $records['recordsFiltered'] =$total;
        $records['data'] = $data;
        echo json_encode($records);
    }

    public function status_change(Request $request)
    {
        $language = Language::find($request->id);

        $language->status = ($language->status =='0') ? '1' : '0';
        $save = $language->save();
        if ($save) {
            return response()->json(['status' => true, 'msg' => "Status changed successfully", 'location' => route('languages.list')]);
        } else {
            return response()->json(['status' => false, 'msg' => "Error occurred. Please try again"]);
        }
        
    }

        
    public function delete(Request $request)
    {
        $category = Language::find($request->id);
        if ($category->delete()) {
            return response()->json(['status' => true, 'location' => route('languages.list'), 'msg' => "Deleted Successfully"]);
            exit;
        } else {
            return response()->json(['status' => false, 'msg' => "Error Occurred, Please try again"]);
        }
    }
              
    public function view($id)
    {
       $language = Language::find($id);
       return view('admin.settings.languages.view', compact('language'));
    }
              
    public function edit($id)
    {
       $language = Language::find($id);
       return view('admin.settings.languages.edit', compact('language'));
    }

    public function update(Request $request)
    {
        $rules = [
            'language'            => 'required',
            'code'                => 'required',        
            'php_locale_code'     => 'required',        
            'status'              => 'required', 
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
        } else {
            $language = Language::find($request->id);
            if($request->flag){
                $flag = 'flag'.'_'.$request->name.time().'.'.request()->flag->getClientOriginalExtension();
                request()->flag->move(public_path('admin/setting/language/flag/'), $flag);
            }else{
                $flag = $language->flag;
            }
            $language->language = $request->language;
            $language->order = $request->order;
            $language->code = $request->code;
            $language->flag = $flag;
            $language->php_locale_code = $request->php_locale_code;
            $language->status = $request->status;

            if ($language->save()) {
                return response()->json(array('status' => true, 'location' =>  route('languages.list'), 'message' => 'Updated successfully!!'));
            } else {
                return response()->json(array('status' => false, 'message' => 'Something Went Wrong!'));
            }
        }
    }
}
