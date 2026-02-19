<?php
// session_start(); // À mettre au tout début de chaque page
?>
<header>
    <nav>
        <a href="index.php" class="btn-nav">Accueil</a>
        <a href="Menu.php" class="btn-nav">Nos Menus</a>
        <a href="contact.php" class="btn-nav">Contact</a>
        
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="profil_utilisateur.php" class="btn-nav">Mon Compte</a>
            <a href="panier.php" class="btn-nav">Panier</a>
        <?php else: ?>
            <a href="login.php" class="btn-nav">Connexion</a>
            <a href="inscription.php" class="btn-nav">Inscription</a>
        <?php endif; ?>
        
        <a href="Avis_client.php" class="btn-nav">Laissez un avis</a>
    </nav>
</header>