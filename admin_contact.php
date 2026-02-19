<?php
session_start();
require_once 'db.php';

// Sécurité : Accès réservé
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'employe')) {
    header("Location: login.php"); exit();
}

// On récupère uniquement la table contact
$messages = $pdo->query("SELECT * FROM contact ORDER BY date_envoi DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="CSS-Accueil.css">
    <title>Admin - Messages Clients</title>
    <?php include 'header.php'; ?>
    <style>
        .msg-card { background: black; color: white; padding: 20px; margin: 15px auto; max-width: 800px; border-radius: 8px; border-left: 5px solid #3498db; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn-repondre { background: #3498db; color: black; padding: 8px 15px; text-decoration: none; border-radius: 4px; display: inline-block; margin-top: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <h1 style="text-align:center; color: white;">📩 Messages Clients (Table Contact)</h1>
    
    <?php if (empty($messages)): ?>
        <p style="text-align:center; color: white;">Aucun message reçu pour le moment.</p>
    <?php endif; ?>

    <?php foreach($messages as $m): ?>
        <div class="msg-card">
            <strong>De : <?= htmlspecialchars($m['nom'] ?? 'Inconnu') ?></strong> 
            (<a href="mailto:<?= htmlspecialchars($m['email']) ?>"><?= htmlspecialchars($m['email'] ?? 'Pas d\'email') ?></a>)<br>
            <small>Reçu le : <?= $m['date_envoi'] ?></small>
            <hr>
            <p><?= nl2br(htmlspecialchars($m['commentaire'] ?? '')) ?></p>
            <a href="mailto:<?= $m['email'] ?>?subject=Réponse Vite & Gourmand" class="btn-repondre">Répondre par e-mail</a>
        </div>
    <?php endforeach; ?>
</body>
<?php include 'footer.php'; ?>
</html>