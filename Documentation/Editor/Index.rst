.. include:: /Includes.rst.txt

.. _editor:

===========
For Editors
===========

.. _faq1:

What happens after a website user is created?
=============================================

After a website user is created the *Website User Form* plugin can behave in the following ways:

* Log in a website user with the provided credentials.

In this case, new website users are authenticated with the provided credentials and can view, update or delete website user data.
For this scenario :ref:`enablefrontenduserautologin` has to be enabled.

* Redirect a website user to the login page.

In this case, new website users are redirected to the page supposedly contains the login form.
For this scenario :ref:`enablefrontenduserautologin` has to be **disabled**, :ref:`enableredirecttologinpage` has to be **enabled**
and :ref:`redirectloginpageid` should be selected.

* Stay on the current page and display the message :php:`Please log in with the provided credentials to view the user account.`

This scenario is possible when:

* :ref:`enablefrontenduserautologin` and :ref:`enableredirecttologinpage` are disabled. If :ref:`redirectloginpageid` is specified the link to the page will be added to the message.
* :ref:`enableredirecttologinpage` is enabled but :ref:`redirectloginpageid` is not specified.
* :ref:`enablefrontenduserautologin` is enabled but a website user is not authenticated for any reason.

.. note::

  Keep in mind that when the option is hidden its value does not reset and still is stored in a database
  which can influence the behavior of the plugin in the third scenario.

.. _faq2:

How to work with images?
========================

By default, TYPO3 is allowed to assign five images to a website user. This has been changed and now it is allowed
to assign one image file. For supported file extensions see
`imagefile_ext <https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/Configuration/Typo3ConfVars/GFX.html?highlight=imagefile_ext#imagefile-ext>`__.
The file size is limited by server configuration.

In the TYPO3 frontend image file size can be set through TypoScript in the setup field of your TypoScript template.
See :ref:`frontenduserimagesize`.

.. important::

  It is important to know that website user image deletion means only removing references between a website user and an image file.
  The actual deletion of old image files should be performed based on project needs and requirements.
