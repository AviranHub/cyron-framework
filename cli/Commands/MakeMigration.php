<?php
require_once __DIR__ . '/../Colors.php';

class MakeMigration
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
        Colors::enable();
    }

    public static function getDescription()
    {
        return "Create a new migration file (use --old for legacy array style)";
    }

    public function execute()
    {
        $name = $this->input->getArgument(1);
        if (!$name) {
            $name = $this->input->ask("Enter migration name (e.g., create_users_table)");
        }

        // بررسی وجود مایگریشن با همین نام کلاس
        $className = $this->generateClassName($name);
        $existing = glob("app/database/Migrations/*.php");
        foreach ($existing as $file) {
            $content = file_get_contents($file);
            if (strpos($content, "class {$className}") !== false) {
                echo Colors::error("Migration with class name '{$className}' already exists in: " . basename($file) . "\n");
                return;
            }
        }

        // تولید timestamp
        $timestamp = date('Y_m_d_His');
        $filename = $timestamp . '_' . $name . '.php';
        $path = 'app/database/Migrations/' . $filename;

        if (file_exists($path)) {
            echo Colors::error("Migration already exists: {$filename}\n");
            return;
        }

        // ایجاد محتوای فایل
        $className = $this->generateClassName($name);
        $tableName = $this->extractTableName($name);

        // بررسی استفاده از روش قدیمی یا جدید
        $useLegacy = $this->input->getOption('old') || $this->input->getOption('legacy');
        $content = $useLegacy
            ? $this->getLegacyStub($className, $tableName)
            : $this->getModernStub($className, $tableName);

        // اطمینان از وجود پوشه
        if (!is_dir('app/database/Migrations')) {
            mkdir('app/database/Migrations', 0777, true);
        }

        file_put_contents($path, $content);

        $method = $useLegacy ? 'legacy' : 'modern (TableBuilder)';
        echo Colors::green("✓ Migration created: {$path} [{$method}]\n");
    }

    protected function generateClassName($name)
    {
        $parts = explode('_', $name);
        $className = '';
        foreach ($parts as $part) {
            $className .= ucfirst($part);
        }
        return $className;
    }

    protected function extractTableName($name)
    {
        if (preg_match('/^create_table_(.+)$/', $name, $matches)) {
            return $matches[1];
        }
        if (preg_match('/^create_(.+)_table$/', $name, $matches)) {
            return $matches[1];
        }
        if (preg_match('/^add_.+_to_(.+)_table$/', $name, $matches)) {
            return $matches[1];
        }
        if (preg_match('/^drop_(.+)_table$/', $name, $matches)) {
            return $matches[1];
        }
        return 'unknown_table';
    }

    /**
     * روش جدید: استفاده از Schema + TableBuilder (مدرن)
     */
    protected function getModernStub($className, $tableName)
    {
        return <<<PHP
<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;


return new class extends Migration
{
    public static function up()
    {
        Schema::create('{$tableName}', function (TableBuilder \$table) {
            // کلید اصلی
            \$table->id();
            
            // // رشته‌ها
            // \$table->string('name', 191);
            // \$table->string('slug', 191)->unique();
            // \$table->string('email', 191)->nullable();
            
            // // متن
            // \$table->text('description', true); // nullable
            // \$table->longText('content');
            
            // // اعداد
            // \$table->integer('price')->default('0');
            // \$table->bigInteger('views')->default('0');
            // \$table->tinyInteger('status')->default('1');
            // \$table->decimal('rating', 3, 2)->default('0.00');
            
            // // بولین
            // \$table->boolean('is_active')->default(true);
            
            // // Enum
            // \$table->enum('role', ['user', 'admin', 'moderator'], 'user');
            
            // // تاریخ و زمان
            // \$table->date('published_date')->nullable();
            // \$table->dateTime('last_seen_at')->nullable();
            // \$table->timestamp('verified_at')->nullable();
            
            // // کلید خارجی
            // \$table->bigInteger('user_id');
            // \$table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
            
            // \$table->bigInteger('category_id');
            // \$table->foreign('category_id')->references('id')->on('categories')->onDelete('SET NULL');
            
            // // ایندکس‌ها
            // \$table->index('status');
            // \$table->index('created_at');
            
            // تایم‌استمپ‌ها (با softDeletes)
            \$table->timestamps(true); // created_at, updated_at, deleted_at
            
            // اگر softDeletes جدا می‌خواهید:
            // \$table->softDeletes();
        });
    }

    public static function down()
    {
        Schema::dropIfExists('{$tableName}');
    }
};
PHP;
    }

    /**
     * روش قدیمی: استفاده از آرایه (legacy)
     */
    protected function getLegacyStub($className, $tableName)
    {
        return <<<PHP
<?php

namespace App\Database\Migrations;

use App\Database\Migration;

return new class extends Migration
{
    public static function up()
    {
        Migration::createTable('{$tableName}', [
            'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
            // 'name' => 'VARCHAR(191) NOT NULL',
            // 'slug' => 'VARCHAR(191) NOT NULL UNIQUE',
            // 'email' => 'VARCHAR(191) NULL',
            // 'description' => 'TEXT NULL',
            // 'content' => 'LONGTEXT NULL',
            // 'price' => 'INT NOT NULL DEFAULT 0',
            // 'views' => 'BIGINT NOT NULL DEFAULT 0',
            // 'status' => 'TINYINT NOT NULL DEFAULT 1',
            // 'rating' => 'DECIMAL(3,2) NOT NULL DEFAULT 0.00',
            // 'is_active' => 'TINYINT(1) NOT NULL DEFAULT 1',
            // "role" => "ENUM('user','admin','moderator') NOT NULL DEFAULT 'user'",
            // 'published_date' => 'DATE NULL',
            // 'last_seen_at' => 'DATETIME NULL',
            // 'verified_at' => 'TIMESTAMP NULL',
            // 'user_id' => 'BIGINT NOT NULL',
            // 'category_id' => 'BIGINT NULL',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'deleted_at' => 'TIMESTAMP NULL',
            // ایندکس‌ها:
            // 'KEY status_index (status)',
            'KEY created_at_index (created_at)',
            'FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE',
        ]);
    }

    public static function down()
    {
        Migration::dropTable('{$tableName}');
    }
};
PHP;
    }
}
