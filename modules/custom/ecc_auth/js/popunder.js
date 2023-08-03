console.log('here');

const url ="https://essex-intranet.ddev.site/ecc-auth?destination=/ecc-auth/logged-in"

const features="width=500,height=550,top=300,left=250,scrollbars=1,resizable=1,toolbar=0,menubar=0,status=1,directories=0"

function cookieExists(name) {
  const cookieArray = document.cookie.split(';');

  for (cookie of cookieArray) {
    if (cookie.includes(name)) {
      return true;
    }
  }
  return false;
}

function loadpopunder(){
  console.log('loadornot')

  const currentUrl = window.location.href;
  if (currentUrl.includes('/ecc-auth')) {
    return;
  }

  winPopunder=window.open(url,"Window", features)
  winPopunder.blur()

  window.addEventListener('message', (e) => {
    const { loggedin } = e.data;
    if (loggedin) {
      console.info('User has logged in from the other window');
      window.location.reload();
    }
  });
}

loadpopunder()
