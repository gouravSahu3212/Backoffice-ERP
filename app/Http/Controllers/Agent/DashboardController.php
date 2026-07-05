<?php

namespace App\Http\Controllers\Agent;

class DashboardController
{
    public function __invoke()
    {
        return view('agent.dashboard');
    }
}