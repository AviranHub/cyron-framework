<?php

use App\Http\Controllers\AdminController;

class LadyToPHPConverter
{
    protected $viewsPath;
    protected $layoutsPath;
    protected $basePath;
    protected $basesPath;
    protected $newPath = false;

    public function __construct($basePath)
    {
        $this->basePath = $basePath;
        $this->basesPath = $basePath . '/app';
        $this->viewsPath = '/Views/';
        $this->layoutsPath = '/Layouts/';
    }

    public function convertView($viewName)
    {
        $ladyFile = $this->basePath . $this->viewsPath . $viewName . '.lady.php';
        $phpFile = $this->basesPath . $this->viewsPath . $viewName . '.lady.php';

        if (!file_exists($ladyFile)) {
            throw new Exception("Lady file does not exist: $ladyFile");
        }

        $ladyContent = file_get_contents($ladyFile);
        $phpContent = $this->convertLadyToPHP($ladyContent, $viewName, 'v');

        file_put_contents($phpFile, $phpContent);
        // echo "Converted $ladyFile to $phpFile\n";
    }

    public function convertLayout($layoutName)
    {
        $ladyFile = $this->basePath . $this->layoutsPath . $layoutName . '.lady.php';
        $phpFile = $this->basesPath . $this->layoutsPath . $layoutName . '.lady.php';

        if (!file_exists($ladyFile)) {
            throw new Exception("Lady file does not exist: $ladyFile");
        }

        $ladyContent = file_get_contents($ladyFile);
        $phpContent = $this->convertLadyToPHP($ladyContent, $layoutName, 'l');

        file_put_contents($phpFile, $phpContent);
        // echo "Converted $ladyFile to $phpFile\n";
    }

    protected function replaceRoute($matches)
    {
        $parameters = isset($matches[3]) ? $matches[3] : 'null';
        $routeResult = route($matches[1], eval("return $parameters;"));
        return $routeResult;
    }

    protected function convertLadyToPHP($ladyContent, $templateName, $type)
    {
        $layout = '';
        // تبدیل تگ‌های Blade
        $phpContent = preg_replace('/@php\s*(.*?)\s*@endphp/s', '<?php $1; ?>', $ladyContent);
        $phpContent = preg_replace('/@endphp/', '', $phpContent);
        $phpContent = preg_replace('/@errors/', '@isset($errors)
        @foreach($errors as $errs)
        @foreach($errs as $err)
        @foreach($err as $error)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">
                خطا!
            </strong>
            <span class="block sm:inline">
                {{ $error }}
            </span>
        </div>
        @endforeach
        @endforeach
        @endforeach
        @endisset', $phpContent);
        $phpContent = preg_replace('/@success/', '@isset($success)
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">
                موفق!
            </strong>
            <span class="block sm:inline">
                {{ $success }}
            </span>
        </div>
        @endisset', $phpContent);

        $phpContent = preg_replace_callback('/@var\(\s*\'(.+?)\'\s*\)/', function ($matches) {
            $varaible = vars($matches[1]);
            return "$varaible";
        }, $phpContent);
        $phpContent = preg_replace_callback('/@storage\(\s*\'(.+?)\'\s*\)/', function ($matches) {
            // بررسی اینکه آیا پروتکل HTTPS است یا خیر
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://";

            // دریافت دامنه
            $domain = $_SERVER['HTTP_HOST'];

            // ترکیب پروتکل و دامنه
            $url = $protocol . $domain . "/storage/public/$matches[1]";
            return $url;
        }, $phpContent);
        $phpContent = preg_replace_callback('/@asset\(\s*\'(.+?)\'\s*\)/', function ($matches) {
            // بررسی اینکه آیا پروتکل HTTPS است یا خیر
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://";

            // دریافت دامنه
            $domain = $_SERVER['HTTP_HOST'];

            // ترکیب پروتکل و دامنه
            $url = $protocol . $domain . "/assets/$matches[1]";
            return $url;
        }, $phpContent);
        $phpContent = preg_replace_callback('/@route\(\s*\'(.+?)\'\s*(?:,\s*([^)]*))?\)/', function ($matches) {
            // بررسی وجود پارامترها
            $parameters = isset($matches[2]) ? eval("return $matches[2];") : [];
            
            // تبدیل آرایه به رشته با حفظ متغیرها
            $params = [];
            foreach ($parameters as $key => $value) {
                // اگر مقدار یک متغیر است، آن را به صورت رشته‌ای با علامت دلار ($) اضافه می‌کنیم
                if (is_string($value) && strpos($value, '$') === 0) {
                    $params[] = "'$key' => $value";
                } else {
                    // در غیر این صورت، مقدار را به صورت معمولی اضافه می‌کنیم
                    $params[] = "'$key' => '" . addslashes($value) . "'";
                }
            }
            
            // ایجاد رشته نهایی
            $paramsString = implode(',', $params);
            
            // ساخت خط کد PHP
            if ($parameters) {
                $route = "<?php route('$matches[1]', [" . $paramsString . "]); ?>";
            } else {
                $route = "<?php route('$matches[1]'); ?>";
            }
        
            return $route; // بازگشت به URL نهایی
        }, $phpContent);


        $phpContent = preg_replace('/{{\s*(.+?)\s*}}/', '<?= $1; ?>', $phpContent);
        $phpContent = preg_replace('/@isset\(\s*(.*?)\s*\)/', '<?php if(isset($1)) { ?>', $phpContent);
        $phpContent = preg_replace('/@endisset/', '<?php } ?>', $phpContent);
        $phpContent = preg_replace('/@if\s*\(\s*(.*?)(\s*\(.*?\))?\s*\)\s*/', '<?php if ($1$2) { ?>', $phpContent);
        $phpContent = preg_replace('/@if\s*\(\s*(.*?)\s*\)\s*/', '<?php if ($1) { ?>', $phpContent);
        $phpContent = preg_replace('/@if\(\s*(.*?)\s*\)/', '<?php if ($1) { ?>', $phpContent);
        $phpContent = preg_replace('/@elseif\(\s*(.*?)\s*\)/', '<?php } elseif ($1) { ?>', $phpContent);
        $phpContent = preg_replace('/@else/', '<?php } else { ?>', $phpContent);
        $phpContent = preg_replace('/@endif/', '<?php } ?>', $phpContent);
        $phpContent = preg_replace('/@unless\(\s*(.*?)\s*\)/', '<?php if (!$1) { ?>', $phpContent);
        $phpContent = preg_replace('/@endunless/', '<?php } ?>', $phpContent);
        $phpContent = preg_replace('/@foreach\(\s*(.*?)\s*\)/', '<?php foreach ($1) { ?>', $phpContent);
        $phpContent = preg_replace('/@endforeach/', '<?php } ?>', $phpContent);
        $phpContent = preg_replace('/@for\(\s*(.*?)\s*\)/', '<?php for ($1) { ?>', $phpContent);
        $phpContent = preg_replace('/@endfor/', '<?php } ?>', $phpContent);
        $phpContent = preg_replace('/@while\(\s*(.*?)\s*\)/', '<?php while ($1) { ?>', $phpContent);
        $phpContent = preg_replace('/@endwhile/', '<?php } ?>', $phpContent);

        $phpContent = preg_replace('/@yield\(\s*\'(.+?)\'\s*\)/', '{{ section_$1 }}', $phpContent);
        $phpContent = $this->includeView($phpContent);
        //         $phpContent = preg_replace('/@section\(\s*\'(.+?)\'\s*\)/', '$template->section("$1",<<<EOD', $phpContent);
        //         $phpContent = preg_replace('/@endsection/', 'EOD);', $phpContent);
        //         $phpContent = preg_replace('/@extends\([\'"](.+?)[\'"]\)/', '<?php
        // $template = new \App\View();

        // // بارگذاری layout
        // $template->extend("$1");

        //         ', $phpContent);
        $phpContent = preg_replace_callback(
            '/@include\(\s*\'(.+?)\'\s*\)/',
            function ($matches) {
                $name = implode('/', explode('.', $matches[1])); // نام مسیر
                ob_start();
                include_once './resources/app/' . $name . '.lady.php';
                $page = ob_get_clean();
                return $page;
            },
            $phpContent
        );
        $phpContent = preg_replace('/@push\(\s*\'(.+?)\'\s*\)/', '<?php ob_start(); ?>', $phpContent);
        $phpContent = preg_replace('/@endpush/', '<?php $content = ob_get_clean(); ?>', $phpContent);
        $phpContent = preg_replace('/@stack\(\s*\'(.+?)\'\s*\)/', '<?= $content; ?>', $phpContent);
        $phpContent = preg_replace('/@lang\(\s*\'(.+?)\'\s*\)/', '<?= __("$1"); ?>', $phpContent);
        $phpContent = preg_replace('/@choice\(\s*\'(.+?)\',\s*(\d+)\s*\)/', '<?= __("$1", $2); ?>', $phpContent);
        $phpContent = preg_replace('/@json\(\s*(.*?)\s*\)/', '<?= json_encode($1); ?>', $phpContent);
        $phpContent = preg_replace('/@csrf/', '<?php echo csrf_token(); ?>', $phpContent);
        $phpContent = preg_replace('/@method\(\s*\'(.+?)\'\s*\)/', '<input type="hidden" name="_method" value="$1">', $phpContent);
        $phpContent = preg_replace('/@can\(\s*\'(.+?)\'\s*\)/', '<?php if (auth()->user()->can(\'$1\')) { ?>', $phpContent);
        $phpContent = preg_replace('/@cannot\(\s*\'(.+?)\'\s*\)/', '<?php if (!auth()->user()->can(\'$1\')) { ?>', $phpContent);
        $phpContent = preg_replace('/@hasSection\(\s*\'(.+?)\'\s*\)/', '<?php if (view()->hasSection(\'$1\')) { ?>', $phpContent);
        $phpContent = preg_replace('/@guest/', '<?php if (Auth::guest()) { ?>', $phpContent);
        $phpContent = preg_replace('/@auth/', '<?php if (Auth::check()) { ?>', $phpContent);
        $phpContent = preg_replace('/@env\(\s*\'(.+?)\'\s*\)/', '<?php if (app()->environment(\'$1\')) { ?>', $phpContent);
        // پایان تگ‌های شرطی و حلقه‌ها
        $phpContent = preg_replace('/@endcan/', '<?php } ?>', $phpContent);
        $phpContent = preg_replace('/@endguest/', '<?php } ?>', $phpContent);
        $phpContent = preg_replace('/@endauth/', '<?php } ?>', $phpContent);
        $phpContent = preg_replace('/@endhasSection/', '<?php } ?>', $phpContent);
        // if ($type == 'v') {
        //     $phpContent .= "\n\$template->render('$templateName');";
        // }
        return $phpContent;
    }

    public function convertAllFiles()
    {
        // اطمینان از وجود دایرکتوری خروجی
        if (!is_dir($this->basesPath)) {
            mkdir($this->basesPath, 0755, true); // ایجاد دایرکتوری در صورت عدم وجود
        }

        // شروع پردازش دایرکتوری‌ها
        $this->processDirectory($this->basePath . $this->viewsPath, $this->basesPath . $this->viewsPath, 'v');
        $this->processDirectory($this->basePath . $this->layoutsPath, $this->basesPath . $this->layoutsPath, 'l');
    }

    private function processDirectory($directory, $outputPath, $type)
    {
        $files = scandir($directory);

        foreach ($files as $file) {
            // نادیده گرفتن دایرکتوری‌های خاص
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filePath = $directory . '/' . $file;

            if (is_dir($filePath)) {
                // اگر دایرکتوری باشد، تابع را به صورت بازگشتی فراخوانی کنید
                $newOutputPath = $outputPath . '/' . $file; // مسیر جدید برای دایرکتوری خروجی
                if (!is_dir($newOutputPath)) {
                    mkdir($newOutputPath, 0755, true); // ایجاد دایرکتوری در صورت عدم وجود
                }
                $this->processDirectory($filePath, $newOutputPath, $type); // پردازش دایرکتوری فرعی
                continue;
            } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                // اگر فایل با پسوند lady.php باشد، آن را پردازش کنید
                $ladyContent = file_get_contents($filePath);
                $phpContent = $this->convertLadyToPHP($ladyContent, pathinfo($file, PATHINFO_FILENAME), $type);

                // ذخیره فایل PHP در مسیر جدید
                $outputFilePath = $outputPath . '/' . pathinfo($file, PATHINFO_FILENAME) . '.php';
                // echo "<br>Processing file: $outputFilePath\n";

                file_put_contents($outputFilePath, $phpContent);
            }
        }
    }
    public function  includeView($ladyContent)
    {
        // الگوی منظم برای شناسایی @section و @endsection
        $pattern = '/@section\(\s*\'(.+?)\'\s*\)(.*?)@endsection/s';
        // استفاده از preg_match_all برای استخراج نام بخش و محتوای آن
        preg_match_all($pattern, $ladyContent, $matches, PREG_SET_ORDER);
        // آرایه برای ذخیره نتایج
        $sections = [];
        foreach ($matches as $match) {
            $sectionName = $match[1]; // نام بخش
            $sectionContent = $match[2]; // محتوای بخش
            // ذخیره نام و محتوای بخش در آرایه
            $sections[$sectionName] = $sectionContent;
        }
        // الگوی منظم برای شناسایی @extends
        $layoutPattern = '/@extends\([\'"](.+?)[\'"]\)/';

        // استفاده از preg_match برای استخراج نام لایه
        if (preg_match($layoutPattern, $ladyContent, $layoutMatch)) {
            $layoutName = implode('/', explode('.', $layoutMatch[1])); // نام لایه
            // echo "Layout Name: $layoutName\n";


            // // الگوی منظم برای جایگزینی @section
            // $sectionPattern = '/@section\([\'"](.+?)[\'"]\)(.*?)@endsection/s';

            // // استفاده از preg_replace_callback برای جایگزینی @section
            // $ladyContent = preg_replace_callback($sectionPattern, function ($match) {
            //     return "{{ $match[1] }}";
            // }, $ladyContent);


            // فرض کنید $sections آرایه‌ای است که قبلاً بخش‌ها را استخراج کرده‌ایم
            // $sections = []; // این آرایه باید شامل بخش‌ها باشد

            // بارگذاری محتوای لایه
            $layoutFilePath = './resources/app/' . $layoutName . '.lady.php'; // مسیر فایل لایه
            if (file_exists($layoutFilePath)) {
                $layoutContent = file_get_contents($layoutFilePath);

                // جایگزینی بخش‌ها در محتوای لایه
                foreach ($sections as $name => $content) {
                    $layoutContent = str_replace("{{ section_$name }}", $content, $layoutContent);
                }

                // // الگوی منظم برای حذف @extends
                // $extendsPattern = '/@extends\([\'"](.+?)[\'"]\)/';

                // // استفاده از preg_replace برای حذف @extends
                // $ladyContent = preg_replace($extendsPattern, '', $ladyContent);
                // نمایش محتوای نهایی لایه

                $pattern = '/\{\{\s*section_(\w+)\s*\}\}/';

                // استفاده از preg_replace برای حذف متن
                $cleanedContent = preg_replace($pattern, '', $layoutContent);
                return $cleanedContent;
            } else {
                echo "Layout file does not exist: $layoutFilePath\n";
            }
        } else {
            return $ladyContent;
        }
    }
}


// $template = new \App\View();

// // بارگذاری layout
// $template->extend('./resources/views/layouts/main.php');

// // تعریف بخش‌ها
// $template->section('header', '<h1>Header Section</h1>');
// $template->section('content', '<p>This is the main content.</p>');

// // بارگذاری نمای مورد نظر
// $template->render('./resources/views/home.php');