<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Tests\Unit\Domain\Model;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Ydt\FrontendUser\Domain\Model\FrontendUserGroup;

/**
 * Class FrontendUserGroupTest
 * Testcase for class \Ydt\FrontendUser\Domain\Model\FrontendUserGroup
 */
class FrontendUserGroupTest extends UnitTestCase
{
    /**
     * Frontend User Group
     *
     * @var FrontendUserGroup
     */
    private $subject;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new FrontendUserGroup('');
    }

    /**
     * Test getTitle
     *
     * @return void
     */
    public function testGetTitle(): void
    {
        $title = 'test';
        $this->subject->setTitle($title);

        $result = $this->subject->getTitle();

        $this->assertIsString($result);
        $this->assertSame($title, $result);
    }

    /**
     * Test getDescription
     *
     * @return void
     */
    public function testGetDescription(): void
    {
        $description = 'test';
        $this->subject->setDescription($description);

        $result = $this->subject->getDescription();

        $this->assertIsString($result);
        $this->assertSame($description, $result);
    }

    /**
     * Test getDescription with NULL
     *
     * @return void
     */
    public function testGetDescriptionReturnsNull(): void
    {
        $result = $this->subject->getDescription();

        $this->assertNull($result);
    }

    /**
     * Test getSubgroups
     *
     * @return void
     */
    public function testGetSubgroups(): void
    {
        $result = $this->subject->getSubgroups();

        $this->assertInstanceOf(ObjectStorage::class, $result);
    }

    /**
     * Test setSubgroups
     *
     * @return void
     */
    public function testSetSubgroups(): void
    {
        $objectStorageMock = $this->createMock(ObjectStorage::class);

        $this->subject->setSubgroups($objectStorageMock);
    }

    /**
     * Test addSubgroup
     *
     * @return void
     */
    public function testAddSubgroup(): void
    {
        $subgroupMock = $this->createMock(FrontendUserGroup::class);

        $objectStorageMock = $this->createMock(ObjectStorage::class);
        $builder = $objectStorageMock->method('contains');
        $builder->with($subgroupMock);
        $builder->willReturn(false);

        $this->subject->setSubgroups($objectStorageMock);

        $this->subject->addSubgroup($subgroupMock);
    }

    /**
     * Test removeSubgroup
     *
     * @return void
     */
    public function testRemoveSubgroup(): void
    {
        $subgroupMock = $this->createMock(FrontendUserGroup::class);

        $objectStorageMock = $this->createMock(ObjectStorage::class);
        $builder = $objectStorageMock->method('contains');
        $builder->with($subgroupMock);
        $builder->willReturn(true);

        $this->subject->setSubgroups($objectStorageMock);

        $this->subject->removeSubgroup($subgroupMock);
    }
}
