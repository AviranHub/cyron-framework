<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cyron Framework Documentation</title>
<style>
:root{color-scheme:dark;--bg:#0d1016;--panel:#151a23;--text:#edf1f7;--muted:#9da8b8;--border:#293140;--accent:#83a5ff;--good:#75d6ad}
*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--text);font:15px/1.75 system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}main{width:min(1120px,94%);margin:32px auto 80px}header,section{background:var(--panel);border:1px solid var(--border);border-radius:16px;padding:26px;margin-bottom:18px}h1,h2,h3{line-height:1.3}h1{margin:0 0 8px;font-size:38px}h2{margin-top:0;color:var(--accent)}h3{margin-bottom:7px}p,li{color:var(--muted)}code{background:#090c11;border:1px solid var(--border);border-radius:6px;padding:2px 6px;color:#dce5ff}pre{overflow:auto;background:#090c11;border:1px solid var(--border);border-radius:10px;padding:16px;color:#dce5ff}a{color:var(--accent)}table{width:100%;border-collapse:collapse}td,th{border-bottom:1px solid var(--border);padding:9px;text-align:left;color:var(--muted)}th{color:var(--text)}.badge{display:inline-block;border:1px solid var(--border);border-radius:999px;padding:3px 10px;color:var(--muted);margin:3px}.note{border-left:3px solid var(--accent);padding-left:14px}.new{color:var(--good)}
</style>
</head>
<body><main>
<header>
<h1>Cyron Framework</h1>
<p>مستندات قابلیت‌های فعلی Cyron؛ شامل هسته HTTP، Template Engine، Database، Authentication، Security، Audit و Analytics.</p>
<span class="badge">Routing</span><span class="badge">Request</span><span class="badge">Lady</span><span class="badge">ORM</span><span class="badge">Validation</span><span class="badge">Auth</span><span class="badge">2FA</span><span class="badge">Sessions</span><span class="badge">Audit</span><span class="badge">Analytics</span><span class="badge">Security</span><span class="badge">Storage</span><span class="badge">Cache</span><span class="badge">CLI</span>
</header>
<section><h2>فهرست</h2><p><a href="#routing">Routing</a> · <a href="#request">Request</a> · <a href="#lady">Lady</a> · <a href="#db">Database</a> · <a href="#validation">Validation</a> · <a href="#auth">Authentication</a> · <a href="#audit">Audit & Activity</a> · <a href="#security">Security</a> · <a href="#storage">Storage</a> · <a href="#localization">Localization</a> · <a href="#actions">Actions</a> · <a href="#cli">CLI</a> · <a href="#errors">Errors</a></p></section>
<section id="routing"><h2>1. Routing</h2><p>روت‌ها با <code>App\Route</code> تعریف می‌شوند و از HTTP methods، controller، name، middleware، prefix و group پشتیبانی می‌کنند.</p><pre><code>use App\Route;

Route::get('/users', [UserController::class, 'index'])-&gt;name('users');
Route::post('/users', [UserController::class, 'store']);

Route::middleware(AuthMiddleware::class)-&gt;prefix('dashboard')-&gt;group(function () {
    Route::get('/', [DashboardController::class, 'index']);
});</code></pre><p>در پروژه می‌توانید route فایل‌های جداگانه را با <code>require_once</code> بارگذاری کنید.</p></section>
<section id="request"><h2>2. Request</h2><p>کلاس Request داده‌های POST/GET، JSON، فایل‌ها، method و اطلاعات route را در اختیار controller قرار می‌دهد.</p><pre><code>use App\Request;

$request-&gt;input('name');
$request-&gt;query('page', 1);
$request-&gt;file('avatar');
$request-&gt;hasFile('avatar');
$request-&gt;method();
$request-&gt;isMethod('POST');
$request-&gt;routeIs('users.*');
$request-&gt;bearerToken();</code></pre><p>درخواست‌های <code>application/json</code> نیز به‌صورت خودکار parse می‌شوند.</p></section>
<section id="lady"><h2>3. Lady Template Engine</h2><p>Lady موتور Template داخلی Cyron است و Layout، Section، Include، Component، Stack، شرط، حلقه و خروجی Escape شده/خام را پشتیبانی می‌کند.</p><pre><code>&#64;extends('layouts.master')

&#64;section('content')
    &#64;if(&#36;users)
        &#64;foreach(&#36;users as &#36;user)
            &amp;lt;h3&amp;gt;&#123;&#123; &#36;user-&amp;gt;name &#125;&#125;&amp;lt;/h3&amp;gt;
        &#64;endforeach
    &#64;endif
&#64;endsection</code></pre><p>توجه: علامت‌های <code>&#64;</code>، <code>&#36;</code> و <code>&#123;&#125;</code> در مثال بالا عمداً HTML-encoded هستند تا خود Documentation توسط Lady اجرا نشود؛ مرورگر آن‌ها را به شکل کد عادی نمایش می‌دهد.</p><table><tr><th>Syntax</th><th>کاربرد</th></tr><tr><td><code>&#64;extends</code></td><td>Layout inheritance</td></tr><tr><td><code>&#64;section</code></td><td>تعریف بخش</td></tr><tr><td><code>&#64;yield</code></td><td>نمایش بخش</td></tr><tr><td><code>&#64;include</code></td><td>درج View دیگر</td></tr><tr><td><code>&#64;if / &#64;foreach</code></td><td>شرط و حلقه</td></tr><tr><td><code>&#64;csrf</code></td><td>توکن CSRF</td></tr><tr><td><code>&#64;error / &#64;errors</code></td><td>نمایش خطا</td></tr><tr><td><code>&#123;&#123; ... &#125;&#125;</code></td><td>خروجی Escape شده</td></tr><tr><td><code>&#123;&#33;&#33; ... &#33;&#33;&#125;</code></td><td>خروجی خام</td></tr></table></section>
<section id="db"><h2>4. Database / Model / Query Builder</h2><p>مدل‌ها از <code>App\Database\Model</code> استفاده می‌کنند. برای مدل‌های پروژه جدول را با <code>protected static $table</code> مشخص کنید.</p><pre><code>class User extends Model
&#123;
    protected static &#36;table = 'users';
    protected static array &#36;fillable = ['name', 'email', 'password'];
&#125;

&#36;users = User::where('status', 'active')
    -&gt;orderBy('created_at', 'desc')
    -&gt;limit(20)
    -&gt;get();

&#36;user = User::where('email', &#36;email)-&gt;first();
&#36;count = User::where('status', 'active')-&gt;count();</code></pre><p>Builder شامل <code>select</code>، <code>where</code>، <code>orWhere</code>، <code>whereIn</code>، <code>whereNull</code>، <code>whereNotNull</code>، <code>orderBy</code>، <code>limit</code>، <code>offset</code>، <code>get</code>، <code>first</code>، <code>count</code>، <code>insert</code>، <code>update</code>، <code>delete</code> و <code>paginate</code> است.</p><h3>Relations / Eager Loading</h3><pre><code>&#36;books = Book::with('author')-&gt;get();

&#36;categories = BookCategory::with([
    'books' =&gt; function (&#36;query) &#123;
        &#36;query-&gt;take(8);
    &#125;
])-&gt;get();</code></pre><h3>Pagination</h3><pre><code>&#36;books = Book::paginate(15, 'page', 2);</code></pre></section>
<section id="validation"><h2>5. Validation</h2><pre><code>&#36;validated = &#36;request-&gt;validate([
    'name' =&gt; 'required|string|min:3',
    'email' =&gt; 'required|email|unique:users,email',
    'password' =&gt; 'required|min:6|confirmed',
]);</code></pre><p>قواعد شامل <code>required</code>، <code>email</code>، <code>string</code>، <code>integer</code>، <code>boolean</code>، <code>url</code>، <code>date</code>، <code>min</code>، <code>max</code>، <code>confirmed</code>، <code>in</code>، <code>not_in</code>، <code>unique</code>، <code>exists</code>، <code>required_if</code>، <code>required_with</code>، <code>different</code>، <code>same</code>، <code>regex</code> و <code>prohibited</code> است.</p></section>
<section id="auth"><h2>6. Authentication / Login Protection / Sessions / 2FA</h2><p class="new">سیستم Authentication جدید Cyron شامل ورود، خروج، محافظت از Login، Session Registry و Two-Factor Authentication است.</p><pre><code>use App\Core\Authentication\Auth;

if (Auth::check()) &#123;
    &#36;user = Auth::user();
&#125;

Auth::login(&#36;user);
Auth::logout();</code></pre><h3>Two-Factor</h3><pre><code>TwoFactor::enable(&#36;userId, 'sms', &#36;target);
TwoFactor::disable(&#36;userId);
TwoFactor::enabled(&#36;userId);
TwoFactor::challenge(&#36;userId);
TwoFactor::verify(&#36;userId, &#36;channel, &#36;code);</code></pre><h3>Session Registry</h3><pre><code>SessionRegistry::register(&#36;userId, &#36;token, &#36;meta);
SessionRegistry::active(&#36;token);
SessionRegistry::touch(&#36;token);
SessionRegistry::revokeToken(&#36;token);
SessionRegistry::revoke(&#36;sessionId);
SessionRegistry::revokeUser(&#36;userId);</code></pre></section>
<section id="audit"><h2>7. Audit Log & Activity Analytics</h2><p class="new">Audit و Activity برای ثبت رخدادهای قابل ردیابی و تحلیل رفتار برنامه در نظر گرفته شده‌اند.</p><h3>Audit</h3><pre><code>use App\Audit\Audit;

Audit::record('book.created', [
    'book_id' =&gt; &#36;book-&gt;id,
    'source' =&gt; 'admin',
]);</code></pre><h3>Activity</h3><pre><code>use App\Analytics\ActivityTracker;

ActivityTracker::record(
    'book.viewed',
    ['book_id' =&gt; &#36;book-&gt;id],
    &#36;userId,
    'Book viewed'
);</code></pre><h3>Registries</h3><pre><code>EventRegistry::register('book.viewed', [
    'label' =&gt; 'Book Viewed',
    'category' =&gt; 'books',
]);

MetricRegistry::register('book_views');
SegmentRegistry::register('readers');</code></pre></section>
<section id="security"><h2>8. Security</h2><p>Cyron به‌صورت پیش‌فرض Middlewareهای امنیتی HTTP و CSRF را در Bootstrap فعال می‌کند.</p><ul><li>CSRF protection برای درخواست‌های state-changing</li><li>Security headers</li><li>Session cookies با HttpOnly و SameSite</li><li>Session strict mode و cookie-only sessions</li><li>Rate limiting در Login Protection</li><li>SQL identifier/operator guarding در Query Builder</li><li>Authorization با Gate</li></ul><h3>Authorization</h3><pre><code>Gate::allows('edit-book', &#36;book);
Gate::authorize('edit-book', &#36;book);</code></pre></section>
<section id="storage"><h2>9. Storage</h2><p>StorageManager برای مدیریت فایل‌ها و URL فایل‌های ذخیره‌شده استفاده می‌شود و از طریق helper/template directive نیز قابل دسترسی است.</p><pre><code>Storage::put('avatars/user.jpg', &#36;contents);
Storage::url('avatars/user.jpg');</code></pre><p>در Template نیز می‌توان از <code>&#64;storage('path')</code> و <code>&#64;asset('path')</code> استفاده کرد.</p></section>
<section id="localization"><h2>10. Localization</h2><p>Translator داخلی Cyron برای ترجمه و pluralization در دسترس است.</p><pre><code>__('messages.welcome');
trans_choice('messages.books', &#36;count);</code></pre><p>در Lady نیز <code>&#64;lang</code> و <code>&#64;choice</code> پشتیبانی می‌شوند.</p></section>
<section id="actions"><h2>11. Actions / Components</h2><p>ساختار Action و Component برای جدا کردن منطق قابل استفاده مجدد و Template components در نظر گرفته شده است.</p><pre><code>class CreateBook extends BaseAction
&#123;
    public function handle(array &#36;data)
    &#123;
        // business logic
    &#125;
&#125;</code></pre></section>
<section id="cli"><h2>12. CLI / Zeno</h2><p><code>zeno</code> ابزار CLI پروژه است. در محیط development می‌توان دیتابیس و داده‌های آزمایشی را آماده کرد.</p><pre><code>php zeno migrate
php zeno dev:seed</code></pre><p><code>dev:seed</code> فقط در <code>APP_ENV=development</code> مجاز است و credentialهای آن باید از متغیرهای محیطی خوانده شوند.</p></section>
<section id="errors"><h2>13. Error Handler / Debugging</h2><p>Handler در حالت debug اطلاعات Exception، request context، source snippet و stack trace را نمایش می‌دهد و امکان Copy کردن گزارش خطا را فراهم می‌کند. اطلاعات حساس مانند password، cookie و headerهای محرمانه نباید در گزارش قرار بگیرند.</p></section>
<section><h2>14. Environment</h2><p>تنظیمات محیطی از <code>.env</code> خوانده می‌شوند. فایل <code>.env.example</code> برای شروع پروژه ارائه می‌شود و فایل واقعی <code>.env</code> نباید commit شود.</p><pre><code>APP_ENV=development
APP_DEBUG=true
DB_HOST=127.0.0.1</code></pre></section>
<section><h2>15. Project Structure</h2><pre><code>app/
├── Auth/
├── Analytics/
├── Audit/
├── Core/
├── Database/
├── Http/
├── Localization/
└── Models/

resources/views/
routes/
storage/
zeno</code></pre></section>
<section><h2>یادداشت</h2><p class="note">این صفحه باید همراه با تغییرات API هسته به‌روزرسانی شود. مثال‌های کد داخل Documentation عمداً طوری نوشته شده‌اند که توسط Lady به‌عنوان Template واقعی تفسیر نشوند.</p></section>
</main></body></html>
