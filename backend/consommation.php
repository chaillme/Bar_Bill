<?php
//require_once 'db.php';

$db = new SQLite3('../database/database.sqlite');

//require_once 'db.php';

//$db = new SQLite3('path/to/your/database.db');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // $id = $_POST['id'] ?? 0;
    $art_id = $_POST['art_id'] ?? '';
    $pre_id = $_POST['pre_id'] ?? '';
    $con_qty = $_POST['con_qty'] ?? '';
    $art_prix = $_POST['art_prix'] ?? '';
 
echo $art_id ;
echo $pre_id ;
echo $con_qty ;


    if (isset($_POST['modifier'])) {
        if ($id > 0 && !empty($nom) && $prix > 0) {
            $stmt = $db->prepare("UPDATE presences SET pre_prenom = :pre_prenom, pre_date = :pre_date WHERE id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->bindValue(':pre_prenom', $prenom, SQLITE3_TEXT);
            //$stmt->bindValue(':pre_date', $date, SQLITE3_FLOAT);
            
            $stmt->execute();
            echo "presence modifié avec succès.";
            header('Location: ../presences.php');
        } else {
            echo "Erreur : Tous les champs sont obligatoires pour la modification.";
        }
    } elseif (isset($_POST['supprimer'])) {
        if ($pre_id > 0 && $con_qty > 0) {
           // $stmt = $db->prepare("DELETE FROM presences WHERE id = :id");
            $stmt = $db->prepare("DELETE from consommations where id IN (SELECT id from consommations where con_pre_id = :con_pre_id and con_art_id = :con_art_id order by id DESC limit 1)");
            $stmt->bindValue(':con_pre_id', $pre_id, SQLITE3_INTEGER);
            $stmt->bindValue(':con_art_id', $art_id, SQLITE3_INTEGER);
            $stmt->execute();
            echo "presence supprimée avec succès.";
            header('Location: ../consommations.php?id='.$pre_id);
        } else {
            echo "Erreur : ID invalide pour la suppression.";
        }
    } elseif (isset($_POST['add'])) {
        if (!empty($pre_id)) {
            $stmt = $db->prepare("INSERT INTO consommations (con_art_id, con_pre_id, con_qty, con_date) VALUES (:con_art_id, :con_pre_id, :con_qty, date())");
            $stmt->bindValue(':con_art_id', $art_id, SQLITE3_TEXT);
            $stmt->bindValue(':con_pre_id', $pre_id, SQLITE3_TEXT);
            $stmt->bindValue(':con_qty', $con_qty, SQLITE3_TEXT);
            //$stmt->bindValue(':pre_date', $date, SQLITE3_FLOAT);
            
            $stmt->execute();
            echo "consommation ajoutée avec succès.";
            header('Location: ../consommations.php?id='.$pre_id);
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