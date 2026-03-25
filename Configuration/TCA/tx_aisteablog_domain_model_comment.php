<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title'         => 'Blog Kommentar',
        'label'         => 'author_name',
        'label_alt'     => 'content',
        'label_alt_force' => true,
        'crdate'        => 'crdate',
        'tstamp'        => 'tstamp',
        'delete'        => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields'  => 'author_name,author_email,content',
        'iconfile'      => 'EXT:core/Resources/Public/Icons/T3Icons/content/content-text.svg',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;Kommentar,
                    approved, post, author_name, author_email, content,
                --div--;Zugriff,
                    hidden
            ',
        ],
    ],
    'columns' => [
        'hidden' => [
            'label'  => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => ['type' => 'check', 'renderType' => 'checkboxToggle', 'items' => [['label' => '', 'invertStateDisplay' => true]]],
        ],
        'approved' => [
            'label'  => 'Freigegeben',
            'config' => [
                'type'       => 'check',
                'renderType' => 'checkboxToggle',
                'default'    => 0,
            ],
        ],
        'post' => [
            'label'  => 'Beitrag',
            'config' => [
                'type'                => 'select',
                'renderType'          => 'selectSingle',
                'foreign_table'       => 'tx_aisteablog_domain_model_post',
                'foreign_table_where' => 'ORDER BY tx_aisteablog_domain_model_post.title',
                'items'               => [['label' => '-- kein Beitrag --', 'value' => 0]],
                'default'             => 0,
                'readOnly'            => true,
            ],
        ],
        'author_name' => [
            'label'  => 'Name',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'readOnly' => true,
            ],
        ],
        'author_email' => [
            'label'  => 'E-Mail',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'readOnly' => true,
            ],
        ],
        'content' => [
            'label'  => 'Kommentar',
            'config' => [
                'type'     => 'text',
                'rows'     => 6,
                'readOnly' => true,
            ],
        ],
    ],
];
