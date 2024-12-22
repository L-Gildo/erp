document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.getElementById("sidebar");
  const menuToggle = document.getElementById("menuToggle");
  const logoutButton = document.getElementById("logoutButton");

  // Alternar menu lateral
  if (menuToggle && sidebar) {
    menuToggle.addEventListener("click", () => {
      sidebar.classList.toggle("collapsed");
    });
  }

  // Fechar a notificação
  const closeNotification = document.getElementById("close-notification");
  if (closeNotification) {
    closeNotification.addEventListener("click", () => {
      document.getElementById("notification-panel").style.display = "none";
    });
  }
});
