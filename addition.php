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
    <h1 class="text-center">Addition pour <?= $prenom ?> </h1>
    <!-- Liste des articles -->
    <h4>Résume des consommation</h4>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Articles</th>
                <th>Qty</th>
                <th>Prix</th>
                <th>Total</th>

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
             JOIN consommations ON articles.id = consommations.con_art_id 
                AND consommations.con_pre_id = :id_pre
             JOIN presences ON presences.id = :id_pre
            GROUP BY articles.id, articles.art_nom, articles.art_prix, presences.id";

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


                <td><?= $row['art_nom'] ?></td>
                <td><b><?= $row['con_qty'] ?></b></td>
                <td><?= $row['art_prix'] ?></td>
                <td><?= $totalqty ?></td>

            </tr>

            <?php
            }

            if (!$hasResults) {
                echo "<tr><td colspan='6' class='text-center'>Aucun enregistrement trouvé</td></tr>";
            }
            ?>



        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        <div class="card" style="width: 18rem;">
            <div class="card-body">
                <h4 class="card-title">Total à payer</h4>
                <?= $totalSum ?> €
                <div class="mt-3">
                    <form action="backend/payment_status.php" method="post" class="d-inline">
                        <input type="hidden" name="id_pre" value="<?= $id_pre ?>">
                        <button type="submit" name="status" value="paid" class="btn btn-success">Payé</button>
                    </form>
                    <form action="backend/payment_status.php" method="post" class="d-inline">
                        <input type="hidden" name="id_pre" value="<?= $id_pre ?>">
                        <button type="submit" name="status" value="nopaid" class="btn btn-danger">Non Payé</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>