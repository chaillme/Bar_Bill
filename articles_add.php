<?php
include("header.php");
?>

<div class="container mt-4">
    <h1 class="text-center">Ajouter un article</h1>
    <a href="articles_list.php" class="btn btn-primary mb-3">Retour à la liste</a>
    <!-- Formulaire ajout de boisson -->
    <form action="backend/articles.php" method="post" enctype="multipart/form-data" class="mb-3">
        <input type="text" name="nom" class="form-control" placeholder="Nom de l'article" required>
        <input type="text" name="description" class="form-control mt-2" placeholder="Description de l'article">
        <input type="number" name="prix" class="form-control mt-2" placeholder="Prix de l'article" step="0.01" required>
        <button type="submit" name="add" class="btn btn-primary mt-2">Ajouter Article</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>