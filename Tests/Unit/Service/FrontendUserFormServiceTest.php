<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Tests\Unit\Service;

use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Ydt\FrontendUser\Service\FrontendUserFormService;
use ReflectionClass;
use ReflectionException;

/**
 * Class FrontendUserFormServiceTest
 * Testcase for class \Ydt\FrontendUser\Service\FrontendUserFormService
 */
class FrontendUserFormServiceTest extends UnitTestCase
{
    /**
     * Frontend User Form Service
     *
     * @var FrontendUserFormService
     */
    private $subject;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $languageServiceMock = $this->createMock(LanguageService::class);
        $GLOBALS['LANG'] = $languageServiceMock;

        $this->subject = new FrontendUserFormService();
    }

    /**
     * Test getFieldItems
     *
     * @return void
     */
    public function testGetFieldItems(): void
    {
        $configuration = ['items' => []];

        $this->subject->getFieldItems($configuration);
    }

    /**
     * Test getLanguageService
     *
     * @return void
     * @throws ReflectionException
     */
    public function testGetLanguageService(): void
    {
        $reflection = new ReflectionClass(FrontendUserFormService::class);
        $method = $reflection->getMethod('getLanguageService');
        $method->setAccessible(true);

        $result = $method->invoke($this->subject);

        $this->assertInstanceOf(LanguageService::class, $result);
    }
}
