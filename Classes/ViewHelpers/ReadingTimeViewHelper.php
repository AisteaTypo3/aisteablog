<?php

declare(strict_types=1);

namespace Aistea\Aisteablog\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Returns the estimated reading time in minutes.
 * Usage: <blog:readingTime text="{post.bodytext}" />
 */
class ReadingTimeViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('text', 'string', 'Text to calculate reading time for', true);
        $this->registerArgument('wordsPerMinute', 'int', 'Reading speed in words per minute', false, 200);
    }

    public function render(): int
    {
        $plain = strip_tags((string)$this->arguments['text']);
        $words = str_word_count($plain);
        return max(1, (int) round($words / (int)$this->arguments['wordsPerMinute']));
    }
}
