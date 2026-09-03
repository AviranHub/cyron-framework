## 📘 مستندات فریمورک «سایرون» (Cyron Framework)

این مستندات به شما کمک می‌کند تا با فریمورک اختصاصی خود (Cyron Framework) آشنا شوید و بتوانید از آن برای ساخت پروژه‌های وب استفاده کنید. فریمورک شما شامل امکاناتی چون مسیریابی حرفه‌ای، قالب‌سازی لیدی، ORM کامل، اعتبارسنجی، احراز هویت، و خط فرمان است.

---

## ۱. نصب و تنظیمات اولیه

### پیش‌نیازها
- PHP 8.2 یا بالاتر
- MySQL / MariaDB
- وب‌سرور Apache (با فعال بودن mod_rewrite)
- Composer 2.x

### مراحل نصب
1. فایل‌های فریمورک را در پوشه `htdocs` کپی کنید.
2. وابستگی‌ها و autoload استاندارد را نصب کنید:
    ```bash
    composer install --no-dev --optimize-autoloader
    ```
3. فایل `.env` را ایجاد کنید (یا از `.env.example`) و اطلاعات دیتابیس را تنظیم کنید:
   ```ini
   DB_USERNAME=root
   DB_PASSWORD=
   DB_NAME=my_database
   ```
4. آپاچی را ریستارت کنید.
5. با اجرای `php zeno migrate` جداول را ایجاد کنید.

---

## ۲. ساختار پوشه‌ها
```
app/
├─ Core/          (هسته فریمورک: روتر، لیدی، validation، localization، ...)
├─ Http/          (کنترلرها، میدلورها، درخواست)
├─ Models/        (مدل‌های دیتابیس)
├─ database/      (مایگریشن‌ها، ORM، Builder)
├─ helpers.php    (توابع کمکی)
├─ router.php     (کلاس Route)
resources/
├─ Layouts/       (فایل‌های layout لیدی)
├─ Views/         (فایل‌های ویو لیدی)
├─ lang/          (فایل‌های ترجمه)
public/           (نقطه ورود، فایل‌های عمومی)
cli/              (کامندهای خط فرمان)
storage/cache/views/ (کش ویوها)
routes/           (web.php و api.php)
zeno              (فایل ورودی CLI)
```

---

## ۳. مسیریابی (Routing)

کلاس `Route` در `app/router.php` تمام متدهای HTTP را پشتیبانی می‌کند.

### تعریف روت ساده
```php
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/contact', [ContactController::class, 'store']);
```

### گروه‌بندی با پیشوند و میدلور
```php
Route::prefix('admin')->middleware(Auth::class)->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users']);
});
```

### نام‌گذاری و تولید آدرس
```php
$url = route('admin.dashboard'); // /admin/dashboard
```

### پارامترهای داینامیک
```php
Route::get('/user/{id}', [UserController::class, 'show'])->name('user.show');
// با پارامتر اختیاری: {id?}
```

### ریدایرکت به روت
```php
return redirect()->route('login');
return redirect()->back()->withErrors($errors)->withInput();
```

---

## ۴. کنترلرها

با استفاده از کامند `make:controller` بسازید:
```bash
php zeno make:controller Auth/RegisterController
```
کنترلر نمونه:
```php
namespace App\Http\Controllers;

use App\Http\Controller;
use App\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', ['title' => 'صفحه اصلی']);
    }
}
```

---

## ۵. ویوها و قالب‌سازی لیدی (Lady)

فایل‌های ویو با پسوند `.lady.php` در پوشه `resources/Views/` قرار می‌گیرند.

### لایه اصلی (layout)
```php
<!-- resources/Layouts/master.lady.php -->
<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'پیش‌فرض')</title>
</head>
<body>
    @section('sidebar')
        این نوار کناری است
    @show

    <div class="container">
        @yield('content')
    </div>
</body>
</html>
```

### ویو فرزند
```php
@extends('layouts.master')

@section('title', 'صفحه درباره ما')

@section('sidebar')
    @parent
    <p>این متن به نوار کناری اضافه شده است</p>
@endsection

@section('content')
    <p>محتوای اصلی صفحه</p>
@endsection
```

### دایرکتیوهای پرکاربرد
| دایرکتیو | توضیح |
|-----------|--------|
| `@extends('layout')` | ارث‌بری از لایه |
| `@section('name')` | شروع بخش |
| `@endsection` | پایان بخش |
| `@yield('name')` | نمایش محتوای بخش |
| `@include('partials.header')` | شامل کردن ویو دیگر |
| `@csrf` | توکن CSRF |
| `@if(...)` | شرط |
| `@foreach(...)` | حلقه |
| `@error('field')` | نمایش خطای فیلد (به همراه `<div>`) |
| `@errors ... @enderrors` | نمایش همه خطاها |
| `{{ $var }}` | خروجی امن (htmlspecialchars) |
| `{!! $html !!}` | خروجی خام |

> **نکته:** ویوها به صورت خودکار کش می‌شوند. برای اعمال تغییرات، کش را با `php zeno cache:clear` (دستور موجود نیست، می‌توانید با حذف فایل‌های `storage/cache/views/`) پاک کنید.

---

## ۶. دیتابیس

### مایگریشن (Migration)
با کامند `make:migration` بسازید:
```bash
php zeno make:migration create_users_table
```
فایل ایجاد شده در `app/database/Migrations/` را ویرایش کنید:
```php
public static function up()
{
    Migration::createTable('users', [
        'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
        'name' => 'VARCHAR(100)',
        'email' => 'VARCHAR(100) UNIQUE',
        'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    ]);
}
```
سپس اجرا کنید:
```bash
php zeno migrate
php zeno migrate:rollback   # برگرداندن آخرین batch
```

### مدل (Model)
با کامند `make:model` بسازید:
```bash
php zeno make:model User
```
فایل `app/Models/User.php`:
```php
namespace App\Models;

use App\Database\Model;

class User extends Model
{
    protected static $table = 'users';
    // protected static $fillable = ['name', 'email'];
}
```

### کوئری بیلدر (Query Builder)
```php
$users = User::where('status', 'active')->orderBy('name')->limit(10)->get();
$user = User::find(1);
$user->name = 'علی';
$user->save();
$user->delete();
User::create(['name' => 'رضا', 'email' => 'r@r.com']);
```

### صفحه‌بندی (Pagination)
```php
$users = User::where('age', '>', 18)->paginate(15);
// $users['data']   => Collection
// $users['current_page'], $users['last_page'], ...
```
در ویو:
```php
@foreach($users['data'] as $user)
    {{ $user->name }}
@endforeach
{{ paginate_links($users) }}
```

### روابط (Relations)
```php
// در مدل User
public function posts() {
    return $this->hasMany(Post::class, 'user_id');
}
// در مدل Post
public function user() {
    return $this->belongsTo(User::class, 'user_id');
}

// استفاده با eager loading
$users = User::with('posts')->get();
foreach ($users as $user) {
    foreach ($user->posts as $post) { ... }
}
```

---

## ۷. اعتبارسنجی (Validation)

در کنترلر از `Request::validate()` استفاده کنید:
```php
$rules = [
    'email' => 'required|email|unique:users,email',
    'password' => 'required|min:6|confirmed',
    'role' => 'in:admin,user',
];
$errors = $request->validate($rules);
if ($errors && $errors->any()) {
    return redirect()->back()->withErrors($errors)->withInput();
}
```
قوانین موجود: `required`, `email`, `string`, `integer`, `boolean`, `url`, `date`, `min:x`, `max:x`, `confirmed`, `in:...`, `not_in:...`, `unique:table,column`, `exists:table,column`, `required_if:field,value`, `required_with:field1,field2`, `different:field`, `same:field`, `regex:/pattern/`, `prohibited`.

---

## ۸. احراز هویت (Auth)

```php
use App\Core\Authentication\Auth;

// ورود
if (Auth::attempt('email@site.com', 'password')) { ... }
// یا با آبجکت کاربر
Auth::login($user);
// خروج
Auth::logout();
// بررسی وضعیت
if (Auth::check()) { $user = Auth::user(); }
```
برای محافظت از روت:
```php
Route::get('/dashboard', [DashboardController::class, 'index'])->auth();
```
یا با میدلور:
```php
Route::middleware(AuthMiddleware::class)->group(function () { ... });
```

---

## ۹. درخواست (Request)

```php
$request = new Request();
$name = $request->input('name', 'پیش‌فرض');
$all = $request->all();
$file = $request->file('image');
if ($request->hasFile('image')) { ... }
$method = $request->method();
if ($request->isMethod('POST')) { ... }
```

تابع `old()` در ویو برای نمایش مقدار قبلی فرم:
```php
<input name="name" value="{{ old('name') }}">
```

---

## ۱۰. توابع کمکی (Helpers)

| تابع | کاربرد |
|-------|--------|
| `view($name, $data)` | رندر ویو و برگرداندن خروجی |
| `route($name, $params)` | تولید آدرس بر اساس نام روت (چاپ می‌کند) |
| `redirect()->back()` | شیء برای ریدایرکت به صفحه قبل |
| `dd(...$vars)` | dump و die |
| `session()->set/get/has` | مدیریت سشن |
| `csrf_field()` | تولید input مخفی CSRF |
| `old($key)` | مقدار قبلی ورودی |
| `paginate_links($paginator)` | تولید لینک‌های صفحه‌بندی |
| `asset($path)` | تولید آدرس فایل در پوشه `public/assets` |

---

## ۱۱. خط فرمان (CLI)

فایل اجرایی `zeno` در ریشه پروژه. دستورات:

| دستور | توضیح |
|-------|--------|
| `php zeno make:controller Name` | ساخت کنترلر (با پوشه: `Auth/NameController`) |
| `php zeno make:model Name -m` | ساخت مدل (و مایگریشن با `-m`) |
| `php zeno make:middleware Name` | ساخت میدلور |
| `php zeno make:migration create_table_name` | ساخت مایگریشن |
| `php zeno make:fake Model` | ساخت کلاس تولید دیتای فیک |
| `php zeno migrate` | اجرای مایگریشن‌ها |
| `php zeno migrate:rollback` | برگرداندن آخرین batch |
| `php zeno route:list` | نمایش همه روت‌ها با رنگ |
| `php zeno fake Model 20` | تولید ۲۰ رکورد فیک برای مدل |
| `php zeno run` | راه‌اندازی سرور داخلی |

---

## ۱۲. مثال عملی: ثبت‌نام کاربر

**کنترلر:**
```php
public function register(Request $request)
{
    $rules = [
        'name' => 'required|string|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
    ];
    $errors = $request->validate($rules);
    if ($errors && $errors->any()) {
        return redirect()->back()->withErrors($errors)->withInput();
    }
    $user = User::create([
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'password' => password_hash($request->input('password'), PASSWORD_DEFAULT),
    ]);
    Auth::login($user);
    return redirect()->route('dashboard');
}
```

**ویو (`register.lady.php`):**
```php
@extends('layouts.master')
@section('content')
<form method="POST" action="{{ route('register') }}">
    @csrf
    <input name="name" value="{{ old('name') }}">
    @error('name') <div class="error">{{ $message }}</div> @enderror
    <input name="email" value="{{ old('email') }}">
    @error('email') <div class="error">{{ $message }}</div> @enderror
    <input type="password" name="password">
    @error('password') <div class="error">{{ $message }}</div> @enderror
    <input type="password" name="password_confirmation">
    <button type="submit">ثبت نام</button>
</form>
@endsection
```

---

## ۱۳. نکات نهایی

- برای پاک کردن کش ویوها، فایل‌های `storage/cache/views/` را حذف کنید.
- خطاهای اعتبارسنجی به صورت خودکار در `$errors` در دسترس هستند و با `@errors` نمایش داده می‌شوند.
- فریمورک از PHP 8.2 استفاده می‌کند و خطاهای `Deprecated` فعال است. برای محیط تولید، `display_errors` را خاموش کنید.
- برای کار با فایل‌های استاتیک، آنها را در پوشه `public/assets` قرار دهید و با `asset('css/style.css')` فراخوانی کنید.

---

**موفق باشید!**  
ساخته شده با ❤️ توسط تیم کلبه کتاب (Cyron Framework)


---

## ۱۴. وضعیت توسعه جدید: Admin & Authentication Platform

شاخه `admin-auth-advanced-01` مجموعه‌ای از قابلیت‌های مدیریتی و امنیتی قابل توسعه را اضافه می‌کند.

### Admin و Audit
- CRUD عمومی قابل توسعه برای مدل‌های ثبت‌شده در Admin
- ثبت خودکار ایجاد، ویرایش و حذف در Audit
- Audit Explorer با جستجو، بازه زمانی، Actor و Action
- نمایش خوانای تغییرات `Before → After`

### Authentication Security
- Login History برای ورود موفق و ناموفق
- Login Protection و محدودسازی تلاش‌های ناموفق
- AuthenticationPipeline و AuthenticationFlow برای یکپارچه‌سازی جریان ورود
- Session Registry، نشست‌های فعال و Force Logout
- بررسی Session لغوشده با `EnsureSessionActive`
- Password Reset Token با Hash، انقضا و یک‌بارمصرف بودن

### Verification و Account Recovery
سیستم Verification وابسته به یک Provider خاص نیست و از Channel پشتیبانی می‌کند:
- Email
- Phone / SMS
- Purposeهای مختلف مانند `password_reset`، `verify` و `two_factor`
- Delivery Layer قابل توسعه با Interface `VerificationChannel`

### Two-Factor Authentication
- 2FA مبتنی بر Email
- 2FA مبتنی بر SMS/Phone
- اتصال 2FA به `AuthenticationFlow`
- مدیریت فعال/غیرفعال کردن و Audit تغییرات

### TOTP / Authenticator Apps
سرویس `App\\Auth\\Totp` برای Authenticator App اضافه شده است:

```php
$secret = Totp::generateSecret();
$uri = Totp::provisioningUri('Cyron', 'user@example.com', $secret);

if (Totp::verify($secret, $code)) {
    // code is valid
}
```

URI تولیدشده با قالب استاندارد `otpauth://totp` سازگار است و برای اپلیکیشن‌های Authenticator طراحی شده است. قبل از استفاده در Production، Secretهای TOTP باید با مکانیزم رمزنگاری کلیدمحور پروژه در حالت encrypted-at-rest نگهداری شوند.

### مسیر بعدی پیشنهادی
1. اتصال کامل این سرویس‌ها به Controller/Auth واقعی Core
2. Password Reset و Verification UI
3. Rotation و versioning کلیدهای encryption برای Secretهای حساس
4. Backup/Recovery Codes برای 2FA
5. Permission Matrix، Bulk Actions و UX پیشرفته Admin
