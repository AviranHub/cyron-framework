<?php

namespace Cyron\Console\Commands;

use Cyron\Console\Colors;

class MakeCommand
{
	protected $input;

	public function __construct($input)
	{
		$this->input = $input;
	}

	public static function getDescription()
	{
		return 'Create a custom application console command';
	}

	public function execute()
	{
		$name = $this->input->getArgument(1);
		if (!$name) {
			$name = $this->input->ask('Enter command name (e.g., CleanupCommand)');
		}

		$name = str_replace(['\\', '/'], '/', trim((string) $name));
		$parts = array_values(array_filter(explode('/', $name)));
		$className = array_pop($parts);
		if (!$className) {
			echo Colors::error("Command name is required.\n");
			return;
		}

		if (!str_ends_with($className, 'Command')) {
			$className .= 'Command';
		}

		$relativeDirectory = 'app/Console/Commands';
		if ($parts) {
			$relativeDirectory .= '/' . implode('/', $parts);
		}

		$directory = BASE_PATH . '/' . $relativeDirectory;
		$path = $directory . '/' . $className . '.php';
		if (!is_dir($directory)) {
			mkdir($directory, 0775, true);
		}

		if (file_exists($path)) {
			echo Colors::error("Command already exists: {$relativeDirectory}/{$className}.php\n");
			return;
		}

		$namespace = 'App\\Console\\Commands';
		if ($parts) {
			$namespace .= '\\' . implode('\\', $parts);
		}

		$commandName = preg_replace('/Command$/', '', $className);
		$commandName = preg_replace('/([a-z])([A-Z])/', '$1:$2', $commandName);
		$commandName = strtolower($commandName);
		$commandName = strtolower(str_replace('_', ':', $commandName));

		$content = str_replace(
			['{{namespace}}', '{{class}}', '{{name}}'],
			[$namespace, $className, $commandName],
			$this->getStub()
		);

		file_put_contents($path, $content);
		echo Colors::green("Command created: {$relativeDirectory}/{$className}.php\n");
	}

	protected function getStub()
	{
		return <<<'PHP'
<?php

namespace {{namespace}};

class {{class}}
{
	protected $input;

	public function __construct($input)
	{
		$this->input = $input;
	}

	public static function getDescription()
	{
		return 'Describe this command';
	}

	public static function getName()
	{
		return '{{name}}';
	}

	public function execute()
	{
		// Add command behavior here.
	}
}
PHP;
	}
}
