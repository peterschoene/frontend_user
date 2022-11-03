.. include:: /Includes.rst.txt

.. _frontenduser-repository:

==========
Repository
==========

:php:`FrontendUserRepository` provides the following functionality:

findByUsername
==============

The method is used to find frontend user by username and page Uid.

.. code-block:: php

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