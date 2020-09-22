<?php

namespace Morphy\FuzzyKeywordSearch\Word;

use Doctrine\Common\Collections\ArrayCollection;

final class GroupedWordsCollection extends ArrayCollection
{
    public function filterContainingAllWords(WordCollection $searchWords): GroupedWordsCollection
    {
        return $this->filter(static function (WordCollection $sourceWords) use ($searchWords) {
            return $sourceWords->containsWords($searchWords);
        });
    }
}
