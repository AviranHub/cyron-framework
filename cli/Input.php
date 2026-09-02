<?php

class Input {
    protected $args;
    protected $options = [];
    
    public function __construct() {
        global $argv;
        $this->args = array_slice($argv, 1);
        $this->parseOptions();
    }
    
    protected function parseOptions() {
        foreach ($this->args as $i => $arg) {
            if (strpos($arg, '--') === 0) {
                // Long option like --help or --port=8080
                $option = substr($arg, 2);
                if (strpos($option, '=') !== false) {
                    list($name, $value) = explode('=', $option, 2);
                    $this->options[$name] = $value;
                } else {
                    $this->options[$option] = true;
                }
                unset($this->args[$i]);
            } elseif (strpos($arg, '-') === 0 && strlen($arg) > 1) {
                // Short option like -h or -q
                $option = substr($arg, 1);
                $this->options[$option] = true;
                unset($this->args[$i]);
            }
        }
        $this->args = array_values($this->args);
    }
    
    public function getCommand() {
        return $this->args[0] ?? 'list';
    }
    
    public function getArgument($index) {
        return $this->args[$index] ?? null;
    }
    
    public function getOption($name, $default = null) {
        return $this->options[$name] ?? $default;
    }
    
    public function hasOption($name) {
        return isset($this->options[$name]);
    }
    
    public function ask($question, $default = null) {
        echo $question . ($default ? " [{$default}]" : '') . ': ';
        $answer = trim(fgets(STDIN));
        return $answer ?: $default;
    }
    
    public function confirm($question, $default = false) {
        $defaultText = $default ? '[Y/n]' : '[y/N]';
        $answer = $this->ask($question . ' ' . $defaultText);
        return strtolower($answer) === 'y' || ($default && $answer === '');
    }
    
    public function choice($question, $choices, $default = null) {
        echo $question . "\n";
        foreach ($choices as $i => $choice) {
            echo "  [{$i}] {$choice}\n";
        }
        $answer = $this->ask("Enter your choice", $default);
        return $choices[$answer] ?? $choices[0];
    }
}