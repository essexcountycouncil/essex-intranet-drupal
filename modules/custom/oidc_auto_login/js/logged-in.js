
if (document.body.classList.contains('user-logged-in')) {
  const payload = { loggedin: true };
  window.opener.document.cookie = "oidc-auto-login=enabled"
  window.opener.location.reload();
}
window.close()