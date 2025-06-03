<?php

declare(strict_types=1);

return [
    'default_core' => [
        'default_error' => 'Something went wrong while trying to process your request.',
        'throttle_error' => 'The number of requests exceeded 4 per minute',
        'model_not_found' => 'Unfortunately, the model was not found in the database',
        'route_not_found' => 'The requested route was not found. Please check the URL or return to the main page.',
        'auth_required' => 'You must be authenticated to perform this action.',
        'server_error' => 'A server error occurred. Please try again later, we’re working to fix it',
    ],
    'product' => [
        'not_found' => 'Product not found'
    ],
    'user' => [
        'invalid_credentials' => 'Invalid credentials',
    ],
    'post' => [
        'access_denied' => 'You have not access to this post',
    ]
];
