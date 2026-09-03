<?php

require_once __DIR__ . '/../Colors.php';

class KeyGenerateCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
        Colors::enable();
    }

    public static function getDescription()
    {
        return 'Generate a new application encryption key';
    }

    public function execute()
    {
        $envPath = BASE_PATH . '/.env';

        if (!is_file($envPath)) {
            echo Colors::red("  Error: .env file not found.\n");
            echo Colors::yellow("  Tip: Copy .env.example to .env before generating an application key.\n");
            return;
        }

        try {
            $key = 'base64:' . base64_encode(random_bytes(32));
        } catch (\Throwable $e) {
            echo Colors::red("  Error: Unable to generate a secure application key.\n");
            return;
        }

        $contents = file_get_contents($envPath);
        if ($contents === false) {
            echo Colors::red("  Error: Unable to read the .env file.\n");
            return;
        }

        if (preg_match('/^APP_KEY=.*$/m', $contents)) {
            $contents = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $key, $contents, 1);
        } else {
            $newline = $contents !== '' && !preg_match('/\R$/', $contents) ? PHP_EOL : '';
            $contents .= $newline . 'APP_KEY=' . $key . PHP_EOL;
        }

        if (file_put_contents($envPath, $contents, LOCK_EX) === false) {
            echo Colors::red("  Error: Unable to write the .env file.\n");
            return;
        }

        $_ENV['APP_KEY'] = $key;
        putenv('APP_KEY=' . $key);

        echo Colors::green("  ✓ Application key generated successfully.\n");
    }
}
