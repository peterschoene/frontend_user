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
use Ydt\FrontendUser\Event\AbstractFrontendUserActionEvent;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class AbstractFrontendUserActionEventTest
 * Testcase for class \Ydt\FrontendUser\Event\AbstractFrontendUserActionEvent
 */
class AbstractFrontendUserActionEventTest extends UnitTestCase
{
    /**
     * Event Mock
     *
     * @var MockObject|AbstractFrontendUserActionEvent
     */
    private $eventMock;

    /**
     * Frontend User Mock
     *
     * @var MockObject|FrontendUser
     */
    private $frontendUserMock;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->frontendUserMock = $this->createMock(FrontendUser::class);

        $this->eventMock = $this->getMockForAbstractClass(
            AbstractFrontendUserActionEvent::class,
            [$this->frontendUserMock],
            '',
            true,
            true,
            true,
            [
                'getFrontendUser',
                'setFrontendUser',
                'getRequest',
                'setRequest',
            ]
        );
    }

    /**
     * Test getFrontendUser
     *
     * @return void
     */
    public function testGetFrontendUser(): void
    {
        $builder = $this->eventMock->method('getFrontendUser');
        $builder->willReturn($this->frontendUserMock);

        $result = $this->eventMock->getFrontendUser();

        $this->assertInstanceOf(FrontendUser::class, $result);
    }

    /**
     * Test setFrontendUser
     *
     * @return void
     */
    public function testSetFrontendUser(): void
    {
        $builder = $this->eventMock->method('getFrontendUser');
        $builder->with($this->frontendUserMock);
        $builder->willReturnSelf();

        $this->eventMock->setFrontendUser($this->frontendUserMock);
    }

    /**
     * Test getRequest
     *
     * @return void
     */
    public function testGetRequest(): void
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);

        $builder = $this->eventMock->method('getRequest');
        $builder->willReturn($requestMock);

        $result = $this->eventMock->getRequest();

        $this->assertInstanceOf(ServerRequestInterface::class, $result);
    }

    /**
     * Test setRequest
     *
     * @return void
     */
    public function testSetRequest(): void
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);

        $builder = $this->eventMock->method('setRequest');
        $builder->with($requestMock);
        $builder->willReturnSelf();

        $this->eventMock->setRequest($requestMock);
    }
}
