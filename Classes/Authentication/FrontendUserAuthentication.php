<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Authentication;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Authentication\LoginType;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication as CoreFrontendUserAuthentication;
use TYPO3\CMS\Core\Exception;

/**
 * Class FrontendUserAuthentication
 * Authentication of frontend user after creation on website
 */
class FrontendUserAuthentication extends CoreFrontendUserAuthentication
{
    /**
     * Permanent login is forced to be enabled
     */
    const PERMANENT_LOGIN_ENABLED = 2;

    /**
     * Form field with login-name
     *
     * @var string
     */
    public $formfield_uname = 'username';

    /**
     * Form field with password
     *
     * @var string
     */
    public $formfield_uident = 'password';

    /**
     * Form object name
     *
     * @var string
     */
    protected $objectName = 'newFrontendUser';

    /**
     * @inheritdoc
     */
    public function start(ServerRequestInterface $request = null): void
    {
        $request = $request ?? $GLOBALS['TYPO3_REQUEST'] ?? ServerRequestFactory::fromGlobals();
        $this->logger->debug('## Beginning of created frontend user auth logging.');

        $parsedBody = $request->getParsedBody();
        $pid = (int)$parsedBody['tx_frontenduser_form']['pid'] ?? 0;
        if ($pid) {
            $this->checkPid_value = $pid;
        }

        $formData = $parsedBody['tx_frontenduser_form'][$this->objectName] ?? [];
        $loginData = $this->getLoginData($formData);

        $this->user = null;
        if (!isset($this->userSessionManager)) {
            $this->initializeUserSessionManager();
        }
        $this->userSession = $this->userSessionManager->createFromRequestOrAnonymous($request, $this->name);

        $this->authenticate($loginData);

        if (!$this->dontSetCookie || $this->isRefreshTimeBasedCookie()) {
            $this->setSessionCookie();
        }

        $this->fetchGroupData($request);
    }

    /**
     * Get login data
     *
     * @param array $formData
     * @return array
     * @throws Exception
     */
    protected function getLoginData(array $formData): array
    {
        $username = $formData[$this->formfield_uname] ?? '';
        $password = $formData[$this->formfield_uident] ?? '';

        if (empty($username) || empty($password)) {
            throw new Exception(
                sprintf('%s and %s are required.', $this->formfield_uname, $this->formfield_uident)
            );
        }

        $loginData = [
            'status'    => LoginType::LOGIN,
            'uname'     => $username,
            'uident'    => $password,
        ];

        $loginData = $this->processLoginData($loginData);

        $isPermanent = (int)$GLOBALS['TYPO3_CONF_VARS']['FE']['permalogin'] === self::PERMANENT_LOGIN_ENABLED;
        $loginData['permanent'] = $isPermanent;
        $this->is_permanent = $isPermanent;

        return $loginData;
    }

    /**
     * @inheritdoc
     */
    protected function setSessionCookie(): void
    {
        parent::setSessionCookie();

        if ($this->setCookie) {
            $cookie = clone $this->setCookie;

            setcookie(
                $cookie->getName(),
                $cookie->getValue(),
                [
                    'expires'   => $cookie->getExpiresTime(),
                    'path'      => $cookie->getPath(),
                    'domain'    => $cookie->getDomain(),
                    'secure'    => $cookie->isSecure(),
                    'httponly'  => $cookie->isHttpOnly(),
                    'samesite'  => $cookie->getSameSite(),
                ]
            );
        }
    }

    /**
     * Authenticate frontend user with provided credentials
     *
     * @param array $loginData
     * @return void
     */
    protected function authenticate(array $loginData): void
    {
        $this->logger->debug(sprintf('Login type: %s', $this->loginType));
        $this->logger->debug('Login data', $this->removeSensitiveLoginDataForLoggingInfo($loginData));

        $this->logoff();

        $authInfo = $this->getAuthInfoArray();
        $tempUsers = [];
        $authenticated = false;

        foreach ($this->getAuthServices('getUser' . $this->loginType, $loginData, $authInfo) as $authService) {
            $user = $authService->getUser();
            if (is_array($user)) {
                $tempUsers[] = $user;
                $this->logger->debug('User found', [
                    $this->userid_column => $user[$this->userid_column],
                    $this->username_column => $user[$this->username_column],
                ]);
                break;
            }
        }

        if (!empty($tempUsers)) {
            $this->logger->debug(sprintf('%s user records found by services', count($tempUsers)));

            foreach ($tempUsers as $tempUser) {
                $this->logger->debug('Auth user', $this->removeSensitiveLoginDataForLoggingInfo($tempUser, true));

                foreach ($this->getAuthServices('authUser' . $this->loginType, $loginData, $authInfo) as $authService) {
                    $result = (int)$authService->authUser($tempUser);
                    if ($result <= 0) {
                        $authenticated = false;
                        break;
                    }

                    if ($result >= 200) {
                        $authenticated = true;
                        break;
                    }

                    if ($result < 100) {
                        $authenticated = true;
                    }
                }

                if ($authenticated) {
                    break;
                }
            }

            if ($authenticated) {
                $this->userSession = $this->createUserSession($tempUser);
                $this->user = array_merge($tempUser, $this->user ?? []);

                $this->loginSessionStarted = true;
                if (is_array($this->user)) {
                    $this->logger->debug('User session finally read', [
                        $this->userid_column => $this->user[$this->userid_column],
                        $this->username_column => $this->user[$this->username_column],
                    ]);
                }

                $this->logger->info(sprintf('User %s logged in from %s',
                    $tempUser[$this->username_column],
                    GeneralUtility::getIndpEnv('REMOTE_ADDR')
                ));
            } else {
                $this->logger->debug('Login failed', [
                    $this->userid_column => $tempUser[$this->userid_column],
                    $this->username_column => $tempUser[$this->username_column],
                ]);
                $this->handleLoginFailure();
            }
        } else {
            $this->logger->debug('No user found by services');
            $this->logger->debug('Login failed', [
                'loginData' => $this->removeSensitiveLoginDataForLoggingInfo($loginData),
            ]);
            $this->handleLoginFailure();
        }
    }

    /**
     * @inheritdoc
     */
    public function checkAuthentication(ServerRequestInterface $request = null): void
    {

    }
}
