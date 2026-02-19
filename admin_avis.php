<?php
session_start();
require_once 'db.php';

// On récupère uniquement ceux qui ne sont pas encore validés
$avis_en_attente = $pdo->query("SELECT * FROM avis WHERE est_valide = 0 ORDER BY date_avis DESC")->fetchAll();

if (isset($_GET['approuver_id'])) {
    $id = $_GET['approuver_id'];
    $pdo->prepare("UPDATE avis SET est_valide = 1 WHERE id = ?")->execute([$id]);
    header("Location: admin_avis.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Modération Avis</title>
    <link rel="stylesheet" href="CSS-Accueil.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container" style="padding: 20px;">
        <h1>Avis en attente de modération</h1>
        
        <?php if (empty($avis_en_attente)): ?>
            <p>Tout est à jour ! Aucun avis à modérer.</p>
        <?php else: ?>
            <?php foreach ($avis_en_attente as $a): ?>
                <div style="border:1px solid #000000; padding:15px; margin-bottom:10px; background:#fff;">
                    <strong><?= htmlspecialchars($a['nom_client']) ?></strong> (Note: <?= $a['note'] ?>/5)
                    <p style="margin:10px 0;"><?= htmlspecialchars($a['commentaire']) ?></p>
                    <a href="?approuver_id=<?= $a['id'] ?>" style="color:green; font-weight:bold; text-decoration:none;">✅ Approuver</a> | 
                    <a href="?supprimer_id=<?= $a['id'] ?>" onclick="return confirm('Supprimer ?')" style="color:red; text-decoration:none;">❌ Supprimer</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <p style="margin-top:20px;"><a href="admin_dashboard.php">⬅ Retour au Dashboard</a></p>
    </div>
</body>
</html>