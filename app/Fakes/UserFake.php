<?php
namespace App\Fakes;

use App\Core\Faker;
use App\Models\User;

class UserFake
{
    public static function definition()
    {
        return [
            // مثال:
            // 'name' => Faker::name(),
            // 'email' => Faker::email(),
            // 'slug' => Faker::slug(Faker::word()),
        ];
    }

    public static function create($count = 1)
    {
        for ($i = 0; $i < $count; $i++) {
            $data = static::definition();
            User::create($data);
        }
    }
}