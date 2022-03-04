<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

/**
 * For more information about this configuration you can go to
 * the following website to see more about this config when
 * you click on the "folder" icon on the website.
 *
 * https://mlocati.github.io/php-cs-fixer-configurator/#configurator
 */
$rules = [
    // Rule sets
    '@PSR12' => true,
    '@PSR12:risky' => true,

    // Alias
    'no_alias_functions' => true,
    'no_mixed_echo_print' => true,

    // Array Notation
    'array_syntax' => ['syntax' => 'short'],
    'no_multiline_whitespace_around_double_arrow' => true,
    'no_trailing_comma_in_singleline_array' => true,
    'no_whitespace_before_comma_in_array' => true,
    'normalize_index_brace' => true,
    'trim_array_spaces' => true,
    'whitespace_after_comma_in_array' => true,

    // Basic
    'psr_autoloading' => true,

    // Casing
    'magic_constant_casing' => true,
    'magic_method_casing' => true, // added from Symfony
    'native_function_casing' => true,

    // Cast Notation
    'cast_spaces' => true,
    'no_short_bool_cast' => true,

    // Class Notation
    'class_attributes_separation' => true,
    'self_accessor' => true,

    // Class Usage
    // no rules.

    // Comment
    'single_line_comment_style' => [
        'comment_types' => ['hash'],
    ],

    // Constant Notation
    // no rules.

    // Control Structure
    'include' => true,
    'no_trailing_comma_in_list_call' => true,
    'no_unneeded_control_parentheses' => true,
    'trailing_comma_in_multiline' => [
        'elements' => ['arrays'],
        'after_heredoc' => true,
    ],

    // Doctrine Annotation
    // no rules, doctrine is not used.

    // Function Notation
    'function_typehint_space' => true,
    'no_unreachable_default_argument_value' => true,
    'phpdoc_to_return_type' => true,

    // Import
    'fully_qualified_strict_types' => true, // added
    'no_unused_imports' => true,
    'ordered_imports' => [
        'sort_algorithm' => 'alpha',
    ],

    // Language Construct
    // no rules.

    // List Notation
    // no rules.

    // Namespace Notation
    'no_leading_namespace_whitespace' => true,

    // Naming
    // no rules.

    // Operator
    'binary_operator_spaces' => [
        'operators' => [
            '=' => 'single_space',
            '=>' => 'single_space',
        ],
    ],
    'concat_space' => ['spacing' => 'one'],
    'increment_style' => ['style' => 'post'],
    'not_operator_with_successor_space' => true,
    'object_operator_without_whitespace' => true,
    'operator_linebreak' => [
        'only_booleans' => true,
        'position' => 'beginning',
    ],
    'standardize_not_equals' => true,
    'unary_operator_spaces' => true,

    // PHP Tag
    'linebreak_after_opening_tag' => true,

    // PHPUnit
    'php_unit_expectation' => true,
    'php_unit_method_casing' => ['case' => 'snake_case'],
    'php_unit_mock_short_will_return' => true,
    'php_unit_no_expectation_annotation' => true,
    'php_unit_test_annotation' => ['style' => 'prefix'],
    'php_unit_test_case_static_method_calls' => ['call_type' => 'this'],

    // PHPDoc
    'no_blank_lines_after_phpdoc' => true,
    'no_empty_phpdoc' => true,
    'phpdoc_add_missing_param_annotation' => true,
    'phpdoc_indent' => true,
    'phpdoc_inline_tag_normalizer' => true,
    'phpdoc_no_access' => true,
    'phpdoc_no_package' => true,
    'phpdoc_no_useless_inheritdoc' => true,
    'phpdoc_scalar' => true,
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_summary' => true,
    'phpdoc_tag_type' => true,
    'phpdoc_to_comment' => true,
    'phpdoc_trim' => true,
    'phpdoc_types' => true,
    'phpdoc_var_without_name' => true,

    // Return Notation
    'no_useless_return' => true,
    'simplified_null_return' => true,

    // Semicolon
    'multiline_whitespace_before_semicolons' => true,
    'no_empty_statement' => true,
    'no_singleline_whitespace_before_semicolons' => true,
    'space_after_semicolon' => true,

    // Strict
    'declare_strict_types' => true,

    // String Notation
    'explicit_string_variable' => true,
    'heredoc_to_nowdoc' => true,
    'simple_to_complex_string_variable' => true,
    'single_quote' => true,

    // Whitespace
    'blank_line_before_statement' => true,
    'no_extra_blank_lines' => [
        'tokens' => [
            'extra',
            'throw',
            'use',
            'use_trait',
        ],
    ],
    'no_spaces_around_offset' => true,
];

$finder = Finder::create()
    ->in(__DIR__)
    ->path([
        'app',
        'config',
        'database',
        'routes',
        'tests',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setFinder($finder)
    ->setRules($rules)
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.cache')
    ->setRiskyAllowed(true)
    ->setUsingCache(true);
