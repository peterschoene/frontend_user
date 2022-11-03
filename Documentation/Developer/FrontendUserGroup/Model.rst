.. include:: /Includes.rst.txt

.. _frontendusergroup-domainmodel:

============
Domain Model
============

:php:`FrontendUserGroup` domain model contains the following properties that are validated with annotation.
The validation rule for a property is specified based on the appropriate :php:`fe_groups` table field description.
Some properties have been renamed to clarify what data are stored in them.
For more information about mapping between a database table and its model
see `Use arbitrary database tables with an Extbase model <https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ExtensionArchitecture/Extbase/Reference/Domain/Persistence.html#use-arbitrary-database-tables-with-an-extbase-model>`__.

title
=====

.. code-block:: php

    /**
     * Title
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     * @Extbase\Validate("StringLength", options={"maximum": 50})
     */
    protected $title;

description
===========

.. code-block:: php

    /**
     * Description
     *
     * @var string|null
     */
    protected $description;

subgroups
=========

`subgroup` field in `fe_groups` table that stores a comma-separated list of frontend user subgroups.

.. code-block:: php

    /**
     * Subgroups
     *
     * @var ObjectStorage<FrontendUserGroup>
     */
    protected $subgroups;