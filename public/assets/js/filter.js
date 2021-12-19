window.onload = () => {
    const FiltersForm = document.querySelector("#filters");
    // boucle input
    document.querySelectorAll("#filters input").forEach(input => {
        input.addEventListener("change", () => {
            //interception click
            // recuperation donnée formulaire
            const Form = new FormData(FiltersForm);
            //fabrication queryString
            const Params = new URLSearchParams();

            Form.forEach((value, key) => {
                Params.append(key, value);
                // console.log(key, value);
                // console.log(Params.toString());

            });
            // recuperation url active
            const Url = new URL(window.location.href);
            // console.log(Url);

            // //lancement de requete ajax
            fetch(Url.pathname + "?" + Params.toString() + "&ajax=1",
                {
                    headers: {
                        "X-Requested-with": "XMLHttpRequest"
                    }
                }).then(response =>
                    response.json()
                    // console.log(response)
                ).then(data => {
                    // console.log(data)
                    const content = document.querySelector("#content");

                    content.innerHTML = data.content;

                    history.pushState({}, null, Url.pathname + "?" + Params.toString());
                }).catch(e => alert(e));

        });
    });
}