<?php

return  [
    'GET api/user' => 'api/user/view',
    'PUT api/user' => 'api/user/update',
    ['class' => \yii\rest\UrlRule::class,
        'controller' => 'api/user',
        'except' => ['delete', 'index'],
        'extraPatterns' => [
            'POST login' => 'login',
            'GET' => 'view',
        ]
    ],

    ['class' => \yii\rest\UrlRule::class,
        'controller' => 'api/profile',
        'except' => ['delete', 'index', 'update', 'create'],
        'tokens' => [
            '{username}' => '<username:\w+>',
        ],
        'extraPatterns' => [
            'POST {username}/follow' => 'follow',
            'DELETE {username}/follow' => 'unfollow',
            'GET {username}' => 'view',
        ]
    ],

    ['class' => \yii\rest\UrlRule::class,
        'controller' => 'api/profile',
        'only' => ['view']
    ],
    ['class' => \yii\rest\UrlRule::class,
        'controller' => 'api/article',
        'tokens' => [
            '{slug}' => '<slug:[a-z0-9]+(?:-[a-z0-9]+)*>',
        ],
        'patterns' => [
            'PUT {slug}' => 'update',
            'DELETE {slug}' => 'delete',
            'GET,HEAD feed' => 'feed',
            'GET,HEAD {slug}' => 'view',
            'POST' => 'create',
            'GET,HEAD' => 'index',
            '{slug}' => 'options',
            '' => 'options',
        ],
        'extraPatterns' => [
            'POST {slug}/favourite' => 'favourite',
            'DELETE {slug}/favourite' => 'delete-favourite',
        ]
    ],

    'POST api/articles/<slug:[a-z0-9]+(?:-[a-z0-9]+)*>/comments' => '/api/comment/create',
    'GET api/articles/<slug:[a-z0-9]+(?:-[a-z0-9]+)*>/comments' => '/api/comment/index',
    'DELETE api/articles/<slug:[a-z0-9]+(?:-[a-z0-9]+)*>/comments/<id:\d+>' => '/api/comment/delete',

    ['class' => \yii\rest\UrlRule::class,
        'controller' => 'api/tag',
        'only' => ['index']
    ],
];
