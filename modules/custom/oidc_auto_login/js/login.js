const autoLoginUrl = "/user/auto-login?destination=/user/auto-login/logged-in";

const autoLoginUrlPattern = '/user/auto-login';

const allowAutoLoginEndpoint = '/api/v1/oidc_auto_login_allowed';

const autoLoginFeatures = "width=500,height=500,top=0,left=0,screenX=0,screenY=0,scrollbars=1,resizable=1,toolbar=0,menubar=0,statusbar=1";

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
 * Load auto-login window if it is required and allowed.
 * @returns {Promise<void>}
 */
async function loadAutoLoginWindow() {
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
  }

  winAutoLoginWindow = window.open(autoLoginUrl, "Window", autoLoginFeatures)
  if (winAutoLoginWindow) {
    document.cookie = "oidc-auto-login=enabled"
    winAutoLoginWindow.blur()
  }
  else {
    document.cookie = "oidc-auto-login=disabled"
  }
}

loadAutoLoginWindow();
