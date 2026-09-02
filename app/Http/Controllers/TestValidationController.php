<?php

namespace App\Http\Controllers;

use App\Http\Controller;
use App\Request;

class TestValidationController extends Controller
{
    public function showForm()
    {
        return view('test-validation');
    }

    public function validateForm()
    {
        $request = new Request();

        $rules = [
            'name' => 'required|string|min:3|max:50',
            'email' => 'required|email',
            'age' => 'integer|min:18|max:99',
            'website' => 'url',
            'birthdate' => 'date',
            'is_active' => 'boolean',
            'status' => 'required|in:active,inactive',
            'role' => 'not_in:admin,superadmin',
        ];

        $errors = $request->validate($rules);

        if ($errors && $errors->any()) {
            return redirect()->back()->withErrors($errors)->withInput()->send();
        }


        echo "✅ همه مقادیر معتبر هستند!";
    }
}
