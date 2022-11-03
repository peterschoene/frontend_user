<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Tests\Unit\Property\TypeConverter;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Config\Resource\FileResource;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileReference as CoreFileReference;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Ydt\FrontendUser\Property\TypeConverter\UploadedFileReferenceConverter;
use ReflectionClass;
use ReflectionException;
use InvalidArgumentException;

/**
 * Class UploadedFileReferenceConverterTest
 * Testcase for class \Ydt\FrontendUser\Property\TypeConverter\UploadedFileReferenceConverter
 */
class UploadedFileReferenceConverterTest extends UnitTestCase
{
    /**
     * Uploaded File Reference Converter
     *
     * @var UploadedFileReferenceConverter
     */
    private $subject;

    /**
     * Storage Repository Mock
     *
     * @var MockObject|StorageRepository
     */
    private $storageRepositoryMock;

    /**
     * Resource Factory Mock
     *
     * @var MockObject|ResourceFactory
     */
    private $resourceFactoryMock;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'] = 'jpeg,jpg';

        $this->storageRepositoryMock = $this->createMock(StorageRepository::class);
        $this->resourceFactoryMock = $this->createMock(ResourceFactory::class);

        $this->subject = new UploadedFileReferenceConverter(
            $this->storageRepositoryMock,
            $this->resourceFactoryMock
        );
    }

    /**
     * Test convertFrom
     *
     * @return void
     */
    public function testConvertFrom(): void
    {
        $source = [
            'tmp_name' => 'test',
            'name' => 'test.jpeg',
            'size' => 100,
            'type' => 'image/jpeg',
        ];

        $defaultStorageMock = $this->createMock(ResourceStorage::class);

        $folderMock = $this->createMock(Folder::class);
        $builder = $defaultStorageMock->method('getFolder');
        $builder->willReturn($folderMock);

        $fileMock = $this->createMock(File::class);
        $builder = $fileMock->method('getUid');
        $builder->willReturn(1);

        $builder = $defaultStorageMock->method('addFile');
        $builder->willReturn($fileMock);

        $builder = $this->storageRepositoryMock->method('getDefaultStorage');
        $builder->willReturn($defaultStorageMock);

        $fileReferenceMock = $this->createMock(CoreFileReference::class);
        $builder = $fileReferenceMock->method('getOriginalFile');
        $builder->willReturn($fileMock);

        $builder = $this->resourceFactoryMock->method('createFileReferenceObject');
        $builder->willReturn($fileReferenceMock);

        $configurationMock = $this->createMock(PropertyMappingConfigurationInterface::class);
        $builder = $configurationMock->method('getConfigurationValue');
        $builder->willReturn('test');

        $result = $this->subject->convertFrom($source, FileResource::class, [], $configurationMock);

        $this->assertInstanceOf(FileReference::class, $result);
    }

    /**
     * Test convertFrom with exception
     *
     * @return void
     */
    public function testConvertFromWithException(): void
    {
        $source = [
            'tmp_name' => 'test',
            'name' => 'test.jpeg',
            'size' => 100,
            'type' => 'image/jpeg',
        ];

        $defaultStorageMock = $this->createMock(ResourceStorage::class);

        $defaultFolderMock = $this->createMock(Folder::class);
        $builder = $defaultStorageMock->method('getDefaultFolder');
        $builder->willReturn($defaultFolderMock);

        $builder = $this->storageRepositoryMock->method('getDefaultStorage');
        $builder->willReturn($defaultStorageMock);

        $builder = $defaultStorageMock->method('addFile');
        $builder->willThrowException(new InvalidArgumentException('File "test.jpeg" does not exist.', 1319552745));

        $result = $this->subject->convertFrom($source, FileResource::class);

        $this->assertInstanceOf(Error::class, $result);
    }

    /**
     * Test canConvertFrom
     *
     * @return void
     */
    public function testCanConvertFrom(): void
    {
        $source = [
            'tmp_name' => 'test',
            'name' => 'test.jpeg',
            'size' => 100,
            'type' => 'image/jpeg',
        ];

        $result = $this->subject->canConvertFrom($source, FileResource::class);

        $this->assertIsBool($result);
        $this->assertTrue($result);
    }

    /**
     * Test isImageFileExtensionValid
     *
     * @return void
     * @throws ReflectionException
     */
    public function testIsImageFileExtensionValid(): void
    {
        $reflection = new ReflectionClass(UploadedFileReferenceConverter::class);
        $method = $reflection->getMethod('isImageFileExtensionValid');
        $method->setAccessible(true);

        $result = $method->invoke($this->subject, 'test.jpg');

        $this->assertIsBool($result);
        $this->assertTrue($result);
    }
}
