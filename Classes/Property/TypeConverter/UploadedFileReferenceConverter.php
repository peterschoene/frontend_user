<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Property\TypeConverter;

use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;
use Exception;

/**
 * Class UploadedFileReferenceConverter
 * Convert uploaded file data array to Extbase file reference
 */
class UploadedFileReferenceConverter extends AbstractTypeConverter
{
    /**
     * Converter configuration
     */
    const CONFIGURATION_TARGET_FOLDER_IDENTIFIER = 'targetFolderIdentifier';

    /**
     * Storage Repository
     *
     * @var StorageRepository
     */
    protected $storageRepository;

    /**
     * Resource Factory
     *
     * @var ResourceFactory
     */
    protected $resourceFactory;

    /**
     * Source types
     *
     * @var array
     */
    protected $sourceTypes = ['array'];

    /**
     * Target type
     *
     * @var string
     */
    protected $targetType = FileReference::class;

    /**
     * Priority
     *
     * @var int
     */
    protected $priority = 15;

    /**
     * UploadedFileReferenceConverter constructor
     *
     * @param StorageRepository $storageRepository
     * @param ResourceFactory $resourceFactory
     */
    public function __construct(
        StorageRepository $storageRepository,
        ResourceFactory $resourceFactory
    ) {
        $this->storageRepository = $storageRepository;
        $this->resourceFactory = $resourceFactory;
    }

    /**
     * @inheritdoc
     */
    public function convertFrom(
        $source,
        string $targetType,
        array $convertedChildProperties = [],
        PropertyMappingConfigurationInterface $configuration = null
    ) {
        $result = null;
        $defaultStorage = $this->storageRepository->getDefaultStorage();
        if ($defaultStorage) {
            try {
                if (
                    $configuration
                    && $configuration->getConfigurationValue(self::class, self::CONFIGURATION_TARGET_FOLDER_IDENTIFIER)
                ) {
                    $targetFolderIdentifier = $configuration->getConfigurationValue(self::class, self::CONFIGURATION_TARGET_FOLDER_IDENTIFIER);
                    $targetFolder = $defaultStorage->getFolder($targetFolderIdentifier);
                } else {
                    $targetFolder = $defaultStorage->getDefaultFolder();
                }

                $file = $defaultStorage->addFile($source['tmp_name'], $targetFolder, $source['name']);

                $fileResource = $this->resourceFactory->createFileReferenceObject(['uid_local' => (int)$file->getUid()]);

                $result = new FileReference();
                $result->setOriginalResource($fileResource);
            } catch (Exception $exception) {
                $result = new Error($exception->getMessage(), (int)uniqid());
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function canConvertFrom($source, string $targetType): bool
    {
        return is_array($source)
            && !empty($source['tmp_name'])
            && !empty($source['name'])
            && !empty($source['size'])
            && !empty($source['type'])
            && $this->isImageFileExtensionValid($source['name']);
    }

    /**
     * Check if image file extension valid
     *
     * @param string $imageFileName
     * @return bool
     */
    protected function isImageFileExtensionValid(string $imageFileName): bool
    {
        $imageFileNameInfo = pathinfo($imageFileName);
        $imageFileExtensions = explode(',', $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']);

        return isset($imageFileNameInfo['extension']) && in_array($imageFileNameInfo['extension'], $imageFileExtensions);
    }
}
