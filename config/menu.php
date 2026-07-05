<?php

return [

    'super_admin' => [
        [
            'title' => 'Dashboard',
            'route' => 'admin.dashboard',
            'icon' => 'home',
        ],
        [
            'title' => 'Agents',
            'route' => 'admin.agents.index',
            'icon' => 'users',
        ],
        [
            'title' => 'Hotels',
            'route' => 'admin.hotels.index',
            'icon' => 'building',
        ],
        [
            'title' => 'Transfers',
            'route' => 'admin.transfers.index',
            'icon' => 'truck',
        ],
        [
            'title' => 'Bookings',
            'route' => 'admin.bookings.index',
            'icon' => 'calendar',
        ],
    ],

    'agent' => [
        [
            'title' => 'Dashboard',
            'route' => 'agent.dashboard',
            'icon' => 'home',
        ],
        [
            'title' => 'Hotels',
            'route' => 'agent.hotels.index',
            'icon' => 'building',
        ],
        [
            'title' => 'Transfers',
            'route' => 'agent.transfers.index',
            'icon' => 'truck',
        ],
        [
            'title' => 'Bookings',
            'route' => 'agent.bookings.index',
            'icon' => 'calendar',
        ],
    ],
];