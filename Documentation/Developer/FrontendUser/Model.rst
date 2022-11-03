.. include:: /Includes.rst.txt

.. _frontenduser-domainmodel:

============
Domain Model
============

:php:`FrontendUser` domain model contains the following properties that are validated with annotation.
The validation rule for a property is specified based on the appropriate :php:`fe_users` table field description.
Some properties have been renamed to clarify what data are stored in them.
For more information about mapping between a database table and its model
see `Use arbitrary database tables with an Extbase model <https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ExtensionArchitecture/Extbase/Reference/Domain/Persistence.html#use-arbitrary-database-tables-with-an-extbase-model>`__.



.. _username:

username
========

..  code-block:: php

    /**
     * Username
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     * @Extbase\Validate("StringLength", options={"maximum": 255})
     * @Extbase\Validate("Text")
     */
    protected $username;



.. _password:

password
========

..  code-block:: php

    /**
     * Password
     *
     * @var string
     */
    protected $password;



.. _passwordconfirmation:

passwordConfirmation
====================

..  code-block:: php

    /**
     * Password confirmation
     *
     * @var string
     * @Extbase\ORM\Transient
     */
    protected $passwordConfirmation;



.. _usergroups:

userGroups
==========

:php:`usergroup` field in :php:`fe_users` table that stores a comma-separated list of user groups.

.. code-block:: php

    /**
     * Frontend user groups
     *
     * @var ObjectStorage<FrontendUserGroup>
     */
    protected $userGroups;



.. _company:

company
=======

.. code-block:: php

    /**
     * Company
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 80})
     * @Extbase\Validate("Text")
     */
    protected $company;



.. _jobtitle:

jobTitle
========

:php:`title` field in :php:`fe_users` table.

.. code-block:: php

    /**
     * Job title
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 40})
     * @Extbase\Validate("Text")
     */
    protected $jobTitle;



.. _name:

name
====

.. code-block:: php

    /**
     * Name
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 160})
     * @Extbase\Validate("Text")
     */
    protected $name;



.. _firstname:

firstName
=========

.. code-block:: php

    /**
     * First name
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 50})
     * @Extbase\Validate("Text")
     */
    protected $firstName;



.. _middlename:

middleName
==========

.. code-block:: php

    /**
     * Middle name
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 50})
     * @Extbase\Validate("Text")
     */
    protected $middleName;



.. _lastname:

lastName
========

.. code-block:: php

    /**
     * Last name
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 50})
     * @Extbase\Validate("Text")
     */
    protected $lastName;



.. _streetaddress:

streetAddress
=============

:php:`address` field in :php:`fe_users` table.

.. code-block:: php

    /**
     * Street address
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 255})
     * @Extbase\Validate("Text")
     */
    protected $streetAddress;



.. _zipcode:

zipCode
=======

:php:`zip` field in :php:`fe_users` table.

.. code-block:: php

    /**
     * Zip code
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 10})
     * @Extbase\Validate("AlphanumericValidator")
     */
    protected $zipCode;



.. _city:

city
====

.. code-block:: php

    /**
     * City
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 50})
     * @Extbase\Validate("Text")
     */
    protected $city;



.. _country:

country
=======

.. code-block:: php

    /**
     * Country
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 40})
     * @Extbase\Validate("Text")
     */
    protected $country;



.. _phone:

phone
=====

:php:`telephone` field in :php:`fe_users` table. See :ref:`phone-validator`.

.. code-block:: php

    /**
     * Phone
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 30})
     * @Extbase\Validate("Ydt\FrontendUser\Domain\Validator\PhoneValidator")
     */
    protected $phone;



.. _fax:

fax
===

See :ref:`digit-validator`.

.. code-block:: php

    /**
     * Fax
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 30})
     * @Extbase\Validate("Ydt\FrontendUser\Validation\Validator\DigitValidator")
     */
    protected $fax;



.. _email:

email
=====

.. code-block:: php

    /**
     * Email
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 255})
     * @Extbase\Validate("EmailAddress")
     */
    protected $email;



.. _url:

url
===

:php:`url` field in :php:`fe_users` table.

.. code-block:: php

    /**
     * Homepage url
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"maximum": 80})
     * @Extbase\Validate("Text")
     * @Extbase\Validate("Url")
     */
    protected $url;



.. _images:

images
======

See :ref:`faq2`

.. code-block:: php

    /**
     * Images
     *
     * @var ObjectStorage<FileReference>
     */
    protected $images;



.. _lastlogin:

lastLogin
=========

:php:`lastlogin` field in :php:`fe_users` table that stores the timestamp of last login date and time.

.. code-block:: php

    /**
     * Last login
     *
     * @var DateTime|null
     */
    protected $lastLogin;