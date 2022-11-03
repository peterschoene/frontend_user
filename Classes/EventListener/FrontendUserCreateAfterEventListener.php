<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\EventListener;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use Ydt\FrontendUser\Authentication\FrontendUserAuthentication;
use Ydt\FrontendUser\Event\FrontendUserCreateAfterEvent;

/**
 * Class FrontendUserCreateAfterEventListener
 * Frontend user create after event listener
 */
class FrontendUserCreateAfterEventListener implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Frontend User Authentication
     *
     * @var FrontendUserAuthentication
     */
    protected $frontendUserAuthentication;

    /**
     * Configuration Manager
     *
     * @var ConfigurationManager
     */
    protected $configurationManager;

    /**
     * FrontendUserCreateAfterEventListener constructor
     *
     * @param FrontendUserAuthentication $frontendUserAuthentication
     * @param ConfigurationManager $configurationManager
     */
    public function __construct(
        FrontendUserAuthentication $frontendUserAuthentication,
        ConfigurationManager $configurationManager
    ) {
        $this->frontendUserAuthentication = $frontendUserAuthentication;
        $this->configurationManager = $configurationManager;
    }

    /**
     * Log in new frontend user
     *
     * @param FrontendUserCreateAfterEvent $event
     * @return void
     */
    public function __invoke(FrontendUserCreateAfterEvent $event): void
    {
        try {
            $settings = $this->configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS);
            $request = $event->getRequest();

            if (isset($settings['enableFrontendUserAutoLogin']) && $settings['enableFrontendUserAutoLogin'] && $request) {
                $this->frontendUserAuthentication->start($request);
            }
        } catch (Exception $exception) {
            $this->logger->error('Login failed. ' . $exception->getMessage());
        }
    }
}
