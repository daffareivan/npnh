<?php

return [
    'badge' => 'Error',
    'version' => 'Version',
    'meta_description' => 'Something didn\'t go as planned. Here\'s what you can do next.',

    'actions' => [
        'back_home' => 'Back to Home',
        'go_back' => 'Go Back',
        'dashboard' => 'Dashboard',
        'admin_dashboard' => 'Admin Dashboard',
    ],

    'extra' => [
        'request_id' => 'Request ID',
        'timestamp' => 'Timestamp',
        'environment' => 'Environment',
    ],

    '400' => [
        'title' => 'Bad Request',
        'description' => 'The request couldn\'t be understood by the server. Please check your input and try again.',
    ],
    '401' => [
        'title' => 'Unauthorized',
        'description' => 'You need to sign in to access this page.',
    ],
    '403' => [
        'title' => 'Access Forbidden',
        'description' => 'You don\'t have permission to access this page.',
    ],
    '404' => [
        'title' => 'Page Not Found',
        'description' => 'The page you\'re looking for doesn\'t exist or has been moved.',
    ],
    '405' => [
        'title' => 'Method Not Allowed',
        'description' => 'This request method isn\'t supported for this page.',
    ],
    '419' => [
        'title' => 'Page Expired',
        'description' => 'Your session has expired. Please refresh the page and try again.',
    ],
    '422' => [
        'title' => 'Unprocessable Request',
        'description' => 'We couldn\'t process your request because some data is invalid.',
    ],
    '429' => [
        'title' => 'Too Many Requests',
        'description' => 'You\'ve made too many requests. Please wait a moment and try again.',
    ],
    '500' => [
        'title' => 'Internal Server Error',
        'description' => 'Something went wrong on our end. We\'re working to fix it.',
    ],
    '502' => [
        'title' => 'Bad Gateway',
        'description' => 'We received an invalid response from the upstream server. Please try again shortly.',
    ],
    '503' => [
        'title' => 'Service Unavailable',
        'description' => 'We\'re currently down for maintenance. Please check back soon.',
    ],
];
