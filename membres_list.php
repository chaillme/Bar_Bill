<?php
include("header.php");
require 'backend/db.php';
$result = $db->query("SELECT * FROM membres");
?>
<div class="container mt-4">
    <h1 class="text-center">Liste des membres</h1>
    <a href="membres_add.php" class="btn btn-primary mb-3">Ajouter un Membre</a>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetchArray(SQLITE3_ASSOC)) : ?>
                <tr>
                    <td><?= $row['nom'] ?></td>
                    <td><?= $row['prenom'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['telephone'] ?></td>
                    <td>
                        <a href="membres_edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                        <form action="backend/membres.php" method="post" class="d-inline">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button type="submit" name="supprimer" class="btn btn-danger btn-sm">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>