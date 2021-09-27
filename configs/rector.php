<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\ClassMethod\UnSpreadOperatorRector;
use Rector\CodingStyle\Rector\PostInc\PostIncDecToPreIncDecRector;
use Rector\Core\Configuration\Option;
use Rector\Php71\Rector\FuncCall\CountOnNullRector;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Php74\Rector\Property\RestoreDefaultNullToNullableTypePropertyRector;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\Property\PropertyTypeDeclarationRector;
use Rector\DowngradePhp70\Rector\GroupUse\SplitGroupedUseImportsRector;
use Rector\CodeQuality\Rector\ClassMethod\DateTimeToDateTimeInterfaceRector;
use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Code\ComposerLoader;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    // Get settings from extra section of composer and provide access for them
    $composerLoader = new ComposerLoader();

    // Paths to refactor
    $parameters->set(Option::PATHS, $composerLoader->getAbsolutePaths('rector.paths'));

    // These are the files to be skipped
    $parameters->set(Option::SKIP, array_merge($composerLoader->getAbsolutePaths('rector.skip'), [
            RestoreDefaultNullToNullableTypePropertyRector::class, // don't work with DTO nullable parameter
            RemoveExtraParametersRector::class, // catting an argument in dump() function
            SplitGroupedUseImportsRector::class, // doesn't work with insteadof resolve naming conflicts between Traits
//            CamelCaseFunctionNamingToUnderscoreRector::class, // it's change helpers
            UnSpreadOperatorRector::class, // it's breaks the middleware
            CountOnNullRector::class, // this rule does not fit, a lot of where it goes wrong
            //THINKING
            DateTimeToDateTimeInterfaceRector::class,
            FlipTypeControlToUseExclusiveTypeRector::class,
            PostIncDecToPreIncDecRector::class
    ]));

    $containerConfigurator->import(SetList::PHP_70);
    $containerConfigurator->import(SetList::PHP_71);
    $containerConfigurator->import(SetList::PHP_72);
    $containerConfigurator->import(SetList::PHP_73);
    $containerConfigurator->import(SetList::PHP_74);
    $containerConfigurator->import(SetList::PHP_80);
    $containerConfigurator->import(SetList::CODE_QUALITY);
    $containerConfigurator->import(SetList::CODING_STYLE);

    // Auto import fully qualified class names
    $parameters->set(Option::AUTO_IMPORT_NAMES, true);

    // Skip classes used in PHP DocBlocks
    $parameters->set(Option::IMPORT_DOC_BLOCKS, false);

//    $containerConfigurator->import(SetList::LARAVEL_STATIC_TO_INJECTION);

    // Get services
    $services = $containerConfigurator->services();

    // Register single rule
    $services->set(TypedPropertyRector::class);
    $services->set(PropertyTypeDeclarationRector::class);
};
