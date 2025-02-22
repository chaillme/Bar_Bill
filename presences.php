<?php
include "header.php";

require 'backend/db.php';

// Obtenir la date du jour
$date_du_jour = date('Y-m-d');
$date_du_jour_eur = date('d-m-Y');

$search = $_GET['search'] ?? '';
$query = "SELECT presences.*, 
                 (SELECT SUM(consommations.con_qty * articles.art_prix) 
                  FROM consommations 
                  JOIN articles ON consommations.con_art_id = articles.id 
                  WHERE consommations.con_pre_id = presences.id) as total_conso 
          FROM presences 
          WHERE pre_date = :date_du_jour";
if (!empty($search)) {
    $query .= " AND pre_prenom LIKE :search";
}
$query .= " order by pre_payment_status asc , pre_prenom asc";
$stmt = $db->prepare($query);
$stmt->bindValue(':date_du_jour', $date_du_jour, SQLITE3_TEXT);
if (!empty($search)) {
    $stmt->bindValue(':search', '%' . $search . '%', SQLITE3_TEXT);
}


$result = $stmt->execute();

$hasResults = false;
?>

<div class="container mt-4">
    <!-- Formulaire de recherche 
    <div class="card p-3 mb-3">
        <form action="presences.php" method="get" class="d-flex align-items-center" autocomplete="off">
            <input type="text" name="search" class="form-control me-2" placeholder="Rechercher une presence" value="<?= $_GET['search'] ?? '' ?>">
            <button type="submit" class="btn btn-secondary">Rechercher</button>
        </form>
    </div>-->


    <div class="card p-3 mb-3">
        <form action="backend/presence.php" method="post" enctype="multipart/form-data"
            class="d-flex align-items-center">
            <input type="text" name="prenom" class="form-control me-2" placeholder="Pour ajouter une présence inscriver le Prenom" required>


            <button type="submit" name="add" class="btn btn-success w-25"">Ajouter presence</button>
    </form>
        </div>



    <!-- Liste des présences sous forme de tuiles -->
    <div class=" row">
                <?php while ($row = $result->fetchArray(SQLITE3_ASSOC)) : ?>
                <?php $hasResults = true; ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body" >
                            <h5 class="card-title"><?= $row['pre_prenom'] ?></h5>
                            <!--<p class="card-text">Date: <?= $row['pre_date'] ?></p>-->
                            <p class="card-text">Total Consommé: <?= $row['total_conso'] ?? 0 ?> €</p>
                            <div class="d-flex justify-content-between">

                                <form action="consommations.php" method="get" class="d-inline">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="consomation" class="btn btn-success btn-sm">Conso
                                        (+)</button>
                                </form>
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm"
                                        onclick="openModal(<?= $row['id'] ?>, '<?= $row['pre_prenom'] ?>', '<?= $row['pre_date'] ?>')">Modifier</button>
                                    <form action="backend/presence.php" method="post" class="d-inline"
                                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet enregistrement ?');">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <button type="submit" name="supprimer"
                                            class="btn btn-outline-secondary btn-sm">Supprimer</button>
                                    </form>
                                </div>
                                <form action="addition.php" method="post" class="d-inline">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <?php if ($row['pre_payment_status'] <> 'paid') : ?>
                                    <button type="submit" name="consomation"
                                        class="btn btn-danger btn-sm">Addition (=)</button>
                                    <?php endif; ?>
                                    <?php if ($row['pre_payment_status'] == 'paid') : ?>
                                    <button type="submit" name="consomation"
                                        class="btn btn-success btn-sm">Payé</button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>

                <?php if (!$hasResults) : ?>
                <div class="col-12 text-center">
                    <p>Aucune présence aujourd'hui (<?= $date_du_jour_eur; ?>)</p>
                </div>
                <?php endif; ?>
    </div>
</div>

<!-- Modal de modification -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Modifier presences</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" action="backend/presence.php" method="post">
                    <input type="hidden" name="id" id="editId">
                    <div class="mb-3">
                        <label for="editPreNom" class="form-label">PreNom</label>
                        <input type="text" class="form-control" name="prenom" id="editPreNom" required>
                    </div>
                    <div class="mb-3">
                        <label for="editPreDate" class="form-label">Date</label>
                        <input type="date" class="form-control" name="date" id="editPreDate" required>
                    </div>
                    <button type="submit" name="modifier" class="btn btn-warning">Modifier</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openModal(id, prenom, date) {
    document.getElementById("editId").value = id;
    document.getElementById("editPreNom").value = prenom;
    document.getElementById("editPreDate").value = date;

    var editModal = new bootstrap.Modal(document.getElementById("editModal"));
    editModal.show();
}


</script>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->

</body>

</html>