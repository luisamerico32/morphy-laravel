<?php

namespace Morphy\FuzzyKeywordSearch\Word;

use Doctrine\Common\Collections\ArrayCollection;

final class WordCollection extends ArrayCollection
{
    public function toString(): string
    {
        $originalWords = [];

        /** @var Word $word */
        foreach ($this as $word) {
            $originalWords[] = $word->originalForm;
        }

        return implode(' ', $originalWords);
    }

    public function containsWord(Word $exceptedWord): bool
    {
        /** @var Word $word */
        foreach ($this as $word) {
            if ($word->originalForm === $exceptedWord->originalForm || $word->baseForm === $exceptedWord->baseForm) {
                return true;
            }
        }

        return false;
    }

    public function intersect(self $otherWordCollection): self
    {
        $intersectedWords = [];

        foreach ($this as $word) {
            if (!$otherWordCollection->containsWord($word)) {
                continue;
            }

            $intersectedWords[] = $word;
        }

        return new self($intersectedWords);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     *
     * @param self $otherWordCollection
     */
    public function containsWords($otherWordCollection): bool
    {
        foreach ($otherWordCollection as $word) {
            if (!$this->containsWord($word)) {
                return false;
            }
        }

        return true;
    }

    public function groupByAdjacentWords(int $groupSize): GroupedWordsCollection
    {
        if ($groupSize <= 0) {
            return new GroupedWordsCollection();
        }

        $groups = [];

        foreach ($this as $word) {
            $adjacentWords = $this->findAdjacentWordsOnRight($word, $groupSize);

            if (count($adjacentWords) !== $groupSize) {
                continue;
            }

            $groups[] = $adjacentWords;
        }

        return new GroupedWordsCollection($groups);
    }

    private function findAdjacentWordsOnRight(Word $word, int $wordCount): self
    {
        $adjacentWordsOnRight = new self([$word]);
        /** @var Word $nextWord */
        $nextWord = $this->get($this->indexOf($word) + 1);

        if ($wordCount < 2) {
            return $adjacentWordsOnRight;
        }

        if ($nextWord && $nextWord->isNear($word)) {
            return $adjacentWordsOnRight->merge($this->findAdjacentWordsOnRight($nextWord, $wordCount - 1));
        }

        return $adjacentWordsOnRight;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function merge(self $words): self
    {
        return new self(array_merge(iterator_to_array($this), iterator_to_array($words)));
    }
}
