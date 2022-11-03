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
use Ydt\FrontendUser\Domain\Model\FrontendUser;
use Ydt\FrontendUser\Domain\Validator\FrontendUserValidator;
use ReflectionClass;

/**
 * Class FrontendUserValidatorTest
 * Testcase for class \Ydt\FrontendUser\Domain\Validator\FrontendUserValidator
 */
class FrontendUserValidatorTest extends UnitTestCase
{
    /**
     * Frontend User Validator
     *
     * @var FrontendUserValidator
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

        $this->subject = new FrontendUserValidator();
    }

    /**
     * Test isValid
     *
     * @dataProvider frontendUserDataProvider
     *
     * @param FrontendUser|string $frontendUser
     * @return void
     */
    public function testValidate($frontendUser): void
    {
        $result = $this->subject->validate($frontendUser);

        $this->assertInstanceOf(Result::class, $result);
    }

    /**
     * Data provider for validate
     *
     * @return array
     */
    public function frontendUserDataProvider(): array
    {
        $frontendUserMock = $this->createMock(FrontendUser::class);

        $builder = $frontendUserMock->method('getPassword');
        $builder->willReturn('test132');
        $builder = $frontendUserMock->method('getPasswordConfirmation');
        $builder->willReturn('test123');

        return [
            ['Frontend User'],
            [$frontendUserMock],
        ];
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
