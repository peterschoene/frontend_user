<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use DateTime;

/**
 * Class FrontendUser
 * Frontend user domain model
 */
class FrontendUser extends AbstractEntity
{
    /**
     * Username
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     * @Extbase\Validate("StringLength", options={"maximum": 255})
     * @Extbase\Validate("Text")
     */
    protected $username;

    /**
     * Password
     *
     * @var string
     */
    protected $password;

    /**
     * Password confirmation
     *
     * @var string
     * @Extbase\ORM\Transient
     */
    protected $passwordConfirmation;

    /**
     * Frontend user groups
     *
     * @var ObjectStorage<FrontendUserGroup>
     */
    protected $userGroups;

    /**
     * Company
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 80})
     * @Extbase\Validate("Text")
     */
    protected $company;

    /**
     * Job title
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 40})
     * @Extbase\Validate("Text")
     */
    protected $jobTitle;

    /**
     * Name
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 160})
     * @Extbase\Validate("Text")
     */
    protected $name;

    /**
     * First name
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 50})
     * @Extbase\Validate("Text")
     */
    protected $firstName;

    /**
     * Middle name
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 50})
     * @Extbase\Validate("Text")
     */
    protected $middleName;

    /**
     * Last name
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 50})
     * @Extbase\Validate("Text")
     */
    protected $lastName;

    /**
     * Street address
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 255})
     * @Extbase\Validate("Text")
     */
    protected $streetAddress;

    /**
     * Zip code
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 10})
     * @Extbase\Validate("AlphanumericValidator")
     */
    protected $zipCode;

    /**
     * City
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 50})
     * @Extbase\Validate("Text")
     */
    protected $city;

    /**
     * Country
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 40})
     * @Extbase\Validate("Text")
     */
    protected $country;

    /**
     * Phone
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 30})
     * @Extbase\Validate("Ydt\FrontendUser\Domain\Validator\PhoneValidator")
     */
    protected $phone;

    /**
     * Fax
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 30})
     * @Extbase\Validate("Ydt\FrontendUser\Validation\Validator\DigitValidator")
     */
    protected $fax;

    /**
     * Email
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 255})
     * @Extbase\Validate("EmailAddress")
     */
    protected $email;

    /**
     * Homepage url
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 80})
     * @Extbase\Validate("Text")
     * @Extbase\Validate("Url")
     */
    protected $url;

    /**
     * Images
     *
     * @var ObjectStorage<FileReference>
     */
    protected $images;

    /**
     * Last login
     *
     * @var DateTime|null
     */
    protected $lastLogin;

    /**
     * FrontendUser constructor
     *
     * @param string $username
     * @param string $password
     */
    public function __construct(
        string $username,
        string $password = ''
    ) {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername(): string
    {
        return (string)$this->username;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return void
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * Get password confirmation
     *
     * @return string
     */
    public function getPasswordConfirmation(): string
    {
        return (string)$this->passwordConfirmation;
    }

    /**
     * Set password confirmation
     *
     * @param string $passwordConfirmation
     * @return void
     */
    public function setPasswordConfirmation(string $passwordConfirmation): void
    {
        $this->passwordConfirmation = $passwordConfirmation;
    }

    /**
     * Get frontend user groups
     *
     * @return ObjectStorage<FrontendUserGroup>
     */
    public function getUserGroups(): ObjectStorage
    {
        if ($this->userGroups === null) {
            $this->userGroups = new ObjectStorage();
        }

        return $this->userGroups;
    }

    /**
     * Set frontend user groups
     *
     * @param ObjectStorage $userGroups
     * @return void
     */
    public function setUserGroups(ObjectStorage $userGroups): void
    {
        $this->userGroups = $userGroups;
    }

    /**
     * Add frontend user group
     *
     * @param FrontendUserGroup $userGroup
     * @return void
     */
    public function addUserGroup(FrontendUserGroup $userGroup): void
    {
        $userGroups = $this->getUserGroups();
        if (!$userGroups->contains($userGroup)) {
            $userGroups->attach($userGroup);
        }
    }

    /**
     * Remove frontend user group
     *
     * @param FrontendUserGroup $userGroups
     * @return void
     */
    public function removeUserGroup(FrontendUserGroup $userGroup): void
    {
        $userGroups = $this->getUserGroups();
        if ($userGroups->contains($userGroup)) {
            $userGroups->detach($userGroup);
        }
    }

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany(): string
    {
        return (string)$this->company;
    }

    /**
     * Set company
     *
     * @param string $company
     * @return void
     */
    public function setCompany(string $company = ''): void
    {
        $this->company = $company;
    }

    /**
     * Get job title
     *
     * @return string
     */
    public function getJobTitle(): string
    {
        return (string)$this->jobTitle;
    }

    /**
     * Set job title
     *
     * @param string $jobTitle
     * @return void
     */
    public function setJobTitle(string $jobTitle = ''): void
    {
        $this->jobTitle = $jobTitle;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return (string)$this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name = ''): void
    {
        $this->name = $name;
    }

    /**
     * Get first name
     *
     * @return string
     */
    public function getFirstName(): string
    {
        return (string)$this->firstName;
    }

    /**
     * Set first name
     *
     * @param string $firstName
     * @return void
     */
    public function setFirstName(string $firstName = ''): void
    {
        $this->firstName = $firstName;
    }

    /**
     * Get middle name
     *
     * @return string
     */
    public function getMiddleName(): string
    {
        return (string)$this->middleName;
    }

    /**
     * Set middle name
     *
     * @param string $middleName
     * @return void
     */
    public function setMiddleName(string $middleName = ''): void
    {
        $this->middleName = $middleName;
    }

    /**
     * Get last name
     *
     * @return string
     */
    public function getLastName(): string
    {
        return (string)$this->lastName;
    }

    /**
     * Set last name
     *
     * @param string $lastName
     * @return void
     */
    public function setLastName(string $lastName = ''): void
    {
        $this->lastName = $lastName;
    }

    /**
     * Get street address
     *
     * @return string
     */
    public function getStreetAddress(): string
    {
        return (string)$this->streetAddress;
    }

    /**
     * Set street address
     *
     * @param string $streetAddress
     * @return void
     */
    public function setStreetAddress(string $streetAddress = ''): void
    {
        $this->streetAddress = $streetAddress;
    }

    /**
     * Get zip code
     *
     * @return string
     */
    public function getZipCode(): string
    {
        return (string)$this->zipCode;
    }

    /**
     * Set zip code
     *
     * @param string $zipCode
     * @return void
     */
    public function setZipCode(string $zipCode = ''): void
    {
        $this->zipCode = $zipCode;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity(): string
    {
        return (string)$this->city;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return void
     */
    public function setCity(string $city = ''): void
    {
        $this->city = $city;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry(): string
    {
        return (string)$this->country;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return void
     */
    public function setCountry(string $country = ''): void
    {
        $this->country = $country;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone(): string
    {
        return (string)$this->phone;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return void
     */
    public function setPhone(string $phone = ''): void
    {
        $this->phone = $phone;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getFax(): string
    {
        return (string)$this->fax;
    }

    /**
     * Set fax
     *
     * @param string $fax
     * @return void
     */
    public function setFax(string $fax = ''): void
    {
        $this->fax = $fax;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail(): string
    {
        return (string)$this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return void
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Get homepage url
     *
     * @return string
     */
    public function getUrl(): string
    {
        return (string)$this->url;
    }

    /**
     * Set homepage url
     *
     * @param string $url
     * @return void
     */
    public function setUrl(string $url = ''): void
    {
        $this->url = $url;
    }

    /**
     * Get images
     *
     * @return ObjectStorage<FileReference>
     */
    public function getImages(): ObjectStorage
    {
        if ($this->images === null) {
            $this->images = new ObjectStorage();
        }

        return $this->images;
    }

    /**
     * Set images
     *
     * @param ObjectStorage<FileReference> $images
     */
    public function setImages(ObjectStorage $images): void
    {
        $this->images = $images;
    }

    /**
     * Add image
     *
     * @param FileReference $image
     * @return void
     */
    public function addImage(FileReference $image): void
    {
        $images = $this->getImages();
        if (!$images->contains($image)) {
            $images->attach($image);
        }
    }

    /**
     * Remove image
     *
     * @param FileReference $image
     * @return void
     */
    public function removeImage(FileReference $image): void
    {
        $images = $this->getImages();
        if ($images->contains($image)) {
            $images->detach($image);
        }
    }

    /**
     * Get first image
     *
     * @return FileReference|null
     */
    public function getImage(): ?FileReference
    {
        $images = $this->getImages();
        $images->rewind();

        return $images->count() > 0 ? $images->current() : null;
    }

    /**
     * Get last login
     *
     * @return DateTime|null
     */
    public function getLastLogin(): ?DateTime
    {
        return $this->lastLogin;
    }

    /**
     * Set last login
     *
     * @param DateTime|null $lastLogin
     * @return void
     */
    public function setLastLogin(?DateTime $lastLogin): void
    {
        $this->lastLogin = $lastLogin;
    }
}
