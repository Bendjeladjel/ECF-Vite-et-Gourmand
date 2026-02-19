<?php
session_start();
require_once 'db.php';

// 1. SÉCURITÉ : Admin ou Employé uniquement
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'employe')) {
    header("Location: login.php");
    exit();
}

// 2. RÉCUPÉRATION DE LA COMMANDE
if (isset($_GET['id'])) {
    $numero_commande = intval($_GET['id']);
    
    // Requête pour avoir les détails de la commande
    $stmt = $pdo->prepare("SELECT * FROM commandes WHERE numero_commande = ?");
    $stmt->execute([$numero_commande]);
    $commande = $stmt->fetch();

    if (!$commande) {
        die("Commande introuvable.");
    }
} else {
    header("Location: admin_commandes.php");
    exit();
}

// 3. LOGIQUE D'ANNULATION PAR L'ADMIN
if (isset($_POST['annuler_commande'])) {
    $update = $pdo->prepare("UPDATE commandes SET statut = 'annulé' WHERE numero_commande = ?");
    $update->execute([$numero_commande]);
    header("Location: admin_commandes.php?msg=annulee");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer la commande #<?= $numero_commande ?></title>
    <link rel="stylesheet" href="CSS-Accueil.css">
    <style>
        .admin-box { background: rgba(0,0,0,0.9); padding: 30px; border-radius: 10px; max-width: 700px; margin: 40px auto; color: white; border: 1px solid #3498db; }
        .detail-row { border-bottom: 1px solid #333; padding: 10px 0; display: flex; justify-content: space-between; }
        .btn-back { color: #ccc; text-decoration: none; display: inline-block; margin-bottom: 20px; }
        .btn-delete { background: #e74c3c; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="admin-box">
        <a href="admin_commandes.php" class="btn-back">⬅️ Retour à la liste</a>
        
        <h2>Détails de la Commande #<?= $numero_commande ?></h2>

        <div class="detail-row">
            <span><strong>Date :</strong></span>
            <span><?= date('d/m/Y H:i', strtotime($commande['date_commande'])) ?></span>
        </div>

        <div class="detail-row">
            <span><strong>Client :</strong></span>
            <span><?= htmlspecialchars($commande['email']) ?></span>
        </div>

        <div class="detail-row">
            <span><strong>Adresse de livraison :</strong></span>
            <span style="max-width: 300px; text-align: right;"><?= nl2br(htmlspecialchars($commande['adresse'])) ?></span>
        </div>

        <div class="detail-row">
            <span><strong>Montant Total :</strong></span>
            <span style="font-size: 1.2em; color: #2ecc71; font-weight: bold;"><?= number_format($commande['total'], 2, ',', ' ') ?> €</span>
        </div>

        <div class="detail-row">
            <span><strong>Statut actuel :</strong></span>
            <span class="badge"><?= strtoupper(htmlspecialchars($commande['statut'])) ?></span>
        </div>

        <?php if ($commande['statut'] !== 'annulé' && $commande['statut'] !== 'terminée'): ?>
            <form method="POST" onsubmit="return confirm('Voulez-vous vraiment annuler cette commande ?');">
                <button type="submit" name="annuler_commande" class="btn-delete">🚫 Annuler et Refuser la commande</button>
            </form>
        <?php else: ?>
            <p style="margin-top: 20px; font-style: italic; color: #7f8c8d;">Cette commande ne peut plus être modifiée (Statut : <?= $commande['statut'] ?>).</p>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>