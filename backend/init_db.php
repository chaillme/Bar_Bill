<?php
require_once 'db.php';

$pdo = Database::getInstance()->getConnection();

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS presences (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        prenom TEXT NOT NULL,
        date DATE NOT NULL
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS boissons (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom TEXT NOT NULL,
        prix FLOAT NOT NULL,
        description TEXT
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS consommations (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        presence_id INTEGER NOT NULL,
        boisson_id INTEGER NOT NULL,
        FOREIGN KEY (presence_id) REFERENCES presences(id),
        FOREIGN KEY (boisson_id) REFERENCES boissons(id)
    )");

    echo "Base de données initialisée avec succès.";
} catch (PDOException $e) {
    die("Erreur lors de l'initialisation de la base de données : " . $e->getMessage());
}
?>