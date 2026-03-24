<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title'         => 'Blog Kategorie',
        'label'         => 'title',
        'crdate'        => 'crdate',
        'tstamp'        => 'tstamp',
        'delete'        => 'deleted',
        'enablecolumns' => ['disabled' => 'hidden'],
        'searchFields'  => 'title,description',
        'iconfile'      => 'EXT:aisteablog/Resources/Public/Icons/category.svg',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'title, slug, description, --div--;Zugriff, hidden',
        ],
    ],
    'columns' => [
        'hidden' => [
            'label'  => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => ['type' => 'check', 'renderType' => 'checkboxToggle', 'items' => [['label' => '', 'invertStateDisplay' => true]]],
        ],
        'title' => [
            'label'  => 'Bezeichnung',
            'config' => [
                'type'     => 'input',
                'eval'     => 'trim',
                'required' => true,
            ],
        ],
        'slug' => [
            'label'  => 'URL Slug',
            'config' => [
                'type'             => 'slug',
                'generatorOptions' => [
                    'fields'         => ['title'],
                    'fieldSeparator' => '-',
                    'replacements'   => ['/' => '-'],
                ],
                'fallbackCharacter' => '-',
                'eval'              => 'uniqueInSite',
            ],
        ],
        'description' => [
            'label'  => 'Beschreibung',
            'config' => [
                'type' => 'text',
                'rows' => 3,
                'cols' => 48,
            ],
        ],
    ],
];
