<?php

namespace Cyron\Lady;

use Cyron\Plugin\HookManager;

class ComponentManager
{
    protected static $instance;
    protected $data;
    protected $components = [];
    protected $current = null;
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

    public function register(string $name, string $viewPath, ?string $extends = null)
    {
        $this->components[$name] = $viewPath;
        if ($extends) {
            $this->inherits[$name] = $extends;
        }
    }

    public function start(string $name, array $data = [], array $allAttributes = [])
    {
        error_log("Component start: name={$name}, data=" . print_r($data, true));
        $this->current = $name;
        $this->data = $data;
        $this->attributes = $allAttributes;
        $this->slots = [];
        ob_start();
    }

    public function slot(string $name)
    {
        $this->slotStack[] = $name;
        ob_start();
    }

    public function endSlot()
    {
        $name = array_pop($this->slotStack);
        $this->slots[$name] = ob_get_clean();
    }

    public function end()
    {
        $slotContent = ob_get_clean();
        $this->slots['slot'] = $slotContent;

        $view = $this->getViewPath($this->current);
        $data = array_merge($this->data, $this->slots);
        $data['attributes'] = new AttributeBag($this->attributes);

        HookManager::trigger("component.before.{$this->current}", $data);

        $output = view($view, $data);
        $output = HookManager::first("component.after.{$this->current}", $output, $data) ?? $output;

        if (isset($this->inherits[$this->current])) {
            $parent = $this->inherits[$this->current];
            $output = $this->wrapWithParent($parent, $output, $data);
        }

        return $output;
    }

    public function render(string $name, array $data, array $allAttributes, callable $slotCallback)
    {
        $this->start($name, $data, $allAttributes);
        echo $slotCallback();
        return $this->end();
    }

    protected function wrapWithParent(string $parentName, string $childContent, array $data)
    {
        $parentView = $this->getViewPath($parentName);
        $data['slot'] = $childContent;
        return view($parentView, $data);
    }

    protected function getViewPath(string $alias): string
    {
        return $this->components[$alias] ?? "components.{$alias}";
    }
}
