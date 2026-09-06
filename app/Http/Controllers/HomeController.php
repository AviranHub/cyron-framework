<?php

namespace App\Http\Controllers;

use Cyron\Http\Storage;
use App\Models\User;
use Cyron\Http\Request;
use App\Models\GuildCategory;
use App\Models\Guild;
use Cyron\Http\ErrorBag;
use Cyron\Support\Str;
use App\Models\Slider;
use App\Database\Collection;
use App\Models\BookCategory;
use App\Models\Book;
use Cyron\Authentication\Auth;
use App\Models\Genre;
use App\Models\BookPart;
use App\Models\Library;
use App\Models\Comment;
use App\Services\DiscountService;
use App\Services\ViewTracker;
use Cyron\Analytics\ActivityTracker;


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

        $publishedOnly = Book::where('status', 'published')->count() > 0;
        $catalog = $publishedOnly ? Book::where('status', 'published') : Book::query();

        $low_books = $catalog
            ->orderBy('views', 'desc')
            ->take(5)
            ->get();

        $newestBooks = ($publishedOnly ? Book::where('status', 'published') : Book::query())
            ->orderBy('created_at', 'DESC')
            ->take(8) // برای نمایش 5 کتاب جدید
            ->get();

        $freeBooks = ($publishedOnly ? Book::where('status', 'published') : Book::query())
            ->where('price', '=', 0)
            ->take(8) // برای نمایش 5 کتاب جدید
            ->get();

        return view('index', [
            'categories' => $categories,
            'low_books' => $low_books,
            'newestBooks' => $newestBooks,
            'freeBooks' => $freeBooks,
            'pageTitle' => 'کلبه کتاب | کشف کتاب، رمان و کتاب صوتی',
            'pageDescription' => 'کلبه کتاب؛ جایی برای پیدا کردن کتاب بعدی، دنبال کردن مسیر مطالعه و گفتگو با آدم‌هایی که مثل شما کتاب دوست دارند.',
            'pageKeywords' => 'کتاب الکترونیکی, کتاب صوتی, رمان فارسی, کتابخوانی اجتماعی, کلبه کتاب',
        ]);
    }

    public function search(Request $request)
    {
        $query = trim((string) $request->input('query', ''));

        if ($query === '') {
            return redirect()->route('books');
        }

        $publishedOnly = Book::where('status', 'published')->count() > 0;
        $books = ($publishedOnly ? Book::where('status', 'published') : Book::query())
            ->where(function ($builder) use ($query) {
                $builder->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('author_name', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->orderBy('views', 'desc')
            ->paginate(14);
        $this->decorateBookPrices($books);

        return view('books', [
            'books' => $books,
            'category' => "نتایج جست‌وجو برای «{$query}»",
            'searchQuery' => $query,
        ]);
    }

    public function books()
    {

        $books = Book::paginate(14); // 10 کتاب در هر صفحه
        $this->decorateBookPrices($books);
        // dd($books);
        return view('books', ['books' => $books]);
    }

    public function library()
    {
        $userId = Auth::id();
        $entries = Library::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
        $books = [];

        foreach ($entries as $entry) {
            $book = Book::find($entry->book_id);
            if ($book) {
                $books[] = $book;
            }
        }

        return view('user/library', compact('books'));
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

        $this->decorateBookPrices($books);

        return view('books', ['category' => $categoryName, 'books' => $books]);
    }

    public function book($slug)
    {
        // Book::where('slug', $slug)->increment('views');

        $book = Book::where('slug', $slug)->first();

        if (!$book) {
            abort(404); // اگر کتاب پیدا نشد
        }

        if (ViewTracker::record(Book::class, (int) $book->id)) {
            $book->update(['views' => (int) ($book->views ?? 0) + 1]);
            ActivityTracker::record('book.viewed', ['subject_type' => Book::class, 'subject_id' => (int) $book->id], Auth::id());
        }

        $genre = Genre::find($book->genre_id);
        $pricing = $this->bookPricing($book);
        $user_id = Auth::id();
        $book_id = $book->id;
        $hasBook = $user_id ? Library::where('book_id', $book_id)->where('user_id', $user_id)->first() : null;

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

        $countComments = Comment::where('commentable_type', Book::class)
            ->where('commentable_id', $book->id)
            ->where('is_approved', 1)
            ->count();
        $comments = Comment::where('commentable_type', Book::class)
            ->where('commentable_id', $book->id)
            ->where('is_approved', 1)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        $pageTitle = "کتاب {$book->title} - ... | کلبه کتاب";
        // Book::find($book->id)->increment('views');

        return view('book/index', [
            'pageTitle' => $pageTitle,
            'book' => $book,
            'similars' => $similars,
            // 'comments' => $comments,
            'hasBook' => $hasBook,
            'countComments' => $countComments,
            'comments' => $comments,
            'genre' => $genre,
            'pageDescription' => $book->description
            , 'pricing' => $pricing
        ]);
    }

    public function bookpage_fv($slug, $id)
    {
        $user_id = Auth::id();
        $book = Book::where('slug', $slug)->first();

        if (!$book) {
            return redirect()->route('books')->with('error', 'کتاب پیدا نشد.');
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
                return redirect()->route('book.buy', [
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
        $pricing = (new DiscountService())->calculate('book', $book);
        if ($pricing['discount'] === null && (float) ($book->copen ?? 0) > 0) {
            $legacyDiscount = (float) $book->price * min(100, max(0, (float) $book->copen)) / 100;
            $pricing['discount_amount'] = round($legacyDiscount, 2);
            $pricing['final_amount'] = max(0, round((float) $book->price - $legacyDiscount, 2));
        }
        $discountedPrice = $pricing['final_amount'];
        return view('book/buy-book', compact('book', 'user', 'discountedPrice'));
    }

    public function add_to_library($slug)
    {
        $book = Book::where('slug', $slug)->first();
        $userId = Auth::id();

        if (!$book || !$userId) {
            return redirect()->route('books')->with('error', 'کتاب پیدا نشد.');
        }

        if (!Library::where('user_id', $userId)->where('book_id', $book->id)->first()) {
            Library::create([
                'user_id' => $userId,
                'book_id' => $book->id,
                'purchased_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->route('user.library')->with('success', 'کتاب به کتابخانه شما اضافه شد.');
    }

    public function online_ready($slug, $id = 1)
    {
        $book = Book::where('slug', $slug)->first();
        if (!$book) {
            return redirect()->route('books')->with('error', 'کتاب پیدا نشد.');
        }

        $bookPart = BookPart::where('book_id', $book->id)->where('page_id', $id)->first();
        $totalPages = BookPart::where('book_id', $book->id)->count();
        if (!$bookPart) {
            return view('book/not-found-page', compact('book', 'bookPart', 'totalPages'));
        }

        return view('book/free-version', [
            'book' => $book,
            'bookPart' => $bookPart,
            'pageNumber' => $id,
            'totalPages' => $totalPages,
            'genre' => Genre::find($book->genre_id),
        ]);
    }

    private function decorateBookPrices($books): void
    {
        foreach ($books as $book) {
            $pricing = $this->bookPricing($book);
            $book->display_price = $pricing['final_amount'];
            $book->discount_amount = $pricing['discount_amount'];
            $book->has_discount = $pricing['discount_amount'] > 0;
        }
    }

    private function bookPricing($book): array
    {
        $pricing = (new DiscountService())->calculate('book', $book);
        if ($pricing['discount'] === null && (float) ($book->copen ?? 0) > 0) {
            $legacyDiscount = (float) $book->price * min(100, max(0, (float) $book->copen)) / 100;
            $pricing['discount_amount'] = round($legacyDiscount, 2);
            $pricing['final_amount'] = max(0, round((float) $book->price - $legacyDiscount, 2));
        }
        return $pricing;
    }

    public function save_comments(Request $request, $slug)
    {
        $user = Auth::user();
        $book = Book::where('slug', $slug)->first();
        $text = trim((string) $request->input('text', ''));

        if (!$user || !$book || $text === '') {
            return redirect()->back()->with('error', 'ثبت نظر ممکن نیست.');
        }

        Comment::create([
            'author_id' => $user->id,
            'author_name' => $user->name ?? 'کاربر کلبه کتاب',
            'text' => $text,
            'reply_id' => $request->input('reply_id') ?: null,
            'depth' => 0,
            'is_public' => 1,
            'is_admin_view' => 1,
            'is_publisher_view' => 1,
            'is_approved' => 0,
            'replies_count' => 0,
            'report_count' => 0,
            'is_edited' => 0,
            'commentable_type' => Book::class,
            'commentable_id' => $book->id,
        ]);

        ActivityTracker::record('comment.created', [
            'subject_type' => Book::class,
            'subject_id' => (int) $book->id,
        ], (int) $user->id);

        return redirect()->back()->with('success', 'نظر شما برای بررسی ثبت شد.');
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
