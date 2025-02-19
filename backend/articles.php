<?php
//require_once 'db.php';

$db = new SQLite3('../database/database.sqlite');

//require_once 'db.php';

//$db = new SQLite3('path/to/your/database.db');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;
    $nom = $_POST['nom'] ?? '';
    $prix = $_POST['prix'] ?? 0;
    $description = $_POST['description'] ?? '';

echo $nom;
echo $prix;
echo $description;



    if (isset($_POST['modifier'])) {
        if ($id > 0 && !empty($nom) && $prix > 0) {
            $stmt = $db->prepare("UPDATE articles SET art_nom = :art_nom, art_prix = :art_prix, art_description = :art_description WHERE id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->bindValue(':art_nom', $nom, SQLITE3_TEXT);
            $stmt->bindValue(':art_prix', $prix, SQLITE3_FLOAT);
            $stmt->bindValue(':art_description', $description, SQLITE3_TEXT);
            $stmt->execute();
            echo "Article modifié avec succès.";
            header('Location: ../articles_list.php');
        } else {
            echo "Erreur : Tous les champs sont obligatoires pour la modification.";
        }
    } elseif (isset($_POST['supprimer'])) {
        if ($id > 0) {
            $stmt = $db->prepare("DELETE FROM articles WHERE id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->execute();
            echo "Boisson supprimée avec succès.";
            header('Location: ../articles_list.php');
        } else {
            echo "Erreur : ID invalide pour la suppression.";
        }
    } elseif (isset($_POST['add'])) {
        if (!empty($nom) && $prix > 0) {
            $stmt = $db->prepare("INSERT INTO articles (art_nom, art_prix, art_description) VALUES (:art_nom, :art_prix, :description)");
            $stmt->bindValue(':art_nom', $nom, SQLITE3_TEXT);
            $stmt->bindValue(':art_prix', $prix, SQLITE3_FLOAT);
            $stmt->bindValue(':art_description', $description, SQLITE3_TEXT);
            $stmt->execute();
            echo "Boisson ajoutée avec succès.";
            header('Location: ../articles_list.php');
        } else {
            echo "Erreur : Le nom et le prix sont obligatoires.";
        }
    } else{
        echo "Erreur : Méthode de requête non autorisée.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $db->query("SELECT * FROM articles");
    $articles = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $articles[] = $row;
    }
    echo json_encode($articles);
}
?>