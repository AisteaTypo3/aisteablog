<?php

declare(strict_types=1);

namespace Aistea\Aisteablog\PageTitle;

use TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider;

class PostPageTitleProvider extends AbstractPageTitleProvider
{
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}
