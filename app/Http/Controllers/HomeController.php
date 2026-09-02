<?php

namespace App\Http\Controllers;

use App\Http\Storage;
use App\Models\User;
use App\Request;
use App\Models\GuildCategory;
use App\Models\Guild;
use App\Http\ErrorBag;
use App\Str;
use App\Models\Slider;
use App\Database\Collection;
use App\Models\BookCategory;
use App\Models\Book;
use App\Core\Authentication\Auth;
use App\Models\Genre;
use App\Models\BookPart;
use App\Models\Library;


class HomeController
{
    //     public function myfunction()
    //     {
    //         // محتوای صفحه اصلی یا هر عملیات دیگر
    //         echo "Welcome to the Home Page!";
    //         // User::create(['id' => 1,'name' => 'reza', 'email' => 'vizpanel@gmail.com']);
    //     }

    // public function index(){
    //     return view('index');
    // }

    public function index()
    {
        // $compiledPath = $GLOBALS['viewEngine']->getCompiledPath('home');
        // include $compiledPath;
        // exit;

        $bag = new ErrorBag;


        $low_books =  new Collection([
            (object)['slug' => 'book1', 'cover' => 'cover1.jpg', 'title' => 'کتاب تست 1'],
            (object)['slug' => 'book2', 'cover' => 'cover2.jpg', 'title' => 'کتاب تست 2'],
            (object)['slug' => 'book3', 'cover' => 'cover3.jpg', 'title' => 'کتاب تست 3'],
        ]);
        $newestBooks = [];   // موقتی خالی
        $freeBooks = [];     // موقتی خالی


        // $categories = GuildCategory::all();
        // if (empty($categories)) {
        //     $bag->addGlobal("empty category");
        //     $categories = [
        //         (object)[
        //             'name' => "دسته تست",
        //             'slug' => "test-category",
        //             'image' => "https://bkhut.ir/assets/icon.png",
        //             'books' => [  // اضافه کردن books
        //                 (object)['slug' => 'book1', 'cover' => 'cover1.jpg', 'title' => 'کتاب تست 1', 'author_name' => 'نویسنده 1', 'price' => 0, 'copen' => 100],
        //                 (object)['slug' => 'book2', 'cover' => 'cover2.jpg', 'title' => 'کتاب تست 2', 'author_name' => 'نویسنده 2', 'price' => 0, 'copen' => 100],
        //             ]
        //         ],
        //     ];
        // }
        // // error_log("res : " . json_encode($categories));

        // $sliders = Slider::all();
        // if (empty($sliders)) {
        //     $sliders = [
        //         ['image' => "https://bkhut.ir/assets/icon.png"],
        //         ['image' => "https://bkhut.ir/assets/icon.png"],
        //         ['image' => "https://bkhut.ir/assets/icon.png"],
        //     ];
        // }


        // $suggestions = Guild::where('suggest', '=', '1')->get();
        // if (empty($suggestions)) {
        //     $suggestions = [
        //         ['name' => "ok", 'slug' => "ok", 'image' => "https://bkhut.ir/assets/icon.png"],
        //         ['name' => "ok", 'slug' => "ok", 'image' => "https://bkhut.ir/assets/icon.png"],
        //         ['name' => "ok", 'slug' => "ok", 'image' => "https://bkhut.ir/assets/icon.png"],
        //     ];
        // }
        // $categories = BookCategory::with(['books' => function ($query) {
        //     $query->take(8); // فقط 10 کتاب از هر دسته
        // }])->get();

        // dd($categories);
        $categories = BookCategory::with(['books' => function ($query) {
            $query->take(8); // فقط 10 کتاب از هر دسته
        }])->get();

        // dd($categories);

        $low_books = Book::orderBy('views', 'asc')->take(5)->get();

        $newestBooks = Book::orderBy('created_at', 'DESC')
            ->take(8) // برای نمایش 5 کتاب جدید
            ->get();

        $freeBooks = Book::where('price', '=', 0)
            ->take(8) // برای نمایش 5 کتاب جدید
            ->get();

        return view('index', ['categories' => $categories, 'low_books' => $low_books, 'newestBooks' => $newestBooks, 'freeBooks' => $freeBooks]);
    }

    public function books()
    {

        $books = Book::paginate(14); // 10 کتاب در هر صفحه
        // dd($books);
        return view('books', ['books' => $books]);
    }

    public function category_books($category)
    {
        $books = null;
        $categoryName = ''; // برای نمایش عنوان صفحه

        if ($category === 'newest') {
            // کتاب‌های جدید: بر اساس تاریخ ایجاد، جدیدترین‌ها
            $books = Book::orderBy('created_at', 'desc')->paginate(14);
            $categoryName = 'جدیدترین کتاب‌ها';
        } elseif ($category === 'free') {
            // کتاب‌های رایگان: فیلتر بر اساس قیمت 0
            $books = Book::where('price', 0)->paginate(14);
            $categoryName = 'کتاب‌های رایگان';
        } else {
            // برای category‌های واقعی: بر اساس slug واقعی
            $categoryModel = BookCategory::where('slug', $category)->firstOrFail();
            $books = Book::where('category_id', $categoryModel->id)->paginate(14);
            $categoryName = $categoryModel->name;
        }

        return view('books', ['category' => $categoryName, 'books' => $books]);
    }

    public function book($slug)
    {
        // Book::where('slug', $slug)->increment('views');

        $book = Book::where('slug', $slug)->first();

        if (!$book) {
            abort(404); // اگر کتاب پیدا نشد
        }

        $genre = Genre::find($book->genre_id);
        $user_id = Auth::id();
        $book_id = $book->id;
        // $hasBook = Library::where('book_id', $book_id)->where('user_id', $user_id)->exists();

        // // کامنت‌ها با withCount
        // $comments = Comment::with(['author', 'replies.author', 'parent'])
        // 	->withCount([
        // 		'likes as likes_count' => function ($query) {
        // 			$query->where('is_like', true);
        // 		},
        // 		'likes as dislikes_count' => function ($query) {
        // 			$query->where('is_like', false);
        // 		}
        // 	])
        // 	->where('commentable_type', Book::class)
        // 	->where('commentable_id', $book->id)
        // 	->where('is_public', true)
        // 	// ->where('is_approved', true)
        // 	// ->whereNull('reply_id')
        // 	// ->orderBy('created_at', 'desc')
        // 	->get();

        // دریافت ۸ کتاب مشابه از همان ژانر
        $similars = Book::where('genre_id', $book->genre_id)
            ->where('id', '!=', $book->id)
            ->take(8)
            ->get();

        $pageTitle = "کتاب {$book->title} - ... | کلبه کتاب";
        // Book::find($book->id)->increment('views');

        return view('book/index', [
            'pageTitle' => $pageTitle,
            'book' => $book,
            'similars' => $similars,
            // 'comments' => $comments,
            // 'hasBook' => $hasBook,
            'genre' => $genre,
            'pageDescription' => $book->description
        ]);
    }

    public function bookpage_fv($slug, $id)
    {
        $user_id = Auth::id();
        $book = Book::where('slug', $slug)->first();

        if (!$book) {
            return redirect()->route('books.index')->with('error', 'کتاب پیدا نشد.');
        }

        $genre = Genre::find($book->genre_id);
        $book_id = $book->id;
        $bookPart = BookPart::where('book_id', $book_id)->where('page_id', $id)->first();

        // محاسبه تعداد کل صفحات
        $totalPages = BookPart::where('book_id', $book_id)->count();

        if ($id > 20) {
            $hasBook = Library::where('book_id', $book_id)->where('user_id', $user_id)->exists();

            if ($hasBook) {
                return redirect()->route('online-ready', [
                    'slug' => $slug,
                    'id' => $id
                ]);
            } else {
                return redirect()->route('buy-book', [
                    'slug' => $slug
                ]);
            }
        } else {
            if ($bookPart == null) {
                return view('book/not-found-page', [
                    'book' => $book,
                    'bookPart' => $bookPart,
                    'pageNumber' => $id,
                    'totalPages' => $totalPages,
                ]);
            }

            return view('book/free-version', [
                'book' => $book,
                'bookPart' => $bookPart,
                'pageNumber' => $id,
                'totalPages' => $totalPages,
                'genre' => $genre
            ]);
        }
    }



    public function buy_book($slug)
    {
        $book = Book::where('slug', $slug)->first();
        $user = Auth::user();
        $discountedPrice = $book->price;

        if ($book->copen > 0) {
            $discountedPrice = $book->price - ($book->price * $book->copen / 100);
        }
        return view('book/buy-book', compact('book', 'user', 'discountedPrice'));
    }


    public function about()
    {
        return view('about');
    }

    
    public function contact()
    {
        return view('contact');
    }

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
    //         view('register', $data);
    //     }
    //     public function suggestions()
    //     {
    //         $suggestions = Guild::where('suggest', '=', '1')->get();
    //         $categories = GuildCategory::all();
    //         return view('suggestions', ['suggestions' => $suggestions, 'categories' => $categories]);
    //     }
    //     public function melon()
    //     {
    //         view('index');
    //     }
    //     public function search()
    //     {
    //         $request = new Request();
    //         $query = $request->query('query');
    //         $category = $request->query('category');
    //         $msg = null;
    //         if (!empty($query)) {
    //             // اضافه کردن % برای جستجو در اطراف کلمه کلیدی
    //             $searchTerm = '%' . $query . '%';

    //             if (!empty($category)) {
    //                 $guilds = Guild::where('category', '=', $category)
    //                     ->orWhere('name', 'LIKE', $searchTerm)
    //                     ->orWhere('description', 'LIKE', $searchTerm)
    //                     ->orWhere('manage', 'LIKE', $searchTerm)
    //                     ->get();
    //             } else {
    //                 $guilds = Guild::where('name', 'LIKE', $searchTerm)
    //                     ->orWhere('description', 'LIKE', $searchTerm)
    //                     ->orWhere('manage', 'LIKE', $searchTerm)
    //                     ->get();
    //             }
    //             if (empty($guilds)) {
    //                 $msg = 'نتیجه یافت نشد';
    //             }
    //         } else {
    //             // error_log('Empty: '.$query);
    //             $guilds = [];
    //         }

    //         $categories = GuildCategory::all();
    //         //// error_log(json_encode(['categories' => $categories, 'guilds' => $guilds, 'msg' => $msg]));
    //         return view('search', ['categories' => $categories, 'guilds' => $guilds, 'query' => $query, 'msg' => $msg]);
    //     }

    //     public function guilds()
    //     {
    //         $guilds = Guild::all();
    //         view('guilds', ['guilds' => $guilds]);
    //     }
    //     public function guilds_category($slug)
    //     {
    //         $category = GuildCategory::where('slug', '=', $slug)->first();
    //         $guilds = Guild::where('category', '=', $category->id)->get();

    //         view('guilds', ['category' => $category, 'guilds' => $guilds]);
    //     }
    //     public function guild($slug)
    //     {
    //         $guild = Guild::where('slug', '=', $slug)->first();
    //         $categories = GuildCategory::all();
    //         view('guild', ['slug' => $slug, 'guild' => $guild, 'categories' => $categories]);
    //     }
    //     public function login()
    //     {
    //         if (session()->has('user')) {
    //             redirect()->route('admin');
    //         } else {
    //             view('login');
    //         }
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
    //             // var_dump($_SESSION); // بررسی سشن‌ها
    //             redirect()->route('admin');
    //         } else {
    //             view('login', ['msg' => 'ورود ناموفق']);
    //         }
    //     }
}
