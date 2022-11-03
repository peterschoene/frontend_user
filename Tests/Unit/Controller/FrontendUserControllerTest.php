<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Tests\Unit\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Http\ResponseFactory;
use TYPO3\CMS\Core\Http\StreamFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Mvc\Controller\MvcPropertyMappingConfigurationService;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Core\Localization\LocalizationFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Property\PropertyMapper;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfiguration;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\View\ViewResolverInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Validation\Validator\ConjunctionValidator;
use TYPO3\CMS\Extbase\Validation\ValidatorResolver;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Ydt\FrontendUser\Controller\FrontendUserController;
use Ydt\FrontendUser\Domain\Model\FrontendUser;
use Ydt\FrontendUser\Domain\Model\FrontendUserGroup;
use Ydt\FrontendUser\Domain\Repository\FrontendUserGroupRepository;
use Ydt\FrontendUser\Domain\Repository\FrontendUserRepository;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;
use ReflectionException;
use TYPO3\CMS\Extbase\Property\Exception;
use TYPO3\CMS\Extbase\Reflection\ClassSchema;
use TYPO3\CMS\Extbase\Reflection\ClassSchema\Method as ClassSchemaMethod;
use TYPO3\CMS\Extbase\Reflection\ClassSchema\MethodParameter as ClassSchemaMethodParameter;

/**
 * Class FrontendUserControllerTest
 * Testcase for class \Ydt\FrontendUser\Controller\FrontendUserController
 */
class FrontendUserControllerTest extends UnitTestCase
{
    /**
     * Frontend User Controller
     *
     * @var FrontendUserController
     */
    private $subject;

    /**
     * Frontend User Repository Mock
     *
     * @var MockObject|FrontendUserRepository
     */
    private $frontendUserRepositoryMock;

    /**
     * Frontend User Group Repository Mock
     *
     * @var MockObject|FrontendUserGroupRepository
     */
    private $frontendUserGroupRepositoryMock;

    /**
     * Password Hash Factory Mock
     *
     * @var MockObject|PasswordHashFactory
     */
    private $passwordHashFactoryMock;

    /**
     * Context Mock
     *
     * @var MockObject|Context
     */
    private $contextMock;

    /**
     * Property Mapper Mock
     *
     * @var MockObject|PropertyMapper
     */
    private $propertyMapperMock;

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

        $GLOBALS['TYPO3_CONF_VARS']['FE']['checkFeUserPid'] = 1;

        $languageServiceMock = $this->createMock(LanguageService::class);
        $GLOBALS['LANG'] = $languageServiceMock;

        $localizationFactoryMock = $this->createMock(LocalizationFactory::class);
        $builder = $localizationFactoryMock->method('getParsedData');
        $builder->willReturn([]);

        GeneralUtility::setSingletonInstance(LocalizationFactory::class, $localizationFactoryMock);

        $uriBuilderMock = $this->createMock(UriBuilder::class);

        $builder = $uriBuilderMock->method('setTargetPageUid');
        $builder->willReturnSelf();
        $builder = $uriBuilderMock->method('buildFrontendUri');
        $builder->willReturn('https://test');

        GeneralUtility::addInstance(UriBuilder::class, $uriBuilderMock);

        $frameworkConfigurationManagerMock = $this->createMock(ConfigurationManagerInterface::class);
        $builder = $frameworkConfigurationManagerMock->method('getConfiguration');
        $builder->with(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK, 'frontend_user');
        $builder->willReturn([]);

        GeneralUtility::setSingletonInstance(ConfigurationManagerInterface::class, $frameworkConfigurationManagerMock);

        $this->frontendUserRepositoryMock = $this->createMock(FrontendUserRepository::class);
        $this->frontendUserGroupRepositoryMock = $this->createMock(FrontendUserGroupRepository::class);
        $this->passwordHashFactoryMock = $this->createMock(PasswordHashFactory::class);
        $this->persistenceManagerMock = $this->createMock(PersistenceManager::class);
        $this->contextMock = $this->createMock(Context::class);
        $this->propertyMapperMock = $this->createMock(PropertyMapper::class);

        $this->subject = new FrontendUserController(
            $this->frontendUserRepositoryMock,
            $this->frontendUserGroupRepositoryMock,
            $this->passwordHashFactoryMock,
            $this->persistenceManagerMock,
            $this->contextMock,
            $this->propertyMapperMock
        );

        $conjunctionValidatorMock = $this->createMock(ConjunctionValidator::class);
        $validatorResolverMock = $this->createMock(ValidatorResolver::class);
        $builder = $validatorResolverMock->method('getBaseValidatorConjunction');
        $builder->willReturn($conjunctionValidatorMock);

        $this->subject->injectValidatorResolver($validatorResolverMock);

        $settingsConfigurationManagerMock = $this->createMock(ConfigurationManagerInterface::class);
        $builder = $settingsConfigurationManagerMock->method('getConfiguration');
        $builder->willReturn([
            'frontendUserStoragePid' => 1,
            'newFrontendUserFormFields' => 'email',
            'editFrontendUserFormFields' => 'email,image',
            'enableFrontendUserDeletion' => 1,
            'redirectLoginPageId' => 10,
            'frontendUserImageFolder' => '1:test',
        ]);

        $this->subject->injectConfigurationManager($settingsConfigurationManagerMock);

        $objectManagerMock = $this->createMock(ObjectManagerInterface::class);
        $this->subject->injectObjectManager($objectManagerMock);

        $mvcPropertyMappingConfigurationServiceMock = $this->createMock(MvcPropertyMappingConfigurationService::class);
        $builder = $mvcPropertyMappingConfigurationServiceMock->method('initializePropertyMappingConfigurationFromRequest');
        $builder->willReturnSelf();

        $this->subject->injectMvcPropertyMappingConfigurationService($mvcPropertyMappingConfigurationServiceMock);

        $viewMock = $this->createMock(ViewInterface::class);

        $builder = $viewMock->method('initializeView');
        $builder->willReturnSelf();
        $builder = $viewMock->method('assign');
        $builder->willReturnSelf();
        $builder = $viewMock->method('assignMultiple');
        $builder->willReturnSelf();
        $builder = $viewMock->method('render');
        $builder->willReturn('');

        $viewResolverMock = $this->createMock(ViewResolverInterface::class);
        $builder = $viewResolverMock->method('resolve');
        $builder->willReturn($viewMock);

        $this->subject->injectViewResolver($viewResolverMock);

        $eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $this->subject->injectEventDispatcher($eventDispatcherMock);

        $responseMock = $this->createMock(ResponseInterface::class);

        $builder = $responseMock->method('withHeader');
        $builder->willReturnSelf();
        $builder = $responseMock->method('withBody');
        $builder->willReturnSelf();

        $responseFactoryMock = $this->createMock(ResponseFactory::class);
        $builder = $responseFactoryMock->method('createResponse');
        $builder->willReturn($responseMock);

        $this->subject->injectResponseFactory($responseFactoryMock);

        $streamMock = $this->createMock(StreamInterface::class);
        $streamFactoryMock = $this->createMock(StreamFactory::class);
        $builder = $streamFactoryMock->method('createStream');
        $builder->willReturn($streamMock);

        $this->subject->injectStreamFactory($streamFactoryMock);

        $internalExtensionServiceMock = $this->createMock(ExtensionService::class);
        $builder = $internalExtensionServiceMock->method('getPluginNamespace');
        $builder->willReturn('tx_frontenduser_form');

        $this->subject->injectInternalExtensionService($internalExtensionServiceMock);

        $flashMessageQueueMock = $this->createMock(FlashMessageQueue::class);
        $builder = $flashMessageQueueMock->method('enqueue');
        $builder->willReturnSelf();

        $internalFlashMessageServiceMock = $this->createMock(FlashMessageService::class);
        $builder = $internalFlashMessageServiceMock->method('getMessageQueueByIdentifier');
        $builder->willReturn($flashMessageQueueMock);

        $this->subject->injectInternalFlashMessageService($internalFlashMessageServiceMock);
    }

    /**
     * Test newAction
     *
     * @return void
     */
    public function testNewAction(): void
    {
        $this->configureRequest();

        $builder = $this->contextMock->method('getPropertyFromAspect');
        $builder->willReturn(null);

        $requestMock = $this->createMock(Request::class);
        $builder = $requestMock->method('getControllerActionName');
        $builder->willReturn('new');

        $this->subject->processRequest($requestMock);
    }

    /**
     * Test newAction with redirect
     *
     * @return void
     * @see StopActionException
     */
    public function testNewActionWithRedirect(): void
    {
        $this->configureRequest();

        $builder = $this->contextMock->method('getPropertyFromAspect');
        $builder->willReturn(1);

        $frontendUserMock = $this->createMock(FrontendUser::class);
        $builder = $this->frontendUserRepositoryMock->method('findByUid');
        $builder->willReturn($frontendUserMock);

        $requestMock = $this->createMock(Request::class);
        $builder = $requestMock->method('getControllerActionName');
        $builder->willReturn('new');

        try {
            $this->subject->processRequest($requestMock);
        } catch (StopActionException $exception) {
        }
    }

    /**
     * Test createAction
     *
     * @dataProvider createActionMethodDataProvider
     *
     * @param FileReference|null $fileReference
     * @param bool $enableRedirectToLoginPage
     * @return void
     * @see StopActionException
     */
    public function testCreateAction(?FileReference $fileReference, bool $enableRedirectToLoginPage): void
    {
        $configurationManagerMock = $this->createMock(ConfigurationManagerInterface::class);
        $builder = $configurationManagerMock->method('getConfiguration');
        $builder->willReturn([
            'defaultFrontendUserGroupId' => 1,
            'frontendUserImageFolder' => '1:test',
            'redirectLoginPageId' => 10,
            'enableRedirectToLoginPage' => $enableRedirectToLoginPage,
        ]);
        $this->subject->injectConfigurationManager($configurationManagerMock);

        $this->configureRequestWithFrontendUserParam();

        $frontendUserMock = $this->createMock(FrontendUser::class);
        $builder = $frontendUserMock->method('getUsername');
        $builder->willReturn('test');

        $hashInstanceMock = $this->createMock(PasswordHashInterface::class);
        $builder = $hashInstanceMock->method('getHashedPassword');
        $builder->willReturn(md5('test123'));

        $builder = $this->passwordHashFactoryMock->method('getDefaultHashInstance');
        $builder->with('FE');
        $builder->willReturn($hashInstanceMock);

        $builder = $frontendUserMock->method('getPassword');
        $builder->willReturn('test123');

        $frontendUserGroupMock = $this->createMock(FrontendUserGroup::class);
        $builder = $this->frontendUserGroupRepositoryMock->method('findByUid');
        $builder->willReturn($frontendUserGroupMock);

        $builder = $frontendUserMock->method('addUserGroup');
        $builder->with($frontendUserGroupMock);
        $builder->willReturnSelf();

        $propertyMappingConfigurationMock = $this->createMock(PropertyMappingConfiguration::class);
        $builder = $propertyMappingConfigurationMock->method('setTypeConverterOptions');
        $builder->willReturnSelf();

        $builder = $this->propertyMapperMock->method('convert');
        $builder->willReturn($fileReference);

        $fileReferenceMock = $this->createMock(FileReference::class);
        $builder = $frontendUserMock->method('addImage');
        $builder->with($fileReferenceMock);
        $builder->willReturnSelf();

        $requestMock = $this->createMock(Request::class);

        $builder = $requestMock->method('getControllerActionName');
        $builder->willReturn('create');
        $builder = $requestMock->method('getControllerExtensionName');
        $builder->willReturn('frontend_user');
        $builder = $requestMock->method('getPluginName');
        $builder->willReturn('frontenduser_form');
        $builder = $requestMock->method('hasArgument');
        $builder->with('frontendUser');
        $builder->willReturn(true);
        $builder = $requestMock->method('getArgument');
        $builder->with('frontendUser');
        $builder->willReturn($frontendUserMock);
        $builder = $requestMock->method('getArguments');
        $builder->willReturn([
            'pid' => 1,
            'image' => [
                'error' => 0,
            ],
        ]);

        try {
            $this->subject->processRequest($requestMock);
        } catch (StopActionException $exception) {
        }
    }

    /**
     * Data provider for testCreateAction
     *
     * @return array
     */
    public function createActionMethodDataProvider(): array
    {
        $fileReferenceMock = $this->createMock(FileReference::class);

        return [
            [$fileReferenceMock, false],
            [null, true],
        ];
    }

    /**
     * Test createAction with forward
     *
     * @return void
     * @see StopActionException
     */
    public function testCreateActionWithForward(): void
    {
        $this->configureRequestWithFrontendUserParam();

        $frontendUserMock = $this->createMock(FrontendUser::class);
        $builder = $frontendUserMock->method('getUsername');
        $builder->willReturn('test');

        $builder = $this->frontendUserRepositoryMock->method('findByUsername');
        $builder->willReturn($frontendUserMock);

        $requestMock = $this->createMock(Request::class);

        $builder = $requestMock->method('getControllerActionName');
        $builder->willReturn('create');
        $builder = $requestMock->method('hasArgument');
        $builder->with('frontendUser');
        $builder->willReturn(true);
        $builder = $requestMock->method('getArgument');
        $builder->with('frontendUser');
        $builder->willReturn($frontendUserMock);
        $builder = $requestMock->method('getArguments');
        $builder->willReturn([
            'pid' => 1,
        ]);

        try {
            $this->subject->processRequest($requestMock);
        } catch (StopActionException $exception) {
        }
    }

    /**
     * Test editAction
     *
     * @return void
     */
    public function testEditAction(): void
    {
        $this->configureRequestWithFrontendUserParam();

        $requestMock = $this->createMock(Request::class);

        $builder = $requestMock->method('getControllerActionName');
        $builder->willReturn('edit');

        $builder = $requestMock->method('hasArgument');
        $builder->with('frontendUser');
        $builder->willReturn(true);

        $frontendUserMock = $this->createMock(FrontendUser::class);
        $builder = $requestMock->method('getArgument');
        $builder->with('frontendUser');
        $builder->willReturn($frontendUserMock);

        $this->subject->processRequest($requestMock);
    }

    /**
     * Test updateAction
     *
     * @dataProvider updateActionMethodDataProvider
     *
     * @param FileReference|null $fileReference
     * @return void
     * @see StopActionException
     */
    public function testUpdateAction(?FileReference $fileReference): void
    {
        $this->configureRequestWithFrontendUserParam();

        $builder = $this->contextMock->method('getPropertyFromAspect');
        $builder->willReturnOnConsecutiveCalls(1, 'test');

        $frontendUserMock = $this->createMock(FrontendUser::class);

        $builder = $frontendUserMock->method('getUid');
        $builder->willReturn(1);
        $builder = $frontendUserMock->method('getUsername');
        $builder->willReturn('test');
        $builder = $frontendUserMock->method('getPassword');
        $builder->willReturn('test123');
        $builder = $frontendUserMock->method('getPasswordConfirmation');
        $builder->willReturn('test123');

        $hashInstanceMock = $this->createMock(PasswordHashInterface::class);
        $builder = $hashInstanceMock->method('getHashedPassword');
        $builder->willReturn(md5('test123'));

        $builder = $this->passwordHashFactoryMock->method('getDefaultHashInstance');
        $builder->with('FE');
        $builder->willReturn($hashInstanceMock);

        $propertyMappingConfigurationMock = $this->createMock(PropertyMappingConfiguration::class);
        $builder = $propertyMappingConfigurationMock->method('setTypeConverterOptions');
        $builder->willReturnSelf();

        $builder = $this->propertyMapperMock->method('convert');
        $builder->willReturn($fileReference);

        $fileReferenceMock = $this->createMock(FileReference::class);
        $builder = $frontendUserMock->method('addImage');
        $builder->with($fileReferenceMock);
        $builder->willReturnSelf();

        $requestMock = $this->createMock(Request::class);

        $builder = $requestMock->method('getControllerActionName');
        $builder->willReturn('update');
        $builder = $requestMock->method('hasArgument');
        $builder->with('frontendUser');
        $builder->willReturn(true);
        $builder = $requestMock->method('getArgument');
        $builder->with('frontendUser');
        $builder->willReturn($frontendUserMock);
        $builder = $requestMock->method('getArguments');
        $builder->willReturn([
            'pid' => 1,
            'changePassword' => 1,
            'image' => [
                'error' => 0,
            ],
        ]);

        try {
            $this->subject->processRequest($requestMock);
        } catch (StopActionException $exception) {
        }
    }

    /**
     * Data provider for testUpdateAction
     *
     * @return array
     */
    public function updateActionMethodDataProvider(): array
    {
        $fileReferenceMock = $this->createMock(FileReference::class);

        return [
            [$fileReferenceMock],
            [null],
        ];
    }

    /**
     * Test updateAction with forward
     *
     * @dataProvider updateActionMethodWithForwardDataProvider
     *
     * @param string $username
     * @param string $password
     * @return void
     * @see StopActionException
     */
    public function testUpdateActionWithForward(string $username, string $password): void
    {
        $this->configureRequestWithFrontendUserParam();

        $builder = $this->contextMock->method('getPropertyFromAspect');
        $builder->willReturnOnConsecutiveCalls(1, $username);

        $frontendUserMock = $this->createMock(FrontendUser::class);

        $builder = $frontendUserMock->method('getUid');
        $builder->willReturn(1);
        $builder = $frontendUserMock->method('getUsername');
        $builder->willReturn('test');
        $builder = $frontendUserMock->method('getPassword');
        $builder->willReturn($password);
        $builder = $frontendUserMock->method('getPasswordConfirmation');
        $builder->willReturn('test123');

        $requestMock = $this->createMock(Request::class);

        $builder = $requestMock->method('getControllerActionName');
        $builder->willReturn('update');
        $builder = $requestMock->method('hasArgument');
        $builder->with('frontendUser');
        $builder->willReturn(true);
        $builder = $requestMock->method('getArgument');
        $builder->with('frontendUser');
        $builder->willReturn($frontendUserMock);
        $builder = $requestMock->method('getArguments');
        $builder->willReturn([
            'pid' => 1,
            'changePassword' => 1,
        ]);

        try {
            $this->subject->processRequest($requestMock);
        } catch (StopActionException $exception) {
        }
    }

    /**
     * Data provider for testUpdateAction
     *
     * @return array
     */
    public function updateActionMethodWithForwardDataProvider(): array
    {
        return [
            ['test2', ''],
            ['test', 'test1234'],
        ];
    }

    /**
     * Test deleteAction
     *
     * @return void
     * @see StopActionException
     */
    public function testDeleteAction(): void
    {
        $this->configureRequestWithFrontendUserParam();

        $builder = $this->contextMock->method('getPropertyFromAspect');
        $builder->willReturn(1);

        $frontendUserMock = $this->createMock(FrontendUser::class);
        $builder = $frontendUserMock->method('getUid');
        $builder->willReturn(1);

        $requestMock = $this->createMock(Request::class);

        $builder = $requestMock->method('getControllerActionName');
        $builder->willReturn('delete');
        $builder = $requestMock->method('hasArgument');
        $builder->with('frontendUser');
        $builder->willReturn(true);
        $builder = $requestMock->method('getArgument');
        $builder->with('frontendUser');
        $builder->willReturn($frontendUserMock);

        try {
            $this->subject->processRequest($requestMock);
        } catch (StopActionException $exception) {
        }
    }

    /**
     * Test deleteImageAction
     *
     * @return void
     * @see StopActionException
     */
    public function testDeleteImageAction(): void
    {
        $this->configureRequestWithParams();

        $builder = $this->contextMock->method('getPropertyFromAspect');
        $builder->willReturn(1);

        $frontendUserMock = $this->createMock(FrontendUser::class);

        $builder = $frontendUserMock->method('getUid');
        $builder->willReturn(1);
        $builder = $frontendUserMock->method('removeImage');
        $builder->willReturnSelf();

        $requestMock = $this->createMock(Request::class);

        $builder = $requestMock->method('getControllerActionName');
        $builder->willReturn('deleteImage');

        $builder = $requestMock->method('hasArgument');
        $builder->withConsecutive(
            ['frontendUser'],
            ['frontendUserImage']
        );
        $builder->willReturnOnConsecutiveCalls(true, true);

        $fileReferenceMock = $this->createMock(FileReference::class);
        $builder = $requestMock->method('getArgument');
        $builder->withConsecutive(
            ['frontendUser'],
            ['frontendUserImage']
        );
        $builder->willReturnOnConsecutiveCalls($frontendUserMock, $fileReferenceMock);

        try {
            $this->subject->processRequest($requestMock);
        } catch (StopActionException $exception) {
        }
    }

    /**
     * Test getErrorFlashMessage
     *
     * @return void
     * @throws ReflectionException
     */
    public function testGetErrorFlashMessage(): void
    {
        $reflection = new ReflectionClass(FrontendUserController::class);
        $method = $reflection->getMethod('getErrorFlashMessage');
        $method->setAccessible(true);

        $result = $method->invoke($this->subject);

        $this->assertIsString($result);
        $this->assertSame('An error occurred while trying to index a user.', $result);
    }

    /**
     * Test getFrontendUserIdFromAspect
     *
     * @return void
     * @throws ReflectionException
     */
    public function testGetFrontendUserIdFromAspect(): void
    {
        $reflection = new ReflectionClass(FrontendUserController::class);
        $method = $reflection->getMethod('getFrontendUserIdFromAspect');
        $method->setAccessible(true);

        $builder = $this->contextMock->method('getPropertyFromAspect');
        $builder->willReturn(1);

        $result = $method->invoke($this->subject);

        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    /**
     * Test getFrontendUserIdFromAspect with exception
     *
     * @return void
     * @throws ReflectionException
     */
    public function testGetFrontendUserIdFromAspectWithException(): void
    {
        $reflection = new ReflectionClass(FrontendUserController::class);
        $method = $reflection->getMethod('getFrontendUserIdFromAspect');
        $method->setAccessible(true);

        $builder = $this->contextMock->method('getPropertyFromAspect');
        $builder->willThrowException(new AspectNotFoundException('No aspect named "frontend_user" found.', 1527777641));

        $result = $method->invoke($this->subject);

        $this->assertIsInt($result);
        $this->assertSame(0, $result);
    }

    /**
     * Test generateHashedPassword
     *
     * @return void
     * @throws ReflectionException
     */
    public function testGenerateHashedPassword(): void
    {
        $reflection = new ReflectionClass(FrontendUserController::class);
        $method = $reflection->getMethod('generateHashedPassword');
        $method->setAccessible(true);

        $hashInstanceMock = $this->createMock(PasswordHashInterface::class);
        $builder = $hashInstanceMock->method('getHashedPassword');
        $builder->willReturn(md5('test123'));

        $builder = $this->passwordHashFactoryMock->method('getDefaultHashInstance');
        $builder->with('FE');
        $builder->willReturn($hashInstanceMock);

        $result = $method->invoke($this->subject, 'test123');

        $this->assertIsString($result);
    }

    /**
     * Test mapArrayToFileReference
     *
     * @return void
     * @throws ReflectionException
     */
    public function testMapArrayToFileReference(): void
    {
        $reflection = new ReflectionClass(FrontendUserController::class);
        $method = $reflection->getMethod('mapArrayToFileReference');
        $method->setAccessible(true);

        $propertyMappingConfigurationMock = $this->createMock(PropertyMappingConfiguration::class);
        $builder = $propertyMappingConfigurationMock->method('setTypeConverterOptions');
        $builder->willReturnSelf();

        $fileReferenceMock = $this->createMock(FileReference::class);
        $builder = $this->propertyMapperMock->method('convert');
        $builder->willReturn($fileReferenceMock);

        $result = $method->invoke($this->subject, []);

        $this->assertInstanceOf(FileReference::class, $result);
    }

    /**
     * Test mapArrayToFileReference with exception
     *
     * @return void
     * @throws ReflectionException
     */
    public function testMapArrayToFileReferenceWithException(): void
    {
        $reflection = new ReflectionClass(FrontendUserController::class);
        $method = $reflection->getMethod('mapArrayToFileReference');
        $method->setAccessible(true);

        $propertyMappingConfigurationMock = $this->createMock(PropertyMappingConfiguration::class);
        $builder = $propertyMappingConfigurationMock->method('setTypeConverterOptions');
        $builder->willReturnSelf();

        $builder = $this->propertyMapperMock->method('convert');
        $builder->willThrowException(new Exception('Exception while property mapping at property path "name".', 1297759968));

        $result = $method->invoke($this->subject, []);

        $this->assertNull($result);
    }

    /**
     * Test getStoragePid
     *
     * @return void
     * @throws ReflectionException
     */
    public function testGetStoragePid(): void
    {
        $reflection = new ReflectionClass(FrontendUserController::class);
        $method = $reflection->getMethod('getStoragePid');
        $method->setAccessible(true);

        $result = $method->invoke($this->subject);

        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    /**
     * Test for getLoginPageUrl
     *
     * @return void
     * @throws ReflectionException
     */
    public function testGetLoginPageUrl(): void
    {
        $reflection = new ReflectionClass(FrontendUserController::class);
        $method = $reflection->getMethod('getLoginPageUrl');
        $method->setAccessible(true);

        $result = $method->invoke($this->subject);

        $this->assertIsString($result);
        $this->assertSame('https://test', $result);
    }

    /**
     * Test getTranslation
     *
     * @return void
     * @throws ReflectionException
     */
    public function testGetTranslation(): void
    {
        $reflection = new ReflectionClass(FrontendUserController::class);
        $method = $reflection->getMethod('getTranslation');
        $method->setAccessible(true);

        $result = $method->invoke($this->subject, 'key');

        $this->assertIsString($result);
    }

    /**
     * Test getUriBuilder
     *
     * @return void
     * @throws ReflectionException
     */
    public function testGetUriBuilder(): void
    {
        $reflection = new ReflectionClass(FrontendUserController::class);
        $method = $reflection->getMethod('getUriBuilder');
        $method->setAccessible(true);

        $result = $method->invoke($this->subject);

        $this->assertInstanceOf(UriBuilder::class, $result);
    }

    /**
     * Configure request without params
     *
     * @return void
     */
    public function configureRequest(): void
    {
        $classSchemaMethodMock = $this->createMock(ClassSchemaMethod::class);
        $builder = $classSchemaMethodMock->method('getParameters');
        $builder->willReturn([]);

        $classSchemaMock = $this->createMock(ClassSchema::class);
        $builder = $classSchemaMock->method('getMethod');
        $builder->willReturn($classSchemaMethodMock);

        $reflectionServiceMock = $this->createMock(ReflectionService::class);
        $builder = $reflectionServiceMock->method('getClassSchema');
        $builder->willReturn($classSchemaMock);

        $this->subject->injectReflectionService($reflectionServiceMock);
    }

    /**
     * Configure request with $frontendUser param
     *
     * @return void
     */
    public function configureRequestWithFrontendUserParam(): void
    {
        $frontendUserParamMock = $this->createMock(ClassSchemaMethodParameter::class);
        $builder = $frontendUserParamMock->method('getType');
        $builder->willReturn(FrontendUser::class);

        $classSchemaMethodMock = $this->createMock(ClassSchemaMethod::class);
        $builder = $classSchemaMethodMock->method('getParameters');
        $builder->willReturn([
            'frontendUser' => $frontendUserParamMock,
        ]);

        $classSchemaMock = $this->createMock(ClassSchema::class);
        $builder = $classSchemaMock->method('getMethod');
        $builder->willReturn($classSchemaMethodMock);

        $reflectionServiceMock = $this->createMock(ReflectionService::class);
        $builder = $reflectionServiceMock->method('getClassSchema');
        $builder->willReturn($classSchemaMock);

        $this->subject->injectReflectionService($reflectionServiceMock);
    }

    /**
     * Configure request with params
     *
     * @return void
     */
    public function configureRequestWithParams(): void
    {
        $frontendUserParamMock = $this->createMock(ClassSchemaMethodParameter::class);
        $builder = $frontendUserParamMock->method('getType');
        $builder->willReturn(FrontendUser::class);

        $fileReferenceParamMock = $this->createMock(ClassSchemaMethodParameter::class);
        $builder = $fileReferenceParamMock->method('getType');
        $builder->willReturn(FileReference::class);

        $classSchemaMethodMock = $this->createMock(ClassSchemaMethod::class);
        $builder = $classSchemaMethodMock->method('getParameters');
        $builder->willReturn([
            'frontendUser' => $frontendUserParamMock,
            'frontendUserImage' => $fileReferenceParamMock,
        ]);

        $classSchemaMock = $this->createMock(ClassSchema::class);
        $builder = $classSchemaMock->method('getMethod');
        $builder->willReturn($classSchemaMethodMock);

        $reflectionServiceMock = $this->createMock(ReflectionService::class);
        $builder = $reflectionServiceMock->method('getClassSchema');
        $builder->willReturn($classSchemaMock);

        $this->subject->injectReflectionService($reflectionServiceMock);
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        $reflectionClass = new ReflectionClass(LocalizationUtility::class);
        $property = $reflectionClass->getProperty('configurationManager');
        $property->setAccessible(true);
        $property->setValue(null);

        GeneralUtility::purgeInstances();
        parent::tearDown();
    }
}
