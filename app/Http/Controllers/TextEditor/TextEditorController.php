<?php
namespace Plugins\TextEditor\Controllers;

use App\Http\Controller;

class TextEditorController extends Controller
{
    public function index()
    {
        return view('texteditor.index', [
            'title' => 'TextEditor Plugin'
        ]);
    }

    public function show($id)
    {
        // منطق نمایش یک آیتم
        return "Show item 1 from TextEditor plugin";
    }
}