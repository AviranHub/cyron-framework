<?php
return [
    'users' => [
        'model'    => \App\Models\User::class,
        'label'    => 'کاربران',
        'columns'  => ['id', 'name', 'email', 'role', 'created_at'],
        'searchable' => ['name', 'email'],
        'form' => [
            'name'     => 'text|required',
            'email'    => 'email|required|unique',
            'password' => 'password|required|min:6|confirmed',
            'role'     => 'select:admin,user,editor',
            'status'   => 'select:active,inactive,suspended',
        ],
    ],
    'roles' => [
        'model' => \App\Models\Role::class,
        'label' => 'نقش‌ها',
        'columns' => ['id','name','slug','is_active','priority'],
        'searchable' => ['name','slug'],
        'form' => [
            'name' => 'text|required',
            'slug' => 'text|required|unique',
            'description' => 'textarea',
            'is_active' => 'select:1,0',
            'priority' => 'number',
        ],
    ],
    'permissions' => [
        'model' => \App\Models\Permission::class,
        'label' => 'دسترسی‌ها',
        'columns' => ['id','name','slug','group','module','is_critical'],
        'searchable' => ['name','slug','group','module'],
        'form' => [
            'name' => 'text|required',
            'slug' => 'text|required|unique',
            'group' => 'text|required',
            'module' => 'text',
            'description' => 'textarea',
            'is_critical' => 'select:0,1',
        ],
    ],
    'books' => [
        'model'    => \App\Models\Book::class,
        'label'    => 'کتاب‌ها',
        'columns'  => ['id', 'title', 'author_name', 'price'],
        'searchable' => ['title', 'author_name'],
        'form' => [
            'title'       => 'text|required',
            'slug'        => 'text|required|unique',
            'author_name' => 'text',
            'price'       => 'number',
            'cover'       => 'file|image',
            'description' => 'textarea',
        ],
    ],
];