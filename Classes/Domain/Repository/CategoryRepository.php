<?php

declare(strict_types=1);

namespace Aistea\Aisteablog\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;

class CategoryRepository extends Repository
{
    public function initializeObject(): void
    {
        $this->defaultQuerySettings = $this->createQuery()->getQuerySettings();
        $this->defaultQuerySettings->setRespectStoragePage(false);
    }
}
