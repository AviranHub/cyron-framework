<?php

namespace App\Core\Lady;

use App\Core\Plugin\HookManager;

class ComponentManager
{
    protected static $instance;
    protected $data;
    protected $components = [];      // ثبت‌های دستی (اختیاری)
    protected $current = null;       // نام کامپوننت در حال اجرا
    protected $slots = [];
    protected $slotStack = [];
    protected $inherits = [];
    protected $attributes = [];

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * ثبت دستی یک کامپوننت (اختیاری)
     * @param string $name      نام کامپوننت (مثلاً alert)
     * @param string $viewPath  مسیر ویو (مثلاً components.alert)
     * @param string|null $extends کامپوننت والد (برای وراثت)
     */
    public function register(string $name, string $viewPath, ?string $extends = null)
    {
        $this->components[$name] = $viewPath;
        if ($extends) {
            $this->inherits[$name] = $extends;
        }
    }

    /**
     * شروع رندر کامپوننت (برای @component)
     * @param string $name
     * @param array $data
     */
    public function start(string $name, array $data = [], array $allAttributes = [])
    {
        error_log("Component start: name={$name}, data=" . print_r($data, true));
        $this->current = $name;
        $this->data = $data;
        $this->attributes = $allAttributes;
        $this->slots = [];
        ob_start();
    }

    /**
     * شروع یک اسلات نام‌دار (برای @slot)
     * @param string $name
     */
    public function slot(string $name)
    {
        $this->slotStack[] = $name;
        ob_start();
    }

    /**
     * پایان اسلات نام‌دار
     */
    public function endSlot()
    {
        $name = array_pop($this->slotStack);
        $this->slots[$name] = ob_get_clean();
    }

    /**
     * پایان رندر کامپوننت و تولید خروجی نهایی
     * @return string
     */
    public function end()
    {
        // محتوای اسلات اصلی (بین تگ‌ها)
        $slotContent = ob_get_clean();
        $this->slots['slot'] = $slotContent;

        // مسیر ویو کامپوننت (اولویت با ثبت دستی، در غیر این صورت components.{name})
        $view = $this->getViewPath($this->current);
        $data = array_merge($this->data, $this->slots);

        // اضافه کردن شیء attributes به ویو
        $data['attributes'] = new AttributeBag($this->attributes);

        // هوک قبل از رندر
        HookManager::trigger("component.before.{$this->current}", $data);

        // رندر ویو
        $output = view($view, $data);

        // هوک بعد از رندر (امکان تغییر نهایی)
        $output = HookManager::first("component.after.{$this->current}", $output, $data) ?? $output;

        // اگر وراثت تعریف شده باشد، خروجی را در قالب والد قرار بده
        if (isset($this->inherits[$this->current])) {
            $parent = $this->inherits[$this->current];
            $output = $this->wrapWithParent($parent, $output, $data);
        }

        return $output;
    }

    /**
     * رندر مستقیم برای تگ‌های <x-tag>
     * @param string $name
     * @param array $data
     * @param callable $slotCallback
     * @return string
     */
    public function render(string $name, array $data, array $allAttributes, callable $slotCallback)
    {
        $this->start($name, $data, $allAttributes);
        echo $slotCallback();
        return $this->end();
    }

    /**
     * پیچیدن خروجی در کامپوننت والد (وراثت)
     * @param string $parentName
     * @param string $childContent
     * @param array $data
     * @return string
     */
    protected function wrapWithParent(string $parentName, string $childContent, array $data)
    {
        $parentView = $this->getViewPath($parentName);
        $data['slot'] = $childContent;
        return view($parentView, $data);
    }

    /**
     * دریافت مسیر ویو کامپوننت (اولویت با ثبت دستی، سپس fallback به components.{name})
     * @param string $alias
     * @return string
     */
    protected function getViewPath(string $alias): string
    {
        return $this->components[$alias] ?? "components.{$alias}";
    }
}
