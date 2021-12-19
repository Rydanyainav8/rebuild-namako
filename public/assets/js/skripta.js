window.onload = () => {
    let activer = document.querySelectorAll("[type=checkbox]")
    for (let bouton of activer) {
        bouton.addEventListener("click", function () {
            let xmlhttp = new XMLHttpRequest;

            xmlhttp.open("get", `/carnet/activer/${this.dataset.id}`)
            xmlhttp.send()
        })
    }
    let supprimer = document.querySelectorAll('.modal-trigger')
    for (let bouton of supprimer) {
        bouton.addEventListener("click", function () {
            document.querySelector(".modal-footer a").href = `/Admin/supprimer/${this.dataset.id}`
            document.querySelector(".modal-content").innerText = `Confirmer la suppression de ${this.dataset.prenom}`
        })
    }
}


