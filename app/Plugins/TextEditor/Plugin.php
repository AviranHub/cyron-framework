<?php
namespace Plugins\TextEditor;

use App\Core\Plugin\Plugin as BasePlugin;

class Plugin extends BasePlugin
{
    protected function registerHooks(): void
    {
        // مثال: ثبت یک هوک برای رندر کردن ویو
        // $this->listen('texteditor.render', function($data = []) {
        //     return $this->view('index', $data);
        // });
    }
}