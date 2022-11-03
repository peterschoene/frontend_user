<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Tests\Unit\Authentication;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Authentication\AuthenticationService;
use TYPO3\CMS\Core\Authentication\GroupResolver;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Session\UserSession;
use TYPO3\CMS\Core\Session\UserSessionManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\ModifyResolvedFrontendGroupsEvent;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Ydt\FrontendUser\Authentication\FrontendUserAuthentication;
use ReflectionClass;
use ReflectionException;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class FrontendUserAuthenticationTest
 * Testcase for class \Ydt\FrontendUser\Authentication\FrontendUserAuthentication
 */
class FrontendUserAuthenticationTest extends UnitTestCase
{
    /**
     * Authentication Service Mock
     *
     * @var MockObject|AuthenticationService
     */
    private $authServiceMock;

    /**
     * Frontend User Authentication
     *
     * @var FrontendUserAuthentication
     */
    private $subject;

    /**
     * @inheritDoc
     */
    protected $resetSingletonInstances = true;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->authServiceMock = $this->createMock(AuthenticationService::class);

        $GLOBALS['T3_SERVICES']['auth'][get_class($this->authServiceMock)] = [
            'className' => AuthenticationService::class,
            'serviceKey' => get_class($this->authServiceMock),
            'serviceType' => 'auth',
            'subtype' => 'getUserFE,authUserFE,processLoginDataFE',
            'available' => true,
            'priority' => 50,
            'quality' => 50,
            'os' => '',
            'exec' => '',
            'serviceSubTypes' => [
                'getUserFE' => 'getUserFE',
                'authUserFE' => 'authUserFE',
                'processLoginDataFE' => 'processLoginDataFE',
            ],
        ];

        $expressionBuilderMock = $this->createMock(ExpressionBuilder::class);
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        $builder = $queryBuilderMock->method('expr');
        $builder->willReturn($expressionBuilderMock);

        $connectionMock = $this->createMock(Connection::class);
        $builder = $connectionMock->method('update');
        $builder->willReturn(1);

        $connectionPoolMock = $this->createMock(ConnectionPool::class);

        $builder = $connectionPoolMock->method('getQueryBuilderForTable');
        $builder->willReturn($queryBuilderMock);
        $builder = $connectionPoolMock->method('getConnectionForTable');
        $builder->willReturn($connectionMock);

        GeneralUtility::addInstance(ConnectionPool::class, $connectionPoolMock);
        GeneralUtility::addInstance(ConnectionPool::class, $connectionPoolMock);
        GeneralUtility::addInstance(ConnectionPool::class, $connectionPoolMock);

        $userSessionMock = $this->createMock(UserSession::class);

        $builder = $userSessionMock->method('isNew');
        $builder->willReturn(true);
        $builder = $userSessionMock->method('getIdentifier');
        $builder->willReturn('123456789');

        $userSessionManagerMock = $this->createMock(UserSessionManager::class);
        $builder = $userSessionManagerMock->method('createAnonymousSession');
        $builder->willReturn($userSessionMock);

        GeneralUtility::addInstance(UserSessionManager::class, $userSessionManagerMock);

        $groupResolverMock = $this->createMock(GroupResolver::class);
        $builder = $groupResolverMock->method('resolveGroupsForUser');
        $builder->willReturn([]);

        GeneralUtility::addInstance(GroupResolver::class, $groupResolverMock);

        $this->subject = new FrontendUserAuthentication();

        $loggerMock = $this->createMock(LoggerInterface::class);
        $this->subject->setLogger($loggerMock);

        $event = new ModifyResolvedFrontendGroupsEvent($this->subject, [], null);
        $eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $builder = $eventDispatcherMock->method('dispatch');
        $builder->willReturn($event);

        GeneralUtility::addInstance(EventDispatcherInterface::class, $eventDispatcherMock);
    }

    /**
     * Test start
     *
     * @return void
     */
    public function testStart(): void
    {
        $userData = [
            'uid' => 1,
            'username' => 'test',
            'password' => md5('test123'),
        ];

        $builder = $this->authServiceMock->method('init');
        $builder->willReturn(true);
        $builder = $this->authServiceMock->method('processLoginData');
        $builder->willReturn(200);
        $builder = $this->authServiceMock->method('getUser');
        $builder->willReturn($userData);
        $builder = $this->authServiceMock->method('authUser');
        $builder->with($userData);
        $builder->willReturn(200);

        GeneralUtility::addInstance(AuthenticationService::class, $this->authServiceMock);
        GeneralUtility::addInstance(AuthenticationService::class, $this->authServiceMock);
        GeneralUtility::addInstance(AuthenticationService::class, $this->authServiceMock);
        
        $requestBody = [
            'tx_frontenduser_form' => [
                'pid' => 1,
                'newFrontendUser' => [
                    'username' => 'test',
                    'password' => 'test123',
                ],
            ],
        ];

        $requestMock = $this->createMock(ServerRequestInterface::class);
        $builder = $requestMock->method('getParsedBody');
        $builder->willReturn($requestBody);

        $this->subject->start($requestMock);
    }

    /**
     * Test setSessionCookie
     *
     * @return void
     * @throws ReflectionException
     */
    public function testSetSessionCookie(): void
    {
        $this->subject->initializeUserSessionManager();

        $reflection = new ReflectionClass(FrontendUserAuthentication::class);
        $method = $reflection->getMethod('setSessionCookie');
        $method->setAccessible(true);

        $method->invoke($this->subject);
    }

    /**
     * Test getLoginData with exception
     *
     * @return void
     * @throws ReflectionException
     */
    public function testGetLoginDataWithException(): void
    {
        $this->expectExceptionObject(new Exception('username and password are required.'));

        $reflection = new ReflectionClass(FrontendUserAuthentication::class);
        $method = $reflection->getMethod('getLoginData');
        $method->setAccessible(true);

        $method->invoke($this->subject, []);
    }

    /**
     * Test authenticate
     *
     * @dataProvider authenticateDataProvider
     *
     * @param bool $initResult
     * @param array $userData
     * @param int $authUserResult
     * @param int $instanceCount
     * @return void
     * @throws ReflectionException
     */
    public function testAuthenticate(bool $initResult, array $userData, int $authUserResult, int $instanceCount): void
    {
        $this->addAuthServiceInstance($initResult, $userData, $authUserResult, $instanceCount);

        $this->subject->initializeUserSessionManager();

        $loginData = [
            'status'            => 'login',
            'uname'             => 'test',
            'uident'            => md5('test123'),
            'uident_text'       => 'test123',
            'is_permanents'     => false,
        ];

        $reflection = new ReflectionClass(FrontendUserAuthentication::class);
        $method = $reflection->getMethod('authenticate');
        $method->setAccessible(true);

        $method->invoke($this->subject, $loginData);
    }

    /**
     * Add auth service instance
     *
     * @param bool $initResult
     * @param array $userData
     * @param int $authUserResult
     * @param int $instanceCount
     * @return void
     */
    protected function addAuthServiceInstance(bool $initResult, array $userData, int $authUserResult, int $instanceCount): void
    {
        $authServiceMock = $this->createMock(AuthenticationService::class);

        $builder = $authServiceMock->method('init');
        $builder->willReturn($initResult);
        $builder = $authServiceMock->method('getUser');
        $builder->willReturn($userData);

        if ($authUserResult === 50) {
            $builder = $authServiceMock->method('authUser');
            $builder->with($userData);
            $builder->willReturnOnConsecutiveCalls($authUserResult, 200);
        } else {
            $builder = $authServiceMock->method('authUser');
            $builder->with($userData);
            $builder->willReturn($authUserResult);
        }

        for ($i = 0; $i < $instanceCount; $i++) {
            GeneralUtility::addInstance(AuthenticationService::class, $authServiceMock);
        }
    }

    /**
     * Data provider for authenticate
     *
     * @return array
     */
    public function authenticateDataProvider(): array
    {
        $userData = [
            'uid' => 1,
            'username' => 'test',
            'password' => md5('test123'),
        ];

        return [
            [false, $userData, 200, 1],
            [true, $userData, 0, 2],
            [true, $userData, 50, 3],
        ];
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();
        parent::tearDown();
    }
}
