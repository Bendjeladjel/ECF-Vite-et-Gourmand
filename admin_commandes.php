<?php
session_start();
require_once 'db.php';

// 1. SÉCURITÉ : On vérifie le rôle (Admin ou Employé uniquement)
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'employe')) {
    header("Location: login.php");
    exit();
}

// 2. MISE À JOUR DU STATUT (Le "U" de CRUD)
if (isset($_POST['update_status'])) {
    $id_cmd = $_POST['commande_id'];
    $nouveau_statut = $_POST['nouveau_statut'];
    
    // On utilise numero_commande conformément à ta BDD
    $stmt = $pdo->prepare("UPDATE commandes SET statut = ? WHERE numero_commande = ?");
    $stmt->execute([$nouveau_statut, $id_cmd]);
    header("Location: admin_commandes.php?msg=ok");
    exit();
}

// 3. RÉCUPÉRATION DES COMMANDES AVEC FILTRE
$statut_filtre = $_GET['statut'] ?? '';
if ($statut_filtre) {
    $stmt = $pdo->prepare("SELECT * FROM commandes WHERE statut = ? ORDER BY date_commande DESC");
    $stmt->execute([$statut_filtre]);
} else {
    $stmt = $pdo->query("SELECT * FROM commandes ORDER BY date_commande DESC");
}
$commandes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Administrative - Vite & Gourmand</title>
    <link rel="stylesheet" href="CSS-Accueil.css">
    <style>
        .admin-container { padding: 20px; max-width: 1100px; margin: auto; color: white; }
        .table-admin { width: 100%; border-collapse: collapse; background: white; color: black; margin-top: 20px; border-radius: 8px; overflow: hidden; }
        .table-admin th { background: #000; color: white; padding: 12px; }
        .table-admin td { padding: 12px; border-bottom: 1px solid #ddd; text-align: center; }
        .filtres { margin: 20px 0; }
        .btn-filtre { text-decoration: none; background: #333; color: white; padding: 8px 15px; border-radius: 5px; margin-right: 5px; font-size: 0.9em; }
        .btn-active { background: #27ae60; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="admin-container">
        <h1>🛠️ Gestion des Commandes (Espace Pro)</h1>

        <div class="filtres">
            <span>Filtrer par : </span>
            <a href="admin_commandes.php" class="btn-filtre <?= !$statut_filtre ? 'btn-active' : '' ?>">Toutes</a>
            <a href="?statut=en attente" class="btn-filtre <?= $statut_filtre == 'en attente' ? 'btn-active' : '' ?>">En attente</a>
            <a href="?statut=en préparation" class="btn-filtre <?= $statut_filtre == 'en préparation' ? 'btn-active' : '' ?>">En préparation</a>
            <a href="?statut=terminée" class="btn-filtre <?= $statut_filtre == 'terminée' ? 'btn-active' : '' ?>">Terminées</a>
        </div>

        <table class="table-admin">
            <thead>
                <tr>
                    <th>N° Commande</th>
                    <th>Client (Email)</th>
                    <th>Statut Actuel</th>
                    <th>Changer Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $cmd): ?>
                <tr>
                    <td><strong>#<?= htmlspecialchars($cmd['numero_commande']) ?></strong></td>
                    <td style="text-align: left;">
                        <?= htmlspecialchars($cmd['email'] ?? 'Client inconnu') ?>
                    </td>
                    <td>
                        <span style="font-weight:bold; color: <?= $cmd['statut'] == 'annulé' ? 'red' : 'green' ?>;">
                            <?= strtoupper(htmlspecialchars($cmd['statut'])) ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="commande_id" value="<?= $cmd['numero_commande'] ?>">
                            <select name="nouveau_statut" onchange="this.form.submit()" style="padding: 5px;">
                                <option value="">--- Modifier ---</option>
                                <option value="en attente">En attente</option>
                                <option value="en préparation">En préparation</option>
                                <option value="livrée">Livrée</option>
                                <option value="terminée">Terminée</option>
                                <option value="annulé">Annuler (Refus)</option>
                            </select>
                            <input type="hidden" name="update_status" value="1">
                        </form>
                    </td>
                    <td>
                        <a href="admin_edit_commande.php?id=<?= $cmd['numero_commande'] ?>" style="color: #3498db; text-decoration: none; font-weight: bold;">[ Gérer ]</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>