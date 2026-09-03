<?php

use App\Core\Env;
use App\Database\Db;

class DevSeedCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
    }

    public static function getDescription()
    {
        return 'Create or update the default admin and user accounts for development.';
    }

    public function execute()
    {
        if (Env::get('APP_ENV', 'production') !== 'development') {
            echo "dev:seed is available only when APP_ENV=development.\n";
            return;
        }

        $adminEmail = Env::get('DEV_ADMIN_EMAIL');
        $adminPassword = Env::get('DEV_ADMIN_PASSWORD');
        $userEmail = Env::get('DEV_USER_EMAIL');
        $userPassword = Env::get('DEV_USER_PASSWORD');

        if (!$adminEmail || !$adminPassword || !$userEmail || !$userPassword) {
            echo "Set DEV_ADMIN_EMAIL, DEV_ADMIN_PASSWORD, DEV_USER_EMAIL and DEV_USER_PASSWORD in .env first.\n";
            return;
        }

        $db = Db::getInstance();
        $sql = "SHOW COLUMNS FROM users";
        $result = $db->query($sql);
        if (!$result) {
            echo "The users table does not exist. Run your user migration first.\n";
            return;
        }

        $columns = [];
        while ($column = $result->fetch_assoc()) {
            $columns[] = $column['Field'];
        }

        $required = ['email', 'password'];
        foreach ($required as $column) {
            if (!in_array($column, $columns, true)) {
                echo "The users table is missing the '{$column}' column.\n";
                return;
            }
        }

        $this->upsert($db, $columns, $adminEmail, $adminPassword, 'admin');
        $this->upsert($db, $columns, $userEmail, $userPassword, 'user');

        echo "Development accounts are ready:\n";
        echo "  Admin: {$adminEmail}\n";
        echo "  User:  {$userEmail}\n";
    }

    protected function upsert($db, array $columns, string $email, string $password, string $role): void
    {
        $email = $db->real_escape_string($email);
        $hash = $db->real_escape_string(password_hash($password, PASSWORD_DEFAULT));
        $role = $db->real_escape_string($role);

        $exists = $db->query("SELECT id FROM users WHERE email = '{$email}' LIMIT 1");
        if ($exists && $exists->num_rows > 0) {
            $updates = ["password = '{$hash}'"];
            if (in_array('role', $columns, true)) $updates[] = "role = '{$role}'";
            if (in_array('status', $columns, true)) $updates[] = "status = 'active'";
            $db->query("UPDATE users SET " . implode(', ', $updates) . " WHERE email = '{$email}' LIMIT 1");
            return;
        }

        $fields = ['email', 'password'];
        $values = ["'{$email}'", "'{$hash}'"];
        if (in_array('role', $columns, true)) { $fields[] = 'role'; $values[] = "'{$role}'"; }
        if (in_array('status', $columns, true)) { $fields[] = 'status'; $values[] = "'active'"; }
        if (in_array('name', $columns, true)) { $fields[] = 'name'; $values[] = "'" . ucfirst($role) . "'"; }

        $db->query("INSERT INTO users (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")");
    }
}
