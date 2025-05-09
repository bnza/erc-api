<?php
putenv('PHP_CS_FIXER_IGNORE_ENV=1');

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->exclude('vendor')
    ->exclude('packages');


return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'global_namespace_import' => ['import_classes' => false],
    ])
    ->setFinder($finder);
