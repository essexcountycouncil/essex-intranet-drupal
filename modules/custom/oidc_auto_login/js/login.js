const autoLoginUrl = "/user/auto-login?destination=";

const autoLoginUrlPattern = '/user/auto-login';

const allowAutoLoginEndpoint = '/api/v1/oidc_auto_login_allowed';

/**
 * Get a cookie.
 * @param {string} name
 * @returns {string}
 */
function getCookie(name) {
  return cookieValue = document.cookie
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
    json = await res.json();

    return json && json.allow_auto_login;
  } catch (error) {
    return false;
  }
}

/**
 * Load auto-login window if it is allowed.
 * @returns {Promise<void>}
 */
async function autoLogin() {
  console.log('Auto login');
  const currentUrl = window.location.href;
  // Do not run on the auto-login urls.
  if (currentUrl.includes(autoLoginUrlPattern)) {
    return;
  }

  // Do not run if auto-login has failed before in this session.
  if (getCookie('oidc-auto-login') === 'disabled') {
    return;
  }

  if (!(await checkAllowed())) {
    document.cookie = "oidc-auto-login=disabled"
    return;
  }

  const redirectUrl = autoLoginUrl + window.location.pathname;
  console.log('Redirect to ' + redirectUrl);
  document.cookie = "oidc-auto-login=disabled"
  window.location.replace(redirectUrl);
}

autoLogin();
