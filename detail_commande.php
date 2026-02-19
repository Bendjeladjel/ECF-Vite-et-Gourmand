<?php
session_start();
require_once 'db.php';

// 1. SÉCURITÉ : On vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$commande = null;

// 2. RÉCUPÉRATION DE LA COMMANDE
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_reçu = intval($_GET['id']);
    
// 1. Récupérer l'email de l'utilisateur en session
$stmtUser = $pdo->prepare("SELECT email FROM utilisateurs WHERE id = ?");
$stmtUser->execute([$_SESSION['user_id']]);
$userEmail = $stmtUser->fetchColumn();

// 2. Chercher la commande ET vérifier qu'elle appartient à cet email
$stmt = $pdo->prepare("SELECT * FROM commandes WHERE numero_commande = ? AND email = ?");
$stmt->execute([$id_reçu, $userEmail]);
$commande = $stmt->fetch();
    // Si la commande n'existe pas, on retourne au profil
    if (!$commande) {
        header("Location: profil_utilisateur.php?err=notfound");
        exit();
    }
} else {
    // Si pas d'ID dans l'URL, on retourne au profil (ce qui cause le "rafraîchissement")
    header("Location: profil_utilisateur.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails Commande #<?= $id_reçu ?></title>
    <link rel="stylesheet" href="CSS-Accueil.css">
    <style>
        .detail-container { 
            background: rgba(0, 0, 0, 0.9); 
            color: white; 
            padding: 30px; 
            max-width: 600px; 
            margin: 50px auto; 
            border-radius: 10px; 
            border: 1px solid #3498db;
        }
        .info-line { margin: 15px 0; border-bottom: 1px solid #333; padding-bottom: 5px; }
        .status-badge { background: #3498db; padding: 5px 10px; border-radius: 4px; font-weight: bold; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="detail-container">
        <a href="profil_utilisateur.php" style="color: #ccc; text-decoration: none;">⬅ Retour au profil</a>
        
        <h2 style="margin-top: 20px;">Détails de la Commande #<?= $id_reçu ?></h2>
        
        <div class="info-line">
            <strong>Date :</strong> <?= date('d/m/Y H:i', strtotime($commande['date_commande'])) ?>
        </div>
        
        <div class="info-line">
            <strong>Statut :</strong> <span class="status-badge"><?= strtoupper($commande['statut']) ?></span>
        </div>
        
        <div class="info-line">
            <strong>Adresse de livraison :</strong><br>
            <?= nl2br(htmlspecialchars($commande['adresse'])) ?>
        </div>
        
        <div class="info-line" style="font-size: 1.3em; color: #2ecc71;">
            <strong>Total :</strong> <?= number_format($commande['total'], 2, ',', ' ') ?> €
        </div>

        <p style="margin-top: 30px; font-style: italic; color: #7f8c8d;">
            Merci de votre confiance chez Vite & Gourmand !
        </p>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>