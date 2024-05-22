<?php

namespace App\Http\Controllers\Admin\Site;

use App\Http\Controllers\Controller;
use App\Models\AdminContactUs;
use Illuminate\Http\Request;
use App\Models\Pages;
use App\Models\Blog;
use App\Models\ContactPage;
use App\Models\EmailTemplate;
use App\Models\FaqAnswer;
use App\Models\FaqTopic;
use App\Models\SeoPage;
use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Content\V1\ContentPage;

class AppearanceController extends Controller
{
    // Pages
    public function index_pages()
    {
        return view('admin.site.pages.index');
    }

    public function create_pages()
    {
        return view('admin.site.pages.create_page');
    }

    public function store_pages(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'type' => 'required',
            'page_status' => 'required',
            'slug' => 'required|unique:pages',
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
            'content' => 'required',
            'banner_image' => 'required',
        ]);
        $records = $request->type;

        $data = Pages::where('type', $records)->count();

        if ($data == '0') {

            $user = Auth::user();
            $page = new Pages();
            $page->title = $validatedData['title'];
            $page->type = $validatedData['type'];
            $page->status = $validatedData['page_status'];
            $page->slug = $validatedData['slug'];
            $page->meta_title = $validatedData['meta_title'];
            $page->meta_description = $validatedData['meta_description'];
            $page->content = $validatedData['content'];
            $page->ogtag = $request->ogtag;
            $page->schema_markup = $request->schema_markup;
            $page->keywords = $request->keywords;
            $page->created_by = $user->id;
            $page->updated_by = $user->id;

            if ($request->hasFile('banner_image')) {
                $image = $request->file('banner_image');
                $imageName = time() . '_' . uniqid() . $image->getClientOriginalExtension();
                $image->move(public_path('admin/appereance/pages/banner_image'), $imageName);
                $page->banner_image = $imageName;
            }

            $page->save();

            return response()->json(['status' => true, 'location' =>  route('admin.appereance.pages'), 'message' => 'Page created successfully']);
        } else {

            return response()->json(['status' => false, 'message' => 'Type : ' . $records . " already exists"]);
        }
    }

    public function list_pages(Request $request)
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
            $offset = $request->start;
        } else {
            $offset = 0;
        }

        $orderRecord = $request->order[0]['dir'];
        $nameOrder = $request->columns[$request->order[0]['column']]['name'];

        $total = Pages::where(function ($query) use ($search) {
            $query->orWhere('type', 'like', '%' . $search . '%')
                ->orWhere('title', 'like', '%' . $search . '%');
        })->count();

        $inventories = Pages::where(function ($query) use ($search) {
            $query->orWhere('type', 'like', '%' . $search . '%')
                ->orWhere('title', 'like', '%' . $search . '%');
        })
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();

        $data = [];
        foreach ($inventories as $key => $page) {
            $action = '<a href="' . route('admin.appereance.view_pages', $page->id) . '" class="px-2 btn btn-primary text-white mx-1" id="showproduct"><i class="dripicons-preview"></i></a>' .
                '<a href="' . route('admin.appereance.edit_pages', $page->id) . '" class="px-2 btn btn-warning text-white mx-1" id="showproduct"><i class="dripicons-document-edit"></i></a>' .
                '<button class="px-2 btn btn-danger delete_page" id="delete_page" data-id="' . $page->id . '" data-name="' . $page->title . '"><i class="dripicons-trash"></i></button>';


            $title = $page->title;
            $banner_image = '<img src="' . asset('public/admin/appereance/pages/banner_image/' . $page->banner_image) . '" alt="Banner Image" width="40px">';
            $type = $page->type;
            $date_times = $page->updated_at;
            $dateTime = new \DateTime($date_times);
            $date_time = $dateTime->format('M j, Y');


            $data[] = [
                $offset + $key + 1,
                $title,
                $banner_image,
                $type,
                $date_time,
                $action,
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function edit_pages($id)
    {
        $pages = Pages::find($id);
        return view('admin.site.pages.edit_page', ['pages' => $pages]);
    }

    public function update_pages(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'type' => 'required',
            'page_status' => 'required',
            'slug' => 'required',
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
            'content' => 'required',
        ]);
        $records = $request->type;
        $id = $request->id;

        $data = Pages::where('type', $records)->count();

        if ($data == '1') {

            $user = Auth::user();
            $page = Pages::find($id);
            $page->title = $validatedData['title'];
            $page->type = $validatedData['type'];
            $page->status = $validatedData['page_status'];
            $page->slug = $validatedData['slug'];
            $page->meta_title = $validatedData['meta_title'];
            $page->meta_description = $validatedData['meta_description'];
            $page->content = $validatedData['content'];
            $page->ogtag = $request->ogtag;
            $page->schema_markup = $request->schema_markup;
            $page->keywords = $request->keywords;
            $page->updated_by = $user->id;

            if ($request->hasFile('banner_image')) {
                $image = $request->file('banner_image');
                $imageName = time() . '_' . uniqid() . $image->getClientOriginalExtension();
                $image->move(public_path('admin/appereance/pages/banner_image'), $imageName);
                $page->banner_image = $imageName;
            }
            $page->save();

            return response()->json(['status' => true, 'location' =>  route('admin.appereance.pages'), 'message' => 'Page Updated Successfully']);
        } else {

            return response()->json(['status' => false, 'message' => 'Type : ' . $records . " already exists"]);
        }
    }

    public function view_pages($id)
    {
        $pages = Pages::find($id);
        return view('admin.site.pages.view_page', ['pages' => $pages]);
    }

    public function delete_page(Request $request)
    {
        $delete = Pages::where('id', $request->id)->delete();
        if ($delete) {
            return response()->json(['status' => true, 'message' => 'Page deleted successfully']);
        } else {

            return response()->json(['status' => false, 'message' => "Some error occurred! , Please try again"]);
        }
    }

    // Blogs 

    public function index_blogs()
    {
        return view('admin.site.blogs.index_blogs');
    }

    public function create_blogs()
    {
        return view('admin.site.blogs.create_blogs');
    }

    public function store_blogs(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'blogs_status' => 'required',
            'slug' => 'required',
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
            'excerpt' => 'required',
            'content' => 'required',
            'banner_image' => 'required|image',
        ]);

    
        $user = Auth::user();
        $blog = new Blog();
        $blog->title = $request->input('title');
        $blog->status = $request->input('blogs_status');
        $blog->slug =  Blog::where('slug', $request->slug)->exists() ?  $request->input('slug') .'-'. Blog::where('slug', $request->slug)->count() : $request->input('slug');
        $blog->tags = $request->input('tag-input');
        $blog->meta_title = $request->input('meta_title');
        $blog->meta_description = $request->input('meta_description');
        $blog->excerpt = $request->input('excerpt');
        $blog->content = $request->input('content');
        $blog->created_by = $user->id;
        $blog->updated_by = $user->id;


        if ($request->hasFile('banner_image')) {
            $image = $request->file('banner_image');
            $imageName = time() . '_' . uniqid() . $image->getClientOriginalExtension();
            $image->move(public_path('admin/appereance/blogs/banner_image'), $imageName);
            $blog->banner_image = $imageName;
        }
        $blog->save();

        return response()->json([
            'status' => true,
            'message' => 'Blog created successfully',
            'location' => route('admin.appereance.blogs')
        ]);
    }

    public function list_blogs(Request $request)
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
            $offset = $request->start;
        } else {
            $offset = 0;
        }

        $orderRecord = $request->order[0]['dir'];
        $nameOrder = $request->columns[$request->order[0]['column']]['name'];

        $total = Blog::where(function ($query) use ($search) {
            $query->orWhere('excerpt', 'like', '%' . $search . '%')
                ->orWhere('title', 'like', '%' . $search . '%')
                ->orWhere('updated_at', 'like', '%' . $search . '%');
        })->count();

        $inventories = Blog::where(function ($query) use ($search) {
            $query->orWhere('excerpt', 'like', '%' . $search . '%')
                ->orWhere('title', 'like', '%' . $search . '%')
                ->orWhere('updated_at', 'like', '%' . $search . '%');
        })
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();

        $data = [];
        foreach ($inventories as $key => $blog) {
            $action = '<a href="' . route('admin.appereance.view_blogs', $blog->id) . '" class="px-2 btn btn-primary text-white mx-1" id="showproduct"><i class="dripicons-preview"></i></a>' .
                '<a href="' . route('admin.appereance.edit_blogs', $blog->id) . '" class="px-2 btn btn-warning text-white mx-1" id="showproduct"><i class="dripicons-document-edit"></i></a>' .
                '<button class="px-2 btn btn-danger delete_blogs" id="delete_blogs" data-id="' . $blog->id . '" data-name="' . $blog->title . '"><i class="dripicons-trash"></i></button>';


            $title = $blog->title;
            $banner_image = '<img src="' . asset('public/admin/appereance/blogs/banner_image/' . $blog->banner_image) . '" alt="Banner Image" width="40px">';
            $excerpt = $blog->excerpt;
            $date_times = $blog->updated_at;
            $dateTime = new \DateTime($date_times);
            $date_time = $dateTime->format('M j, Y');


            $data[] = [
                $offset + $key + 1,
                $title,
                $banner_image,
                $excerpt,
                $date_time,
                $action,
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function edit_blogs($id)
    {
        $blogs = Blog::find($id);
        return view('admin.site.blogs.edit_blogs', ['blogs' => $blogs]);
    }

    public function update_blogs(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'blogs_status' => 'required',
            'slug' => 'required',
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
            'content' => 'required',
            'excerpt' => 'required',
        ]);
        $id = $request->id;

        $user = Auth::user();
        $blog = Blog::find($id);
        $blog->title = $validatedData['title'];
        $blog->status = $validatedData['blogs_status'];
        $blog->tags = $request->input('tag-input');
        $blog->slug = $validatedData['slug'];
        $blog->excerpt = $validatedData['excerpt'];
        $blog->meta_title = $validatedData['meta_title'];
        $blog->meta_description = $validatedData['meta_description'];
        $blog->content = $validatedData['content'];
        $blog->updated_by = $user->id;

        if ($request->hasFile('banner_image')) {
            $image = $request->file('banner_image');
            $imageName = time() . '_' . uniqid() . $image->getClientOriginalExtension();
            $image->move(public_path('admin/appereance/blogs/banner_image'), $imageName);
            $blog->banner_image = $imageName;
        }

        $blog->save();

        return response()->json(['status' => true, 'location' =>  route('admin.appereance.blogs'), 'message' => 'Blog updated successfully']);
    }

    public function view_blogs($id)
    {
        $blog = BLog::find($id);
        return view('admin.site.blogs.view_blogs', ['blog' => $blog]);
    }

    public function delete_blogs(Request $request)
    {
        $delete = Blog::where('id', $request->id)->delete();
        if ($delete) {
            return response()->json(['status' => true, 'message' => 'Blog deleted successfully']);
        } else {

            return response()->json(['status' => false, 'message' => "Some error occurred! , Please try again"]);
        }
    }

    // email template

    public function index_templates()
    {
        return view('admin.site.templates.index_templates');
    }

    public function create_templates()
    {
        return view('admin.site.templates.create_templates');
    }

    public function store_templates(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'template_type' => 'required',
            'template_for' => 'required',
            'template_status' => 'required',
            'sender_email' => 'email',
            'sender_name' => 'required',
            'subject' => 'required',
            'short_codes' => 'nullable',
            'content' => 'required',
        ]);

        $user = Auth::user();
        $emailTemplate = new EmailTemplate();
        $emailTemplate->name = $request->input('name');
        $emailTemplate->template_type = $request->input('template_type');
        $emailTemplate->template_for = $request->input('template_for');
        $emailTemplate->status = $request->input('template_status');
        $emailTemplate->sender_email = $request->input('sender_email');
        $emailTemplate->sender_name = $request->input('sender_name');
        $emailTemplate->subject = $request->input('subject');
        $emailTemplate->short_codes = $request->input('short_codes');
        $emailTemplate->body = $request->input('content');
        $emailTemplate->created_by = $user->id;
        $emailTemplate->updated_by = $user->id;
        $emailTemplate->save();

        return response()->json(['status' => true, 'location' =>  route('admin.appereance.templates'), 'message' => 'Email template created successfully']);
    }

    public function list_templates(Request $request)
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
            $offset = $request->start;
        } else {
            $offset = 0;
        }

        $orderRecord = $request->order[0]['dir'];
        $nameOrder = $request->columns[$request->order[0]['column']]['name'];

        $total = EmailTemplate::where(function ($query) use ($search) {
            $query->orWhere('sender_email', 'like', '%' . $search . '%')
                ->orWhere('name', 'like', '%' . $search . '%')
                ->orWhere('subject', 'like', '%' . $search . '%')
                ->orWhere('sender_email', 'like', '%' . $search . '%')
                ->orWhere('sender_name', 'like', '%' . $search . '%');
        })->count();

        $inventories =  EmailTemplate::where(function ($query) use ($search) {
            $query->orWhere('sender_email', 'like', '%' . $search . '%')
                ->orWhere('name', 'like', '%' . $search . '%')
                ->orWhere('subject', 'like', '%' . $search . '%')
                ->orWhere('sender_email', 'like', '%' . $search . '%')
                ->orWhere('sender_name', 'like', '%' . $search . '%');
        })
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();

        $data = [];
        foreach ($inventories as $key => $blog) {
            $action = '<a href="' . route('admin.appereance.view_templates', $blog->id) . '" class="px-2 btn btn-primary text-white mx-1" id="showproduct"><i class="dripicons-preview"></i></a>' .
                '<a href="' . route('admin.appereance.edit_templates', $blog->id) . '" class="px-2 btn btn-warning text-white mx-1" id="showproduct"><i class="dripicons-document-edit"></i></a>' .
                '<button class="px-2 btn btn-danger delete_templates" id="delete_templates" data-id="' . $blog->id . '" data-name="' . $blog->name . '"><i class="dripicons-trash"></i></button>';


            $name = $blog->name;
            $template = $blog->template_for;
            if ($template == "0") {
                $template_for = "Website";
            } elseif ($template == "1") {
                $template_for = "Merchant";
            }
            $sender_name = $blog->sender_name;
            $sender_email = $blog->sender_email;
            $subject = $blog->subject;



            $data[] = [
                $offset + $key + 1,
                $name,
                $template_for,
                $sender_name,
                $sender_email,
                $subject,
                $action,
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function edit_templates($id)
    {
        $template = EmailTemplate::find($id);
        return view('admin.site.templates.edit_templates', ['template' => $template]);
    }

    public function update_templates(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'template_type' => 'required',
            'template_for' => 'required',
            'template_status' => 'required',
            'sender_email' => 'email',
            'sender_name' => 'required',
            'subject' => 'required',
            'short_codes' => 'nullable',
            'content' => 'required',
        ]);

        $id = $request->id;
        $user = Auth::user();
        $emailTemplate = EmailTemplate::find($id);
        $emailTemplate->name = $request->input('name');
        $emailTemplate->template_type = $request->input('template_type');
        $emailTemplate->template_for = $request->input('template_for');
        $emailTemplate->status = $request->input('template_status');
        $emailTemplate->sender_email = $request->input('sender_email');
        $emailTemplate->sender_name = $request->input('sender_name');
        $emailTemplate->subject = $request->input('subject');
        $emailTemplate->short_codes = $request->input('short_codes');
        $emailTemplate->body = $request->input('content');
        $emailTemplate->updated_by = $user->id;
        $emailTemplate->save();

        return response()->json(['status' => true, 'location' =>  route('admin.appereance.templates'), 'message' => 'Email template updated successfully']);
    }

    public function view_templates($id)
    {
        $template = EmailTemplate::find($id);
        return view('admin.site.templates.view_templates', ['template' => $template]);
    }

    public function delete_templates(Request $request)
    {
        $delete = EmailTemplate::where('id', $request->id)->delete();
        if ($delete) {
            return response()->json(['status' => true, 'message' => 'Template deleted successfully']);
        } else {

            return response()->json(['status' => false, 'message' => "Some error occurred! , Please try again"]);
        }
    }

    public function index_faq()
    {
        $topic = FaqTopic::where('status', 'active')->get();

        if ($topic) {
            return view('admin.site.faq.index_faq', ['topic' => $topic]);
        } else {
            $topic = "";
            return view('admin.site.faq.index_faq', ['topic' => $topic]);
        }
    }

    public function store_faq_topic(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'faq_for' => 'required',
            'topic_status' => 'required',
        ]);

        $user = Auth::user();
        $topic = new FaqTopic();
        $topic->topic_name = $validatedData['name'];
        $topic->faq_for = $validatedData['faq_for'];
        $topic->status = $validatedData['topic_status'];
        $topic->created_by = $user->id;
        $topic->updated_by = $user->id;
        $topic->save();

        return response()->json(['status' => true, 'location' =>  route('admin.appereance.faq'), 'message' => 'Topic added successfully']);
    }

    public function list_faq_topic(Request $request)
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
            $offset = $request->start;
        } else {
            $offset = 0;
        }

        $orderRecord = $request->order[0]['dir'];
        $nameOrder = $request->columns[$request->order[0]['column']]['name'];

        $total = FaqTopic::where(function ($query) use ($search) {
            $query->orWhere('topic_name', 'like', '%' . $search . '%')
                ->orWhere('faq_for', 'like', '%' . $search . '%');
        })->count();

        $inventories =  FaqTopic::where(function ($query) use ($search) {
            $query->orWhere('topic_name', 'like', '%' . $search . '%')
                ->orWhere('faq_for', 'like', '%' . $search . '%');
        })
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();

        $data = [];
        foreach ($inventories as $key => $topic) {
            $action = '<button class="px-1 btn btn-warning text-white edit_topics" id="edit_topics" data-id="' . $topic->id . '" data-name="' . $topic->name . '"><i class="dripicons-document-edit"></i></button> ' .
                '<button class="px-1 btn btn-danger delete_topics" id="delete_topics" data-id="' . $topic->id . '" data-name="' . $topic->topic_name . '"><i class="dripicons-trash"></i></button>';


            $topic_name = $topic->topic_name;
            $faq_for = ucwords($topic->faq_for);

            $data[] = [
                $offset + $key + 1,
                $topic_name,
                $faq_for,
                $action,
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function edit_faq_topic($id)
    {
        $topic = FaqTopic::find($id);
        return response()->json($topic);
    }

    public function update_faq_topic(Request $request)
    {
        $id = $request->id_edit;
        $validatedData = $request->validate([
            'topic_name' => 'required',
            'faq_for_edit' => 'required',
            'topic_status_edit' => 'required',
        ]);

        $user = Auth::user();
        $topic = FaqTopic::find($id);
        $topic->topic_name = $validatedData['topic_name'];
        $topic->faq_for = $validatedData['faq_for_edit'];
        $topic->status = $validatedData['topic_status_edit'];
        $topic->updated_by = $user->id;
        $topic->save();

        return response()->json(['status' => true, 'location' =>  route('admin.appereance.faq'), 'message' => 'Topic Updated Successfully']);
    }

    public function delete_topics(Request $request)
    {
        $delete = FaqTopic::where('id', $request->id)->delete();
        if ($delete) {
            $delete_answer = FaqAnswer::where('faq_topics_id', $request->id)->delete();
            if ($delete_answer) 
            {
                return response()->json(['status' => true, 'message' => 'Topic deleted successfully']);
            }
            else
            {
                return response()->json(['status' => true, 'message' => 'Topic deleted but Faq Answer not deleted successfully']);
            }
        } else {

            return response()->json(['status' => false, 'message' => "Some error occurred! , Please try again"]);
        }
    }

    // faq_ans

    public function store_faq(Request $request)
    {
        $validatedData = $request->validate([
            'faq_topic' => 'required',
            'faq_status' => 'required',
            'question' => 'required',
            'answer' => 'required',
        ]);

        $user = Auth::user();
        $topic = new FaqAnswer();
        $topic->faq_topics_id = $validatedData['faq_topic'];
        $topic->status = $validatedData['faq_status'];
        $topic->question = $validatedData['question'];
        $topic->answer = $validatedData['answer'];
        $topic->created_by = $user->id;
        $topic->updated_by = $user->id;
        $topic->save();

        return response()->json(['status' => true, 'location' =>  route('admin.appereance.faq'), 'message' => 'FAQ added successfully']);
    }

    public function list_faq(Request $request)
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
            $offset = $request->start;
        } else {
            $offset = 0;
        }

        $orderRecord = $request->order[0]['dir'];
        $nameOrder = $request->columns[$request->order[0]['column']]['name'];

        $total = FaqAnswer::where(function ($query) use ($search) {
            $query->orWhere('question', 'like', '%' . $search . '%')
                ->orWhere('answer', 'like', '%' . $search . '%');
        })->count();

        $inventories =  FaqAnswer::where(function ($query) use ($search) {
            $query->orWhere('question', 'like', '%' . $search . '%')
                ->orWhere('answer', 'like', '%' . $search . '%');
        })
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();

        $data = [];
        foreach ($inventories as $key => $faq) {
            $action = '<button class="px-1 btn btn-warning text-white edit_faqs" id="edit_faqs" data-id="' . $faq->id . '" data-name="' . $faq->question . '"><i class="dripicons-document-edit"></i></button> ' .
                '<button class="px-1 btn btn-danger delete_faq" id="delete_faq" data-id="' . $faq->id . '" data-name="' . $faq->question . '"><i class="dripicons-trash"></i></button>';

            $details = '<label for="">' . $faq->question . '</label><br>' .
                '<span>' . $faq->answer . '</span>';

            $topic_data = FaqTopic::where('id', $faq->faq_topics_id)->first();
            if ($topic_data) {
                $template = $topic_data->faq_for;
                if ($template == "merchant") {
                    $faq_for = "Merchant";
                } elseif ($template == "customer") {
                    $faq_for = "Customer";
                }

                $topic_name = $topic_data->topic_name;
            } else {
                $faq_for = "";
                $topic_name = "";
            }


            $topic =  '<label for="">' . $topic_name . '</label><br>' .
                '<span class="label label-default">' . $faq_for . '</span>';


            $date_times = $faq->updated_at;
            $dateTime = new \DateTime($date_times);
            $last_updated = $dateTime->format('M j, Y');



            $data[] = [
                $offset + $key + 1,
                $details,
                $topic,
                $last_updated,
                $action,
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function edit_faq($id)
    {
        $faq_answer = FaqAnswer::find($id);
        return response()->json($faq_answer);
    }

    public function update_faq(Request $request)
    {
        $id = $request->id_answer;

        $validatedData = $request->validate([
            'faq_topic_edit' => 'required',
            'faq_status_edit' => 'required',
            'question_edit' => 'required',
            'edit_answer' => 'required',
        ]);

        $user = Auth::user();
        $topic = FaqAnswer::find($id);
        $topic->faq_topics_id = $validatedData['faq_topic_edit'];
        $topic->status = $validatedData['faq_status_edit'];
        $topic->question = $validatedData['question_edit'];
        $topic->answer = $validatedData['edit_answer'];
        $topic->updated_by = $user->id;
        $topic->save();

        return response()->json(['status' => true, 'location' =>  route('admin.appereance.faq'), 'message' => 'FAQ updated successfully']);
    }

    public function delete_faq(Request $request)
    {
        $delete = FaqAnswer::where('id', $request->id)->delete();
        if ($delete) {
            return response()->json(['status' => true, 'message' => 'FAQ deleted successfully']);
        } else {

            return response()->json(['status' => false, 'message' => "Some error occurred! , Please try again"]);
        }
    }


    public function contact_us(){
         $contact = AdminContactUs::take(1)->first() ?? null;
        return view('admin.site.contact_us', compact('contact'));
    }

    public function update_contact_us(Request $request){

        $validatedData = $request->validate([
            'email' => 'required',
            'phone' => 'required',
        ]);

          if( AdminContactUs::count()> 0){
              $contact = AdminContactUs::find(1)->update($request->all());
          }
          else{
            $contact = AdminContactUs::create($request->all());
          }

        if($contact){
            return response()->json(['status' => true, 'message' => 'Response Store Successfully']);

        }
        else{
            return response()->json(['status' => false, 'message' => 'Something Went Wrong']);

        }
    }

    // show listing of content 
    public function dynamicContent(){
         $data =  ContactPage::all();
         
        return view('admin.site.dynamic_content.index', compact('data'));
    }

    public function addContent(){
        return view('admin.site.dynamic_content.create');
    }

    public function PostContent(Request $request){
        $alreadyExists = ContactPage::where('type',$request->type)->first();  
        if($alreadyExists){
        return response()->json(['status' => false , 'message' =>  $request->type .' content already exists']);
        }

        $data = new ContactPage();
        $data->type = $request->type;
        $data->slug = $request->slug;
        $data->meta_title = $request->meta_title;
        $data->meta_description = $request->meta_description;
        $data->ogtag = $request->ogtag;
        $data->schema_markup = $request->schema_markup;
        $data->keywords = $request->keywords;
        $data->content = $request->content;

        if ($request->hasFile('banner_image')) {
            $image = $request->file('banner_image');
            $imageName = time() . '_' . uniqid() . $image->getClientOriginalExtension();
            $image->move(public_path('admin/contact'), $imageName);
            $data->banner_image = $imageName;
        }
        $data->save();

        return response()->json(['status' => true, 'location' =>  route('admin.appereance.content'), 'message' => 'Content Store Successfully']);

    }


    public function editContent($id){
        $data = ContactPage::find($id);
        return view('admin.site.dynamic_content.edit', compact('data'));
    }

    public function updateContent(Request $request){ 
        $data = ContactPage::find($request->id);
        $data->meta_title = $request->meta_title;
        $data->meta_description = $request->meta_description;
        $data->ogtag = $request->ogtag;
        $data->schema_markup = $request->schema_markup;
        $data->keywords = $request->keywords;
        $data->content = $request->content;

        if ($request->hasFile('banner_image')) {
            $image = $request->file('banner_image');
            $imageName = time() . '_' . uniqid() . $image->getClientOriginalExtension();
            $image->move(public_path('admin/contact'), $imageName);
            $data->banner_image = $imageName;
        }
        $data->save();
        return response()->json(['status' => true, 'location' =>  route('admin.appereance.content'), 'message' => 'Content Update Successfully']);
    }

    public function deleteContent(Request $request){
        $delete = ContactPage::where('id', $request->id)->delete();
        if ($delete) {
            return response()->json(['status' => true, 'message' => 'Content deleted successfully']);
        } else {

            return response()->json(['status' => false, 'message' => "Some error occurred! , Please try again"]);
        }
    }


    public function seo_index_pages(){    
        $pages = SeoPage::get();
    
        return view('admin.site.seo_pages.index',compact('pages'));
    }

    public function seo_create_pages(){
        return view('admin.site.seo_pages.create_page');
    }

    public function seo_store_pages(Request $request){
        $validatedData = $request->validate([
            'type' => 'required',
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
        ]);
        $records = $request->type;

        $data = SeoPage::where('type', $records)->count();

        if ($data == '0') {
            $page = new SeoPage();
            $page->type = $request->type;
            $page->meta_title = $request['meta_title'];
            $page->meta_description = $request['meta_description'];
            $page->og_tag = $request->ogtag;
            $page->schema_markup = $request->schema_markup;
            $page->keywords = $request->keywords;
            $page->save();

            return response()->json(['status' => true, 'location' =>  route('admin.appereance.seo.pages'), 'message' => 'Page created successfully']);
        } else {

            return response()->json(['status' => false, 'message' => 'Type : ' . $records . " already exists"]);
        }
    } 

    public function seo_edit_pages($id)
    {
        $pages = SeoPage::find($id);
        $optionsArray = array(
            "Faq" => "Faq",
            "Search" => "Search",
            "Wishlist" => "Wishlist",
            "Dashboard" => "Dashboard",
            "Order-Detail" => "Order Detail",
            "Contact-Seller" => "Contact Seller",
            "Raise-Dispute" => "Raise Dispute",
            "Cancel-Items" => "Cancel Items",
            "Dispute-Detail" => "Dispute Detail",
            "Cart" => "Cart" ,
            "Vendor" => "Vendor",
            "Payment" => "Payment",
            "Order-Confirmed" => "Order Confirmed" ,
        );
        return view('admin.site.seo_pages.edit_page',compact('pages','optionsArray'));
    }

    public function seo_update_pages(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required',
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
        ]);
        // return $request->all();
        $records = $request->type;
        $id = $request->id;

        $data = SeoPage::where('type', $records)->count();

        if ($data == '1') {
            $page = SeoPage::find($id);
            $page->type = $request->type;
            $page->meta_title = $request['meta_title'];
            $page->meta_description = $request['meta_description'];
            $page->og_tag = $request->ogtag;
            $page->schema_markup = $request->schema_markup;
            $page->keywords = $request->keywords;
            $page->save();


            return response()->json(['status' => true, 'location' =>  route('admin.appereance.seo.pages'), 'message' => 'Page Updated Successfully']);
        } else {

            return response()->json(['status' => false, 'message' => 'Type : ' . $records . " already exists"]);
        }
    }

    public function seo_view_pages($id)
    {
        $pages = SeoPage::find($id);
        return view('admin.site.seo_pages.view_page', ['pages' => $pages]);
    }

    public function seo_delete_page(Request $request)
    {
        $delete = SeoPage::where('id', $request->id)->delete();
        if ($delete) {
            return response()->json(['status' => true, 'message' => 'Page deleted successfully']);
        } else {

            return response()->json(['status' => false, 'message' => "Some error occurred! , Please try again"]);
        }
    }


}
