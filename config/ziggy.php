<?php

return [
    'groups' => [
        'admin' => ['admin.*'],
        'partner' => ['partner.*'],
        'common' => [
            'auth.*',
            'current-user-photo.*',
            'current-user.*',
            'login',
            'logout',
            'register',
            'user-password.*',
            'user-profile-information.*',
            'root',
            'other-browser-sessions.*',
            'password.*',
            'profile.*',
            'two-factor.*',
            'verification.*',
         ],
    ],
];
