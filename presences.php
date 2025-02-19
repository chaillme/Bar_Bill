<?php
include("header.php");

require 'backend/db.php';

// Obtenir la date du jour
$date_du_jour = date('Y-m-d');

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
$stmt = $db->prepare($query);
$stmt->bindValue(':date_du_jour', $date_du_jour, SQLITE3_TEXT);
if (!empty($search)) {
    $stmt->bindValue(':search', '%' . $search . '%', SQLITE3_TEXT);
}
$result = $stmt->execute();

$hasResults = false;
?>

<div class="container mt-4">
    <!--<h4 class="text-center">Gestion des présences</h4> -->

    <!-- Formulaire de ajout presence 
    <div class="card p-3 mb-3">
        <h3>Ajouter une presence</h3>
        <form action="backend/presence.php" method="post" enctype="multipart/form-data" class="d-flex align-items-center">
            <input type="text" name="prenom" class="form-control me-2" placeholder="Prenom et nom" required>
            <button type="submit" name="add" class="btn btn-success">Ajouter presence</button>
        </form>
    </div>-->

    <!-- Formulaire de recherche -->
    <div class="card p-3 mb-3">
       <!-- <h3>Rechercher une presence</h3> -->
        <form action="presences.php" method="get" class="d-flex align-items-center" autocomplete="off">
            <input type="text" name="search" class="form-control me-2" placeholder="Rechercher une presence" value="<?= $_GET['search'] ?? '' ?>">
            <button type="submit" class="btn btn-secondary">Rechercher</button>
        </form>
    </div>

    <!-- Liste des presence 
    <h4>Liste des présences du jour</h4>-->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Consomation</th>
                <th>Nom</th>
                <th>Date</th>
                <th>Total Consommé (€)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetchArray(SQLITE3_ASSOC)) : ?>
                <?php $hasResults = true; ?>
                <tr>
                    <td>
                        <form action="consommations.php" method="get" class="d-inline">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button type="submit" name="consomation" class="btn btn-success btn-sm">Ajouter conso (+)</button>
                        </form>
                    </td>
                    <td><?= $row['pre_prenom'] ?></td>
                    <td><?= $row['pre_date'] ?></td>
                    <td><?= $row['total_conso'] ?? 0 ?> €</td>
                    <td>
                        <button class="btn btn-outline-secondary btn-sm" onclick="openModal(<?= $row['id'] ?>, '<?= $row['pre_prenom'] ?>', '<?= $row['pre_date'] ?>')">Modifier</button>
                        <form action="backend/presence.php" method="post" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet enregistrement ?');">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button type="submit" name="supprimer" class="btn btn-outline-secondary btn-sm">Supprimer</button>
                        </form>
                        <form action="addition.php" method="post" class="d-inline">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                           
                            <?php 
                            // controle si c'est paye ou pas
                            if ($row['pre_payment_status'] <> 'paid') : ?> 
                            <button type="submit" name="consomation" class="btn btn-danger btn-sm">Addition</button>
                            <?php endif; ?>
                            <?php 
                            if ($row['pre_payment_status'] == 'paid') : ?> 
                            <button type="submit" name="consomation" class="btn btn-success btn-sm">Payé</button>
                            <?php endif; ?>    

                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>

            <?php if (!$hasResults) : ?>
                <tr>
                    <td colspan="5" class="text-center">Aucun enregistrement trouvé</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
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