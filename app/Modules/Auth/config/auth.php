<?php
// app/Config/auth.php after publish

return [
    'login_fields' => ['email', 'phone', 'username'],
    'password_reset_expiry' => 60, // minutes
    'phone_verification_expiry' => 10, // minutes
    'default_role' => 'user',
];