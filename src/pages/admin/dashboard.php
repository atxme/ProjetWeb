<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'ADMIN') {
    header('Location: ../index.php');
    exit();
}
?>
<div class="admin-dashboard">
    <nav>
        <ul>
            <li><a href="create-contest.php">Créer un concours</a></li>
            <li><a href="manage-users.php">Gérer les utilisateurs</a></li>
            <li><a href="assign-jury.php">Assigner les jurys</a></li>
            <li><a href="statistics.php">Statistiques</a></li>
        </ul>
    </nav>
    <!-- Contenu du tableau de bord -->
</div>