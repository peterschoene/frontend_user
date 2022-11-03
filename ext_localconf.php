<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use Ydt\FrontendUser\Controller\FrontendUserController;
use Ydt\FrontendUser\Property\TypeConverter\UploadedFileReferenceConverter;

defined('TYPO3') or die();

(function () {
    ExtensionManagementUtility::addTypoScriptConstants(
        "@import 'EXT:frontend_user/Configuration/TypoScript/constants.typoscript'"
    );
    ExtensionManagementUtility::addTypoScriptSetup(
        "@import 'EXT:frontend_user/Configuration/TypoScript/setup.typoscript'"
    );

    ExtensionUtility::configurePlugin(
        'FrontendUser',
        'Form',
        [FrontendUserController::class => 'new, edit, create, update, delete, deleteImage'],
        [FrontendUserController::class => 'new, edit, create, update, delete, deleteImage'],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    ExtensionManagementUtility::addPageTSConfig(
        "@import 'EXT:frontend_user/Configuration/TsConfig/Page/Mod/Wizards/NewContentElement.tsconfig'"
    );

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['ydt'] = ['Ydt\FrontendUser\ViewHelpers'];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['EXT:core/Resources/Private/Language/locallang_general.xlf'][]
        = 'EXT:frontend_user/Resources/Private/Language/locallang_be.xlf';
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['de']['EXT:core/Resources/Private/Language/locallang_general.xlf'][]
        = 'EXT:frontend_user/Resources/Private/Language/de.locallang_be.xlf';

    ExtensionUtility::registerTypeConverter(UploadedFileReferenceConverter::class);
})();
