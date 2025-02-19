<?php
include("header.php");
?>

<div class="container mt-4">
    <h1 class="text-center">Ajouter une présence</h1>

    <!-- Formulaire ajout de boisson -->
    <form action="backend/presence.php" method="post" enctype="multipart/form-data" class="d-flex align-items-center">
        <input type="text" name="prenom" class="form-control me-2" placeholder="Prenom et nom" required>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked" checked>
            <label class="form-check-label" for="flexSwitchCheckChecked">Membre</label>
        </div>
        <button type="submit" name="add" class="btn btn-success ">Ajouter presence</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>