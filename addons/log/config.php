<?php

return [
    [
        'name' => 'level',
        'title' => '日志级别',
        'type' => 'selects',
        'content' => [
            'log' => 'log',
            'error' => 'error',
            'notice' => 'notice',
            'info' => 'info',
            'debug' => 'debug',
            'sql' => 'sql',
        ],
        'value' => 'log,error,notice,info,debug,sql',
        'rule' => 'required',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
];
