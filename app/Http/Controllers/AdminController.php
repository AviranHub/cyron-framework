<?php

namespace App\Http\Controllers;


// use App\Http\Controller;
// use App\Models\User;
// use App\Request;
// use App\Http\Middlewares\Auth;
// use App\Http\Storage;
// use App\Models\GuildCategory;
// use App\Http\ErrorBag;
// use App\Models\Guild;
// use App\Str;
// use App\Models\Slider;


// class AdminController extends Controller
// {
//     public  function __construct()
//     {
//         $this->middleware(Auth::class);
//         $this->handleMiddleware();
//     }

//     public function myfunction()
//     {
//         // محتوای صفحه اصلی یا هر عملیات دیگر
//         echo "Welcome to the Home Page!";
//         // User::create(['id' => 1,'name' => 'reza', 'email' => 'vizpanel@gmail.com']);
//     }

//     public function dashboard()
//     {
//         $count_guilds = Guild::count();
//         $count_suggest_guilds = Guild::where('suggest', '=', '1')->count();
//         $count_waiting_guilds = Guild::where('status', '=', '1')->count();
//         $count_confirm_guilds = Guild::where('status', '=', '2')->count();
//         $data = [
//             'count_suggest_guilds' => $count_suggest_guilds,
//             'count_waiting_guilds' => $count_waiting_guilds,
//             'count_confirm_guilds' => $count_confirm_guilds,
//             'count_guilds' => $count_guilds,
//         ];
//         view('admin/index', $data);
//     }
//     public function guilds()
//     {
//         $guilds = Guild::all();
//         if (empty($guilds)) {
//             $guilds = [];
//         }
//         view('admin/guilds', ['guilds' => $guilds]);
//     }

//     public function guilds_suggestion()
//     {
//         $guilds = Guild::where('suggest', '=', '1')->get();
//         if (empty($guilds)) {
//             $guilds = [];
//         }
//         view('admin/guilds', ['guilds' => $guilds]);
//     }

//     public function guilds_confirms()
//     {
//         $guilds = Guild::where('status', '=', '2')->get();
//         if (empty($guilds)) {
//             $guilds = [];
//         }
//         view('admin/guilds', ['guilds' => $guilds]);
//     }
//     public function guilds_denyes()
//     {
//         $guilds = Guild::where('status', '=', '0')->get();
//         if (empty($guilds)) {
//             $guilds = [];
//         }
//         view('admin/guilds', ['guilds' => $guilds]);
//     }

//     public function guilds_waiting()
//     {
//         $guilds = Guild::where('status', '=', '1')->get();
//         if (empty($guilds)) {
//             $guilds = [];
//         }
//         view('admin/guilds', ['guilds' => $guilds]);
//     }
//     public function guild_confirm($slug)
//     {
//         $guild = Guild::where('slug', '=', $slug)->first();
//         $id =  $guild->id;
//         Guild::update($id, ['status' => 2]);
//         $guilds = Guild::all();
//         if (empty($guilds)) {
//             $guilds = [];
//         }
//         view('admin/guilds', ['guilds' => $guilds, 'success' =>  'مغازه با موفقیت تایید شد']);
//     }
//     public function guild_deny($slug)
//     {
//         $guild = Guild::where('slug', '=', $slug)->first();
//         $id =  $guild->id;
//         Guild::update($id, ['status' => 0]);
//         $guilds = Guild::all();
//         if (empty($guilds)) {
//             $guilds = [];
//         }
//         view('admin/guilds', ['guilds' => $guilds, 'success' =>  'مغازه با موفقیت رد شد']);
//     }
//     public function guild_suggest($slug)
//     {
//         $guild = Guild::where('slug', '=', $slug)->first();
//         $id =  $guild->id;
//         Guild::update($id, ['suggest' => 1]);
//         $guilds = Guild::all();
//         if (empty($guilds)) {
//             $guilds = [];
//         }
//         view('admin/guilds', ['guilds' => $guilds, 'success' =>  'مغازه با موفقیت پیشنهاد شد']);
//     }
//     public function guild_unsuggest($slug)
//     {
//         $guild = Guild::where('slug', '=', $slug)->first();
//         $id =  $guild->id;
//         Guild::update($id, ['suggest' => 0]);
//         $guilds = Guild::all();
//         if (empty($guilds)) {
//             $guilds = [];
//         }
//         view('admin/guilds', ['guilds' => $guilds, 'success' =>  'مغازه با موفقیت از لیست پیشنهادات حذف شد']);
//     }
//     public function slider()
//     {
//         $sliders = Slider::all();
//         if (empty($sliders)) {
//             $sliders = [];
//         }
//         view('admin/slider', ['sliders' => $sliders]);
//     }
//     public function slider_add()
//     {
//         $bag = new ErrorBag;
//         $request = new Request();

//         $rules = [
//             'desc' => 'string',
//             'image' => 'file',
//         ];

//         $errors = $request->validate($rules);
//         if (!empty($errors)) {
//             $bag->addArray($errors);
//         }

//         $image = $request->file('image');


//         $data = [];

//         if (empty($bag->all())) {
//             $image_name = Storage::driver('public')->upload($image);
//             $desc = $request->input('desc');

//             Slider::create([
//                 'description' => $desc,
//                 'image' => $image_name,
//             ]);
//             $data['success'] = "تصویر با موفقیت به اسلایدر اضافه شد";
//         } else {
//             $data['errors'] = $bag->all();
//         }


//         $sliders = Slider::all();
//         if (empty($sliders)) {
//             $sliders = [];
//         }
//         $data['sliders'] = $sliders;

//         view('admin/slider', $data);
//     }


//     public function slider_delete()
//     {
//         $bag = new ErrorBag;
//         $request = new Request();

//         $rules = [
//             'id' => 'integer',
//         ];

//         $errors = $request->validate($rules);
//         if (!empty($errors)) {
//             $bag->addArray($errors);
//         }

//         $data = [];

//         if (empty($bag->all())) {
//             $id = $request->input('id');
//             Slider::delete($id);
//             $data['success'] = "تصویر با موفقیت از اسلایدر حذف شد";
//         } else {
//             $data['errors'] = $bag->all();
//         }

//         $sliders = Slider::all();
//         if (empty($sliders)) {
//             $sliders = [];
//         }
//         $data['sliders'] = $sliders;

//         view('admin/slider', $data);
//     }

//     public function register()
//     {

//         $categories = GuildCategory::all();
//         if (empty($categories)) {
//             return response()->json(['error' => "empty category"]);
//         }
//         // // error_log("res : " . json_encode($categories));

//         view('admin/register', ['categories' => $categories]);
//     }
//     public function register_confirm()
//     {
//         $bag = new ErrorBag;
//         $request = new Request();

//         $rules = [
//             'name' => 'required|string',
//             'desc' => 'required|string',
//             'image' => 'file',
//             'manage' => 'required|string',
//             'insta' => 'string',
//             'address' => 'required|string',
//             'category' => 'required|integer',
//         ];

//         $errors = $request->validate($rules);
//         if (!empty($errors)) {
//             $bag->addArray($errors);
//         }
//         $image = $request->file('image');


//         $data = [];

//         if (empty($bag->all())) {
//             $image_name = Storage::driver('public')->upload($image);
//             $name = $request->input('name');
//             $desc = $request->input('desc');
//             $manage = 'reza'; //$request->input('manage');
//             $address = $request->input('address');
//             $category = $request->input('category');
//             $insta = $request->input('insta');
//             $image = $request->input('image');

//             Guild::create([
//                 'name' => $name,
//                 'slug' => Str::slug($name),
//                 'description' => $desc,
//                 'image' => $image_name,
//                 'manage' => $manage,
//                 'insta' => $insta,
//                 'address' => $address,
//                 'category' => $category,
//                 'status' => '1',
//                 'suggest' => '0',
//             ]);
//             $data['success'] = "صنف با موفقیت ایجاد شد";
//         } else {
//             $data['errors'] = $bag->all();
//         }


//         $categories = GuildCategory::all();
//         if (empty($categories)) {
//             $categories = [];
//         }

//         $data['categories'] = $categories;
//         view('admin/register', $data);
//     }
//     public function guild_edit($slug)
//     {
//         $guild = Guild::where('slug', '=', $slug)->first();

//         // بررسی اینکه آیا کسب و کار وجود دارد یا خیر
//         if (!$guild) {
//             return response()->json(['error' => 'Guild not found'], 404);
//         }

//         // بارگذاری دسته‌بندی‌ها
//         $categories = GuildCategory::all();
//         if (empty($categories)) {
//             $categories = [];
//         }

//         // ارسال کسب و کار و دسته‌بندی‌ها به ویو
//         view('admin/guild-edit', [
//             'guild' => $guild,
//             'categories' => $categories,
//         ]);
//     }
//     public function guild_edit_confirm()
//     {
//         $bag = new ErrorBag;
//         $request = new Request();

//         $rules = [
//             'name' => 'required|string',
//             'slug' => 'required|string',
//             'description' => 'required|string',
//             'manage' => 'required|string',
//             'insta' => 'string|nullable',
//             'address' => 'required|string',
//             'category' => 'required|integer', // اگر دسته‌بندی وجود دارد
//             'image' => 'file|nullable', // ورودی عکس اختیاری
//         ];

//         $errors = $request->validate($rules);
//         if (!empty($errors)) {
//             $bag->addArray($errors);
//         }

//         $slug = $request->input('slug');

//         $data = [];

//         // بارگذاری گیلد
//         $guild = Guild::where('slug', '=', $slug)->first();
//         if (!$guild) {
//             $bag->add('slug',  'کسب و کار پیدا نشد');
//         } else {
//             if (empty($bag->all())) {
//                 $name = $request->input('name');
//                 $description = $request->input('description');
//                 $manage = $request->input('manage');
//                 $insta = $request->input('insta');
//                 $address = $request->input('address');
//                 $category = $request->input('category');

//                 // اگر عکسی آپلود شده باشد
//                 if ($request->hasFile('image')  && $request->file('image')->isValid()) {

//                     $image = $request->file('image');
//                     $image_name = Storage::driver('public')->upload($image);
//                 } else {
//                     $image_name = $guild->image; // اگر عکسی آپلود نشده، عکس قبلی را نگه‌دارید
//                 }

//                 // به‌روزرسانی اطلاعات کسب و کار
//                 Guild::update($guild->id, [
//                     'name' => $name,
//                     'slug' => Str::slug($name),
//                     'description' => $description,
//                     'image' => $image_name,
//                     'manage' => $manage,
//                     'insta' => $insta,
//                     'address' => $address,
//                     'category' => $category,
//                 ]);

//                 $guild = Guild::where('slug', '=', Str::slug($name))->first();

//                 $data['success'] = "کسب و کار با موفقیت ویرایش شد";
//             } else {
//                 $data['errors'] = $bag->all();
//             }
//         }

//         // بارگذاری دسته‌بندی‌ها
//         $categories = GuildCategory::all();
//         if (empty($categories)) {
//             $categories = [];
//         }

//         $data['guild'] = $guild;
//         $data['categories'] = $categories;

//         view('admin/guild-edit', $data);
//     }
//     public function guild_delete()
//     {

//         $bag = new ErrorBag;
//         $request = new Request();

//         $rules = [
//             'id' => 'integer',
//         ];

//         $errors = $request->validate($rules);
//         if (!empty($errors)) {
//             $bag->addArray($errors);
//         }

//         $data = [];

//         if (empty($bag->all())) {
//             $id = $request->input('id');
//             Guild::delete($id);
//             $data['success'] = "مغازه با موفقیت حذف شد";
//         } else {
//             $data['errors'] = $bag->all();
//         }

//         $guilds = Guild::all();
//         if (empty($guilds)) {
//             $guilds = [];
//         }
//         $data['guilds'] = $guilds;

//         view('admin/guilds', $data);
//     }
//     public function guild_category_add()
//     {
//         view('admin/add-category-guild');
//     }
//     public function guild_category_add_confirm()
//     {
//         $bag = new ErrorBag;
//         $request = new Request();

//         $rules = [
//             'name' => 'required|string|unique:guild_categories',
//             'description' => 'required|string',
//             'image' => 'file',
//             // قوانین دیگر ...
//         ];

//         $errors = $request->validate($rules);
//         if (!empty($errors)) {
//             $bag->addArray($errors);
//         }
//         $image = $request->file('image');

//         $data = [];

//         if (empty($bag->all())) {
//             $image_name = Storage::driver('public')->upload($image);
//             $name = $request->input('name');
//             $description = $request->input('description');

//             // // error_log(json_encode($errors));

//             GuildCategory::create([
//                 'name' => $name,
//                 'slug' => Str::slug($name),
//                 'description' => $description,
//                 'image' => $image_name,
//             ]);
//             $data['success'] = "دسته با موفقیت ساخته شد";
//         } else {
//             $data['errors'] = $bag->all();
//         }

//         // return redirect()->back()->with('success', 'Category Added Successfully');
//         view('admin/add-category-guild', $data);
//     }

//     public function guild_category_edit()
//     {

//         $categories = GuildCategory::all();
//         if (empty($categories)) {
//             $categories = [];
//         }
//         view('admin/edit-category-guild', ['categories' => $categories]);
//     }
//     public function guild_category_edit_confirm()
//     {

//         $bag = new ErrorBag;
//         $request = new Request();

//         $rules = [
//             'id' => 'required|integer',
//             'name' => 'required|string',
//             'description' => 'required|string',
//             'image' => 'file',
//         ];

//         $errors = $request->validate($rules);
//         if (!empty($errors)) {
//             $bag->addArray($errors);
//         }
//         $image = $request->file('image');


//         $data = [];

//         if (empty($bag->all())) {
//             $image_name = Storage::driver('public')->upload($image);
//             $name = $request->input('name');
//             $description = $request->input('description');
//             $id = $request->input('id');

//             // // error_log("name of $name");

//             GuildCategory::update($id, [
//                 'name' => $name,
//                 'slug' => Str::slug($name),
//                 'description' => $description,
//                 'image' => $image_name,
//             ]);
//             $data['success'] = "دسته با موفقیت ویرایش شد";
//         } else {
//             $data['errors'] = $bag->all();
//         }


//         $categories = GuildCategory::all();
//         if (empty($categories)) {
//             $categories = [];
//         }

//         $data['categories'] = $categories;
//         view('admin/edit-category-guild', $data);
//     }

//     public function guild_category_delete()
//     {
//         $categories = GuildCategory::all();
//         if (empty($categories)) {
//             $categories = [];
//         }
//         view('admin/del-category-guild', ['categories' => $categories]);
//     }
//     public function guild_category_delete_confirm()
//     {
//         $bag = new ErrorBag;
//         $request = new Request();

//         $rules = [
//             'id' => 'required|integer',
//         ];

//         $errors = $request->validate($rules);
//         if (!empty($errors)) {
//             $bag->addArray($errors);
//         }

//         $data = [];

//         if (empty($bag->all())) {
//             // $image_name = Storage::driver('public')->upload($image);
//             $id = $request->input('id');

//             // // error_log(json_encode($errors));

//             GuildCategory::delete($id);
//             $data['success'] = "دسته با موفقیت حذف شد";
//         } else {
//             $data['errors'] = $bag->all();
//         }


//         $categories = GuildCategory::all();
//         if (empty($categories)) {
//             $categories = [];
//         }

//         $data['categories'] = $categories;

//         // return redirect()->back()->with('success', 'Category Added Successfully');
//         view('admin/del-category-guild', $data);
//     }
// }
