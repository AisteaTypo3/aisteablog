<?php

declare(strict_types=1);

namespace Aistea\Aisteablog\Controller;

use Aistea\Aisteablog\Domain\Model\Category;
use Aistea\Aisteablog\Domain\Model\Comment;
use Aistea\Aisteablog\Domain\Model\Post;
use Aistea\Aisteablog\Domain\Repository\CategoryRepository;
use Aistea\Aisteablog\Domain\Repository\CommentRepository;
use Aistea\Aisteablog\Domain\Repository\PostRepository;
use Aistea\Aisteablog\PageTitle\PostPageTitleProvider;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class PostController extends ActionController
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly CommentRepository $commentRepository,
        private readonly MetaTagManagerRegistry $metaTagManagerRegistry,
        private readonly PostPageTitleProvider $pageTitleProvider,
    ) {}

    public function listAction(int $currentPage = 1): ResponseInterface
    {
        $postsPerPage = (int)($this->settings['postsPerPage'] ?? 6);
        $posts = $this->postRepository->findAll();
        $paginator = new QueryResultPaginator($posts, $currentPage, $postsPerPage);

        if ($currentPage > 1) {
            // Follow-up pages get their own title and description instead of duplicating page 1.
            $pageTitle = (string)($this->getPageRecord()['title'] ?? '');
            $this->pageTitleProvider->setTitle($this->withPageNumber($pageTitle, $currentPage));
            $description = (string)($this->getPageRecord()['description'] ?? '');
            if ($description !== '') {
                $this->setMetaTag('description', $this->withPageNumber($description, $currentPage));
            }
        }

        $this->view->assignMultiple([
            'paginator'  => $paginator,
            'pagination' => new SimplePagination($paginator),
            'categories' => $this->categoryRepository->findAll(),
        ]);

        return $this->htmlResponse();
    }

    public function showAction(Post $post): ResponseInterface
    {
        $shareUrl = $this->uriBuilder
            ->reset()
            ->setCreateAbsoluteUri(true)
            ->uriFor('show', ['post' => $post]);

        $this->postRepository->incrementViewCount($post);
        $this->pageTitleProvider->setTitle($post->getTitle());
        $description = $this->shorten($post->getTeaser() !== '' ? $post->getTeaser() : strip_tags($post->getBodytext()));
        if ($description !== '') {
            $this->setMetaTag('description', $description);
        }
        $this->setOpenGraphTags($post, $shareUrl, $description);

        $this->view->assignMultiple([
            'post'       => $post,
            'shareUrl'   => $shareUrl,
            'categories' => $this->categoryRepository->findAll(),
            'comments'   => $this->commentRepository->findApprovedByPost($post),
            'newComment' => new Comment(),
        ]);

        return $this->htmlResponse();
    }

    public function categoryAction(Category $category, int $currentPage = 1): ResponseInterface
    {
        $postsPerPage = (int)($this->settings['postsPerPage'] ?? 6);
        $posts = $this->postRepository->findByCategory($category);
        $paginator = new QueryResultPaginator($posts, $currentPage, $postsPerPage);

        $title = (string)LocalizationUtility::translate('meta.category.title', 'Aisteablog', [$category->getTitle()]);
        $this->pageTitleProvider->setTitle($this->withPageNumber($title, $currentPage));
        $description = $category->getDescription() !== ''
            ? $this->shorten($category->getDescription())
            : (string)LocalizationUtility::translate('meta.category.description', 'Aisteablog', [$category->getTitle()]);
        $this->setMetaTag('description', $this->withPageNumber($description, $currentPage));
        $this->setMetaTag('og:title', $title);
        $this->setMetaTag('og:description', $description);

        $this->view->assignMultiple([
            'category'   => $category,
            'paginator'  => $paginator,
            'pagination' => new SimplePagination($paginator),
            'categories' => $this->categoryRepository->findAll(),
        ]);

        return $this->htmlResponse();
    }

    public function createCommentAction(Post $post, Comment $newComment): ResponseInterface
    {
        // Honeypot-Spam-Schutz
        $honeypot = $this->request->hasArgument('website') ? (string)$this->request->getArgument('website') : '';
        if ($honeypot !== '') {
            return $this->redirect('show', null, null, ['post' => $post]);
        }

        // Validierung
        $hasError = trim($newComment->getAuthorName()) === ''
            || !filter_var($newComment->getAuthorEmail(), FILTER_VALIDATE_EMAIL)
            || trim($newComment->getContent()) === '';

        if ($hasError) {
            $this->addFlashMessage(
                'Bitte fülle alle Pflichtfelder korrekt aus.',
                '',
                ContextualFeedbackSeverity::ERROR
            );
            return $this->redirect('show', null, null, ['post' => $post]);
        }

        $newComment->setPost($post->getUid());
        $newComment->_setProperty('pid', $post->getPid());
        $this->commentRepository->add($newComment);

        $this->sendCommentNotification($post, $newComment);

        $this->addFlashMessage(
            'Danke! Dein Kommentar wird nach Freigabe angezeigt.',
            '',
            ContextualFeedbackSeverity::OK
        );

        return $this->redirect('show', null, null, ['post' => $post]);
    }

    private function sendCommentNotification(Post $post, Comment $comment): void
    {
        $adminEmail = $this->settings['comments']['adminEmail'] ?? '';
        if ($adminEmail === '') {
            return;
        }

        $mail = GeneralUtility::makeInstance(MailMessage::class);
        $mail->to($adminEmail)
            ->subject('Neuer Kommentar: ' . $post->getTitle())
            ->html(sprintf(
                '<p><strong>Beitrag:</strong> %s</p>'
                . '<p><strong>Von:</strong> %s (%s)</p>'
                . '<p><strong>Kommentar:</strong></p>'
                . '<p>%s</p>',
                htmlspecialchars($post->getTitle()),
                htmlspecialchars($comment->getAuthorName()),
                htmlspecialchars($comment->getAuthorEmail()),
                nl2br(htmlspecialchars($comment->getContent()))
            ))
            ->send();
    }

    private function setOpenGraphTags(Post $post, string $shareUrl, string $description): void
    {
        $tags = [
            'og:type'             => 'article',
            'og:title'            => $post->getTitle(),
            'og:description'      => $description,
            'og:url'              => $shareUrl,
            'twitter:card'        => 'summary_large_image',
            'twitter:title'       => $post->getTitle(),
            'twitter:description' => $description,
        ];

        $coverImage = $post->getFirstCoverImage();
        if ($coverImage !== null) {
            $imageUrl = $coverImage->getOriginalResource()->getPublicUrl();
            if ($imageUrl !== null && $imageUrl !== '') {
                if (!str_starts_with($imageUrl, 'http')) {
                    $normalizedParams = $this->request->getAttribute('normalizedParams');
                    $imageUrl = rtrim($normalizedParams->getSiteUrl(), '/') . '/' . ltrim($imageUrl, '/');
                }
                $tags['og:image']      = $imageUrl;
                $tags['og:image:alt']  = $post->getTitle();
                $tags['twitter:image'] = $imageUrl;
            }
        }

        foreach ($tags as $property => $value) {
            if ($value !== '') {
                $this->setMetaTag($property, $value);
            }
        }
    }

    /**
     * Replaces a tag set by the page record (e.g. the blog page's own description).
     */
    private function setMetaTag(string $property, string $value): void
    {
        $this->metaTagManagerRegistry
            ->getManagerForProperty($property)
            ->addProperty($property, $value, [], true);
    }

    private function withPageNumber(string $text, int $currentPage): string
    {
        if ($currentPage <= 1 || $text === '') {
            return $text;
        }

        return $text . ' – ' . LocalizationUtility::translate('meta.pageNumber', 'Aisteablog', [$currentPage]);
    }

    /**
     * Plain text, at most ~160 characters, cut at a word boundary.
     */
    private function shorten(string $text, int $maxLength = 160): string
    {
        $text = trim((string)preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }
        $cut = mb_substr($text, 0, $maxLength - 1);
        $lastSpace = mb_strrpos($cut, ' ');

        return rtrim($lastSpace !== false && $lastSpace > $maxLength * 0.6 ? mb_substr($cut, 0, $lastSpace) : $cut, ' ,.;:–-') . '…';
    }

    /**
     * @return array<string, mixed>
     */
    private function getPageRecord(): array
    {
        $pageInformation = $this->request->getAttribute('frontend.page.information');

        return $pageInformation !== null ? $pageInformation->getPageRecord() : [];
    }
}
