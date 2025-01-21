<div class="status-bar">
    <div class="status">
        <?php echo htmlspecialchars($_SESSION['login']); ?> : 
        <span class="role-badge"><?php echo ucfirst(htmlspecialchars($_SESSION['role'])); ?></span>
    </div>
    <div class="logout">
        <a href="?logout=true">Déconnexion</a>
    </div>
</div> 