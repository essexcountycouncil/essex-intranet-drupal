const autoLoginUrl = "/user/auto-login?destination=/user/auto-login/logged-in";

const autoLoginUrlPattern = '/user/auto-login';

const allowAutoLoginEndpoint = '/api/internal/v1/allow_auto_login';

const autoLoginFeatures= "width=500,height=500,top=0,left=0,screenX=0,screenY=0,scrollbars=1,resizable=1,toolbar=0,menubar=0,statusbar=1";

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
 * Check a cookie exists.
 * @param {string} name
 * @returns {boolean}
 */
function cookieExists(name) {
  return !!getCookie(name);
}

/**
 * Check if auto-login is allowed.
 * Response from API endpoint must contain JSON {"allow_auto_login": true}.
 * Response must contain Essex header to show browser is on Essex network.
 * @returns {Promise<any|boolean>}
 */
async function checkAllowed() {
  try {
    let res = await fetch(allowAutoLoginEndpoint);
    $isEssex = await res.headers.get('Essex') === 'true';
    if (!$isEssex) {
      // TODO: Remove.
      console.log('Header does not match');

      return false;
    }
    json = await res.json();

    // TODO: Remove.
    console.log(json);

    return json && json.allow_auto_login;
  } catch (error) {
    // TODO: Remove.
    console.log(error);
  }
  return false;
}

/**
 * Load auto-login window if it is required and allowed.
 * @returns {Promise<void>}
 */
async function loadAutoLoginWindow() {
  // TODO: Remove.
  console.log('loadAutoLoginWindow');

  const currentUrl = window.location.href;
  // Do not run on the auto-login urls.
  if (currentUrl.includes(autoLoginUrlPattern)) {
    return;
  }

  // Do not run unless the user has logged in before on the Essex network.
  if (!cookieExists('essex-user')) {
    return;
  }

  if (!(await checkAllowed())) {
    console.log('Not allowed');
    return;
  }
  console.log('Allowed');

  winAutoLoginWindow = window.open(autoLoginUrl, "Window", autoLoginFeatures)
  winAutoLoginWindow.blur()

  // Reload after user has been logged in.
  window.addEventListener('message', (e) => {
    const { loggedin } = e.data;
    if (loggedin) {
      // TODO: Remove.
      console.log('User has logged in from the other window');

      window.location.reload();
    }
  });
}

loadAutoLoginWindow();
