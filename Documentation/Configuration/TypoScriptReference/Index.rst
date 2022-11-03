.. include:: /Includes.rst.txt

.. _configuration-typoscript:

====================
TypoScript Reference
====================

.. _configuration-typoscript-constants:

Constants
=========

.. _frontenduserimagesize:

User Image Size
---------------

.. confval:: frontendUserImageSize

   :type: int
   :Default: 150

   Width of website user image size in the TYPO3 frontend. In case of overriding a template, this setting can be used
   as image height or just ignored for styling form with CSS.

   Example::

      plugin.tx_frontenduser_form.settings.frontendUserImageSize = 200
