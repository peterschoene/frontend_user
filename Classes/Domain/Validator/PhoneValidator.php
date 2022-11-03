<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Domain\Validator;

use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Class PhoneValidator
 * Phone frontend user domain model property validator
 */
class PhoneValidator extends AbstractValidator
{
    /**
     * @inheritdoc
     */
    protected function isValid($value): void
    {
        if (!is_string($value) || !(preg_match('/^[\d+](?:[\d]*\([\d]*\)){0,1}[\d\s\-]*\d$/u', $value))) {
            $this->addError(
                $this->translateErrorMessage(
                    'validator.property.phone.notValid',
                    'frontend_user'
                ),
                (int)uniqid()
            );

            return;
        }
    }
}
