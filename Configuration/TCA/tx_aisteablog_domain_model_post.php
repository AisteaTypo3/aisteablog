<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title'            => 'Blog Post',
        'label'            => 'title',
        'label_alt'        => 'publish_date',
        'label_alt_force'  => true,
        'crdate'           => 'crdate',
        'tstamp'           => 'tstamp',
        'delete'           => 'deleted',
        'enablecolumns'    => [
            'disabled'  => 'hidden',
            'starttime' => 'starttime',
            'endtime'   => 'endtime',
        ],
        'searchFields'     => 'title,teaser,bodytext,author',
        'iconfile'         => 'EXT:aisteablog/Resources/Public/Icons/post.svg',
        'languageField'        => 'sys_language_uid',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'translationSource' => 'l10n_source',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;Inhalt,
                    title, slug, teaser, bodytext, cover_image,
                --div--;Metadaten,
                    publish_date, author, categories, tags,
                --div--;Zugriff,
                    hidden, starttime, endtime,
                --div--;Sprache,
                    sys_language_uid, l18n_parent, l18n_diffsource,
                --div--;Statistiken,
                    view_count
            ',
        ],
    ],
    'columns' => [
        'sys_language_uid' => [
            'label'  => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => ['type' => 'language'],
        ],
        'l18n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label'       => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config'      => [
                'type'                => 'select',
                'renderType'          => 'selectSingle',
                'items'               => [['label' => '', 'value' => 0]],
                'foreign_table'       => 'tx_aisteablog_domain_model_post',
                'foreign_table_where' => 'AND {#tx_aisteablog_domain_model_post}.{#pid}=###CURRENT_PID### AND {#tx_aisteablog_domain_model_post}.{#sys_language_uid} IN (-1,0)',
                'default'             => 0,
            ],
        ],
        'l18n_diffsource' => [
            'config' => ['type' => 'passthrough'],
        ],
        'hidden' => [
            'label'  => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => ['type' => 'check', 'renderType' => 'checkboxToggle', 'items' => [['label' => '', 'invertStateDisplay' => true]]],
        ],
        'starttime' => [
            'label'  => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => ['type' => 'datetime', 'default' => 0],
        ],
        'endtime' => [
            'label'  => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => ['type' => 'datetime', 'default' => 0],
        ],
        'title' => [
            'label'  => 'Titel',
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
                    'fields'               => ['title'],
                    'fieldSeparator'       => '-',
                    'prefixParentPageSlug' => false,
                    'replacements'         => ['/' => '-'],
                ],
                'fallbackCharacter' => '-',
                'eval'              => 'uniqueInSite',
            ],
        ],
        'teaser' => [
            'label'  => 'Teaser',
            'config' => [
                'type' => 'text',
                'rows' => 4,
                'cols' => 48,
            ],
        ],
        'bodytext' => [
            'label'  => 'Inhalt',
            'config' => [
                'type'            => 'text',
                'enableRichtext'  => true,
                'richtextConfiguration' => 'aisteacorp',
                'rows'            => 15,
                'cols'            => 48,
            ],
        ],
        'cover_image' => [
            'label'  => 'Titelbild',
            'config' => [
                'type'     => 'file',
                'maxitems' => 1,
                'allowed'  => 'common-image-types',
            ],
        ],
        'author' => [
            'label'  => 'Autor',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
            ],
        ],
        'publish_date' => [
            'label'  => 'Veröffentlichungsdatum',
            'config' => [
                'type'    => 'datetime',
                'format'  => 'date',
                'eval'    => 'int',
                'default' => 0,
            ],
        ],
        'categories' => [
            'label'  => 'Kategorien',
            'config' => [
                'type'          => 'select',
                'renderType'    => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_aisteablog_domain_model_category',
                'MM'            => 'tx_aisteablog_post_category_mm',
                'size'          => 5,
                'maxitems'      => 99,
            ],
        ],
        'tags' => [
            'label'  => 'Tags',
            'config' => [
                'type'          => 'select',
                'renderType'    => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_aisteablog_domain_model_tag',
                'MM'            => 'tx_aisteablog_post_tag_mm',
                'size'          => 5,
                'maxitems'      => 99,
            ],
        ],
        'view_count' => [
            'label'  => 'Aufrufe',
            'config' => [
                'type'     => 'number',
                'readOnly' => true,
                'default'  => 0,
            ],
        ],
    ],
];
