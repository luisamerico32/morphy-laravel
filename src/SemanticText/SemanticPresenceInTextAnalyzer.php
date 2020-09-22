<?php

namespace Morphy\SemanticText;

use Morphy\FuzzyKeywordSearch\FuzzyKeywordSearcher;
use Morphy\FuzzyKeywordSearch\Word\Factory\WordCollectionFactory;
use Morphy\FuzzyKeywordSearch\Word\WordCollection;

class SemanticPresenceInTextAnalyzer
{
    private $wordCollectionFactory;
    private $fuzzyKeywordSearchService;
    private $semanticObjectRepository;

    public function __construct(
        FuzzyKeywordSearcher $fuzzyKeywordSearchService,
        WordCollectionFactory $wordCollectionFactory,
        SemanticObjectRepositoryInterface $semanticObjectRepository
    ) {
        $this->fuzzyKeywordSearchService = $fuzzyKeywordSearchService;
        $this->wordCollectionFactory = $wordCollectionFactory;
        $this->semanticObjectRepository = $semanticObjectRepository;
    }

    /**
     * @return SemanticMatch[]
     */
    public function analyzeText(string $text): array
    {
        $text = mb_strtolower($text);

        $originalWords = $this->wordCollectionFactory->createFromString($text);

        $foundCombinationsWordsSemanticMatches = [];

        foreach ($this->semanticObjectRepository->findAllForSemanticAnalyze() as $semanticModel) {
            $searchWords = $this->wordCollectionFactory->createFromString($semanticModel->getText());

            $foundSimilarKeyword = $this->findSemanticWordInText($text, $originalWords, $searchWords);

            if (empty($foundSimilarKeyword)) {
                continue;
            }

            if ($this->isHaveAdvancedSemanticObject($semanticModel, $foundCombinationsWordsSemanticMatches)) {
                continue;
            }

            $foundCombinationsWordsSemanticMatches[] = new SemanticMatch($semanticModel, $foundSimilarKeyword);
        }

        return $foundCombinationsWordsSemanticMatches;
    }

    private function findSemanticWordInText(string $text, WordCollection $originalWords, WordCollection $searchWords): string
    {
        return $this->fuzzyKeywordSearchService->searchKeywordInSourceString(
            $text,
            $originalWords,
            $searchWords
        );
    }

    /**
     * @param SemanticMatch[] $foundCombinationsWordsSemanticMatches
     */
    private function isHaveAdvancedSemanticObject(SemanticObjectInterface $semanticObject, iterable $foundCombinationsWordsSemanticMatches): bool
    {
        foreach ($foundCombinationsWordsSemanticMatches as $foundCombinationsWordsSemanticMatch) {
            $positionStartSemanticObjectText = mb_stripos($foundCombinationsWordsSemanticMatch->semanticObject->getText(), $semanticObject->getText());

            if ($positionStartSemanticObjectText !== false) {
                return true;
            }
        }

        return false;
    }
}
