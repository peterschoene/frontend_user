.. include:: /Includes.rst.txt

.. _issues:

==============
Known Issues
==============

* Displaying of validation errors

Build-in and custom validators are responsible for the validation of website user data on creation. That is why
the validation result is rendered by an appropriate template with an error flash message
:php:`An error occurred while trying to create a user`. Nevertheless, the additional validation of the username is required
to ensure the uniqueness of this field. This validation occurs in the controller and an error message is displayed
as a flash message, not a validation error.

When website user data is updated, it is not required to update a password as well so the validation of password
and password confirmation values is moved to the controller. If validation fails, an error flash message is displayed.

When an error occurs during the upload of the website user image file, an error flash message also will be displayed.

* The issue with multiple plugins on a page

During extension testing it was found out that in case of *Website User Form* plugin and *Login Form* plugin
(or any other plugin that submits a form) are located on the same page an error can occur. An error occurs in the case
when validation of website user data fails and :php:`errorAction()` is called, and then any other form on the page
is submitted. The *Website User Form* plugin tries to perform an action from the URL but the form data is empty and
the required arguments are not set.