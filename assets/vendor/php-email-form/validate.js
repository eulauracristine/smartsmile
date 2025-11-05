
document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector(".php-email-form");

  form.addEventListener("submit", function(e) {
    e.preventDefault();

    const sentMessage = this.querySelector(".sent-message");

    // Mostra mensagem
    sentMessage.style.display = "block";

    // Reseta form
    this.reset();

    // Esconde depois de 4s
    setTimeout(() => {
      sentMessage.style.display = "none";
    }, 4000);
  });
});
