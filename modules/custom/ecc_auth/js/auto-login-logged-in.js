
if (document.body.classList.contains('user-logged-in')) {
  const payload = { loggedin: true };
  window.opener.location.reload();
}
window.close()