<?php

return [
    'de_de' => [
        'morph' => [
            'dictionariesFolder' => 'package',
            'options' => [
                'storage' => 'file',
                'predict_by_suffix' => true,
                'predict_by_db' => true,
                'graminfo_as_text' => true,
            ],
        ],
        'stopWords' => [
            'winzer eg',
            'weingut',
            'producer',
        ],
        'wordParserFilters' => [
            \Morphy\FuzzyKeywordSearch\StringFilters\StringToLower::class,
            \Morphy\FuzzyKeywordSearch\StringFilters\StopWordFilter::class,
            \Morphy\FuzzyKeywordSearch\StringFilters\HtmlLineBreaksToSingleBreakFilter::class,
            \Morphy\FuzzyKeywordSearch\StringFilters\StripTagsFilter::class,
            \Morphy\FuzzyKeywordSearch\StringFilters\DotAndCommaToSingleSpaceFilter::class,
            \Morphy\FuzzyKeywordSearch\StringFilters\MultipleLineBreaksToSingleSpaceFilter::class,
            \Morphy\FuzzyKeywordSearch\StringFilters\MultipleSpacesToSingleSpaceFilter::class,
            \Morphy\FuzzyKeywordSearch\StringFilters\RemoveExtraSpacesFilter::class,
        ],
        \Morphy\SemanticText\SemanticObjectRepositoryInterface::class => null,
        'cache' => [
            'driver' => 'array',
            'ttl' => 5 * 60,
            'key' => 'some_key',
        ],
    ],
    'en_en' => [
        'morph' => [
            'dictionariesFolder' => 'package',
            'options' => [
                'storage' => 'file',
                'predict_by_suffix' => true,
                'predict_by_db' => true,
                'graminfo_as_text' => true,
            ],
        ],
        'stopWords' => [
            'Producer',
        ],
        'wordParserFilters' => [
            \Morphy\FuzzyKeywordSearch\StringFilters\StringToLower::class,
            \Morphy\FuzzyKeywordSearch\StringFilters\StopWordFilter::class,
            \Morphy\FuzzyKeywordSearch\StringFilters\HtmlLineBreaksToSingleBreakFilter::class,
            \Morphy\FuzzyKeywordSearch\StringFilters\StripTagsFilter::class,
            \Morphy\FuzzyKeywordSearch\StringFilters\DotAndCommaToSingleSpaceFilter::class,
            \Morphy\FuzzyKeywordSearch\StringFilters\MultipleLineBreaksToSingleSpaceFilter::class,
            \Morphy\FuzzyKeywordSearch\StringFilters\MultipleSpacesToSingleSpaceFilter::class,
            \Morphy\FuzzyKeywordSearch\StringFilters\RemoveExtraSpacesFilter::class,
        ],
        \Morphy\SemanticText\SemanticObjectRepositoryInterface::class => null,
        'cache' => [
            'driver' => 'file',
            'ttl' => 5 * 60,
            'key' => 'some_key_en',
        ],
    ],
];
