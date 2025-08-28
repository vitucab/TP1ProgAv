// Ejemplo: mostrar alerta al enviar el form
document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form");
  if (form) {
    form.addEventListener("submit", () => {
      console.log("Formulario enviado");
    });
  }
});
