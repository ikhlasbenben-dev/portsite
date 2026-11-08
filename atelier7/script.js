const bouton = document.getElementById("btn");
const message = document.getElementById("message");

bouton.addEventListener("click", function () {
  message.textContent = "Le JavaScript fonctionne correctement !";
});
