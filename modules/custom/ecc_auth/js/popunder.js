//Pop-under window- By JavaScript Kit
//Credit notice must stay intact for use
//Visit http://javascriptkit.com for this script
console.log('here');
//specify page to pop-under
var popunder="https://essex-intranet.ddev.site/ecc-auth?destination=/ecc-auth/logged-in"

//specify popunder window features
//set 1 to enable a particular feature, 0 to disable
var winfeatures="width=500,height=550,top=300,left=250,scrollbars=1,resizable=1,toolbar=0,menubar=0,status=1,directories=0"
//var winfeatures = "status=1,toolbar=1,width=500,height=500,top=300,left=250,resizable=yes"
//Pop-under only once per browser session? (0=no, 1=yes)
//Specifying 0 will cause popunder to load every time page is loaded
var once_per_session= 1

///No editing beyond here required/////


function cookieExists(name) {
  const cookieArray = document.cookie.split(';');

  for (cookie of cookieArray) {
    //console.log(cookie);
    if (cookie.includes(name)) {
      //console.log('match')
      return true;
    }
  }
  //console.log('no match')
  return false;
}

function get_cookie(Name) {
  var search = Name + "="
  var returnvalue = "";
  if (document.cookie.length > 0) {
    offset = document.cookie.indexOf(search)
    if (offset != -1) { // if cookie exists
      offset += search.length
      // set index of beginning of value
      end = document.cookie.indexOf(";", offset);
      // set index of end of cookie value
      if (end == -1)
        end = document.cookie.length;
      returnvalue=unescape(document.cookie.substring(offset, end))
    }
  }
  return returnvalue;
}

function loadornot(){
  //console.log('loadornot')
  const currentUrl = window.location.href;
  //console.log(currentUrl);
  if (currentUrl.includes('/ecc-auth')) {
    return;
  }
  if (!cookieExists('popunder')){
    //console.log('!cookieExists')
    loadpopunder()
    document.cookie="popunder=yes"
  }
}

const sleep = async (milliseconds) => {
  await new Promise(resolve => {
    return setTimeout(resolve, milliseconds)
  });
};

const mywait = async () => {
  await delay(5000);
  console.log("Waited 5s");
};

function loadpopunder(){
  win2=window.open(popunder,"Window",winfeatures)
  win2.blur()

  window.addEventListener('message', (e) => {
    const { loggedin } = e.data;
    if (loggedin) {
      console.info('User has logged in from the other tab!');
      window.location.reload();
    }
  });
}

loadornot()
