<?php

namespace App;

class ViewEngine
{
    protected $sections = [];
    protected $pushes = [];
    protected $currentSection = null;
    protected $layout = null;
    
    /**
     * شروع یک بخش (section)
     */
    public function startSection($name)
    {
        $this->currentSection = $name;
        ob_start();
    }
    
    /**
     * پایان یک بخش
     */
    public function stopSection()
    {
        $content = ob_get_clean();
        if ($this->currentSection !== null) {
            $this->sections[$this->currentSection] = $content;
            $this->currentSection = null;
        }
    }
    
    /**
     * نمایش محتوای یک بخش
     */
    public function yieldContent($name, $default = '')
    {
        return $this->sections[$name] ?? $default;
    }
    
    /**
     * شروع یک استک (push)
     */
    public function startPush($name)
    {
        if (!isset($this->pushes[$name])) {
            $this->pushes[$name] = [];
        }
        $this->currentSection = $name;
        ob_start();
    }
    
    /**
     * پایان استک
     */
    public function stopPush()
    {
        $content = ob_get_clean();
        if ($this->currentSection !== null) {
            $this->pushes[$this->currentSection][] = $content;
            $this->currentSection = null;
        }
    }
    
    /**
     * نمایش محتوای استک
     */
    public function yieldPushContent($name)
    {
        return isset($this->pushes[$name]) ? implode('', $this->pushes[$name]) : '';
    }
    
    /**
     * تنظیم layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }
    
    /**
     * دریافت layout
     */
    public function getLayout()
    {
        return $this->layout;
    }
    
    /**
     * بررسی وجود layout
     */
    public function hasLayout()
    {
        return $this->layout !== null;
    }
    
    /**
     * رندر کردن view با layout
     */
    public function render($viewContent, $data = [])
    {
        extract($data);
        
        // اگر layout وجود دارد، ابتدا view رو اجرا میکنیم تا sections ذخیره بشن
        if ($this->hasLayout()) {
            // اجرای view برای ذخیره sections
            ob_start();
            eval('?>' . $viewContent);
            $viewOutput = ob_get_clean();
            
            // حالا layout رو اجرا میکنیم
            ob_start();
            include $this->getLayout();
            $layoutOutput = ob_get_clean();
            
            // جایگزینی yield ها با محتوای sections
            foreach ($this->sections as $name => $content) {
                $layoutOutput = str_replace("@yield('{$name}')", $content, $layoutOutput);
                $layoutOutput = str_replace('@yield("' . $name . '")', $content, $layoutOutput);
            }
            
            return $layoutOutput;
        }
        
        // بدون layout، مستقیم view رو نمایش بده
        ob_start();
        eval('?>' . $viewContent);
        return ob_get_clean();
    }
}