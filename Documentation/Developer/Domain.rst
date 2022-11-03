.. include:: /Includes.rst.txt

.. _developer-domain:

==========================================
Domain Models, Repositories and Validators
==========================================

As :php:`Frontend User` and :php:`Frontend User Group` domain models have been marked as deprecated,
the extension provides its implementation of these domain models and repositories for them. Names of domain models' properties
have been changed a bit to make them more precise (for example, :php:`$userGroups` instead of :php:`$userGroup`
for the property of :php:`TYPO3\CMS\Extbase\Persistence\ObjectStorage` type) as well as appropriate labels in the TYPO3 backend.

.. toctree::
   :maxdepth: 5
   :titlesonly:

   FrontendUser/Index
   FrontendUserGroup/Index