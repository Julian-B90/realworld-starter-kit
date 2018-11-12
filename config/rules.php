<?php

return  [
    ['class' => \yii\rest\UrlRule::class,
        'controller' => 'api/user',
        'except' => ['delete', 'index'],
        'extraPatterns' => [
            'POST login' => 'login',
        ]
    ],
    ['class' => \yii\rest\UrlRule::class,
        'controller' => 'api/profile',
        'only' => ['view']
    ],
    ['class' => \yii\rest\UrlRule::class,
        'controller' => 'api/article',
        'tokens' => [
            '{slug}' => '<slug:\w+>',
        ],
        'patterns' => [
            'PUT {slug}' => 'update',
            'DELETE {slug}' => 'delete',
            'GET,HEAD {slug}' => 'view',
            'POST' => 'create',
            'GET,HEAD' => 'index',
            '{slug}' => 'options',
            '' => 'options',
        ]
    ],
    ['class' => \yii\rest\UrlRule::class,
        'controller' => 'api/tag',
        'only' => ['index']
    ],
];
