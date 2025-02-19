document.addEventListener("DOMContentLoaded", function() {
    chargerBoissons();
});

function ajouterBoisson() {
    let nom = document.getElementById("nomBoisson").value;
    let prix = document.getElementById("prixBoisson").value;
    let description = document.getElementById("descBoisson").value;

    if (!nom || !prix) return;

    fetch("backend/boissons.php", {
        method: "POST",
        body: new URLSearchParams({ nom, prix, description })
    }).then(() => {
        document.getElementById("nomBoisson").value = "";
        document.getElementById("prixBoisson").value = "";
        document.getElementById("descBoisson").value = "";
        chargerBoissons();
    });
}

function chargerBoissons() {
    fetch("backend/boissons.php")
        .then(response => response.json())
        .then(data => {
            let liste = document.getElementById("listeBoissons");
            liste.innerHTML = "";
            data.forEach(item => {
                liste.innerHTML += `<li class='list-group-item d-flex justify-content-between'>
                    <span>${item.nom} - ${item.prix}€</span>
                    <button class='btn btn-warning btn-sm' onclick='modifierBoisson(${item.id}, "${item.nom}", ${item.prix}, "${item.description}")'>Modifier</button>
                    <button class='btn btn-danger btn-sm' onclick='supprimerBoisson(${item.id})'>Supprimer</button>
                </li>`;
            });
        });
}

function modifierBoisson(id, nom, prix, description) {
    let nouveauPrix = prompt("Modifier le prix :", prix);
    let nouvelleDescription = prompt("Modifier la description :", description);
    if (nouveauPrix !== null && nouvelleDescription !== null) {
        fetch("backend/boissons.php", {
            method: "PUT",
            body: new URLSearchParams({ id, prix: nouveauPrix, description: nouvelleDescription })
        }).then(() => chargerBoissons());
    }
}

function supprimerBoisson(id) {
    fetch(`backend/boissons.php?id=${id}`, { method: "DELETE" })
        .then(() => chargerBoissons());
}