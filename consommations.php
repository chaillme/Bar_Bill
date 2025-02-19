<?php
require 'backend/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pre = $_POST['id'] ?? 0;
    $prenom = $_POST['prenom'] ?? 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id_pre = $_GET['id'] ?? 0;
}

if ($id_pre > 0) {
    $stmt = $db->prepare("SELECT pre_prenom FROM presences WHERE id = :id");
    $stmt->bindValue(':id', $id_pre, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);
    if ($row) {
        $prenom = $row['pre_prenom'];
    }
}

include("header.php");
?>

<div class="container mt-4">
    <h1 class="text-center">Ajouter des consommations à<br> <b><?= $prenom ?></b></h1>
    <!-- Liste des articles -->
    
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Ajouter (+)</th>
                <th>Articles</th>
                <th>Qty</th>
                <th>Prix</th>
                <th>Total</th>
                <th>Retirer (-)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT 
                articles.*, 
                COALESCE(SUM(consommations.con_qty), 0) as con_qty,
                presences.id as presence_id,
                (COALESCE(SUM(consommations.con_qty), 0) * articles.art_prix) as total_price
            FROM articles 
            LEFT JOIN consommations ON articles.id = consommations.con_art_id 
                AND consommations.con_pre_id = :id_pre
            LEFT JOIN presences ON presences.id = :id_pre
            GROUP BY articles.id, articles.art_nom, articles.art_prix, presences.id
            ORDER by articles.art_nom";

            $stmt = $db->prepare($query);
            $stmt->bindValue(':id_pre', $id_pre, SQLITE3_INTEGER);
            $result = $stmt->execute();

            $hasResults = false;
            $totalSum = 0;
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $hasResults = true;
                $totalqty = $row['art_prix'] * $row['con_qty'];
                $totalSum += $totalqty;
                ?>
                <tr>
                    <td>
                        <form action='backend/consommation.php' method='post' class='d-inline'>
                            <input type='hidden' name='art_id' value='<?= $row['id'] ?>'>
                            <input type='hidden' name='pre_id' value='<?= $id_pre ?>'>
                            <input type='hidden' name='con_qty' value=1>
                            <input type='hidden' name='art_prix' value='<?= $row['art_prix'] ?>'>
                            <button type='submit' name='add' class='btn btn-success btn-sm'>+</button>
                        </form>
                    </td>
                    <td><?= $row['art_nom'] ?></td>
                    <td><b><?= $row['con_qty'] ?></b></td>
                    <td><?= $row['art_prix'] ?></td>
                    <td><?= $totalqty ?></td>
                    <td>
                        <form action='backend/consommation.php' method='post' class='d-inline'>
                            <input type='hidden' name='art_id' value='<?= $row['id'] ?>'>
                            <input type='hidden' name='pre_id' value='<?= $id_pre ?>'>
                            <input type='hidden' name='con_qty' value=1>
                            <input type='hidden' name='art_prix' value='<?= $row['art_prix'] ?>'>
                            <button type='submit' name='supprimer' class='btn btn-danger btn-sm'>-</button>
                        </form>
                    </td>
                </tr>
                <?php
            }

            if (!$hasResults) {
                echo "<tr><td colspan='6' class='text-center'>Aucun enregistrement trouvé</td></tr>";
            }
            ?>
            <tr class='table-dark'>
                <td colspan='4'><strong>Total</strong></td>
                <td><strong><?= $totalSum ?></strong></td>
                <td colspan='2'></td>
            </tr>
        </tbody>
    </table>
</div>

<script>
function openModal(id, prenom, date) {
    document.getElementById("editId").value = id;
    document.getElementById("editPreNom").value = prenom;
    // Add date handling if needed

    var editModal = new bootstrap.Modal(document.getElementById("editModal"));
    editModal.show();
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>