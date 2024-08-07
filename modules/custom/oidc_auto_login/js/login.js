/**
 * @file
 * OpenID Connect Auto Login.
 */
(function ($, Drupal) {

  'use strict';

  const autoLoginUrl = "/user/auto-login?destination=";

  const autoLoginUrlPattern = '/user/auto-login';

  const allowAutoLoginEndpoint = '/api/v1/oidc_auto_login_allowed';

  /**
   * Get a cookie.
   * @param {string} name
   * @returns {string}
   */
  function getCookie(name) {
    return document.cookie
      .split("; ")
      .find((row) => row.startsWith(name + "="))
      ?.split("=")[1];
  }

  /**
   * Check if auto-login is allowed.
   * Response from API endpoint must contain JSON {"allow_auto_login": true}.
   * @returns {Promise<any|boolean>}
   */
  async function checkAllowed() {
    try {
      const res = await fetch(allowAutoLoginEndpoint);
      const json = await res.json();

      return json && json.allow_auto_login;
    } catch (error) {
      return false;
    }
  }

  /**
   * AutoLogin behaviour.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.autoLogin = {
    attach: function attach(context, settings) {
      const currentUrl = window.location.href;
      // Do not run on the auto-login url.
      if (currentUrl.includes(autoLoginUrlPattern)) {
        return;
      }

      // Do not attempt auto-login more than once in this session.
      if (getCookie('oidc-auto-login') === 'attempted') {
        return;
      }
      const cookieMaxAge= drupalSettings.oidc_auto_login.cookieMaxAge.toString() ?? "300";
      document.cookie = "oidc-auto-login=attempted; max-age=" + cookieMaxAge;

      if (!(checkAllowed())) {
        return;
      }

      const redirectUrl = autoLoginUrl + window.location.pathname;
      window.location.replace(redirectUrl);
    }
  }
})(jQuery, Drupal);
