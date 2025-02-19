<?php
//$db = new SQLite3('database/database.sqlite');


// Connexion à la base de données SQLite sans PDO
$db_file = 'database/database.sqlite';

// Vérifier si la base de données existe
if (!file_exists($db_file)) {
    die("Erreur : La base de données n'existe pas.");
}

// Ouvrir la connexion à la base de données
$db = new SQLite3('database/database.sqlite');
if (!$db) {
    die("Erreur de connexion à la base de données : " . $error);
}


function prenom($id,$db) {
    if ($id > 0) {
    $stmt = $db->prepare("SELECT pre_prenom FROM presences WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);
    if ($row) {
        $prenom = $row['pre_prenom'];
    }
}
return $prenom;
}



?>

