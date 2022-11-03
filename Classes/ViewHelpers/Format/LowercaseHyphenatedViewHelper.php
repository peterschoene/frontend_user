<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\ViewHelpers\Format;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use Closure;

/**
 * Class LowercaseHyphenatedViewHelper
 * Split string by capital letters, make parts lowercase and join them with a hyphen
 */
class LowercaseHyphenatedViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @inheritdoc
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('value', 'string', 'String value in camelCase', true, []);
    }

    /**
     * @inheritdoc
     */
    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        $value = $arguments['value'];

        if (empty($value)) {
            $value = $renderChildrenClosure();
        }

        $parts = preg_split('/(?=[A-Z])/', $value);

        $formattedParts = [];
        foreach ($parts as $part) {
            $formattedParts[] = strtolower(trim($part));
        }

        return implode('-', $formattedParts);
    }
}
