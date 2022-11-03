<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Enumeration\FrontendUser;

use TYPO3\CMS\Core\Type\Enumeration;

/**
 * Class Property
 * List of frontend user properties
 */
final class Property extends Enumeration
{
    /**
     * List of frontend user properties
     */
    const COMPANY = 'company';
    const JOB_TITLE = 'jobTitle';
    const NAME = 'name';
    const FIRST_NAME = 'firstName';
    const MIDDLE_NAME = 'middleName';
    const LAST_NAME = 'lastName';
    const STREET_ADDRESS = 'streetAddress';
    const ZIP_CODE = 'zipCode';
    const CITY = 'city';
    const COUNTRY = 'country';
    const PHONE = 'phone';
    const FAX = 'fax';
    const EMAIL = 'email';
    const URL = 'url';
    const IMAGE = 'image';
}
