console.log('here');

const url ="https://essex-intranet.ddev.site/ecc-auth?destination=/ecc-auth/logged-in"

const features="width=500,height=550,top=300,left=250,scrollbars=1,resizable=1,toolbar=0,menubar=0,status=1,directories=0"

function ccccookieExists(name) {
  const cookieArray = document.cookie.split(';');

  for (cookie of cookieArray) {
    if (cookie.includes(name)) {
      return true;
    }
  }
  return false;
}


function getCookie(name) {
  return cookieValue = document.cookie
    .split("; ")
    .find((row) => row.startsWith(name + "="))
    ?.split("=")[1];
}

function cookieExists(name) {
  return !!getCookie(name);

}
function checkCookie( cookieToBeSearched){
  const cookie = document.cookie;
  if(cookie === "" || cookie === undefined){
    return false
  }
  let res = cookie.split(";").some(cookie => {
    let eachCookie = cookie.split("=");
    return eachCookie[0].trim() === cookieToBeSearched
  });
  return res;
}

function doAuth() {
  console.log('doAuth')

  //const data = response.json();
  data.auth;
  console.log(data);
  console.log(data.auth);
  return data.auth;
}

async function callAuth() {
  let url = 'https://essex-intranet.ddev.site/api/internal/v1/ecc_auth';
  try {
    let res = await fetch(url);
    return await res.json();
  } catch (error) {
    // TODO: Remove.
    console.log(error);
  }
}

async function loadpopunder(){
  console.log('loadpopunder');


  const currentUrl = window.location.href;
  // Do not run on the popupunder url.
  if (currentUrl.includes('/ecc-auth')) {
    return;
  }

  // Do not run if the user hasn't logged in before on the Essex network.
  if (!cookieExists('essex-user')) {
    return;
  }


  let doit = await callAuth();
  // TODO: Remove.
  console.log(doit);
  if (!doit || !doit.auth) {
    return;
  }

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
