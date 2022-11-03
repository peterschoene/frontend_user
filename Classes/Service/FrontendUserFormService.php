<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Service;

use TYPO3\CMS\Core\Localization\LanguageService;
use Ydt\FrontendUser\Enumeration\FrontendUser\Property as FrontendUserProperty;

/**
 * Class FrontendUserFormService
 * Frontend user form service
 */
class FrontendUserFormService
{
    /**
     * itemsProcFunc for getting new/edit frontend user form fields
     *
     * @param array $configuration
     * @return void
     */
    public function getFieldItems(array &$configuration): void
    {
        $languageService = $this->getLanguageService();

        $items = [];
        foreach (FrontendUserProperty::getConstants() as $property) {
            $items[] = [
                $languageService->sL('LLL:EXT:frontend_user/Resources/Private/Language/locallang.xlf:' . $property),
                $property,
            ];
        }

        $configuration['items'] = $items;
    }

    /**
     * Get language service
     *
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
