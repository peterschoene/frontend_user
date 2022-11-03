.. include:: /Includes.rst.txt

.. _configuration-settings-view:

=============
View Settings
=============

.. figure:: ../../Images/ViewTab.png
   :class: with-shadow
   :alt: View Tab in the TYPO3 backend

   *View Tab* in the TYPO3 Backend

.. _newfrontenduserformfields:

Create User Form Fields
=======================
.. container:: table-row

   Property
         newFrontendUserFormFields

   Data type
         string

   Description
         List of fields that will be displayed in the form for website user creation.

   .. note::

      :php:`Username`, :php:`Password`, and :php:`Password Confirmation` are required fields and are displayed by default.



.. _editfrontenduserformfields:

Edit User Form Fields
=====================
.. container:: table-row

   Property
         editFrontendUserFormFields

   Data type
         string

   Description
         List of fields that will be displayed in the form for website user update.

   .. note::

      :php:`Username` field is disabled as it cannot be updated by a website user.
      :php:`Password` and :php:`Password Confirmation` are displayed and updated only if :php:`Change Password` is checked.



.. _streetaddresslinenum:

Number of Lines in Street Address
=================================
.. container:: table-row

   Property
         streetAddressLineNum

   Data type
         int+

   Default
         2

   Description
         The number of lines for the street address :php:`textarea` field. Available values are ranged between 1-4.
