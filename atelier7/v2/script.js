const form = document.getElementById("demo-form");
const input = document.getElementById("user-input");
const message = document.getElementById("message");

form.addEventListener("submit", (event) => {
  event.preventDefault();
  // Démonstration volontairement vulnérable : la saisie est interprétée comme du HTML.
  message.innerHTML = input.value;
});
