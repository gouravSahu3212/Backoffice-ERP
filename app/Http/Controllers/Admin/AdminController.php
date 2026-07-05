<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

abstract class AdminController extends Controller
{
    protected function success(string $message)
    {
        return redirect()->back()->with('success', $message);
    }

    protected function error(string $message)
    {
        return redirect()->back()->with('error', $message);
    }
}