<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Validation\Validator;

use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Class DigitValidator
 * Validator for digital strings
 */
class DigitValidator extends AbstractValidator
{
    /**
     * Check if all characters in the given string value are numerical
     *
     * @param mixed $value
     * @return void
     */
    protected function isValid($value): void
    {
        if (!is_string($value) || !ctype_digit($value)) {
            $this->addError(
                $this->translateErrorMessage(
                    'validator.digit.notValid',
                    'frontend_user'
                ),
                (int)uniqid()
            );

            return;
        }
    }
}
