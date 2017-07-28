<?php

return  [
    'OPTION api/users' => 'user/option',
    'POST api/users' => 'user/create',

    'OPTION api/users/login' => 'user/option',
    'POST api/users/login' => 'user/login',

    'OPTION api/user' => 'user/option',
    'GET api/user' => 'user/index',

    'OPTION api/user' => 'user/option',
    'PUT api/user' => 'user/update',

    'OPTION api/profiles' => 'profile/option',
    'GET api/profiles/<username>' => 'profile/view',
];
