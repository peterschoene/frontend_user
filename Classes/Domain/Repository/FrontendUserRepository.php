<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use Ydt\FrontendUser\Domain\Model\FrontendUser;

/**
 * Class FrontendUserRepository
 * Frontend user repository
 */
class FrontendUserRepository extends Repository
{
    /**
     * Find frontend user by username
     *
     * @param string $username
     * @param int $pid
     * @return FrontendUser|null
     */
    public function findByUsername(string $username, int $pid = 0): ?FrontendUser
    {
        $query = $this->createQuery();

        if ($pid) {
            $settings = $query->getQuerySettings();
            $storagePageIds = $settings->getStoragePageIds();
            $storagePageIds[] = $pid;
            $settings->setStoragePageIds(array_unique($storagePageIds));
        }

        $constraint = $query->equals('username', $username);
        $query->matching($constraint);

        $result = $query->execute();

        return $result->getFirst();
    }
}
