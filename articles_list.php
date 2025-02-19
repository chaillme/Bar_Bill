<?php
include("header.php");
require 'backend/db.php';

$search = $_GET['search'] ?? '';

$query = "SELECT * FROM articles ";
if (!empty($search)) {
    $query .= " WHERE art_nom LIKE :search";
}
$query .= " order by art_nom";
$stmt = $db->prepare($query);
if (!empty($search)) {
    $stmt->bindValue(':search', '%' . $search . '%', SQLITE3_TEXT);
}
$result = $stmt->execute();

$hasResults = false;
?>

<div class="container mt-4">
    <h1 class="text-center">Gestion des articles</h1>

    <a href="articles_add.php" class="btn btn-primary mb-3">Ajouter une Boisson</a>

    <!-- Formulaire de recherche -->
    <div class="card mb-3">
        <div class="card-body">
            <form action="articles_list.php" method="get" class="mb-3">
                <input type="text" name="search" class="form-control" placeholder="Rechercher une boisson" value="<?= $_GET['search'] ?? '' ?>">
                <button type="submit" class="btn btn-secondary mt-2">Rechercher</button>
            </form>
        </div>
    </div>

    <!-- Liste des articles -->
    <h3>Liste des articles Disponibles</h3>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Prix (€)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetchArray(SQLITE3_ASSOC)) : ?>
                <?php $hasResults = true; ?>
                <tr>
                    <td><?= $row['art_nom'] ?></td>
                    <td><?= $row['art_description'] ?></td>
                    <td><?= $row['art_prix'] ?></td>
                    <td>
                        <button class='btn btn-warning btn-sm' onclick='openModal(<?= $row['id'] ?>, "<?= $row['art_nom'] ?>", "<?= $row['art_description'] ?>", <?= $row['art_prix'] ?>)'>Modifier</button>
                        <form action='backend/articles.php' method='post' class='d-inline'>
                            <input type='hidden' name='id' value='<?= $row['id'] ?>'>
                            <button type='submit' name='supprimer' class='btn btn-danger btn-sm'>Supprimer</button>
                        </form>
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
                <h5 class="modal-title" id="editModalLabel">Modifier Boisson</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" action="backend/articles.php" method="post">
                    <input type="hidden" name="id" id="editId">
                    <div class="mb-3">
                        <label for="editNom" class="form-label">Nom</label>
                        <input type="text" class="form-control" name="nom" id="editNom" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <input type="text" class="form-control" name="description" id="editDescription">
                    </div>
                    <div class="mb-3">
                        <label for="editPrix" class="form-label">Prix (€)</label>
                        <input type="number" class="form-control" name="prix" id="editPrix" step="0.01" required>
                    </div>
                    <button type="submit" name="modifier" class="btn btn-warning">Modifier</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openModal(id, nom, description, prix) {
        document.getElementById("editId").value = id;
        document.getElementById("editNom").value = nom;
        document.getElementById("editDescription").value = description;
        document.getElementById("editPrix").value = prix;
        var editModal = new bootstrap.Modal(document.getElementById("editModal"));
        editModal.show();
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>