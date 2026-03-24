<?php

declare(strict_types=1);

namespace Aistea\Aisteablog\Domain\Model;

use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Post extends AbstractEntity
{
    protected string $title = '';
    protected string $slug = '';
    protected string $teaser = '';
    protected string $bodytext = '';
    protected string $author = '';
    protected ?\DateTime $publishDate = null;
    protected int $viewCount = 0;

    /**
     * @var ObjectStorage<FileReference>
     */
    #[Lazy]
    protected ObjectStorage $coverImage;

    /**
     * @var ObjectStorage<Category>
     */
    #[Lazy]
    protected ObjectStorage $categories;

    /**
     * @var ObjectStorage<Tag>
     */
    #[Lazy]
    protected ObjectStorage $tags;

    public function __construct()
    {
        $this->coverImage = new ObjectStorage();
        $this->categories = new ObjectStorage();
        $this->tags = new ObjectStorage();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getTeaser(): string
    {
        return $this->teaser;
    }

    public function setTeaser(string $teaser): void
    {
        $this->teaser = $teaser;
    }

    public function getBodytext(): string
    {
        return $this->bodytext;
    }

    public function setBodytext(string $bodytext): void
    {
        $this->bodytext = $bodytext;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function getPublishDate(): ?\DateTime
    {
        return $this->publishDate;
    }

    public function setPublishDate(?\DateTime $publishDate): void
    {
        $this->publishDate = $publishDate;
    }

    public function getViewCount(): int
    {
        return $this->viewCount;
    }

    public function setViewCount(int $viewCount): void
    {
        $this->viewCount = $viewCount;
    }

    public function getCoverImage(): ObjectStorage
    {
        return $this->coverImage;
    }

    public function getFirstCoverImage(): ?FileReference
    {
        $this->coverImage->rewind();
        return $this->coverImage->current() ?: null;
    }

    public function setCoverImage(ObjectStorage $coverImage): void
    {
        $this->coverImage = $coverImage;
    }

    public function getCategories(): ObjectStorage
    {
        return $this->categories;
    }

    public function setCategories(ObjectStorage $categories): void
    {
        $this->categories = $categories;
    }

    public function getTags(): ObjectStorage
    {
        return $this->tags;
    }

    public function setTags(ObjectStorage $tags): void
    {
        $this->tags = $tags;
    }
}
