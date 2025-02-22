<?php
include("header.php");
?>
<div class="container mt-4">
    <h1 class="text-center">Ajouter un membre</h1>
    <form action="backend/membres.php" method="post" class="mb-3">
        <input type="text" name="nom" class="form-control" placeholder="Nom" required>
        <input type="text" name="prenom" class="form-control mt-2" placeholder="Prénom" required>
        <input type="email" name="email" class="form-control mt-2" placeholder="Email">
        <input type="text" name="telephone" class="form-control mt-2" placeholder="Téléphone">
        <button type="submit" name="add" class="btn btn-primary mt-2">Ajouter Membre</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>