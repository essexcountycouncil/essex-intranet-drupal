if (document.body.classList.contains('user-logged-in')) {
  if (window.opener) {
    window.opener.document.cookie = "oidc-auto-login=enabled"
    window.opener.location.reload();
  }
}
window.close()
