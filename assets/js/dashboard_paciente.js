document.addEventListener("DOMContentLoaded", function () {
  const linkPerfil = document.querySelector('a.sidebar-link[href="paciente_perfil.php"]');
  const mainContent = document.querySelector("main.content");

  if (linkPerfil && mainContent) {
    linkPerfil.addEventListener("click", function (e) {
      e.preventDefault();

      fetch("paciente_perfil.php", {
        credentials: "include" // 👈 Envia o cookie da sessão
      })
        .then(response => {
          if (!response.ok) throw new Error("Erro ao carregar o perfil");
          return response.text();
        })
        .then(html => {
          mainContent.innerHTML = html;
          if (window.feather) feather.replace();
        })
        .catch(err => {
          console.error(err);
          mainContent.innerHTML = `<div class="alert alert-danger mt-3">Erro ao carregar o perfil.</div>`;
        });
    });
  }
});
