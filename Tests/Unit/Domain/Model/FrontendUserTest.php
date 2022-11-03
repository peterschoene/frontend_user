<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Tests\Unit\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use Ydt\FrontendUser\Domain\Model\FrontendUser;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Ydt\FrontendUser\Domain\Model\FrontendUserGroup;
use DateTime;

/**
 * Class FrontendUserTest
 * Testcase for class \Ydt\FrontendUser\Domain\Model\FrontendUser
 */
class FrontendUserTest extends UnitTestCase
{
    /**
     * Frontend User
     *
     * @var FrontendUser
     */
    private $subject;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new FrontendUser('');
    }

    /**
     * Test getUsername
     *
     * @return void
     */
    public function testGetUsername(): void
    {
        $username = 'test';
        $this->subject->setUsername($username);

        $result = $this->subject->getUsername();

        $this->assertIsString($result);
        $this->assertEquals($username, $result);
    }

    /**
     * Test getPassword
     *
     * @return void
     */
    public function testGetPassword(): void
    {
        $password = 'test123';
        $this->subject->setPassword($password);

        $result = $this->subject->getPassword();

        $this->assertIsString($result);
    }

    /**
     * Test getPasswordConfirmation
     *
     * @return void
     */
    public function testGetPasswordConfirmation(): void
    {
        $password = 'test123';
        $this->subject->setPasswordConfirmation($password);

        $result = $this->subject->getPasswordConfirmation();

        $this->assertIsString($result);
    }

    /**
     * Test getUserGroups
     *
     * @return void
     */
    public function testGetUserGroups(): void
    {
        $result = $this->subject->getUserGroups();

        $this->assertInstanceOf(ObjectStorage::class, $result);
    }

    /**
     * Test setUserGroups
     *
     * @return void
     */
    public function testSetUserGroups(): void
    {
        $objectStorageMock = $this->createMock(ObjectStorage::class);

        $this->subject->setUserGroups($objectStorageMock);
    }

    /**
     * Test addUserGroup
     *
     * @return void
     */
    public function testAddUserGroup(): void
    {
        $userGroupMock = $this->createMock(FrontendUserGroup::class);

        $objectStorageMock = $this->createMock(ObjectStorage::class);
        $builder = $objectStorageMock->method('contains');
        $builder->with($userGroupMock);
        $builder->willReturn(false);

        $this->subject->setUserGroups($objectStorageMock);

        $this->subject->addUserGroup($userGroupMock);
    }

    /**
     * Test removeUserGroup
     *
     * @return void
     */
    public function testRemoveUserGroup(): void
    {
        $userGroupMock = $this->createMock(FrontendUserGroup::class);

        $objectStorageMock = $this->createMock(ObjectStorage::class);
        $builder = $objectStorageMock->method('contains');
        $builder->with($userGroupMock);
        $builder->willReturn(true);

        $this->subject->setUserGroups($objectStorageMock);

        $this->subject->removeUserGroup($userGroupMock);
    }

    /**
     * Test getCompany
     *
     * @return void
     */
    public function testGetCompany(): void
    {
        $company = 'test';
        $this->subject->setCompany($company);

        $result = $this->subject->getCompany();

        $this->assertIsString($result);
        $this->assertSame($result, $company);
    }

    /**
     * Test getJobTitle
     *
     * @return void
     */
    public function testGetJobTitle(): void
    {
        $jobTitle = 'test';
        $this->subject->setJobTitle($jobTitle);

        $result = $this->subject->getJobTitle();

        $this->assertIsString($result);
        $this->assertSame($result, $jobTitle);
    }

    /**
     * Test getName
     *
     * @return void
     */
    public function testGetName(): void
    {
        $name = 'test';
        $this->subject->setName($name);

        $result = $this->subject->getName();

        $this->assertIsString($result);
        $this->assertSame($result, $name);
    }

    /**
     * Test getFirstName
     *
     * @return void
     */
    public function testGetFirstName(): void
    {
        $firstName = 'test';
        $this->subject->setFirstName($firstName);

        $result = $this->subject->getFirstName();

        $this->assertIsString($result);
        $this->assertSame($result, $firstName);
    }

    /**
     * Test getMiddleName
     *
     * @return void
     */
    public function testGetMiddleName(): void
    {
        $middleName = 'test';
        $this->subject->setMiddleName($middleName);

        $result = $this->subject->getMiddleName();

        $this->assertIsString($result);
        $this->assertSame($result, $middleName);
    }

    /**
     * Test getLastName
     *
     * @return void
     */
    public function testGetLastName(): void
    {
        $lastName = 'test';
        $this->subject->setLastName($lastName);

        $result = $this->subject->getLastName();

        $this->assertIsString($result);
        $this->assertSame($result, $lastName);
    }

    /**
     * Test getStreetAddress
     *
     * @return void
     */
    public function testGetStreetAddress(): void
    {
        $streetAddress = 'test';
        $this->subject->setStreetAddress($streetAddress);

        $result = $this->subject->getStreetAddress();

        $this->assertIsString($result);
        $this->assertSame($result, $streetAddress);
    }

    /**
     * Test getZipCode
     *
     * @return void
     */
    public function testGetZipCode(): void
    {
        $zipCode = '123';
        $this->subject->setZipCode($zipCode);

        $result = $this->subject->getZipCode();

        $this->assertIsString($result);
        $this->assertSame($result, $zipCode);
    }

    /**
     * Test getCity
     *
     * @return void
     */
    public function testGetCity(): void
    {
        $city = 'test';
        $this->subject->setCity($city);

        $result = $this->subject->getCity();

        $this->assertIsString($result);
        $this->assertSame($result, $city);
    }

    /**
     * Test getCountry
     *
     * @return void
     */
    public function testGetCountry(): void
    {
        $country = 'test';
        $this->subject->setCountry($country);

        $result = $this->subject->getCountry();

        $this->assertIsString($result);
        $this->assertSame($result, $country);
    }

    /**
     * Test getPhone
     *
     * @return void
     */
    public function testGetPhone(): void
    {
        $phone = '123';
        $this->subject->setPhone($phone);

        $result = $this->subject->getPhone();

        $this->assertIsString($result);
        $this->assertSame($phone, $result);
    }

    /**
     * Test getFax
     *
     * @return void
     */
    public function testGetFax(): void
    {
        $fax = '123';
        $this->subject->setFax($fax);

        $result = $this->subject->getFax();

        $this->assertIsString($result);
        $this->assertSame($result, $fax);
    }

    /**
     * Test getEmail
     *
     * @return void
     */
    public function testGetEmail(): void
    {
        $email = 'test@test.com';
        $this->subject->setEmail($email);

        $result = $this->subject->getEmail();

        $this->assertIsString($result);
        $this->assertEquals($email, $result);
    }

    /**
     * Test getUrl
     *
     * @return void
     */
    public function testGetUrl(): void
    {
        $url = 'https://test';
        $this->subject->setUrl($url);

        $result = $this->subject->getUrl();

        $this->assertIsString($result);
        $this->assertSame($result, $url);
    }

    /**
     * Test getImages
     *
     * @return void
     */
    public function testGetImages(): void
    {
        $objectStorageMock = $this->createMock(ObjectStorage::class);
        $this->subject->setImages($objectStorageMock);

        $result = $this->subject->getImages();

        $this->assertInstanceOf(ObjectStorage::class, $result);
    }

    /**
     * Test setImages
     *
     * @return void
     */
    public function testSetImages(): void
    {
        $objectStorageMock = $this->createMock(ObjectStorage::class);

        $this->subject->setImages($objectStorageMock);
    }

    /**
     * Test addImage
     *
     * @return void
     */
    public function testAddImage(): void
    {
        $fileReferenceMock = $this->createMock(FileReference::class);

        $objectStorageMock = $this->createMock(ObjectStorage::class);
        $builder = $objectStorageMock->method('contains');
        $builder->with($fileReferenceMock);
        $builder->willReturn(false);

        $this->subject->setImages($objectStorageMock);

        $this->subject->addImage($fileReferenceMock);
    }

    /**
     * Test removeImage
     *
     * @return void
     */
    public function testRemoveImage(): void
    {
        $fileReferenceMock = $this->createMock(FileReference::class);

        $objectStorageMock = $this->createMock(ObjectStorage::class);
        $builder = $objectStorageMock->method('contains');
        $builder->with($fileReferenceMock);
        $builder->willReturn(true);

        $this->subject->setImages($objectStorageMock);

        $this->subject->removeImage($fileReferenceMock);
    }

    /**
     * Test getImage
     *
     * @return void
     */
    public function testGetImage(): void
    {
        $fileReferenceMock = $this->createMock(FileReference::class);
        $objectStorageMock = $this->createMock(ObjectStorage::class);

        $builder = $objectStorageMock->method('count');
        $builder->willReturn(1);
        $builder = $objectStorageMock->method('current');
        $builder->willReturn($fileReferenceMock);

        $this->subject->setImages($objectStorageMock);

        $result = $this->subject->getImage();

        $this->assertInstanceOf(FileReference::class, $result);
    }

    /**
     * Test getImage if object storage is empty
     *
     * @return void
     */
    public function testGetImageIfIsNull(): void
    {
        $result = $this->subject->getImage();

        $this->assertNull($result);
    }

    /**
     * Test getLastLogin
     *
     * @return void
     */
    public function testGetLastLogin(): void
    {
        $dateTimeMock = $this->createMock(DateTime::class);
        $this->subject->setLastLogin($dateTimeMock);

        $result = $this->subject->getLastLogin();

        $this->assertInstanceOf(DateTime::class, $result);
    }
}
