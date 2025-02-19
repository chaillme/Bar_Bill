<?php
//require_once 'db.php';

$db = new SQLite3('../database/database.sqlite');

//require_once 'db.php';

//$db = new SQLite3('path/to/your/database.db');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;
    $prenom = $_POST['prenom'] ?? '';
    //$date = $_POST['date'] ?? '';


echo $id;
echo $prenom;
//echo $date;    
echo $_POST['modifier'];
   // $date = date('d-m-Y');



    if (isset($_POST['modifier'])) {
        if ($id > 0 && !empty($prenom) ) {
            $stmt = $db->prepare("UPDATE presences SET pre_prenom = :pre_prenom WHERE id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->bindValue(':pre_prenom', $prenom, SQLITE3_TEXT);
           // $stmt->bindValue(':pre_date', $date, SQLITE3_FLOAT);
            
            $stmt->execute();
            echo "presence modifié avec succès.";
            header('Location: ../presences.php');
        } else {
            echo "Erreur : Tous les champs sont obligatoires pour la modification.";
        }
    } elseif (isset($_POST['supprimer'])) {
        if ($id > 0) {
            $stmt = $db->prepare("DELETE FROM presences WHERE id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->execute();
            echo "presence supprimée avec succès.";
            header('Location: ../presences.php');
        } else {
            echo "Erreur : ID invalide pour la suppression.";
        }
    } elseif (isset($_POST['add'])) {
        if (!empty($prenom)) {
            $stmt = $db->prepare("INSERT INTO presences (pre_prenom, pre_date) VALUES (:pre_prenom, date())");
            $stmt->bindValue(':pre_prenom', $prenom, SQLITE3_TEXT);
            //$stmt->bindValue(':pre_date', $date, SQLITE3_FLOAT);
            
            $stmt->execute();
            echo "presence ajoutée avec succès.";
            header('Location: ../presences.php');
        } else {
            echo "Erreur : Le prenom est obligatoires.";
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