<?php
include("header.php");

require 'backend/db.php';

$search_date = $_GET['search_date'] ?? '';
$search_nom = $_GET['search_nom'] ?? '';
$query = "SELECT * FROM presences";
if (!empty($search_date) || !empty($search_nom)) {
    $query .= " WHERE pre_date LIKE :search_date and pre_prenom LIKE :search_nom";
}
$stmt = $db->prepare($query);
if (!empty($search)) {
    $stmt->bindValue(':search_date', '%' . $search_date . '%', SQLITE3_TEXT);
    $stmt->bindValue(':search_nom', '%' . $search_nom . '%', SQLITE3_TEXT);
}
$result = $stmt->execute();

$hasResults = false;
?>

<div class="container mt-4">
    <h1 class="text-center">Gestion des présences</h1>


    

    <!-- Formulaire de recherche     -->
    
    <div class="card p-3 mb-3">
        
        <form action="presences_list.php" method="get"  class="d-flex align-items-center">
            <input type="text" name="search_date" class="form-control me-2" placeholder="Date de presence"
                value="<?= $_GET['searchd_date'] ?? '' ?>">
            <button type="submit" class="btn btn-secondary">Rechercher</button>
        </form>
    </div>

    <div class="card p-3 mb-3">
        
        <form action="presences_list.php" method="get"  class="d-flex align-items-center">
            <input type="text" name="search_nom" class="form-control me-2" placeholder="Nom de la presence"
                value="<?= $_GET['search_nom'] ?? '' ?>">
            <button type="submit" class="btn btn-secondary">Rechercher</button>
        </form>
    </div>


    <!-- Liste des presence -->
    <h3>Liste des presences</h3>
    <table class="table table-hover">
        <thead>
            <tr>
                
                <th>Nom</th>
                <th>Date</th>
                <th>Addition</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetchArray(SQLITE3_ASSOC)) : ?>
            <?php $hasResults = true; ?>
            <tr>
                <td><?= $row['pre_prenom'] ?></td>
                <td><?= $row['pre_date'] ?></td>
             
                <td>
                <?= $row['pre_payment_status'] ?>
                </td>
            </tr>
            <?php endwhile; ?>

            <?php if (!$hasResults) : ?>
            <tr>
                <td colspan="4" class="text-center">Aucun enregistrement trouvé</td>
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
    // Add date handling if needed

    var editModal = new bootstrap.Modal(document.getElementById("editModal"));
    editModal.show();
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>