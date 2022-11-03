/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */
$(document).ready(function () {
  const showPassword = document.getElementById('show-password');
  const changePassword = document.getElementById('change-password');
  const changePasswordFieldset = document.getElementById('change-password-fieldset');
  const password = document.getElementById('password');
  const passwordConfirmation = document.getElementById('password-confirmation');
  const deleteUserLinkWrapper = document.getElementById('delete-user-link-wrapper');

  if (showPassword) {
    showPassword.checked = false;
  }

  if (changePasswordFieldset && !changePassword.checked) {
    changePasswordFieldset.hidden = true;
    password.disabled = true;
    passwordConfirmation.disabled = true;
  }

  if (deleteUserLinkWrapper) {
    deleteUserLinkWrapper.hidden = true;
  }

  $('#change-password').on('click', function (event) {
    changePasswordFieldset.hidden = !this.checked;
    password.disabled = !this.checked;
    passwordConfirmation.disabled = !this.checked;
    this.value = Number(this.checked);
  });

  $('#show-password').on('click', function (event) {
    const textInputType = 'text';
    const passwordInputType = 'password';

    if (this.checked) {
      password.type = textInputType;
      passwordConfirmation.type = textInputType;
    } else {
      password.type = passwordInputType;
      passwordConfirmation.type = passwordInputType;
    }
  });

  $('#delete-user-link').on('click', function (event) {
    deleteUserLinkWrapper.hidden = false;
  });
});
