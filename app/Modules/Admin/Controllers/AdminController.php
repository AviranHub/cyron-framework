<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controller;
use App\Request;
use App\Database\Model;

class AdminController extends Controller
{
    protected $model;
    protected $config;
    protected $modelKey;

    public function __construct($modelKey)
    {
        $this->modelKey = $modelKey;
        $configs = require __DIR__ . '/../config/admin_models.php';
        if (!isset($configs[$modelKey])) {
            abort(404, "Model not configured");
        }
        $this->config = $configs[$modelKey];
        $this->model = $this->config['model'];
    }

    public function dashboard()
    {
        $configs = require __DIR__ . '/../config/admin_models.php';
        $stats = ['users' => 0, 'today' => 0, 'activities' => 0];
        if (isset($configs['users']['model'])) {
            $model = $configs['users']['model'];
            $stats['users'] = $model::query()->count();
        }
        return view('admin.dashboard', ['stats' => $stats, 'adminModules' => $configs]);
    }

    public function index()
    {
        $query = $this->model::query();
        // جستجو
        if ($search = request()->input('search')) {
            $searchable = $this->config['searchable'] ?? [];
            $query->where(function($q) use ($search, $searchable) {
                foreach ($searchable as $field) {
                    $q->orWhere($field, 'LIKE', "%{$search}%");
                }
            });
        }
        if ($this->modelKey === 'users' && ($status = request()->input('status'))) {
            if (in_array($status, ['active', 'inactive', 'suspended'], true)) $query->where('status', '=', $status);
        }
        $items = $query->paginate(20);
        return view('admin.index', [
            'items'   => $items,
            'config'  => $this->config,
            'modelKey'=> $this->modelKey,
        ]);
    }

    public function create()
    {
        return view('admin.create', [
            'config'  => $this->config,
            'modelKey'=> $this->modelKey,
        ]);
    }

    public function store(Request $request)
    {
        $rules = $this->buildValidationRules();
        $errors = $request->validate($rules);
        if ($errors && $errors->any()) {
            return redirect()->back()->withErrors($errors)->withInput();
        }
        $data = $this->filterAllowedInput($request->all());
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
        $this->model::create($data);
        return redirect()->route("admin.{$this->modelKey}.index")->with('success', 'ایجاد شد');
    }

    public function edit($id)
    {
        $item = $this->model::find($id);
        if (!$item) abort(404);
        return view('admin.edit', [
            'item'    => $item,
            'config'  => $this->config,
            'modelKey'=> $this->modelKey,
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
        $data = $this->filterAllowedInput($request->all());
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
        $item->update($data);
        return redirect()->route("admin.{$this->modelKey}.index")->with('success', 'به‌روز شد');
    }

    public function changeUserStatus($id, string $status)
    {
        if ($this->modelKey !== 'users') abort(404);
        if (!in_array($status, ['active', 'inactive', 'suspended'], true)) abort(422);
        $user = $this->model::find($id);
        if (!$user) abort(404);
        $user->update(['status' => $status]);
        return redirect()->back()->with('success', 'وضعیت کاربر به‌روزرسانی شد');
    }

    public function destroy($id)
    {
        $item = $this->model::find($id);
        if ($item) $item->delete();
        return redirect()->back()->with('success', 'حذف شد');
    }

    protected function filterAllowedInput(array $data): array
    {
        $allowed = array_keys($this->config['form'] ?? []);
        return array_intersect_key($data, array_flip($allowed));
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