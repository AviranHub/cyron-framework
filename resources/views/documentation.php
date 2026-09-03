<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cyron Framework Documentation</title>
    <style>
        :root { color-scheme: dark; --bg:#0f1115; --panel:#171a21; --text:#e8eaf0; --muted:#9aa2b1; --border:#292e39; --accent:#7c9cff; }
        * { box-sizing:border-box; }
        body { margin:0; background:var(--bg); color:var(--text); font:16px/1.7 system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif; }
        main { width:min(1050px,92%); margin:40px auto 80px; }
        header, section { background:var(--panel); border:1px solid var(--border); border-radius:16px; padding:28px; margin-bottom:20px; }
        h1,h2,h3 { line-height:1.3; }
        h1 { margin-top:0; font-size:36px; }
        h2 { margin-top:0; color:var(--accent); }
        h3 { margin-bottom:8px; }
        p,li { color:var(--muted); }
        code { background:#0b0d11; border:1px solid var(--border); border-radius:6px; padding:2px 6px; color:#dce4ff; }
        pre { overflow:auto; background:#0b0d11; border:1px solid var(--border); border-radius:10px; padding:16px; color:#dce4ff; }
        a { color:var(--accent); }
        .badge { display:inline-block; border:1px solid var(--border); border-radius:999px; padding:3px 10px; color:var(--muted); margin:3px; }
        .note { border-left:3px solid var(--accent); padding-left:14px; }
    </style>
</head>
<body>
<main>
<header>
    <h1>Cyron Framework</h1>
    <p>راهنمای سریع استفاده از قابلیت‌های اصلی Cyron، با تمرکز روی قابلیت‌های جدیدتر فریمورک.</p>
    <span class="badge">Routing</span><span class="badge">ORM / Query Builder</span><span class="badge">Validation</span><span class="badge">Authentication</span><span class="badge">Pagination</span><span class="badge">Request</span><span class="badge">Environment</span>
</header>

<section>
    <h2>1. Routing</h2>
    <p>روت‌ها با <code>App\Route</code> تعریف می‌شوند.</p>
<pre><code>use App\Route;

Route::get('/users', [UserController::class, 'index'])->name('users');
Route::post('/users', [UserController::class, 'store']);

Route::get('/hello', function () {
    return 'Hello Cyron!';
});</code></pre>
    <h3>پارامترهای Route</h3>
<pre><code>Route::get('/users/{id}', function ($id) {
    return "User: {$id}";
});</code></pre>
    <h3>Prefix و Middleware</h3>
<pre><code>Route::middleware(AuthMiddleware::class)
    ->prefix('dashboard')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
    });</code></pre>
</section>

<section>
    <h2>2. Controllers و Response</h2>
<pre><code>class UserController
{
    public function index()
    {
        return view('users', ['users' => User::all()]);
    }

    public function store(Request $request)
    {
        $name = $request->input('name');
        // ...
    }
}</code></pre>
    <p>برای صفحات HTML از <code>view()</code> و برای خروجی ساده می‌توان مستقیماً مقدار متنی/داده‌ای برگرداند.</p>
</section>

<section>
    <h2>3. Request — ورودی POST، JSON، Query و File</h2>
<pre><code>$request->input('email');
$request->input();
$request->query('page', 1);
$request->file('avatar');
$request->hasFile('avatar');
$request->method();
$request->isMethod('POST');</code></pre>
    <p>در درخواست‌های <code>application/json</code>، داده JSON نیز داخل ورودی Request در دسترس قرار می‌گیرد.</p>
</section>

<section>
    <h2>4. Database / Model</h2>
    <p>مدل‌ها از <code>App\Database\Model</code> ارث‌بری می‌کنند. در مدل‌های فعلی بهتر است نام جدول صراحتاً مشخص شود.</p>
<pre><code>class User extends Model
{
    protected static $table = 'users';

    protected static array $fillable = [
        'name', 'email', 'password'
    ];
}</code></pre>
    <h3>Query Builder</h3>
<pre><code>$users = User::where('status', 'active')
    ->orderBy('created_at', 'desc')
    ->limit(20)
    ->get();

$user = User::where('email', $email)->first();
$count = User::where('status', 'active')->count();</code></pre>
    <p>قابلیت‌های مهم Builder شامل <code>where</code>، <code>orWhere</code>، <code>whereIn</code>، <code>whereNull</code>، <code>whereNotNull</code>، <code>orderBy</code>، <code>limit</code>، <code>offset</code>، <code>first</code>، <code>get</code>، <code>count</code>، <code>insert</code>، <code>update</code> و <code>delete</code> است.</p>
</section>

<section>
    <h2>5. Relationships و Eager Loading</h2>
<pre><code>$books = Book::with('author')->get();

$categories = BookCategory::with([
    'books' => function ($query) {
        $query->take(8);
    }
])->get();</code></pre>
    <p>برای جلوگیری از Queryهای اضافه، روابط را با <code>with()</code> به‌صورت eager loading دریافت کنید. همچنین می‌توان برای Query رابطه constraint تعریف کرد.</p>
</section>

<section>
    <h2>6. Collection</h2>
<pre><code>$users = User::where('status', 'active')->get();

foreach ($users as $user) {
    echo $user->name;
}

$first = $users[0] ?? null;</code></pre>
    <p>متد <code>get()</code> مجموعه‌ای از Modelها را داخل <code>App\Database\Collection</code> برمی‌گرداند.</p>
</section>

<section>
    <h2>7. Pagination</h2>
<pre><code>$books = Book::orderBy('created_at', 'desc')->paginate(15);</code></pre>
    <p>خروجی Paginator اطلاعاتی مثل <code>data</code>، <code>current_page</code>، <code>per_page</code>، <code>total</code>، <code>last_page</code>، <code>has_prev</code> و <code>has_next</code> را در اختیار برنامه قرار می‌دهد.</p>
<pre><code>$books = Book::paginate(15, 'page', 2);</code></pre>
</section>

<section>
    <h2>8. Validation</h2>
    <p>برای فرم‌ها از سیستم Validation و ErrorBag استفاده کنید و اعتبارسنجی را قبل از عملیات حساس انجام دهید.</p>
<pre><code>$request = new Request();

// نمونه‌ی استفاده در Controller:
$name = $request->input('name');
$email = $request->input('email');

// قوانین اعتبارسنجی پروژه را در Validation layer اعمال کنید
// و خطاها را از ErrorBag به View منتقل کنید.</code></pre>
</section>

<section>
    <h2>9. Authentication</h2>
    <p>Cyron دارای مسیرهای ورود، خروج، ثبت‌نام، بازیابی رمز عبور، تأیید تلفن و داشبورد محافظت‌شده است.</p>
<pre><code>Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);

Route::middleware(AuthMiddleware::class)->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});</code></pre>
    <p class="note">مسیرهای Authentication را می‌توان از <code>routes/auth.php</code> جدا نگه داشت تا routeهای هسته و routeهای برنامه با هم قاطی نشوند.</p>
</section>

<section>
    <h2>10. Environment</h2>
    <p>تنظیمات محیطی از فایل <code>.env</code> خوانده می‌شوند و <code>.env.example</code> برای الگوی تنظیمات پروژه استفاده می‌شود.</p>
<pre><code>use App\Core\Env;

$dbHost = Env::get('DB_HOST');
$appEnv = Env::get('APP_ENV', 'production');

if (Env::has('DEBUG')) {
    // ...
}</code></pre>
    <p>اطلاعات محرمانه مانند رمز دیتابیس نباید داخل Repository قرار بگیرد.</p>
</section>

<section>
    <h2>11. Debugging و Error Handler</h2>
    <p>در محیط توسعه، Cyron خطاها را با اطلاعاتی مانند Exception، Message، File، Line، Request و Stack Trace نمایش می‌دهد. گزارش خطا قابلیت Copy کردن دارد تا بتوان آن را مستقیماً برای دیباگ استفاده کرد.</p>
    <p class="note">گزارش قابل کپی نباید شامل Password، Cookie، Headerهای حساس یا سایر Secretهای درخواست باشد.</p>
</section>

<section>
    <h2>12. CLI و Development Seed</h2>
<pre><code>php zeno migrate
php zeno dev:seed</code></pre>
    <p><code>dev:seed</code> فقط برای محیط Development طراحی شده و برای ساخت/به‌روزرسانی حساب‌های آزمایشی استفاده می‌شود. اطلاعات حساب‌های Development باید از <code>.env</code> خوانده شوند و در کد hard-code نشوند.</p>
</section>

<section>
    <h2>13. Security</h2>
    <ul>
        <li>برای نام جدول و ستون از مقادیر کنترل‌شده استفاده کنید؛ Query Builder برای Identifierها Guard دارد.</li>
        <li>مقادیر کاربر را مستقیماً داخل SQL قرار ندهید؛ از APIهای Query Builder استفاده کنید.</li>
        <li>رمز عبور را هرگز plaintext ذخیره نکنید.</li>
        <li>Routeهای حساس را با Middleware محافظت کنید.</li>
        <li>Secretهای محیطی را در <code>.env</code> نگه دارید و در Git commit نکنید.</li>
    </ul>
</section>

<section>
    <h2>14. ساختار پیشنهادی پروژه</h2>
<pre><code>app/
  Auth/
  Core/
  Database/
  Http/
  Models/
  ...
routes/
  web.php
  auth.php
  api.php
resources/
  views/
.env
.env.example
zeno</code></pre>
    <p>برای یک پروژه جدید، routeها، Controllerها، Modelها و Viewهای اختصاصی برنامه را از کد هسته Framework جدا نگه دارید.</p>
</section>

</main>
</body>
</html>
