<?php

declare(strict_types=1);

namespace Aistea\Aisteablog\Controller;

use Aistea\Aisteablog\Domain\Model\Category;
use Aistea\Aisteablog\Domain\Model\Post;
use Aistea\Aisteablog\Domain\Repository\CategoryRepository;
use Aistea\Aisteablog\Domain\Repository\PostRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;

class PostController extends ActionController
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly CategoryRepository $categoryRepository,
    ) {}

    public function listAction(int $currentPage = 1): ResponseInterface
    {
        $postsPerPage = (int)($this->settings['postsPerPage'] ?? 6);
        $posts = $this->postRepository->findAll();
        $paginator = new QueryResultPaginator($posts, $currentPage, $postsPerPage);

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

        $this->view->assignMultiple([
            'post'       => $post,
            'shareUrl'   => $shareUrl,
            'categories' => $this->categoryRepository->findAll(),
            'prevPost'   => $this->postRepository->findPreviousPost($post),
            'nextPost'   => $this->postRepository->findNextPost($post),
        ]);

        return $this->htmlResponse();
    }

    public function categoryAction(Category $category, int $currentPage = 1): ResponseInterface
    {
        $postsPerPage = (int)($this->settings['postsPerPage'] ?? 6);
        $posts = $this->postRepository->findByCategory($category);
        $paginator = new QueryResultPaginator($posts, $currentPage, $postsPerPage);

        $this->view->assignMultiple([
            'category'   => $category,
            'paginator'  => $paginator,
            'pagination' => new SimplePagination($paginator),
            'categories' => $this->categoryRepository->findAll(),
        ]);

        return $this->htmlResponse();
    }
}
