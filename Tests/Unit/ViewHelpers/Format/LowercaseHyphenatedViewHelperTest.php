<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Tests\Unit\ViewHelpers\Format;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Ydt\FrontendUser\ViewHelpers\Format\LowercaseHyphenatedViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Class LowercaseHyphenatedViewHelperTest
 * Testcase for class \Ydt\FrontendUser\ViewHelpers\Format\LowercaseHyphenatedViewHelper
 */
class LowercaseHyphenatedViewHelperTest extends UnitTestCase
{
    /**
     * Lowercase Hyphenated View Helper
     *
     * @var LowercaseHyphenatedViewHelper
     */
    private $subject;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new LowercaseHyphenatedViewHelper();

        $this->subject->initializeArguments();
    }

    /**
     * Test renderStatic
     *
     * @dataProvider argumentsDataProvider
     *
     * @param array $arguments
     * @return void
     */
    public function testRenderStatic(array $arguments): void
    {
        $renderChildrenClosureMock = function () {
            return 'firstName';
        };
        $contextMock = $this->createMock(RenderingContextInterface::class);

        $result = $this->subject->renderStatic($arguments, $renderChildrenClosureMock, $contextMock);

        $this->assertIsString($result);
        $this->assertSame($result, 'first-name');
    }

    /**
     * Data provider for testRenderStatic
     *
     * @return array
     */
    public function argumentsDataProvider(): array
    {
        return [
            [['value' => 'firstName']],
            [['value' => '']],
        ];
    }
}
