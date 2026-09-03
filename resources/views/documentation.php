<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cyron Framework Documentation</title>
<style>
:root{color-scheme:dark;--bg:#0d1016;--panel:#151a23;--panel2:#10141c;--text:#edf1f7;--muted:#9da8b8;--border:#293140;--accent:#83a5ff;--good:#75d6ad;--warn:#f3c969}
*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--text);font:15px/1.75 system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}main{width:min(1120px,94%);margin:32px auto 80px}header,section{background:var(--panel);border:1px solid var(--border);border-radius:16px;padding:26px;margin-bottom:18px}h1,h2,h3{line-height:1.3}h1{margin:0 0 8px;font-size:38px}h2{margin-top:0;color:var(--accent)}h3{margin-bottom:7px}p,li{color:var(--muted)}code{background:#090c11;border:1px solid var(--border);border-radius:6px;padding:2px 6px;color:#dce5ff}pre{overflow:auto;background:#090c11;border:1px solid var(--border);border-radius:10px;padding:16px;color:#dce5ff}a{color:var(--accent)}table{width:100%;border-collapse:collapse}td,th{border-bottom:1px solid var(--border);padding:9px;text-align:left;color:var(--muted)}th{color:var(--text)}.badge{display:inline-block;border:1px solid var(--border);border-radius:999px;padding:3px 10px;color:var(--muted);margin:3px}.note{border-left:3px solid var(--accent);padding-left:14px}.new{color:var(--good)}.toc a{display:inline-block;margin:4px 12px 4px 0}
</style>
</head>
<body><main>
<header>
<h1>Cyron Framework</h1>
<p>راهنمای قابلیت‌های فعلی Cyron بر اساس کد موجود در Framework. این صفحه عمداً APIهای واقعی هسته را مستند می‌کند، نه فقط مثال‌های قدیمی پروژه.</p>
<span class="badge">Routing</span><span class="badge">Lady</span><span class="badge">ORM</span><span class="badge">Auth</span><span class="badge">2FA</span><span class="badge">Sessions</span><span class="badge">Audit</span><span class="badge">Analytics</span><span class="badge">Security</span><span class="badge">Storage</span><span class="badge">Cache</span><span class="badge">CLI</span>
</header>

<section class="toc"><h2>فهرست</h2>
<a href="#routing">Routing</a><a href="#request">Request / Response</a><a href="#lady">Lady</a><a href="#db">Database & ORM</a><a href="#validation">Validation</a><a href="#auth">Authentication</a><a href="#audit">Audit & Activity</a><a href="#security">Security</a><a href="#storage">Storage</a><a href="#cache">Cache & Log</a><a href="#localization">Localization</a><a href="#actions">Actions</a><a href="#cli">CLI</a><a href="#errors">Errors</a><a href="#structure">Structure</a>
</section>

<section id="routing"><h2>1. Routing</h2>
<p>روت‌ها با <code>App\Route</code> تعریف می‌شوند و می‌توان آن‌ها را نام‌گذاری، گروه‌بندی و با Middleware محافظت کرد.</p>
<pre><code>use App\Route;

Route::get('/users', [UserController::class, 'index'])->name('users');
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'show']);

Route::middleware(AuthMiddleware::class)
    ->prefix('dashboard')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
    });</code></pre>
<p>برای URLهای نام‌گذاری‌شده از <code>route()</code> و برای انتقال پاسخ از <code>redirect()</code> استفاده کنید. روت‌های API نیز می‌توانند با <code>ApiAuthMiddleware</code> از Bearer Token استفاده کنند.</p>
</section>

<section id="request"><h2>2. Request و Response</h2>
<pre><code>$request->input('email');
$request->input();
$request->query('page', 1);
$request->file('avatar');
$request->hasFile('avatar');
$request->method();
$request->isMethod('POST');
$request->route('id');
$request->bearerToken();</code></pre>
<p>Request داده‌های POST/GET، فایل‌ها و در درخواست‌های <code>application/json</code> داده JSON را جمع می‌کند. در لایه Response نیز پاسخ‌های موفقیت، خطا، اعتبارسنجی و Unauthorized در دسترس هستند.</p>
</section>

<section id="lady"><h2>3. Lady Template Engine</h2>
<p>Lady موتور Template داخلی Cyron است و Parser، Compiler، Engine، ComponentManager و AttributeBag دارد. فایل‌های template در ساختار منابع پروژه نگهداری می‌شوند.</p>
<pre><code>@extends('layouts.master')

@section('title', 'Users')

@section('content')
    @if($users)
        @foreach($users as $user)
            &lt;h3&gt;{{ $user->name }}&lt;/h3&gt;
        @endforeach
    @endif
@endsection</code></pre>
<table><tr><th>Directive</th><th>کاربرد</th></tr><tr><td><code>@extends</code></td><td>ارث‌بری از Layout</td></tr><tr><td><code>@section / @endsection</code></td><td>تعریف بخش</td></tr><tr><td><code>@yield</code></td><td>نمایش بخش Layout</td></tr><tr><td><code>@include</code></td><td>درج Template دیگر</td></tr><tr><td><code>@if</code> / <code>@foreach</code></td><td>شرط و حلقه</td></tr><tr><td><code>@csrf</code></td><td>توکن CSRF</td></tr><tr><td><code>@error</code> / <code>@errors</code></td><td>نمایش خطاهای Validation</td></tr><tr><td><code>{{ }}</code></td><td>خروجی Escape شده</td></tr><tr><td><code>{!! !!}</code></td><td>خروجی خام</td></tr></table>
</section>

<section id="db"><h2>4. Database / Model / Query Builder</h2>
<p>مدل‌ها از <code>App\Database\Model</code> استفاده می‌کنند. برای مدل‌های پروژه نام جدول را صریحاً با <code>$table</code> مشخص کنید.</p>
<pre><code>class User extends Model
{
    protected static $table = 'users';
    protected static array $fillable = ['name', 'email', 'password'];
}

$users = User::where('status', 'active')
    ->orderBy('created_at', 'desc')
    ->limit(20)
    ->get();

$user = User::where('email', $email)->first();
$count = User::where('status', 'active')->count();</code></pre>
<p>Builder شامل <code>where</code>، <code>orWhere</code>، <code>whereIn</code>، <code>whereNull</code>، <code>whereNotNull</code>، <code>orWhereNull</code>، <code>orWhereNotNull</code>، <code>select</code>، <code>orderBy</code>، <code>limit</code>، <code>offset</code>، <code>first</code>، <code>get</code>، <code>count</code>، <code>insert</code>، <code>update</code> و <code>delete</code> است.</p>
<h3>Relations و Eager Loading</h3>
<pre><code>$books = Book::with('author')->get();

$categories = BookCategory::with([
    'books' =&gt; function ($query) {
        $query->take(8);
    }
])-&gt;get();</code></pre>
<h3>Pagination</h3><pre><code>$books = Book::paginate(15, 'page', 2);</code></pre><p>Paginator اطلاعاتی مانند <code>data</code>، <code>current_page</code>، <code>per_page</code>، <code>total</code>، <code>last_page</code>، <code>has_prev</code> و <code>has_next</code> ارائه می‌کند.</p>
</section>

<section id="validation"><h2>5. Validation</h2>
<pre><code>$errors = $request->validate([
    'name' => 'required|string|min:3',
    'email' => 'required|email|unique:users,email',
    'password' => 'required|min:6|confirmed',
]);

if ($errors && $errors->any()) {
    return redirect()->back()->withErrors($errors)->withInput();
}</code></pre>
<p>قواعد موجود شامل <code>required</code>، <code>email</code>، <code>string</code>، <code>integer</code>، <code>boolean</code>، <code>url</code>، <code>date</code>، <code>min</code>، <code>max</code>، <code>confirmed</code>، <code>in</code>، <code>not_in</code>، <code>unique</code>، <code>exists</code>، <code>required_if</code>، <code>required_with</code>، <code>different</code>، <code>same</code>، <code>regex</code> و <code>prohibited</code> است.</p>
</section>

<section id="auth"><h2>6. Authentication — Auth، Login Protection، Sessions و 2FA</h2>
<p class="new">این بخش یکی از سیستم‌های جدیدتر Cyron است.</p>
<pre><code>use App\Core\Authentication\Auth;

if (Auth::check()) {
    $user = Auth::user();
}

Auth::login($user);
Auth::logout();</code></pre>
<p><code>LoginManager</code> جریان ورود را مدیریت می‌کند: بررسی Rate Limit، اعتبارسنجی credential، تشخیص 2FA، ساخت session و ثبت نتیجه ورود. شکست و موفقیت Login از طریق <code>AuthenticationPipeline</code> قابل رهگیری است.</p>
<h3>Two-Factor Authentication</h3><pre><code>TwoFactor::enable($userId, 'sms', $target);
TwoFactor::disable($userId);
TwoFactor::enabled($userId);
TwoFactor::challenge($userId);
TwoFactor::verify($userId, $channel, $code);</code></pre>
<p>کانال‌های Verification شامل Email و SMS هستند. Login دارای حالت pending برای 2FA و انقضای challenge است.</p>
<h3>Session Registry</h3><pre><code>SessionRegistry::register($userId, $token, $meta);
SessionRegistry::active($token);
SessionRegistry::touch($token);
SessionRegistry::revokeToken($token);
SessionRegistry::revoke($sessionId);
SessionRegistry::revokeUser($userId);</code></pre>
<p>توکن session به صورت SHA-256 در Registry نگهداری می‌شود و امکان revoke کردن یک session یا تمام sessionهای کاربر وجود دارد.</p>
</section>

<section id="audit"><h2>7. Audit Log و Activity Analytics</h2>
<p class="new">Audit و Activity جزو قابلیت‌های مهم جدید Framework هستند و در نسخه‌های قبلی مستندات تقریباً غایب بودند 😅.</p>
<h3>Audit</h3>
<pre><code>use App\Audit\Audit;

Audit::record('book.created', [
    'book_id' =&gt; $book->id,
    'source' =&gt; 'admin',
]);</code></pre>
<p><code>Audit::record()</code> یک رخداد قابل‌ردیابی ثبت می‌کند. Actor در صورت عدم ارسال از کاربر جاری گرفته می‌شود و context درخواست شامل route، method، IP و user-agent است.</p>
<h3>Activity Tracker</h3>
<pre><code>use App\Analytics\ActivityTracker;

ActivityTracker::record(
    'book.viewed',
    ['book_id' =&gt; $book->id],
    $userId,
    'Book viewed'
);</code></pre>
<h3>Event / Metric / Segment Registry</h3>
<pre><code>EventRegistry::register('book.viewed', [
    'label' =&gt; 'Book Viewed',
    'category' =&gt; 'books',
]);

MetricRegistry::register('book_views', [
    'event' =&gt; 'book.viewed',
    'aggregation' =&gt; 'count',
]);

SegmentRegistry::register('readers', [
    'resolver' =&gt; $resolver,
]);</code></pre>
<p><code>EventRegistry</code> تعریف رخدادها را نگه می‌دارد؛ <code>MetricRegistry</code> برای تعریف Metricها و نوع aggregation؛ و <code>SegmentRegistry</code> برای تعریف Segmentهای قابل‌محاسبه استفاده می‌شود.</p>
</section>

<section id="security"><h2>8. Security Middleware و Authorization</h2>
<h3>CSRF</h3><p>درخواست‌های غیر GET/HEAD/OPTIONS که cookie-authenticated هستند باید توکن CSRF معتبر داشته باشند. APIهای مسیر <code>/api</code> از CSRF cookie flow مستثنا هستند و با Bearer Token احراز هویت می‌شوند.</p>
<pre><code>&lt;form method="POST"&gt;
    @csrf
    ...
&lt;/form&gt;</code></pre>
<h3>Security Headers</h3><p>Middleware هدرهای <code>X-Content-Type-Options</code>، <code>X-Frame-Options</code>، <code>Referrer-Policy</code> و <code>Permissions-Policy</code> را اعمال می‌کند و در HTTPS، HSTS را فعال می‌کند.</p>
<h3>Rate Limiter</h3><pre><code>new RateLimiter('login');       // 5 attempts / minute
new RateLimiter('register');    // 5 / minute
new RateLimiter('password_reset'); // 3 / minute
new RateLimiter('api');          // 60 / minute

$limiter->setMaxAttempts(30)->setDecayMinutes(5);</code></pre>
<h3>Authorization / Gate</h3><pre><code>Gate::allows('books.edit');
Gate::allowsAny(['books.edit', 'books.admin']);
Gate::allowsAll(['books.edit', 'books.publish']);
Gate::denies('books.delete');
Gate::hasRole('admin');
Gate::authorize('books.edit');</code></pre>
<h3>Permission / Ownership Middleware</h3><pre><code>Route::middleware(new PermissionMiddleware('books.edit'))-&gt;group(function () {
    // ...
});

new ResourceOwnershipMiddleware(
    Book::class,
    'user_id',
    'id'
);</code></pre>
<p>همچنین Middlewareهای <code>AuthMiddleware</code>، <code>ApiAuthMiddleware</code>، <code>VerifiedPhoneMiddleware</code>، <code>SessionTimeoutMiddleware</code> و <code>RequestHardeningMiddleware</code> در هسته موجود هستند.</p>
</section>

<section id="storage"><h2>9. Storage و File Upload</h2>
<pre><code>use App\Core\Storage\StorageManager;

StorageManager::put('avatars/a.txt', $contents);
StorageManager::get('avatars/a.txt');
StorageManager::exists('avatars/a.txt');
StorageManager::delete('avatars/a.txt');
StorageManager::url('avatars/a.txt');

$name = StorageManager::upload($request->file('avatar'), 'avatars');

StorageManager::disk('private');</code></pre>
<p>Storage دارای diskهای <code>public</code> و <code>private</code> است. Upload فایل MIME واقعی را بررسی می‌کند، اندازه را محدود می‌کند و نام تصادفی تولید می‌کند. همچنین API قدیمی‌تر <code>App\Http\Storage</code> برای driverهای public/private وجود دارد.</p>
</section>

<section id="cache"><h2>10. Cache و Logging</h2>
<h3>Cache</h3><pre><code>CacheManager::put('key', $value, 3600);
$value = CacheManager::get('key', $default);
CacheManager::has('key');
CacheManager::remember('users', 600, fn () =&gt; User::all());
CacheManager::increment('counter');
CacheManager::forget('key');
CacheManager::flush();</code></pre>
<h3>Log</h3><pre><code>LogManager::info('User logged in');
LogManager::warning('Unexpected request');
LogManager::error('Payment failed', ['order' =&gt; $id]);
LogManager::setMinLevel('warning');</code></pre>
<p>Driver فعلی Cache و Log بر پایه فایل است و سطح حداقل Log قابل تنظیم است.</p>
</section>

<section id="localization"><h2>11. Localization</h2>
<pre><code>use App\Core\Localization\Translator;

Translator::get('welcome');
Translator::get('hello', ['name' =&gt; $user->name]);
Translator::setLocale('en');
Translator::getLocale();
Translator::isRtl();</code></pre>
<p>زبان‌های فعلی <code>fa</code> و <code>en</code> هستند. Locale می‌تواند از session، cookie یا زبان مرورگر تشخیص داده شود و فایل ترجمه از <code>resources/Lang/&lt;locale&gt;/messages.php</code> خوانده می‌شود.</p>
</section>

<section id="actions"><h2>12. Actions</h2>
<p>برای جدا کردن منطق عملیاتی از Controller می‌توان از <code>BaseAction</code> استفاده کرد.</p>
<pre><code>class CreateBook extends BaseAction
{
    public function execute(array $data = [])
    {
        $errors = $this->validate($data, [
            'title' =&gt; 'required|string|min:2',
        ]);

        if ($errors) return $this->validationError($errors);
        return $this->success(['created' =&gt; true]);
    }
}</code></pre>
<p>BaseAction ابزارهای <code>execute</code>، <code>user</code>، <code>check</code>، <code>success</code>، <code>error</code>، <code>unauthorized</code>، <code>validationError</code>، <code>validate</code> و <code>get</code> را فراهم می‌کند.</p>
</section>

<section id="cli"><h2>13. CLI / Zeno</h2>
<pre><code>php zeno migrate
php zeno migrate:rollback
php zeno make:controller UserController
php zeno make:model User -m
php zeno make:middleware AdminMiddleware
php zeno make:migration create_users_table
php zeno make:fake User
php zeno route:list
php zeno fake User 20
php zeno run
php zeno dev:seed</code></pre>
<p><code>dev:seed</code> فقط برای Development است و credentialهای حساب‌های آزمایشی را از Environment می‌گیرد. هرگز credential توسعه را hard-code نکنید.</p>
</section>

<section id="errors"><h2>14. Error Handling و Debugging</h2>
<p>Handler توسعه اطلاعات Exception، message، file، line، request context و stack trace را نمایش می‌دهد و گزارش را قابل Copy می‌کند. برای جلوگیری از نشت اطلاعات، secretها، passwordها و cookieهای حساس نباید در گزارش قرار بگیرند.</p>
<p>در Production، <code>APP_ENV</code> و <code>APP_DEBUG</code> باید به شکل امن تنظیم شوند و Production Guard از تنظیمات ناامن جلوگیری می‌کند.</p>
</section>

<section><h2>15. Environment</h2>
<pre><code>use App\Core\Env;

$env = Env::get('APP_ENV', 'production');
$db = Env::get('DB_NAME');
if (Env::has('SOME_OPTION')) { ... }</code></pre>
<p>تنظیمات حساس را در <code>.env</code> نگه دارید. <code>.env.example</code> برای الگوی تنظیمات است و فایل واقعی <code>.env</code> نباید commit شود.</p>
</section>

<section id="structure"><h2>16. ساختار Framework</h2>
<pre><code>app/
  Actions/       # Action classes
  Analytics/     # Activity / Event / Metric / Segment
  Audit/         # Audit logging
  Auth/          # Login, recovery, verification, 2FA, sessions
  Config/        # configuration
  Core/          # authentication, authorization, cache, Lady, localization, log, ...
  Http/          # controllers, middleware, request, storage
  Models/        # application/framework models
  Plugin/        # plugin and hooks
  Security/      # security helpers
  Sitemap/       # sitemap generation
  database/      # ORM, Builder, migrations
routes/          # web/api/auth routes
resources/       # views, layouts, translations
storage/         # cache, logs, files
zeno             # CLI entry point</code></pre>
<p class="note">این مستندات برای APIهای موجود در کد فعلی نوشته شده‌اند. اگر API جدیدی به Cyron اضافه شود، بهتر است همین صفحه همراه همان commit به‌روزرسانی شود.</p>
</section>
</main></body></html>
