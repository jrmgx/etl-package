<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->notPath('node_modules')
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'linebreak_after_opening_tag' => true,
        'mb_str_functions' => true,
        'no_php4_constructor' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'php_unit_strict' => true,
        'phpdoc_order' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'no_multiline_whitespace_around_double_arrow' => false,
        'concat_space' => ['spacing' => 'one'],
    ])
    ->setFinder($finder)
;
