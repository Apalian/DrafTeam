<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Modifier Joueur</title>
</head>
<body>

<div class="container">
    <h1 class="form-title">Modifier le Joueur</h1>
    <form method="POST" class="player-form">
        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" class="form-input" value="<?php echo htmlspecialchars($joueur->getNom()); ?>" required>
        </div>

        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" class="form-input" value="<?php echo htmlspecialchars($joueur->getPrenom()); ?>" required>
        </div>

        <div class="form-group">
            <label for="dateNaissance">Date de naissance :</label>
            <input type="date" id="dateNaissance" name="dateNaissance" class="form-input" value="<?php echo htmlspecialchars($joueur->getDateNaissance()); ?>" required>
        </div>

        <div class="form-group">
            <label for="statut">Statut :</label>
            <input type="text" id="statut" name="statut" class="form-input" value="<?php echo htmlspecialchars($joueur->getStatut()); ?>" required>
        </div>

        <div class="form-group">
            <label for="commentaire">Commentaire :</label>
            <textarea id="commentaire" name="commentaire" class="form-textarea"><?php echo htmlspecialchars($joueur->getCommentaire()); ?></textarea>
        </div>

        <div class="form-group">
            <label for="taille">Taille (cm) :</label>
            <input type="number" id="taille" name="taille" class="form-input" value="<?php echo htmlspecialchars($joueur->getTaille()); ?>" required>
        </div>

        <div class="form-group">
            <label for="poids">Poids (kg) :</label>
            <input type="number" id="poids" name="poids" class="form-input" value="<?php echo htmlspecialchars($joueur->getPoids()); ?>" required>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn-submit">Valider</button>
            <a href="../Controller/GestionJoueursController.php" class="btn-cancel"><button type="button">Annuler</button></a>
        </div>
    </form>
</div>

</body>
</html>
