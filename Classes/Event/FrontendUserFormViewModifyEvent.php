<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Event;

use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * Class FrontendUserFormViewModifyEvent
 * Frontend user form view modify event
 */
final class FrontendUserFormViewModifyEvent
{
    /**
     * View
     *
     * @var ViewInterface
     */
    protected $view;

    /**
     * FrontendUserFormViewModifyEvent constructor
     *
     * @param ViewInterface $view
     */
    public function __construct(
        ViewInterface $view
    ) {
        $this->view = $view;
    }

    /**
     * Get view
     *
     * @return ViewInterface
     */
    public function getView(): ViewInterface
    {
        return $this->view;
    }

    /**
     * Set view
     *
     * @param ViewInterface $view
     * @return void
     */
    public function setView(ViewInterface $view): void
    {
        $this->view = $view;
    }
}
