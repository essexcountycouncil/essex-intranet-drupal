console.log('here');

const url = "https://essex-intranet.ddev.site/user/auto-login?destination=/user/auto-login/logged-in";

const features= "width=500,height=550,top=300,left=250,scrollbars=1,resizable=1,toolbar=0,menubar=0,status=1,directories=0";

function getCookie(name) {
  return cookieValue = document.cookie
    .split("; ")
    .find((row) => row.startsWith(name + "="))
    ?.split("=")[1];
}

function cookieExists(name) {
  return !!getCookie(name);

}

async function checkAllowed() {
  let url = 'https://essex-intranet.ddev.site/api/internal/v1/allow_auto_login';
  try {
    let res = await fetch(url);
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

async function loadpopunder(){
  // TODO: Remove.
  console.log('loadpopunder');

  const currentUrl = window.location.href;
  // Do not run on the popupunder url.
  if (currentUrl.includes('/user/auto')) {
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

  winPopunder=window.open(url,"Window", features)
  winPopunder.blur()

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

loadpopunder();
