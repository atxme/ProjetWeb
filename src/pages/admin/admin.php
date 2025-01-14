<?php
session_start();
require_once '../../includes/db.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Concours de Dessin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-box">
            <div class="admin-header">
                <h2>Gestion des Concours</h2>
            </div>

            <!-- Formulaire de création de concours -->
            <form id="concoursForm" method="POST" action="process/create_concours.php">
                <div class="form-group">
                    <label for="theme">Thème du concours</label>
                    <input type="text" id="theme" name="theme" required>
                </div>

                <div class="form-group">
                    <label for="descriptif">Descriptif</label>
                    <textarea id="descriptif" name="descriptif" required></textarea>
                </div>

                <div class="form-group">
                    <label for="dateDeb">Date de début</label>
                    <input type="date" id="dateDeb" name="dateDeb" required>
                </div>

                <div class="form-group">
                    <label for="dateFin">Date de fin</label>
                    <input type="date" id="dateFin" name="dateFin" required>
                </div>

                <div class="form-group">
                    <label for="etat">État du concours</label>
                    <select id="etat" name="etat" required>
                        <option value="pas commence">Non commencé</option>
                        <option value="en cours">En cours</option>
                        <option value="attente">En attente</option>
                        <option value="resultat">Résultat</option>
                        <option value="evalue">Évalué</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit">Créer le concours</button>
            </form>

            <!-- Formulaire d'ajout d'utilisateur -->
            <div class="admin-header">
                <h2>Gestion des Utilisateurs</h2>
            </div>

            <form id="userForm" method="POST" action="process/create_user.php">
                <div class="form-group">
                    <label for="type">Type d'utilisateur</label>
                    <select id="type" name="type" required>
                        <option value="competiteur">Compétiteur</option>
                        <option value="evaluateur">Évaluateur</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" required>
                </div>

                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" required>
                </div>

                <div class="form-group">
                    <label for="age">Âge</label>
                    <input type="number" id="age" name="age" required>
                </div>

                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse" required>
                </div>

                <button type="submit" class="btn-submit">Ajouter l'utilisateur</button>
            </form>
        </div>
    </div>

    <script src="assets/js/admin.js"></script>
</body>
</html>
