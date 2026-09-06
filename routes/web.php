<?php

use Cyron\Routing\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\TestValidationController;
use Cyron\Support\Str;
use App\Http\Middlewares\AuthMiddleware;
use App\Http\Controllers\User\LibraryController;
use App\Http\Controllers\User\WalletController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ReaderController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ForumController;


// Route::get('/home', function() {
//     echo 'Welcome to Home!';
// });
// Route::get('/about', function() {
//     echo 'About Us';
// });



// Route::get('/',[HomeController::class,'index'])->name("index");




// Route::get('/register',[HomeController::class,'register'])->name("register");
// Route::get('/confirm',[HomeController::class,'confirm'])->name("confirm");
// Route::get('/suggestions',[HomeController::class,'suggestions'])->name("suggestions");



// Route::get('/melon',[HomeController::class,'melon']);

// Route::get('/guilds',[HomeController::class,'guilds']);
// Route::get('/guild/{slug}/{id}',[HomeController::class,'guild']);


// Route::get('/welcome', function () {
//     return view('welcome',[]);
// });


// Route::get('/hogo',[HomeController::class,'myfunction'])->name("hogo");


// Route::prefix('admin')->group(function (){
//     Route::get('/upo',[AdminController::class,'dashboard'])->name("dashboard");
//     Route::get('/login',[AdminController::class,'login'])->name("login");
//     Route::get('/add-category',[HomeController::class,'add_category'])->name("add-category");
// });


// Route::any('/404','404@lady.php');


// Route::get('/', function() {
//     return view('welcome');
// });

if (strtolower((string) \Cyron\Support\Env::get('APP_ENV', 'production')) !== 'production') {
    Route::get('/test-component', function () {
        return view('test-component');
    });

    Route::get('/raw', function() {
        return "Raw output - framework is working!";
    });

    Route::get('/test',function (){
        return view('test');
    });

    Route::get('/test-validation', [TestValidationController::class, 'showForm'])->name('test-validation.form');
    Route::post('/test-validation', [TestValidationController::class, 'validateForm'])->name('test-validation.validate');
}

Route::get('/about-us', [HomeController::class, 'about'])->name('about-us');
Route::get('/contact-us', [HomeController::class, 'contact'])->name('contact-us');


// // Define routes
Route::get('/', [HomeController::class, 'index'])->name('home');
// Route::get('/search', [HomeController::class, 'search'])->name('search.results');
// Route::get('/searching', [HomeController::class, 'searching'])->name('searching');
// // Route::get('/register', [HomeController::class, 'register'])->name('register');
// // Route::post('/register', [HomeController::class, 'register_confirm'])->name('register');
// Route::get('/suggestions', [HomeController::class, 'suggestions'])->name('suggestions');
// Route::get('/guild/{slug}', [HomeController::class, 'guild'])->name("guild");
// Route::get('/guilds/{slug}', [HomeController::class, 'guilds_category'])->name("guilds-category");
// Route::get('/guilds', [HomeController::class, 'guilds'])->name("guilds");
// Route::get('/login', [HomeController::class, 'login'])->name("login");
// Route::post('/login/check', [HomeController::class, 'login_check'])->name("login-check");


// // Define a route group with prefix
// Route::prefix('/admin')->group(function () {
//     Route::get('', [AdminController::class, 'dashboard'])->name('admin'); // Use '' instead of '/'
//     Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard'); // Use '' instead of '/'
//     Route::get('/register', [AdminController::class, 'register'])->name('admin.register'); // Use '' instead of '/'
//     Route::post('/register', [AdminController::class, 'register_confirm'])->name('admin.register.confirm'); // Use '' instead of '/'
//     Route::get('/guilds/suggestion', [AdminController::class, 'guilds_suggestion'])->name('admin.guilds.suggestion');
//     Route::get('/guilds/confirms', [AdminController::class, 'guilds_confirms'])->name('admin.guilds.confirms');
//     Route::get('/guilds/denyes', [AdminController::class, 'guilds_denyes'])->name('admin.guilds.denyes');
//     Route::get('/guilds/waiting', [AdminController::class, 'guilds_waiting'])->name('admin.guilds.waiting');
//     Route::get('/guilds', [AdminController::class, 'guilds'])->name('admin.guilds');
//     // Route::get('/guilds/suggest/{slug}', [AdminController::class, 'guilds'])->name('admin.guilds');
//     Route::get('/guilds/suggest/{slug}', [AdminController::class, 'guild_suggest'])->name('admin.guilds.suggest');
//     Route::get('/guilds/unsuggest/{slug}', [AdminController::class, 'guild_unsuggest'])->name('admin.guilds.unsuggest');
//     Route::get('/guilds/confirm/{slug}', [AdminController::class, 'guild_confirm'])->name('admin.guilds.confirm');
//     Route::get('/guilds/deny/{slug}', [AdminController::class, 'guild_deny'])->name('admin.guilds.deny');
//     Route::get('/guilds/edit/{slug}', [AdminController::class, 'guild_edit'])->name('admin.guilds.edit');
//     Route::post('/guilds/edit', [AdminController::class, 'guild_edit_confirm'])->name('admin.guilds.edit.confirm');
//     Route::post('/guilds/delete', [AdminController::class, 'guild_delete'])->name('admin.guilds.delete');
//     Route::get('/guild/category/add', [AdminController::class, 'guild_category_add'])->name('admin.guild.category.add');
//     Route::post('/guild/category/add', [AdminController::class, 'guild_category_add_confirm'])->name('admin.guild.category.add');
//     Route::get('/guild/category/edit', [AdminController::class, 'guild_category_edit'])->name('admin.guild.category.edit');
//     Route::post('/guild/category/edit', [AdminController::class, 'guild_category_edit_confirm'])->name('admin.guild.category.edit');
//     Route::get('/guild/category/delete', [AdminController::class, 'guild_category_delete'])->name('admin.guild.category.delete');
//     Route::post('/guild/category/delete', [AdminController::class, 'guild_category_delete_confirm'])->name('admin.guild.category.delete');
// });


// // Define a route group with prefix
// Route::prefix('/admin/setting')->group(function () {
//     Route::get('/slider', [AdminController::class, 'slider'])->name('admin.setting.slider'); // Use '' instead of '/'
//     Route::post('/slider/add', [AdminController::class, 'slider_add'])->name('admin.setting.slider.add');
//     Route::post('/slider/delete', [AdminController::class, 'slider_delete'])->name('admin.setting.slider.delete');
// });

// Route::get('/test-db', function() {
//     $user = \App\Models\User::first();
//     dd($user);
// });


Route::prefix('book')->group(function () {
	Route::get('/{slug}', [HomeController::class, 'book'])->name('book');
	Route::get('/{slug}/page/{id}', [HomeController::class, 'bookpage_fv'])->name('bookpage-fv');
	Route::get('/{slug}/buy', [HomeController::class, 'buy_book'])->name('book.buy')/*->middleware(AuthMiddleware::class)*/;
    Route::post('/{slug}/purchase', [PaymentController::class, 'purchaseBook'])->name('book.purchase')->middleware(AuthMiddleware::class);
    Route::get('/{slug}/reader', [ReaderController::class, 'show'])->name('book.reader')->middleware(AuthMiddleware::class);
    Route::get('/{slug}/read/page/{id}', [HomeController::class, 'online_ready'])->name('online-ready')->middleware(AuthMiddleware::class);
    Route::post('/{slug}/comment', [HomeController::class, 'save_comments'])->name('save-comments')->middleware(AuthMiddleware::class);
	// Route::get('/{slug}/read/page/{id}', [HomeController::class, 'online_ready'])->name('online-ready');
	// Route::post('/{slug}/comment', [HomeController::class, 'save_comments'])->name('save-comments');
    // Route::post('/{book}/purchase', [PayController::class, 'purchaseBook'])->name('book.purchase')->middleware('auth');
    // Route::get('/{slug}/reader', [UserController::class, 'showReader'])->name('book.reader');
    // Route::get('/{slug}/pdf', [UserController::class, 'downloadPdf'])->name('book.pdf');
	
});

Route::get('/dashboard/add-to-library/{slug}', [HomeController::class, 'add_to_library'])
    ->name('add-to-library')->middleware(AuthMiddleware::class);

Route::get('/books', [HomeController::class, 'books'])->name('books');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/books/category/{category}', [HomeController::class, 'category_books'])->name('category.books');
Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.plans');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
Route::get('/forum/category/{slug}', [ForumController::class, 'category'])->name('forum.category');
Route::get('/forum/topic/{slug}', [ForumController::class, 'show'])->name('forum.topic');
Route::middleware(AuthMiddleware::class)->prefix('forum')->group(function () {
    Route::get('/create', [ForumController::class, 'create'])->name('forum.create');
    Route::post('/topics', [ForumController::class, 'store'])->name('forum.store');
    Route::post('/topic/{slug}/replies', [ForumController::class, 'reply'])->name('forum.reply');
});
Route::get('/author/{authorName}', [AuthorController::class, 'show'])->name('author.profile');
Route::get('/u/{username}', [UserProfileController::class, 'show'])->name('user.profile');
Route::post('/author/{authorName}/follow', [AuthorController::class, 'toggleFollow'])->name('author.follow')->middleware(AuthMiddleware::class);
Route::post('/author/{authorName}/support', [AuthorController::class, 'support'])->name('author.support')->middleware(AuthMiddleware::class);
Route::post('/toggle-like', [LikeController::class, 'toggle'])->name('toggle.like')->middleware(AuthMiddleware::class);

Route::middleware(AuthMiddleware::class)->prefix('dashboard')->group(function () {
    Route::get('/library', [LibraryController::class, 'index'])->name('user.library');
    Route::post('/library/shelves', [LibraryController::class, 'storeShelf'])->name('user.library.shelves.store');
    Route::get('/library/shelves/{id}', [LibraryController::class, 'showShelf'])->name('user.library.shelves.show');
    Route::post('/subscriptions/{id}/purchase', [SubscriptionController::class, 'purchase'])->name('subscriptions.purchase');
    Route::get('/wallet', [WalletController::class, 'index'])->name('user.wallet');
    Route::post('/wallet/recharge', [PaymentController::class, 'recharge'])->name('wallet.recharge');
});

Route::middleware(AuthMiddleware::class)->prefix('support')->group(function () {
    Route::get('/messages', [\App\Http\Controllers\SupportController::class, 'messages'])->name('support.messages');
    Route::post('/messages', [\App\Http\Controllers\SupportController::class, 'send'])->name('support.messages.send');
    Route::get('/attachments/{filename}', [\App\Http\Controllers\SupportController::class, 'attachment'])->name('support.attachments.show');
});

Route::get('/payment/verify', [PaymentController::class, 'verify'])->name('payment.verify');

Route::get('/lang/{locale}', function($locale) {
    set_locale($locale);
    redirect()->back();
})->name('change-locale');


require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/admin.php';
require_once __DIR__ . '/api.php';
require_once __DIR__ . '/author.php';

Route::any('/404','404');

Route::fallback(function() {
    return view('errors.404');
});


// // Define routes
// Route::get('/home', function () {
//     echo 'Welcome to Home!';
// })->name('home');

// Route::post('/user', function () {
//     // Handle user creation
//     echo 'User  created';
// })->name('user.create');

// Route::get('/user/{id}', function ($id) {
//     echo "User  ID: $id";
// })->name('user.show');

