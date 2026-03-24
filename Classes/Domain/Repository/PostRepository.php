<?php

declare(strict_types=1);

namespace Aistea\Aisteablog\Domain\Repository;

use Aistea\Aisteablog\Domain\Model\Category;
use Aistea\Aisteablog\Domain\Model\Post;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class PostRepository extends Repository
{
    public function __construct(private readonly ConnectionPool $connectionPool)
    {
        parent::__construct();
    }

    public function incrementViewCount(Post $post): void
    {
        $connection = $this->connectionPool->getConnectionForTable('tx_aisteablog_domain_model_post');

        $record = $connection
            ->select(['uid', 'l18n_parent'], 'tx_aisteablog_domain_model_post', ['uid' => $post->getUid()])
            ->fetchAssociative();

        $targetUid = ($record && (int)$record['l18n_parent'] > 0)
            ? (int)$record['l18n_parent']
            : $post->getUid();

        $connection->executeStatement(
            'UPDATE tx_aisteablog_domain_model_post SET view_count = view_count + 1 WHERE uid = :uid',
            ['uid' => $targetUid]
        );
    }

    protected $defaultOrderings = [
        'publishDate' => QueryInterface::ORDER_DESCENDING,
    ];

    public function initializeObject(): void
    {
        $this->defaultQuerySettings = $this->createQuery()->getQuerySettings();
        $this->defaultQuerySettings->setRespectStoragePage(false);
    }

    public function findByCategory(Category $category): QueryResultInterface
    {
        $query = $this->createQuery();
        return $query->matching(
            $query->contains('categories', $category)
        )->execute();
    }

    public function findLatest(int $limit = 3): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->setLimit($limit);
        return $query->execute();
    }

    public function findPreviousPost(Post $post): ?Post
    {
        if ($post->getPublishDate() === null) {
            return null;
        }
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->lessThan('publishDate', $post->getPublishDate()),
                $query->logicalNot($query->equals('uid', $post->getUid()))
            )
        );
        $query->setOrderings(['publishDate' => QueryInterface::ORDER_DESCENDING]);
        $query->setLimit(1);
        return $query->execute()->getFirst();
    }

    public function findNextPost(Post $post): ?Post
    {
        if ($post->getPublishDate() === null) {
            return null;
        }
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->greaterThan('publishDate', $post->getPublishDate()),
                $query->logicalNot($query->equals('uid', $post->getUid()))
            )
        );
        $query->setOrderings(['publishDate' => QueryInterface::ORDER_ASCENDING]);
        $query->setLimit(1);
        return $query->execute()->getFirst();
    }
}
