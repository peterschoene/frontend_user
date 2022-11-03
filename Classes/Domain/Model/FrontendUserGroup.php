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
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class FrontendUserGroup
 * Frontend user group domain model
 */
class FrontendUserGroup extends AbstractEntity
{
    /**
     * Title
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     * @Extbase\Validate("StringLength", options={"maximum": 50})
     */
    protected $title;

    /**
     * Description
     *
     * @var string|null
     */
    protected $description;

    /**
     * Subgroups
     *
     * @var ObjectStorage<FrontendUserGroup>
     */
    protected $subgroups;

    /**
     * FrontendUserGroup constructor
     *
     * @param string $title
     */
    public function __construct(
        string $title
    ) {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return (string)$this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string|null $description
     * @return void
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Get subgroups
     *
     * @return ObjectStorage<FrontendUserGroup>
     */
    public function getSubgroups(): ObjectStorage
    {
        if ($this->subgroups == null) {
            $this->subgroups = new ObjectStorage();
        }

        return $this->subgroups;
    }

    /**
     * Set subgroups
     *
     * @param ObjectStorage<FrontendUserGroup> $subgroups
     * @return void
     */
    public function setSubgroups(ObjectStorage $subgroups): void
    {
        $this->subgroups = $subgroups;
    }

    /**
     * Add subgroup
     *
     * @param FrontendUserGroup $subgroup
     * @return void
     */
    public function addSubgroup(FrontendUserGroup $subgroup): void
    {
        $subgroups = $this->getSubgroups();
        if (!$subgroups->contains($subgroup)) {
            $subgroups->attach($subgroup);
        }
    }

    /**
     * Remove subgroup
     *
     * @param FrontendUserGroup $subgroup
     * @return void
     */
    public function removeSubgroup(FrontendUserGroup $subgroup): void
    {
        $subgroups = $this->getSubgroups();
        if ($subgroups->contains($subgroup)) {
            $subgroups->detach($subgroup);
        }
    }
}