document.addEventListener("DOMContentLoaded", function () {
  const cadastroMenu = document.querySelector('a[href="#cadastro"]');
  const modal = document.getElementById("cadastroModal");
  const closeModal = document.getElementById("closeModal");

  cadastroMenu.addEventListener("click", function (e) {
    e.preventDefault(); // Evita navegação padrão
    modal.style.display = "block"; // Exibe o modal
  });

  closeModal.addEventListener("click", function () {
    modal.style.display = "none"; // Oculta o modal
  });

  // Fecha o modal ao clicar fora dele
  window.addEventListener("click", function (e) {
    if (e.target === modal) {
      modal.style.display = "none";
    }
  });
});
