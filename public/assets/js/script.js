window.onload = () => {
    let TicketActive = document.querySelectorAll("[type=checkbox]")
    for (let bouton of TicketActive) {
        bouton.addEventListener("click", function () {
            let xmlhttp = new XMLHttpRequest;

            xmlhttp.open("get", `/ticket/activer/${this.dataset.id}`)
            xmlhttp.send()
        })
    }
    let supprimer = document.querySelectorAll('.modal-trigger')
    for (let bouton of supprimer) {
        bouton.addEventListener("click", function () {
            document.querySelector(".modal-footer a").href = `/user/supprimer/${this.dataset.id}`
            document.querySelector(".modal-content").innerText = `Confirmer la suppression de ${this.dataset.prenom}`
        })
    }
}

const eye = document.querySelector(".fa-eye");
const eyeoff = document.querySelector(".fa-eye-slash");
const passwordField = document.querySelector("input[type=password]");

eye.addEventListener("click", () => {
    eye.style.display = "none";
    eyeoff.style.display = "block";
    passwordField.type = "text";
});

eyeoff.addEventListener("click", () => {
    eyeoff.style.display = "none";
    eye.style.display = "block";
    passwordField.type = "password";
});
