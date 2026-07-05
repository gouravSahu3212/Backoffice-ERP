<?php

namespace App\Http\Controllers\Admin;

class DashboardController
{
    public function __invoke()
    {
        return view('admin.dashboard');
    }
}