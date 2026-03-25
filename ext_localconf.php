<?php

defined('TYPO3') or die('Access denied.');

use Aistea\Aisteablog\Controller\PostController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::configurePlugin(
    'Aisteablog',
    'Blog',
    [PostController::class => 'list, show, category, createComment'],
    [PostController::class => 'show, createComment'],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);
