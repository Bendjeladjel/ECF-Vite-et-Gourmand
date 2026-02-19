<?php
session_start();
require_once 'db.php';

// Sécurité : Uniquement Admin ou Employé
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'employe')) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Vite & Gourmand</title>
    <link rel="stylesheet" href="CSS-Accueil.css">
        <?php include 'header.php'; ?>
</head>
<body>
    <h1>Tableau de bord - <?= strtoupper($_SESSION['role']) ?></h1>
    
    <nav class="admin-nav">
        <div class="section">
            <h3>📦 Commandes & Clients</h3>
            <a href="admin_commandes.php" class="btn">Gérer les commandes</a>
            <a href="admin_avis.php" class="btn">Modérer les avis</a>
             <a href="admin_contact.php" class="btn">les messages de contact</a>
        </div>

        <div class="section">
            <h3>🍽️ Carte & Menus</h3>
            <a href="admin_menus.php" class="btn">Modifier les Menus/Plats</a>
            <a href="admin_horaires.php" class="btn">Gérer les Horaires</a>
        </div>

        <?php if ($_SESSION['role'] === 'admin'): ?>
        <div class="section admin-only">
            <h3>👥 Gestion Interne (Admin)</h3>
            <a href="admin_comptes.php" class="btn">Créer/Désactiver un employé</a>
            <a href="generer_stats_nosql.php" class="btn">Statistiques NoSQL</a>
        </div>
        <?php endif; ?>
    </nav>
  <?php include 'footer.php'; ?>      
</body>
</html>
