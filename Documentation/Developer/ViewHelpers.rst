.. include:: /Includes.rst.txt
.. highlight:: html

.. _developer-viewhelpers:

===========
ViewHelpers
===========

.. _lowercasehyphenated:

lowercaseHyphenated
===================

ViewHelper splits text strings by capital letters, making parts lowercase, and joining them with a hyphen.

It is used for the generating form field ids from website user properties.

Default
-------

Code: ::

	<span><ydt:format.lowercaseHyphenated value="firstName"/></span>


Output: ::

	<span>first-name</span>


Inline Notation
---------------

Code: ::

    <f:for each="{fields}" as="field">
        <label for="{field -> ydt:format.lowercaseHyphenated()}">...</label>
    </f:for>

Output: ::

	<label for="first-name">...</label>