<?php

return [

    'super_admin' => [
        [
            'title' => 'Dashboard',
            'route' => 'admin.dashboard',
            'icon' => 'home',
        ],
        [
            'title' => 'Hotels',
            'route' => 'admin.hotels.index',
            'icon' => 'building',
        ],
        [
            'title' => 'Agents',
            'route' => 'admin.agents.index',
            'icon' => 'users',
        ],
        [
            'title' => 'Transfers',
            'route' => 'admin.transfers.index',
            'icon' => 'truck',
            'submenu' => [
                [
                    'title' => 'Booking Enquiries',
                    'route' => 'admin.transfer-requests.index',
                    'icon' => 'ticket',
                ],
            ],
        ],
        [
            'title' => 'Tours',
            'route' => 'admin.tours.index',
            'icon' => 'map',
            'submenu' => [
                [
                    'title' => 'Booking Enquiries',
                    'route' => 'admin.tour-requests.index',
                    'icon' => 'ticket',
                ],
            ],
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
            'submenu' => [
                [
                    'title' => 'Hotel Bookings',
                    'route' => 'agent.hotel-bookings.index',
                    'icon' => 'ticket',
                ],
            ],
        ],
        [
            'title' => 'Transfers',
            'route' => 'agent.transfers.index',
            'icon' => 'truck',
            'submenu' => [
                [
                    'title' => 'Booking Enquiries',
                    'route' => 'agent.transfer-requests.index',
                    'icon' => 'ticket',
                ],
            ],
        ],
        [
            'title' => 'Tours',
            'route' => 'agent.tours.index',
            'icon' => 'map',
            'submenu' => [
                [
                    'title' => 'Booking Enquiries',
                    'route' => 'agent.tour-requests.index',
                    'icon' => 'ticket',
                ],
            ],
        ],
        [
            'title' => 'Bookings',
            'route' => 'agent.bookings.index',
            'icon' => 'calendar',
        ],
    ],
];
