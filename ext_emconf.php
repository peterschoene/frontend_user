<?php

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'Frontend User',
    'description' => 'The template-based plugin for managing website users on the TYPO3 frontend',
    'version' => '1.1.1',
    'category' => 'fe',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.99.99',
            'extbase' => '11.5.0-11.99.99',
            'fluid' => '11.5.0-11.99.99',
            'frontend' => '11.5.0-11.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'author_email' => 'typo3@ydt-global.com',
    'author' => 'YDT Global Team',
    'author_company' => 'Your Dev Team Global',
    'autoload' => [
        'psr-4' => [
            'Ydt\\FrontendUser\\' => 'Classes',
        ],
    ],
];
