<?php
require 'backend/db.php';

$search = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pre = $_POST['id'] ?? 0;
    $prenom = $_POST['prenom'] ?? 0;
    $search = $_POST['search'] ?? '';
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id_pre = $_GET['id'] ?? 0;
    $search = $_GET['search'] ?? '';
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
    <h4 class="text-center">Ajouter à <b><?= $prenom ?></b></h4>
    <!-- Champ de recherche -->
    <div class="mb-3">
        <form id="searchForm" autocomplete="off">
            <input type="hidden" name="id" value="<?= $id_pre ?>">
            <input type="text" id="searchInput" name="search" class="form-control" placeholder="Rechercher des articles..." value="<?= htmlspecialchars($search) ?>">
        </form>
    </div>
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
        <tbody id="articlesTableBody">
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
            WHERE articles.art_nom LIKE :search
            GROUP BY articles.id, articles.art_nom, articles.art_prix, presences.id
            ORDER by articles.art_nom";

            $stmt = $db->prepare($query);
            $stmt->bindValue(':id_pre', $id_pre, SQLITE3_INTEGER);
            $stmt->bindValue(':search', '%' . $search . '%', SQLITE3_TEXT);
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
                        <button type='button' class='btn btn-success btn-sm add-btn' data-art-id='<?= $row['id'] ?>' data-pre-id='<?= $id_pre ?>' data-art-prix='<?= $row['art_prix'] ?>'>+</button>
                    </td>
                    <td><?= $row['art_nom'] ?></td>
                    <td><b><?= $row['con_qty'] ?></b></td>
                    <td><?= $row['art_prix'] ?></td>
                    <td><?= $totalqty ?></td>
                    <td>
                        <button type='button' class='btn btn-danger btn-sm remove-btn' data-art-id='<?= $row['id'] ?>' data-pre-id='<?= $id_pre ?>' data-art-prix='<?= $row['art_prix'] ?>'>-</button>
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
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('searchInput').addEventListener('input', function() {
        var searchInput = document.getElementById('searchInput').value;
        var idPre = <?= $id_pre ?>;
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'consommations.php?id=' + idPre + '&search=' + encodeURIComponent(searchInput), true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var parser = new DOMParser();
                var doc = parser.parseFromString(xhr.responseText, 'text/html');
                var newTableBody = doc.getElementById('articlesTableBody').innerHTML;
                document.getElementById('articlesTableBody').innerHTML = newTableBody;
                attachEventListeners();
            }
        };
        xhr.send();
    });

    function attachEventListeners() {
        document.querySelectorAll('.add-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                var artId = this.getAttribute('data-art-id');
                var preId = this.getAttribute('data-pre-id');
                var artPrix = this.getAttribute('data-art-prix');
                updateConsommation('add', artId, preId, artPrix);
            });
        });

        document.querySelectorAll('.remove-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                var artId = this.getAttribute('data-art-id');
                var preId = this.getAttribute('data-pre-id');
                var artPrix = this.getAttribute('data-art-prix');
                updateConsommation('supprimer', artId, preId, artPrix);
            });
        });
    }

    function updateConsommation(action, artId, preId, artPrix) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'backend/consommation.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var searchInput = document.getElementById('searchInput').value;
                var idPre = <?= $id_pre ?>;
                var xhrSearch = new XMLHttpRequest();
                xhrSearch.open('GET', 'consommations.php?id=' + idPre + '&search=' + encodeURIComponent(searchInput), true);
                xhrSearch.onreadystatechange = function() {
                    if (xhrSearch.readyState === 4 && xhrSearch.status === 200) {
                        var parser = new DOMParser();
                        var doc = parser.parseFromString(xhrSearch.responseText, 'text/html');
                        var newTableBody = doc.getElementById('articlesTableBody').innerHTML;
                        document.getElementById('articlesTableBody').innerHTML = newTableBody;
                        attachEventListeners();
                    }
                };
                xhrSearch.send();
            }
        };
        xhr.send('art_id=' + artId + '&pre_id=' + preId + '&con_qty=1&art_prix=' + artPrix + '&' + action + '=true');
    }

    attachEventListeners();
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>