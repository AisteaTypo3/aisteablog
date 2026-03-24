<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Aistea Blog',
    'description' => 'Slim blog extension for the aistea.me portfolio',
    'category' => 'plugin',
    'constraints' => [
        'depends' => [
            'typo3' => '14.0.0-14.99.99',
            'extbase' => '14.0.0-14.99.99',
            'fluid' => '14.0.0-14.99.99',
        ],
        'conflicts' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'Aistea\\Aisteablog\\' => 'Classes',
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Yannick Aister',
    'author_email' => 'yannick.aister@aistea.me',
    'author_company' => 'AIstea',
    'version' => '1.0.0',
];
