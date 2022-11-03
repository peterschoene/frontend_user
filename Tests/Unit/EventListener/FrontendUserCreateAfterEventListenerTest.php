<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Tests\Unit\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Ydt\FrontendUser\EventListener\FrontendUserCreateAfterEventListener;
use Ydt\FrontendUser\Authentication\FrontendUserAuthentication;
use Ydt\FrontendUser\Event\FrontendUserCreateAfterEvent;
use Psr\Http\Message\ServerRequestInterface;
use Ydt\FrontendUser\Domain\Model\FrontendUser;

/**
 * Class FrontendUserCreateAfterEventListenerTest
 * Testcase for class \Ydt\FrontendUser\EventListener\FrontendUserCreateAfterEventListener
 */
class FrontendUserCreateAfterEventListenerTest extends UnitTestCase
{
    /**
     * Frontend User Create After Event Listener
     *
     * @var FrontendUserCreateAfterEventListener
     */
    private $subject;

    /**
     * Configuration Manager Mock
     *
     * @var MockObject
     */
    private $configurationManagerMock;

    /**
     * Frontend User Authentication Mock
     *
     * @var MockObject
     */
    private $frontendUserAuthenticationMock;

    /**
     * Logger Mock
     *
     * @var MockObject|LoggerInterface
     */
    private $loggerMock;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->configurationManagerMock = $this->createMock(ConfigurationManager::class);
        $this->frontendUserAuthenticationMock = $this->createMock(FrontendUserAuthentication::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->subject = new FrontendUserCreateAfterEventListener(
            $this->frontendUserAuthenticationMock,
            $this->configurationManagerMock
        );

        $this->subject->setLogger($this->loggerMock);
    }

    /**
     * Test __invoke
     *
     * @return void
     */
    public function testInvoke(): void
    {
        $builder = $this->configurationManagerMock->method('getConfiguration');
        $builder->with(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS);
        $builder->willReturn(['enableFrontendUserAutoLogin' => true]);

        $frontendUserMock = $this->createMock(FrontendUser::class);
        $requestMock = $this->createMock(ServerRequestInterface::class);

        $event = new FrontendUserCreateAfterEvent($frontendUserMock, $requestMock);

        $builder = $this->frontendUserAuthenticationMock->method('start');
        $builder->willReturnSelf();

        $this->subject->__invoke($event);
    }

    /**
     * Test __invoke with exception
     *
     * @return void
     */
    public function testInvokeWithException(): void
    {
        $builder = $this->configurationManagerMock->method('getConfiguration');
        $builder->willThrowException(new Exception('Invalid configuration type "Settings"', 1206031879));

        $frontendUserMock = $this->createMock(FrontendUser::class);
        $requestMock = $this->createMock(ServerRequestInterface::class);

        $event = GeneralUtility::makeInstance(FrontendUserCreateAfterEvent::class, $frontendUserMock, $requestMock);

        $this->subject->__invoke($event);
    }
}
