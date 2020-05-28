<?php

$finder = \PhpCsFixer\Finder::create()
    ->in(['src'])
;
return \PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        '@PHP71Migration' => true,
        'concat_space' => ['spacing' => 'one'],
        'phpdoc_summary' => false,
        'phpdoc_align' => false,
        'no_short_echo_tag' => true,
        'no_useless_else' => true,
        'is_null' => true,
        'multiline_whitespace_before_semicolons' => true,
        'list_syntax' => ['syntax' => 'short'],
        'array_syntax' => ['syntax' => 'short'],
        'php_unit_strict' => false,
        'strict_comparison' => true,
        'strict_param' => true,
        'declare_strict_types' => true,
        'yoda_style' => false,
        'ordered_class_elements' => true,
        'date_time_immutable' => true,
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'native_function_invocation' => [
            'include' => ['@compiler_optimized']
        ],
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline'
        ],
        'fully_qualified_strict_types' => true,
        'no_unreachable_default_argument_value' => true,
        'static_lambda' => true,
        'no_superfluous_phpdoc_tags' => false,
        'single_line_throw' => false,
    ])
    ->setFinder($finder)
;
