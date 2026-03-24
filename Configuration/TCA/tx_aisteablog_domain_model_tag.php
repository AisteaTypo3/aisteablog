<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title'         => 'Blog Tag',
        'label'         => 'title',
        'crdate'        => 'crdate',
        'tstamp'        => 'tstamp',
        'delete'        => 'deleted',
        'enablecolumns' => ['disabled' => 'hidden'],
        'searchFields'  => 'title',
        'iconfile'      => 'EXT:aisteablog/Resources/Public/Icons/tag.svg',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'title, --div--;Zugriff, hidden',
        ],
    ],
    'columns' => [
        'hidden' => [
            'label'  => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => ['type' => 'check', 'renderType' => 'checkboxToggle', 'items' => [['label' => '', 'invertStateDisplay' => true]]],
        ],
        'title' => [
            'label'  => 'Tag',
            'config' => [
                'type'     => 'input',
                'eval'     => 'trim',
                'required' => true,
            ],
        ],
    ],
];
