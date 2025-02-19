<?php
//require 'db.php';

$db = new SQLite3('../database/database.sqlite');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pre = $_POST['id_pre'] ?? '';
    $status = $_POST['status'] ?? '';

    if ($id_pre > 0 && $status == 'paid') {
        $stmt = $db->prepare("UPDATE presences SET pre_payment_status = :status WHERE id = :id");
        $stmt->bindValue(':status', $status, SQLITE3_TEXT);
        $stmt->bindValue(':id', $id_pre, SQLITE3_INTEGER);
        $stmt->execute();
       
    }

    if ($id_pre > 0 && $status == 'nopaid') {
        $stmt = $db->prepare("UPDATE presences SET pre_payment_status = :status WHERE id = :id");
        $stmt->bindValue(':status', $status, SQLITE3_TEXT);
        $stmt->bindValue(':id', $id_pre, SQLITE3_INTEGER);
        $stmt->execute();
        
    }
        

}

//header('Location: ../addition.php?id=' . $id_pre);
header('Location: ../presences.php');
exit;