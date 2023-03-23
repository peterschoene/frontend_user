<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Controller;

use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Property\PropertyMapper;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfiguration;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Crypto\PasswordHashing\InvalidPasswordHashException;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use Ydt\FrontendUser\Domain\Model\FrontendUser;
use Ydt\FrontendUser\Domain\Model\FrontendUserGroup;
use Ydt\FrontendUser\Domain\Repository\FrontendUserRepository;
use Ydt\FrontendUser\Domain\Repository\FrontendUserGroupRepository;
use Ydt\FrontendUser\Event\FrontendUserCreateAfterEvent;
use Ydt\FrontendUser\Event\FrontendUserUpdateAfterEvent;
use Ydt\FrontendUser\Event\FrontendUserDeleteAfterEvent;
use Ydt\FrontendUser\Event\FrontendUserFormViewModifyEvent;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Property\Exception;
use Ydt\FrontendUser\Property\TypeConverter\UploadedFileReferenceConverter;

/**
 * Class FrontendUserController
 * Frontend user controller
 */
class FrontendUserController extends ActionController
{
    /**
     * Frontend User Repository
     *
     * @var FrontendUserRepository
     */
    protected $frontendUserRepository;

    /**
     * Frontend User Group Repository
     *
     * @var FrontendUserGroupRepository
     */
    protected $frontendUserGroupRepository;

    /**
     * Password Hash Factory
     *
     * @var PasswordHashFactory
     */
    protected $passwordHashFactory;

    /**
     * Persistence Manager
     *
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * Context
     *
     * @var Context
     */
    protected $context;

    /**
     * Property Mapper
     *
     * @var PropertyMapper
     */
    protected $propertyMapper;

    /**
     * FrontendUserController constructor
     *
     * @param FrontendUserRepository $frontendUserRepository
     * @param FrontendUserGroupRepository $frontendUserGroupRepository
     * @param PasswordHashFactory $passwordHashFactory
     * @param PersistenceManager $persistenceManager
     * @param Context $context
     * @param PropertyMapper $propertyMapper
     */
    public function __construct(
        FrontendUserRepository $frontendUserRepository,
        FrontendUserGroupRepository $frontendUserGroupRepository,
        passwordHashFactory $passwordHashFactory,
        PersistenceManager $persistenceManager,
        Context $context,
        PropertyMapper $propertyMapper
    ) {
        $this->frontendUserRepository = $frontendUserRepository;
        $this->frontendUserGroupRepository = $frontendUserGroupRepository;
        $this->passwordHashFactory = $passwordHashFactory;
        $this->persistenceManager = $persistenceManager;
        $this->context = $context;
        $this->propertyMapper = $propertyMapper;
    }

    /**
     * Create new frontend user
     *
     * @param FrontendUser|null $newFrontendUser
     * @return ResponseInterface
     */
    public function newAction(?FrontendUser $newFrontendUser = null): ResponseInterface
    {
        $frontendUserId = $this->getFrontendUserIdFromAspect();
        if ($frontendUserId) {
            $frontendUser = $this->frontendUserRepository->findByUid($frontendUserId);

            return $this->redirect('edit', null, null, ['frontendUser' => $frontendUser]);
        }

        $formFields = [];
        if (!empty($this->settings['newFrontendUserFormFields'])) {
            $formFields = explode(',', $this->settings['newFrontendUserFormFields']);
        }

        $this->view->assignMultiple([
            'newFrontendUser'   => $newFrontendUser,
            'storagePid'        => $this->getStoragePid(),
            'formFields'        => $formFields,
        ]);

        $this->eventDispatcher->dispatch(new FrontendUserFormViewModifyEvent($this->view));

        return $this->htmlResponse();
    }

    /**
     * Create frontend user
     *
     * @param FrontendUser $newFrontendUser
     * @return ResponseInterface
     * @throws IllegalObjectTypeException
     * @throws InvalidPasswordHashException
     * @Extbase\Validate (param="newFrontendUser", validator="Ydt\FrontendUser\Domain\Validator\FrontendUserValidator")
     */
    public function createAction(FrontendUser $newFrontendUser): ResponseInterface
    {
        $arguments = $this->request->getArguments();
        $pid = (int)$arguments['pid'] ?? 0;

        $username = $newFrontendUser->getUsername();
        $frontendUser = $this->frontendUserRepository->findByUsername($username, $pid);
        if ($frontendUser instanceof FrontendUser) {
            $this->addFlashMessage(
                $this->getTranslation('errorMessage.username.notAvailable'),
                '',
                FlashMessage::ERROR
            );

            return (new ForwardResponse('new'))
                ->withArguments(['forwarded' => true]);
        }

        $newFrontendUser->setPid($pid);

        $password = $newFrontendUser->getPassword();
        if (strlen($password) < 10 || strlen($password) > 32 || !preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,}$/', $password)) {
            $this->addFlashMessage(
                $this->getTranslation('errorMessage.password.complexity'),
                '',
                FlashMessage::ERROR
            );
            return (new ForwardResponse('new'))
                ->withArguments(['forwarded' => true]);
        }
        $hashedPassword = $this->generateHashedPassword($password);
        $newFrontendUser->setPassword($hashedPassword);

        $defaultFrontendUserGroupId = $this->settings['defaultFrontendUserGroupId'] ?? '';
        if (!empty($defaultFrontendUserGroupId)) {
            $defaultFrontendUserGroup = $this->frontendUserGroupRepository->findByUid($defaultFrontendUserGroupId);
            if ($defaultFrontendUserGroup instanceof FrontendUserGroup) {
                $newFrontendUser->addUserGroup($defaultFrontendUserGroup);
            }
        }

        $imageFileData = $arguments['image'] ?? [];
        if (!empty($imageFileData) && (!isset($imageFileData['error']) || $imageFileData['error'] === UPLOAD_ERR_OK)) {
            $imageFileReference = $this->mapArrayToFileReference($imageFileData);
            if ($imageFileReference) {
                $newFrontendUser->addImage($imageFileReference);
            } else {
                $this->addFlashMessage(
                    $this->getTranslation('errorMessage.image.canNotBeSaved'),
                    '',
                    FlashMessage::ERROR
                );
            }
        }

        $this->frontendUserRepository->add($newFrontendUser);
        $this->persistenceManager->persistAll();

        $this->eventDispatcher->dispatch(new FrontendUserCreateAfterEvent($newFrontendUser, $this->request));

        $loginPageUrl = $this->getLoginPageUrl();
        if (
            isset($this->settings['enableRedirectToLoginPage'])
            && $this->settings['enableRedirectToLoginPage']
            && !empty($loginPageUrl)
        ) {
            return $this->redirectToUri($loginPageUrl);
        }

        return $this->redirect('edit', null, null, ['frontendUser' => $newFrontendUser]);
    }

    /**
     * Edit frontend user
     *
     * @param FrontendUser $frontendUser
     * @return ResponseInterface
     * @Extbase\IgnoreValidation("frontendUser")
     */
    public function editAction(FrontendUser $frontendUser): ResponseInterface
    {
        $formFields = [];
        if (!empty($this->settings['editFrontendUserFormFields'])) {
            $formFields = explode(',', $this->settings['editFrontendUserFormFields']);
        }

        $loginPageUrl = $this->getLoginPageUrl();

        $this->view->assignMultiple([
            'frontendUser'  => $frontendUser,
            'formFields'    => $formFields,
            'loginPageUrl'  => $loginPageUrl,
        ]);

        $this->eventDispatcher->dispatch(new FrontendUserFormViewModifyEvent($this->view));

        return $this->htmlResponse();
    }

    /**
     * Update frontend user
     *
     * @param FrontendUser $frontendUser
     * @return ResponseInterface
     * @throws AspectNotFoundException
     * @throws IllegalObjectTypeException
     * @throws InvalidPasswordHashException
     * @throws UnknownObjectException
     */
    public function updateAction(FrontendUser $frontendUser): ResponseInterface
    {
        $frontendUserId = $this->getFrontendUserIdFromAspect();
        if ($frontendUserId === (int)$frontendUser->getUid()) {
            $frontendUserUsername = (string)$this->context->getPropertyFromAspect('frontend.user', 'username');
            if (!$frontendUserUsername || $frontendUserUsername !== $frontendUser->getUsername()) {
                $this->addFlashMessage(
                    $this->getTranslation('errorMessage.username.canNotBeChanged'),
                    '',
                    FlashMessage::ERROR
                );

                return (new ForwardResponse('edit'))
                    ->withArguments([
                        'forwarded' => true,
                        'frontendUser' => $frontendUser,
                    ]);
            }

            $arguments = $this->request->getArguments();

            if (isset($arguments['changePassword']) && $arguments['changePassword']) {
                $password = $frontendUser->getPassword();
                $passwordConfirmation = $frontendUser->getPasswordConfirmation();
                if ($password && $passwordConfirmation && $password === $passwordConfirmation) {
                    $hashedPassword = $this->generateHashedPassword($password);
                    $frontendUser->setPassword($hashedPassword);
                } else {
                    $this->addFlashMessage(
                        $this->getTranslation('errorMessage.password.notMatched'),
                        '',
                        FlashMessage::ERROR
                    );

                    return (new ForwardResponse('edit'))
                        ->withArguments([
                            'forwarded' => true,
                            'frontendUser' => $frontendUser,
                        ]);
                }
            }

            $imageFileData = $arguments['image'] ?? [];
            if (!empty($imageFileData) && (!isset($imageFileData['error']) || $imageFileData['error'] === UPLOAD_ERR_OK)) {
                $imageFileReference = $this->mapArrayToFileReference($imageFileData);
                if ($imageFileReference) {
                    $frontendUser->addImage($imageFileReference);
                } else {
                    $this->addFlashMessage(
                        $this->getTranslation('errorMessage.image.canNotBeSaved'),
                        '',
                        FlashMessage::ERROR
                    );
                }
            }

            $this->frontendUserRepository->update($frontendUser);
            $this->persistenceManager->persistAll();

            $this->addFlashMessage($this->getTranslation('successMessage.frontendUserSaved'));

            $this->eventDispatcher->dispatch(new FrontendUserUpdateAfterEvent($frontendUser, $this->request));
        }

        return $this->redirect('edit', null, null, ['frontendUser' => $frontendUser]);
    }

    /**
     * Delete frontend user
     *
     * @param FrontendUser $frontendUser
     * @return ResponseInterface
     * @throws IllegalObjectTypeException
     * @Extbase\IgnoreValidation("frontendUser")
     */
    public function deleteAction(FrontendUser $frontendUser): ResponseInterface
    {
        $frontendUserId = $this->getFrontendUserIdFromAspect();
        if (
            isset($this->settings['enableFrontendUserDeletion'])
            && $this->settings['enableFrontendUserDeletion']
            && $frontendUserId === (int)$frontendUser->getUid()
        ) {
            $this->frontendUserRepository->remove($frontendUser);

            $this->eventDispatcher->dispatch(new FrontendUserDeleteAfterEvent($frontendUser, $this->request));
        }

        return $this->redirect('new');
    }

    /**
     * Delete frontend user image
     *
     * @param FrontendUser $frontendUser
     * @param FileReference $frontendUserImage
     * @return ResponseInterface
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     * @Extbase\IgnoreValidation("frontendUser")
     */
    public function deleteImageAction(FrontendUser $frontendUser, FileReference $frontendUserImage): ResponseInterface
    {
        $frontendUserId = $this->getFrontendUserIdFromAspect();
        if ($frontendUserId === (int)$frontendUser->getUid()) {
            $frontendUser->removeImage($frontendUserImage);
            $this->frontendUserRepository->update($frontendUser);
        }

        return $this->redirect('edit', null, null, ['frontendUser' => $frontendUser]);
    }

    /**
     * @inheritdoc
     */
    protected function getErrorFlashMessage(): string
    {
        $action = substr($this->actionMethodName, 0, strpos($this->actionMethodName, 'Action'));

        return sprintf('An error occurred while trying to %s a user.', $action);
    }

    /**
     * Get frontend user id property from aspect
     *
     * @return int
     */
    protected function getFrontendUserIdFromAspect(): int
    {
        try {
            $frontendUserId = (int)$this->context->getPropertyFromAspect('frontend.user', 'id');
        } catch (AspectNotFoundException $exception) {
            $frontendUserId = 0;
        }

        return $frontendUserId;
    }

    /**
     * Generate hashed password
     *
     * @param string $password
     * @return string
     * @throws InvalidPasswordHashException
     */
    protected function generateHashedPassword(string $password): string
    {
        $hashInstance = $this->passwordHashFactory->getDefaultHashInstance('FE');
        return $hashInstance->getHashedPassword($password);
    }

    /**
     * Map an array to FileReference
     *
     * @param array $imageFileData
     * @return FileReference|null
     */
    protected function mapArrayToFileReference(array $imageFileData): ?FileReference
    {
        $frontendUserImageFolderId = '';
        if (!empty($this->settings['frontendUserImageFolder'])) {
            $frontendUserImageFolderId = substr(
                $this->settings['frontendUserImageFolder'],
                strpos($this->settings['frontendUserImageFolder'], ':') + 1
            );
        }

        try {
            $configuration = new PropertyMappingConfiguration();
            $configuration->setTypeConverterOptions(
                UploadedFileReferenceConverter::class,
                [UploadedFileReferenceConverter::CONFIGURATION_TARGET_FOLDER_IDENTIFIER => $frontendUserImageFolderId]
            );

            $fileReference = $this->propertyMapper->convert($imageFileData, FileReference::class, $configuration);
        } catch (Exception $exception) {
            $fileReference = null;
        }

        return $fileReference;
    }

    /**
     * Get frontend user storage page ID
     *
     * @return int
     */
    protected function getStoragePid(): int
    {
        $storagePid = 0;
        if (
            isset($GLOBALS['TYPO3_CONF_VARS']['FE']['checkFeUserPid'])
            && $GLOBALS['TYPO3_CONF_VARS']['FE']['checkFeUserPid']
            && isset($this->settings['frontendUserStoragePid'])
        ) {
            $storagePid = (int)$this->settings['frontendUserStoragePid'];
        }

        return $storagePid;
    }

    /**
     * Get login page url
     *
     * @return string
     */
    protected function getLoginPageUrl(): string
    {
        $loginPageUrl = '';
        if (!empty($this->settings['redirectLoginPageId'])) {
            $uriBuilder = $this->getUriBuilder();
            $uriBuilder->setTargetPageUid((int)$this->settings['redirectLoginPageId']);
            $loginPageUrl = $uriBuilder->buildFrontendUri();
        }

        return $loginPageUrl;
    }

    /**
     * Get translation
     *
     * @param string $key
     * @return string
     */
    protected function getTranslation(string $key): string
    {
        return (string)LocalizationUtility::translate($key, 'frontend_user');
    }

    /**
     * Get uri builder
     *
     * @return UriBuilder
     */
    private function getUriBuilder(): UriBuilder
    {
        if ($this->uriBuilder === null) {
            $this->uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        }

        return $this->uriBuilder;
    }
}
