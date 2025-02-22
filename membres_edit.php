<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['telephone'] ?? '';

    if (isset($_POST['modifier'])) {
        if ($id > 0 && !empty($nom) && !empty($prenom)) {
            $stmt = $db->prepare("UPDATE membres SET nom = :nom, prenom = :prenom, email = :email, telephone = :telephone WHERE id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->bindValue(':nom', $nom, SQLITE3_TEXT);
            $stmt->bindValue(':prenom', $prenom, SQLITE3_TEXT);
            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
            $stmt->bindValue(':telephone', $telephone, SQLITE3_TEXT);
            $stmt->execute();
            header('Location: ../membres_list.php');
        } else {
            echo "Erreur : Tous les champs sont obligatoires pour la modification.";
        }
    } elseif (isset($_POST['supprimer'])) {
        if ($id > 0) {
            $stmt = $db->prepare("DELETE FROM membres WHERE id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->execute();
            header('Location: ../membres_list.php');
        } else {
            echo "Erreur : ID invalide pour la suppression.";
        }
    } elseif (isset($_POST['add'])) {
        if (!empty($nom) && !empty($prenom)) {
            $stmt = $db->prepare("INSERT INTO membres (nom, prenom, email, telephone) VALUES (:nom, :prenom, :email, :telephone)");
            $stmt->bindValue(':nom', $nom, SQLITE3_TEXT);
            $stmt->bindValue(':prenom', $prenom, SQLITE3_TEXT);
            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
            $stmt->bindValue(':telephone', $telephone, SQLITE3_TEXT);
            $stmt->execute();
            header('Location: ../membres_list.php');
        } else {
            echo "Erreur : Le nom et le prénom sont obligatoires.";
        }
    } else {
        echo "Erreur : Méthode de requête non autorisée.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $db->query("SELECT * FROM membres");
    $membres = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $membres[] = $row;
    }
    echo json_encode($membres);
}
?>