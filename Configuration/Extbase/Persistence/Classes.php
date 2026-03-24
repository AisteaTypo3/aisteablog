<?php

declare(strict_types=1);

return [
    \Aistea\Aisteablog\Domain\Model\Post::class => [
        'tableName'  => 'tx_aisteablog_domain_model_post',
        'properties' => [
            'coverImage'  => ['fieldName' => 'cover_image'],
            'publishDate' => ['fieldName' => 'publish_date'],
        ],
    ],
    \Aistea\Aisteablog\Domain\Model\Category::class => [
        'tableName' => 'tx_aisteablog_domain_model_category',
    ],
    \Aistea\Aisteablog\Domain\Model\Tag::class => [
        'tableName' => 'tx_aisteablog_domain_model_tag',
    ],
];
