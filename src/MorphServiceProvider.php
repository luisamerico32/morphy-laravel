<?php

namespace LaravelMorphy;

use Morphy\FuzzyKeywordSearch\StringFilters\StopWordFilter;
use Morphy\FuzzyKeywordSearch\Analyzer\PresenceGroupsWordsInStringAnalyzer;
use Morphy\FuzzyKeywordSearch\FuzzyKeywordSearcher;
use Morphy\FuzzyKeywordSearch\Word\Factory\WordCollectionFactory;
use Morphy\FuzzyKeywordSearch\Word\Factory\WordFactory;
use Morphy\FuzzyKeywordSearch\Word\Factory\WordsParser;
use Morphy\SemanticText\SemanticObjectRepositoryInterface;
use Morphy\SemanticText\SemanticPresenceInTextAnalyzer;

class MorphServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/morph.php' => config_path('morph.php'),
        ], 'config');

        foreach ($this->app['config']->get('morph') as $locale => $settings) {
            $shortLocale = substr($locale, 0, 2);

            $this->app->singleton($shortLocale.'PhpMorphy', function () use ($locale, $settings) {
                $dictionariesFolder = $settings['morph']['dictionariesFolder'] !== 'package' ? $settings['morph']['dictionariesFolder'] : __DIR__.'/dictionaries';

                return new \phpMorphy(
                    $dictionariesFolder,
                    $locale,
                    $settings['morph']['options']
                );
            });

            $this->app->singleton($shortLocale.'StopWordFilter', function () use ($settings) {
                return new StopWordFilter($settings['stopWords']);
            });

            $this->app->singleton($shortLocale.'WordsParser', function () use ($settings, $shortLocale) {
                return new WordsParser(array_map(function ($filter) use ($shortLocale) {
                    if ($filter === StopWordFilter::class) {
                        return $this->app->get($shortLocale.'StopWordFilter');
                    }

                    return new $filter();
                }, $settings['wordParserFilters']));
            });

            $this->app->singleton($shortLocale.'WordCollectionFactory', function () use ($shortLocale) {
                return new WordCollectionFactory(
                    $this->app->get($shortLocale.'WordsParser'),
                    new WordFactory($this->app->get($shortLocale.'PhpMorphy'))
                );
            });

            $this->app->singleton($shortLocale.'SemanticPresenceInTextAnalyzer', function () use ($settings, $shortLocale) {
                return new SemanticPresenceInTextAnalyzer(
                    new FuzzyKeywordSearcher(new PresenceGroupsWordsInStringAnalyzer()),
                    $this->app->get($shortLocale.'WordCollectionFactory'),
                    $this->app->get($settings[SemanticObjectRepositoryInterface::class])
                );
            });
        }
    }
}
