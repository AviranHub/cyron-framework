<?php

namespace App\Http\Controllers;

// use App\Http\Storage;
// use App\Models\User;
// use App\Request;
// use App\Models\GuildCategory;
// use App\Models\Guild;
// use App\Http\ErrorBag;

// class ApiController
// {
//     public function myfunction()
//     {
//         // محتوای صفحه اصلی یا هر عملیات دیگر
//         echo "Welcome to the Home Page!";
//         // User::create(['id' => 1,'name' => 'reza', 'email' => 'vizpanel@gmail.com']);
//     }
//     public function index()
//     {
//         view('index');
//     }
//     public function register()
//     {

//         $categories = GuildCategory::all();
//         if (empty($categories)) {
//             return response()->json(['error' => "empty category"]);
//         }
//         // // error_log("res : " . json_encode($categories));

//         view('register', ['categories' => $categories]);
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
//                 'description' => $desc,
//                 'image' => $image_name,
//                 'manage' => $manage,
//                 'insta' => $insta,
//                 'address' => $address,
//                 'category' => $category,
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
//         view('register', $data);
//     }
//     public function suggestions()
//     {
//         view('suggestions');
//     }
//     public function melon()
//     {
//         view('index');
//     }
//     public function guilds()
//     {
//         view('guilds');
//     }
//     public function guild($slug, $id)
//     {
//         view('guild', ['id' => $id, 'slug' => $slug]);
//     }
//     public function login()
//     {
//         view('login');
//     }
//     public function login_check()
//     {
//         $request = new Request();
//         $username = $request->input('username');
//         $password = $request->input('password');
//         $saveme = $request->input('saveme');
//         $admin_mail = vars('ADMIN_EMAIL');
//         $admin_pass = vars('ADMIN_PASSWORD');
//         session()->start(30 * 24 * 60 * 60); // شروع جلسه با زمان انقضا 30 روز
//         if ($username == $admin_mail && $password == $admin_pass) {
//             // return redirect('admin/dashboard')->with('success', 'Login Successfull');
//             if (isset($saveme) and $saveme === true) {
//                 cookie()->set('user', $username, time() + (30 * 24 * 60 * 60));
//             }
//             session()->set('user', $username);
//             session()->set('user', $username);
//             var_dump($_SESSION); // بررسی سشن‌ها
//             redirect()->route('admin');
//         } else {
//             view('login', ['msg' => 'ورود ناموفق']);
//         }
//     }
// }
