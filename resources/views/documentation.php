<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
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
<pre><code>request-&gt;input('email');
request-&gt;input();
request-&gt;query('page', 1);
request-&gt;file('avatar');
request-&gt;hasFile('avatar');
request-&gt;method();
request-&gt;isMethod('POST');
request-&gt;route('id');
request-&gt;bearerToken();</code></pre>
<p>Request داده‌های POST/GET، فایل‌ها و در درخواست‌های <code>application/json</code> داده JSON را جمع می‌کند. در لایه Response نیز پاسخ‌های موفقیت، خطا، اعتبارسنجی و Unauthorized در دسترس هستند.</p>
</section>
<section id="lady"><h2>3. Lady Template Engine</h2>
<p>Lady موتور Template داخلی Cyron است و Parser، Compiler، Engine، ComponentManager و AttributeBag دارد.</p>
<pre><code>@extends('layouts.master')

@section('title', 'Users')

@section('content')
    @if(&#36;users)
        @foreach(&#36;users as &#36;user)
            &lt;h3&gt;{{ &#36;user-&gt;name }}&lt;/h3&gt;
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

&#36;users = User::where('status', 'active')
    ->orderBy('created_at', 'desc')
    ->limit(20)
    ->get();

&#36;user = User::where('email', &#36;email)->first();
&#36;count = User::where('status', 'active')->count();</code></pre>
<p>Builder شامل <code>where</code>، <code>orWhere</code>، <code>whereIn</code>، <code>whereNull</code>، <code>whereNotNull</code>، <code>orWhereNull</code>، <code>orWhereNotNull</code>، <code>select</code>، <code>orderBy</code>، <code>limit</code>، <code>offset</code>، <code>first</code>، <code>get</code>، <code>count</code>، <code>insert</code>، <code>update</code> و <code>delete</code> است.</p>
<h3>Relations و Eager Loading</h3>
<pre><code>&#36;books = Book::with('author')->get();

&#36;categories = BookCategory::with([
    'books' =&gt; function (&#36;query) {
        &#36;query-&gt;take(8);
    }
])-&gt;get();</code></pre>
<h3>Pagination</h3><pre><code>&#36;books = Book::paginate(15, 'page', 2);</code></pre><p>Paginator اطلاعاتی مانند <code>data</code>، <code>current_page</code>، <code>per_page</code>، <code>total</code>، <code>last_page</code>، <code>has_prev</code> و <code>has_next</code> ارائه می‌کند.</p>
</section>
<section id="validation"><h2>5. Validation</h2>
<pre><code>&#36;errors = &#36;request-&gt;validate([
    'name' =&gt; 'required|string|min:3',
    'email' =&gt; 'required|email|unique:users,email',
    'password' =&gt; 'required|min:6|confirmed',
]);</code></pre>
<p>قواعد موجود شامل <code>required</code>، <code>email</code>، <code>string</code>، <code>integer</code>، <code>boolean</code>، <code>url</code>، <code>date</code>، <code>min</code>، <code>max</code>، <code>confirmed</code>، <code>in</code>، <code>not_in</code>، <code>unique</code>، <code>exists</code>، <code>required_if</code>، <code>required_with</code>، <code>different</code>، <code>same</code>، <code>regex</code> و <code>prohibited</code> است.</p>
</section>
<section id="auth"><h2>6. Authentication — Auth، Login Protection، Sessions و 2FA</h2>
<p class="new">این بخش یکی از سیستم‌های جدیدتر Cyron است.</p>
<pre><code>use App\Core\Authentication\Auth;

if (Auth::check()) {
    &#36;user = Auth::user();
}

Auth::login(&#36;user);
Auth::logout();</code></pre>
<p><code>LoginManager</code> جریان ورود را مدیریت می‌کند: بررسی Rate Limit، اعتبارسنجی credential، تشخیص 2FA، ساخت session و ثبت نتیجه ورود.</p>
<h3>Two-Factor Authentication</h3><pre><code>TwoFactor::enable(&#36;userId, 'sms', &#36;target);
TwoFactor::disable(&#36;userId);
TwoFactor::enabled(&#36;userId);
TwoFactor::challenge(&#36;userId);
TwoFactor::verify(&#36;userId, &#36;channel, &#36;code);</code></pre>
<h3>Session Registry</h3><pre><code>SessionRegistry::register(&#36;userId, &#36;token, &#36;meta);
SessionRegistry::active(&#36;token);
SessionRegistry::touch(&#36;token);
SessionRegistry::revokeToken(&#36;token);
SessionRegistry::revoke(&#36;sessionId);
SessionRegistry::revokeUser(&#36;userId);</code></pre>
</section>
<section id="audit"><h2>7. Audit Log و Activity Analytics</h2>
<p class="new">Audit و Activity برای ثبت رخدادهای قابل‌ردیابی و تحلیل رفتار برنامه استفاده می‌شوند.</p>
<h3>Audit</h3><pre><code>use App\Audit\Audit;

Audit::record('book.created', [
    'book_id' =&gt; &#36;book-&gt;id,
    'source' =&gt; 'admin',
]);</code></pre>
<h3>Activity Tracker</h3><pre><code>use App\Analytics\ActivityTracker;

ActivityTracker::record(
    'book.viewed',
    ['book_id' =&gt; &#36;book-&gt;id],
    &#36;userId,
    'Book viewed'
);</code></pre>
<h3>Event / Metric / Segment Registry</h3><pre><code>EventRegistry::register('book.viewed', [
    'label' =&gt; 'Book Viewed',
    'category' =&gt; 'books',
]);

MetricRegistry::register('book_views', [
    'event' =&gt; 'book.viewed',
    'aggregation' =&gt; 'count',
]);

SegmentRegistry::register('readers', [
    'resolver' =&gt; &#36;resolver,
]);</code></pre>
</section>
<section id="security"><h2>8. Security Middleware و Authorization</h2>
<h3>CSRF</h3><p>درخواست‌های غیر GET/HEAD/OPTIONS که cookie-authenticated هستند باید توکن CSRF معتبر داشته باشند. APIهای مسیر <code>/api</code> از CSRF cookie flow مستثنا هستند و با Bearer Token احراز هویت می‌شوند.</p>
<pre><code>&lt;form method="POST"&gt;
    @csrf
    ...
&lt;/form&gt;</code></pre>
<h3>Security Headers</h3><p>Middleware امنیتی هدرهای استاندارد محافظتی را به پاسخ اضافه می‌کند.</p>
<h3>Authorization</h3><pre><code>Gate::define('books.update', function (&#36;user, &#36;book) {
    return &#36;user-&gt;id === &#36;book-&gt;owner_id;
});

Gate::allows('books.update', &#36;book);
Gate::authorize('books.update', &#36;book);</code></pre>
</section>
<section id="storage"><h2>9. Storage</h2>
<pre><code>&#36;path = Storage::put('avatars/user.jpg', &#36;contents);
&#36;exists = Storage::exists('avatars/user.jpg');
&#36;contents = Storage::get('avatars/user.jpg');
Storage::delete('avatars/user.jpg');</code></pre>
</section>
<section id="cache"><h2>10. Cache و Logging</h2>
<pre><code>&#36;value = Cache::get('key');
Cache::put('key', &#36;value, 3600);
&#36;value = Cache::remember('key', 3600, fn () =&gt; expensiveOperation());
Cache::forget('key');</code></pre>
<p>Logging از طریق <code>LogManager</code> انجام می‌شود و levelهای debug تا emergency را پشتیبانی می‌کند.</p>
<pre><code>LogManager::info('Book viewed', ['book_id' =&gt; &#36;bookId]);
LogManager::error('Payment failed', ['order_id' =&gt; &#36;orderId]);</code></pre>
</section>
<section id="localization"><h2>11. Localization</h2>
<pre><code>__('messages.welcome');
Translator::get('messages.welcome');</code></pre>
<p>Translator برای بارگذاری ترجمه‌ها و انتخاب locale فعال استفاده می‌شود.</p>
</section>
<section id="actions"><h2>12. Actions</h2>
<pre><code>class CreateBook extends BaseAction
{
    public function execute(array &#36;data = [])
    {
        if (!&#36;this-&gt;check()) {
            return &#36;this-&gt;unauthorized();
        }

        return &#36;this-&gt;success(['created' =&gt; true]);
    }
}</code></pre>
<p><code>BaseAction</code> دسترسی به کاربر جاری، Auth check، Validation و Response helpers را فراهم می‌کند.</p>
</section>
<section id="cli"><h2>13. CLI / Zeno</h2>
<pre><code>php zeno migrate
php zeno dev:seed
php zeno route:list</code></pre>
<p><code>dev:seed</code> برای محیط توسعه حساب‌های تست را ایجاد یا به‌روزرسانی می‌کند. تنظیمات محیطی از <code>.env</code> خوانده می‌شوند.</p>
</section>
<section id="errors"><h2>14. Error Handling و Debug</h2>
<p>در محیط توسعه، Handler اطلاعات Exception، فایل، خط، Source Snippet، Request Context و Stack Trace را نمایش می‌دهد. خروجی Debug قابلیت Copy دارد تا خطا مستقیماً برای گزارش یا بررسی ارسال شود.</p>
</section>
<section id="structure"><h2>15. ساختار پیشنهادی پروژه</h2>
<pre><code>app/
  Auth/
  Audit/
  Analytics/
  Actions/
  Core/
  Database/
  Http/
  Models/
resources/
  views/
routes/
storage/
public/
.env</code></pre>
<p class="note">برای پروژه‌های جدید، قابلیت‌های اختصاصی محصول را خارج از هسته Framework نگه دارید و از APIهای Cyron برای Routing، Auth، Database، Validation، Audit، Activity و سایر سرویس‌ها استفاده کنید.</p>
</section>
</main></body></html>
