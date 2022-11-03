.. include:: /Includes.rst.txt

.. _frontenduser-validators:

==========
Validators
==========

FrontendUser Validator
======================

The extension contains a validator for :php:`FrontendUser` domain model that checks :php:`password` and :php:`passwordConfirmation`
properties as well as type of :php:`$frontendUser` argument.

Validation error codes
----------------------
* Error message:
   * :php:`The given object is not an instance of FrontendUser class.`
   * :php:`Please make sure your passwords match.`



.. _digit-validator:

Digit Validator (:yaml:`Digit`)
===============================
The digital validator checks the string contains only numeric characters [0-9].

Validation error codes
----------------------
* Error message: :php:`The given subject is not a valid digit string.`



.. _phone-validator:

Phone Validator (:yaml:`Phone`)
===============================
Phone frontend user domain model property validator. It checks if the provided string matches the following requirements:

* Digit or :php:`+` sign in the beginning
* Digit in the end
* One combination of digits in parentheses is allowed
* Space and dash as digit separator are allowed

Validation error codes
----------------------
* Error message: :php:`The given subject is not a valid phone.`
