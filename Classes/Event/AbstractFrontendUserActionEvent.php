<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Event;

use Psr\Http\Message\ServerRequestInterface;
use Ydt\FrontendUser\Domain\Model\FrontendUser;

/**
 * Class AbstractFrontendUserActionEvent
 * Abstract frontend user action event
 */
abstract class AbstractFrontendUserActionEvent
{
    /**
     * Frontend user
     *
     * @var FrontendUser
     */
    protected $frontendUser;

    /**
     * Request
     *
     * @var ServerRequestInterface|null
     */
    protected $request;

    /**
     * AbstractFrontendUserActionEvent constructor
     *
     * @param FrontendUser $frontendUser
     * @param ServerRequestInterface|null $request
     */
    public function __construct(
        FrontendUser $frontendUser,
        ServerRequestInterface $request = null
    ) {
        $this->frontendUser = $frontendUser;
        $this->request = $request;
    }

    /**
     * Get frontend user
     *
     * @return FrontendUser
     */
    public function getFrontendUser(): FrontendUser
    {
        return $this->frontendUser;
    }

    /**
     * Set frontend user
     *
     * @param FrontendUser $frontendUser
     */
    public function setFrontendUser(FrontendUser $frontendUser): void
    {
        $this->frontendUser = $frontendUser;
    }

    /**
     * Get request
     *
     * @return ServerRequestInterface|null
     */
    public function getRequest(): ?ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * Set request
     *
     * @param ServerRequestInterface|null $request
     * @return void
     */
    public function setRequest(?ServerRequestInterface $request): void
    {
        $this->request = $request;
    }
}
