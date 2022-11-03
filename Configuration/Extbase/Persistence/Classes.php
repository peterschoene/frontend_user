<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

use Ydt\FrontendUser\Domain\Model\FrontendUser;
use Ydt\FrontendUser\Domain\Model\FrontendUserGroup;

return [
    FrontendUser::class => [
        'tableName' => 'fe_users',
        'properties' => [
            'userGroups' => [
                'fieldName' => 'usergroup',
            ],
            'jobTitle' => [
                'fieldName' => 'title',
            ],
            'streetAddress' => [
                'fieldName' => 'address',
            ],
            'zipCode' => [
                'fieldName' => 'zip',
            ],
            'phone' => [
                'fieldName' => 'telephone',
            ],
            'url' => [
                'fieldName' => 'www',
            ],
            'images' => [
                'fieldName' => 'image',
            ],
            'lastLogin' => [
                'fieldName' => 'lastlogin',
            ],
        ],
    ],
    FrontendUserGroup::class => [
        'tableName' => 'fe_groups',
        'properties' => [
            'subgroups' => [
                'fieldName' => 'subgroup',
            ],
        ],
    ],
];
