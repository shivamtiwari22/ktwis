<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contactus;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ContactUsers extends Controller
{
    public function contect_user_list(){
        return view('admin.contact_user.contactUser');
    }
    public function contect_user_list_render(Request $request)
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
       
        $total =  Contactus::select('contactuses.*')
                ->Where(function ($query) use ($search) {
                        $query->orWhere('contactuses.name', 'like', '%' . $search . '%');
                        $query->orWhere('contactuses.email', 'like', '%' . $search . '%');
                        $query->orWhere('contactuses.subject', 'like', '%' . $search . '%');
                        $query->orWhere('contactuses.message', 'like', '%' . $search . '%');
                        $query->orWhere('contactuses.created_at', 'like', '%' . $search . '%');
                    });
                    $total = $total->count();

        $contact_us =  Contactus::select('contactuses.*')
                ->Where(function ($query) use ($search) {
                    $query->orWhere('contactuses.name', 'like', '%' . $search . '%');
                    $query->orWhere('contactuses.email', 'like', '%' . $search . '%');
                    $query->orWhere('contactuses.subject', 'like', '%' . $search . '%');
                    $query->orWhere('contactuses.message', 'like', '%' . $search . '%');
                    $query->orWhere('contactuses.created_at', 'like', '%' . $search . '%');
                    });
             
        $contact_us = $contact_us->orderBy('id',$orderRecord)->limit($limit)->offset($ofset)->get();

        $i = 1 + $ofset;
        $data = [];
        foreach ($contact_us as $key => $contact_us) {
            $data[] = array(
                $i + $key,
                $contact_us->name,
                $contact_us->email,
                $contact_us->subject,
                Carbon::parse($contact_us->created_at)->format('Y-m-d') . ' (' . Carbon::parse($contact_us->created_at)->format('h:i A') . ')',
                $contact_us->message,
            );
        }
        $records['recordsTotal'] =$total;
        $records['recordsFiltered'] =$total;
        $records['data'] = $data;
        echo json_encode($records);
    }
}
