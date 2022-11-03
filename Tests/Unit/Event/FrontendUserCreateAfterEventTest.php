<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Tests\Unit\Event;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Ydt\FrontendUser\Domain\Model\FrontendUser;
use Ydt\FrontendUser\Event\FrontendUserCreateAfterEvent;

/**
 * Class FrontendUserCreateAfterEventTest
 * Testcase for class \Ydt\FrontendUser\Event\FrontendUserCreateAfterEvent
 */
class FrontendUserCreateAfterEventTest extends UnitTestCase
{
    /**
     * Frontend User Create After Event
     *
     * @var FrontendUserCreateAfterEvent
     */
    private $subject;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $frontendUserMock = $this->createMock(FrontendUser::class);

        $this->subject = new FrontendUserCreateAfterEvent($frontendUserMock);
    }

    /**
     * Test getFrontendUser
     *
     * @return void
     */
    public function testGetFrontendUser(): void
    {
        $result = $this->subject->getFrontendUser();

        $this->assertInstanceOf(FrontendUser::class, $result);
    }

    /**
     * Test setFrontendUser
     *
     * @return void
     */
    public function testSetFrontendUser(): void
    {
        $frontendUserMock = $this->createMock(FrontendUser::class);

        $this->subject->setFrontendUser($frontendUserMock);
    }

    /**
     * Test getRequest
     *
     * @return void
     */
    public function testGetRequest(): void
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $this->subject->setRequest($requestMock);

        $result = $this->subject->getRequest();

        $this->assertInstanceOf(ServerRequestInterface::class, $result);
    }
}
