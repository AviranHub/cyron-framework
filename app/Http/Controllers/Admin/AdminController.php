<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controller;
use App\Request;
use App\Audit\Audit;

class AdminController extends Controller
{
    protected $model;
    protected $config;
    protected $modelKey;

    public function __construct($modelKey = null)
    {

        if ($modelKey !== null) {
            // error_log("////////////////////////////// : {$modelKey}");
            $this->modelKey = $modelKey;
            $configs = require APP_PATH . '/Config/admin.php';
            if (!isset($configs[$modelKey])) {
                // abort(404, "Model configuration not found.");
                // error_log("////////////////////////////// false : {$modelKey}");
            }
            $this->config = $configs[$modelKey];
            $this->model = $this->config['model'];
        }
        // اگر $modelKey === null باشد، یعنی داشبورد (فعلاً نیازی به تنظیمات ندارد)
    }

    public function dashboard()
    {
        $configs = require APP_PATH . '/Config/admin.php';
        $stats = [];

        foreach ($configs as $key => $config) {
            // اگر مدلی وجود نداشته باشد (مثل بخش settings) رد کن
            if (!isset($config['model']) || !$config['model'] || !class_exists($config['model'])) {
                continue;
            }

            $modelClass = $config['model'];
            $stats[$key] = [
                'label' => $config['label'],
                'icon' => $config['icon'] ?? $this->getDefaultIcon($key),
                'count' => $modelClass::count(),
                'color' => $this->getCardColor($key),
                'route' => route("admin.{$key}.index"),
            ];
        }

        // آمارهای ویژه (اختیاری)
        $stats['recent_users'] = [
            'label' => 'کاربران جدید (هفته اخیر)',
            'icon' => 'user-plus',
            'count' => \App\Models\User::query()->where('created_at', '>=', date('Y-m-d', strtotime('-7 days')))->count(),
            'color' => 'green',
        ];

        $recentUsers = \App\Models\User::query()->orderBy('created_at', 'desc')->limit(6)->get();
        $recentActivities = \App\Models\UserActivity::query()->orderBy('created_at', 'desc')->limit(6)->get();
        $todayActivities = \App\Models\UserActivity::query()->where('created_at', '>=', date('Y-m-d') . ' 00:00:00')->count();

        // $stats['today_orders'] = [
        //     'label' => 'سفارشات امروز',
        //     'icon' => 'shopping-cart',
        //     'count' => \App\Models\Order::whereDate('created_at', date('Y-m-d'))->count() ?? 0,
        //     'color' => 'blue',
        // ];

        return view('admin.dashboard', [
            'stats' => $stats,
            'adminModules' => $configs,
            'recentUsers' => $recentUsers,
            'recentActivities' => $recentActivities,
            'todayActivities' => $todayActivities,
            'total_models' => count($stats),
        ]);
    }

    protected function getDefaultIcon($key)
    {
        $icons = [
            'users' => 'users',
            'roles' => 'shield',
            'permissions' => 'key',
            'articles' => 'file-text',
            'categories' => 'folder',
            'comments' => 'message-circle',
            'tags' => 'tag',
            'books' => 'book',
            'book_categories' => 'folder-open',
            'orders' => 'shopping-cart',
            'transactions' => 'credit-card',
        ];
        return $icons[$key] ?? 'box';
    }

    protected function getCardColor($key)
    {
        $colors = [
            'users' => 'indigo',
            'roles' => 'purple',
            'permissions' => 'pink',
            'articles' => 'blue',
            'categories' => 'green',
            'comments' => 'yellow',
            'tags' => 'orange',
            'books' => 'red',
            'book_categories' => 'teal',
            'orders' => 'cyan',
            'transactions' => 'gray',
        ];
        return $colors[$key] ?? 'gray';
    }

    public function index()
    {
        // error_log(" ((((((((((((((((((((((((((( {$this->modelKey}");
        $query = $this->model::query();
        // جستجو
        if ($search = request()->input('search')) {
            $searchable = $this->config['searchable'] ?? [];
            $query->where(function ($q) use ($search, $searchable) {
                foreach ($searchable as $field) {
                    $q->orWhere($field, 'LIKE', "%{$search}%");
                }
            });
        }
        $items = $query->paginate(20);
        return view('admin.index', [
            'items'   => $items,
            'config'  => $this->config,
            'modelKey' => $this->modelKey,
        ]);
    }

    public function create()
    {
        return view('admin.create', [
            'config'  => $this->config,
            'modelKey' => $this->modelKey,
        ]);
    }

    public function store(Request $request)
    {
        $rules = $this->buildValidationRules();
        $errors = $request->validate($rules);
        if ($errors && $errors->any()) {
            return redirect()->back()->withErrors($errors)->withInput();
        }
        $data = $request->all();
        // پردازش فایل‌ها
        foreach ($this->config['form'] as $field => $def) {
            if (strpos($def, 'file') !== false && $request->hasFile($field)) {
                $data[$field] = storage()->upload($request->file($field), $this->modelKey);
            }
        }
        // حذف فیلدهای اضافی مثل password_confirmation
        if (isset($data['password_confirmation'])) unset($data['password_confirmation']);
        // هش کردن پسورد
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $created = $this->model::create($data);
        Audit::record('admin.model.created', ['model_key'=>$this->modelKey,'model_class'=>$this->model,'target_id'=>$created->id ?? null,'after'=>$data]);
        return redirect()->route("admin.{$this->modelKey}.index")->with('success', 'ایجاد شد');
    }

    public function edit($id)
    {
        $item = $this->model::find($id);
        if (!$item) abort(404);
        return view('admin.edit', [
            'item'    => $item,
            'config'  => $this->config,
            'modelKey' => $this->modelKey,
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = $this->model::find($id);
        if (!$item) abort(404);
        $rules = $this->buildValidationRules($id);
        $errors = $request->validate($rules);
        if ($errors && $errors->any()) {
            return redirect()->back()->withErrors($errors)->withInput();
        }
        $data = $request->all();
        foreach ($this->config['form'] as $field => $def) {
            if (strpos($def, 'file') !== false && $request->hasFile($field)) {
                // حذف فایل قبلی
                if ($item->{$field}) storage()->delete($item->{$field});
                $data[$field] = storage()->upload($request->file($field), $this->modelKey);
            }
        }
        if (isset($data['password'])) {
            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
        }
        unset($data['password_confirmation']);
        $before = $item->toArray();
        $item->update($data);
        Audit::record('admin.model.updated', ['model_key'=>$this->modelKey,'model_class'=>$this->model,'target_id'=>$item->id ?? $id,'before'=>$before,'after'=>$data]);
        return redirect()->route("admin.{$this->modelKey}.index")->with('success', 'به‌روز شد');
    }

    public function destroy($id)
    {
        $item = $this->model::find($id);
        if ($item) { $before = $item->toArray(); $item->delete(); Audit::record('admin.model.deleted', ['model_key'=>$this->modelKey,'model_class'=>$this->model,'target_id'=>$id,'before'=>$before]); }
        return redirect()->back()->with('success', 'حذف شد');
    }

    protected function buildValidationRules($id = null)
    {
        $rules = [];
        foreach ($this->config['form'] as $field => $def) {
            $parts = explode('|', $def);
            $type = array_shift($parts);
            $ruleStr = '';
            if (in_array('required', $parts)) $ruleStr .= 'required|';
            if ($type === 'email') $ruleStr .= 'email|';
            if ($type === 'number') $ruleStr .= 'integer|';
            if ($type === 'textarea' || $type === 'text') $ruleStr .= 'string|';
            if (in_array('confirmed', $parts, true)) $ruleStr .= 'confirmed|';
            if (in_array('unique', $parts)) {
                $ruleStr .= "unique:{$this->model::getTable()},{$field}";
                if ($id) $ruleStr .= ",{$id}";
                $ruleStr .= '|';
            }
            $rules[$field] = rtrim($ruleStr, '|');
        }
        return $rules;
    }
}
