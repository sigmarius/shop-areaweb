<?php

declare(strict_types=1);

return [
    'default_core' => [
        'default_error' => 'Что-то пошло не так. Пожалуйста, проверьте введённую информацию',
        'throttle_error' => 'Количество запросов превысило 4 в минуту',
        'model_not_found' => 'К сожалению, модель :model не найдена в базе данных',
        'route_not_found' => 'Запрашиваемый маршрут не найден. Пожалуйста, проверьте URL или вернитесь на главную страницу',
        'auth_required' => 'Вы должны быть авторизованы для выполнения этого действия.',
        'server_error' => 'Произошла ошибка на сервере. Пожалуйста, попробуйте позже, мы уже работаем над решением',
    ],
    'product' => [
        'not_found' => 'Продукт не найден'
    ],
    'user' => [
        'invalid_credentials' => 'Пользователь с такими данными не найден в базе данных',
    ],
    'post' => [
        'access_denied' => 'У вас недостаточно прав для выполнения данного действия',
    ]
];
