<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Tests\Unit\Event;

use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Ydt\FrontendUser\Event\FrontendUserFormViewModifyEvent;

/**
 * Class FrontendUserFormViewModifyEventTest
 * Testcase for class \Ydt\FrontendUser\Event\FrontendUserFormViewModifyEvent
 */
class FrontendUserFormViewModifyEventTest extends UnitTestCase
{
    /**
     * Frontend User Form View Modify Event
     *
     * @var FrontendUserFormViewModifyEvent
     */
    private $subject;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $viewMock = $this->createMock(ViewInterface::class);

        $this->subject = new FrontendUserFormViewModifyEvent($viewMock);
    }

    /**
     * Test getView
     *
     * @return void
     */
    public function testGetView(): void
    {
        $result = $this->subject->getView();

        $this->assertInstanceOf(ViewInterface::class, $result);
    }

    /**
     * Test setView
     *
     * @return void
     */
    public function testSetView(): void
    {
        $viewMock = $this->createMock(ViewInterface::class);

        $this->subject->setView($viewMock);
    }
}
