<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Tests\Unit\Domain\Validator;

use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LocalizationFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Error\Result;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Ydt\FrontendUser\Domain\Validator\PhoneValidator;
use ReflectionClass;

/**
 * Class PhoneValidatorTest
 * Testcase for class \Ydt\FrontendUser\Domain\Validator\PhoneValidator
 */
class PhoneValidatorTest extends UnitTestCase
{
    /**
     * Phone Validator
     *
     * @var PhoneValidator
     */
    private $subject;

    /**
     * @inheritDoc
     */
    protected $resetSingletonInstances = true;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $languageServiceMock = $this->createMock(LanguageService::class);
        $GLOBALS['LANG'] = $languageServiceMock;

        $localizationFactoryMock = $this->createMock(LocalizationFactory::class);
        $builder = $localizationFactoryMock->method('getParsedData');
        $builder->willReturn([]);

        GeneralUtility::setSingletonInstance(LocalizationFactory::class, $localizationFactoryMock);

        $configurationManagerMock = $this->createMock(ConfigurationManagerInterface::class);
        $builder = $configurationManagerMock->method('getConfiguration');
        $builder->with(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK, 'frontend_user');
        $builder->willReturn([]);

        GeneralUtility::setSingletonInstance(ConfigurationManagerInterface::class, $configurationManagerMock);

        $this->subject = new PhoneValidator();
    }

    /**
     * Test validate
     *
     * @return void
     */
    public function testValidate(): void
    {
        $result = $this->subject->validate('+123(45)6789 ');

        $this->assertInstanceOf(Result::class, $result);
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        $reflectionClass = new ReflectionClass(LocalizationUtility::class);
        $property = $reflectionClass->getProperty('configurationManager');
        $property->setAccessible(true);
        $property->setValue(null);

        GeneralUtility::purgeInstances();
        parent::tearDown();
    }
}
