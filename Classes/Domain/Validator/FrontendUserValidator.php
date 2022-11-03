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
use Ydt\FrontendUser\Domain\Model\FrontendUser;

/**
 * Class FrontendUserValidator
 * Frontend user domain model validator
 */
class FrontendUserValidator extends AbstractValidator
{
    /**
     * @inheritdoc
     */
    protected function isValid($frontendUser): void
    {
        if (!$frontendUser instanceof FrontendUser) {
            $this->addError(
                $this->translateErrorMessage(
                    'validator.object.wrongClass',
                    'frontend_user'
                ),
                (int)uniqid()
            );

            return;
        }

        if (
            !$frontendUser->getPassword()
            || !$frontendUser->getPasswordConfirmation()
            || $frontendUser->getPassword() !== $frontendUser->getPasswordConfirmation()
        ) {
            $this->addError(
                $this->translateErrorMessage(
                    'validator.property.password.notMatched',
                    'frontend_user'
                ),
                (int)uniqid()
            );

            return;
        }
    }
}
