document.addEventListener("DOMContentLoaded", function() {
    chargerPresences();
    chargerBoissons();
    chargerConsommations();
});

function ajouterPresence() {
    let prenom = document.getElementById("prenom").value;
    if (!prenom) return;

    fetch("backend/presence.php", {
        method: "POST",
        body: new URLSearchParams({ prenom })
    }).then(() => {
        document.getElementById("prenom").value = "";
        chargerPresences();
    });
}

function chargerPresences() {
    fetch("backend/presence.php")
        .then(response => response.json())
        .then(data => {
            let liste = document.getElementById("listePresences");
            let select = document.getElementById("presenceSelect");
            liste.innerHTML = "";
            select.innerHTML = "";
            data.forEach(item => {
                liste.innerHTML += `<li class='list-group-item'>${item.prenom}</li>`;
                select.innerHTML += `<option value='${item.id}'>${item.prenom}</option>`;
            });
        });
}

function chargerBoissons() {
    fetch("backend/boissons.php")
        .then(response => response.json())
        .then(data => {
            let select = document.getElementById("boissonSelect");
            select.innerHTML = "";
            data.forEach(item => {
                select.innerHTML += `<option value='${item.id}'>${item.nom} - ${item.prix}€</option>`;
            });
        });
}

function ajouterConsommation() {
    let presence_id = document.getElementById("presenceSelect").value;
    let boisson_id = document.getElementById("boissonSelect").value;

    fetch("backend/consommations.php", {
        method: "POST",
        body: new URLSearchParams({ presence_id, boisson_id })
    }).then(() => {
        chargerConsommations();
    });
}

function chargerConsommations() {
    fetch("backend/consommations.php")
        .then(response => response.json())
        .then(data => {
            let liste = document.getElementById("listeConsommations");
            liste.innerHTML = "";
            data.forEach(item => {
                liste.innerHTML += `<li class='list-group-item'>${item.prenom} a consommé ${item.boisson} (${item.prix}€)</li>`;
            });
        });
}