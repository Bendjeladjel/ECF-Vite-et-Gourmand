<?php
// 1. DÉMARRAGE DE LA SESSION ET CONNEXION BDD
session_start();
require_once 'db.php';

// 2. SÉCURITÉ : Vérifier si l'utilisateur est connecté
// Si la session n'existe pas, on redirige vers la page de connexion
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 3. RÉCUPÉRATION DES INFOS DE L'UTILISATEUR
$id_user = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id_user]);
$user = $stmt->fetch();

// Si l'utilisateur n'existe plus en BDD (cas rare), on déconnecte
if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Variables prêtes pour l'affichage
$prenom = htmlspecialchars($user['prenom']);
$nom = htmlspecialchars($user['nom']);
$email = htmlspecialchars($user['email']);
$date_inscription = date('d/m/Y', strtotime($user['date_creation']));

// 4. RÉCUPÉRATION DE L'HISTORIQUE DES COMMANDES
// On cherche dans la table 'commandes' toutes celles qui correspondent à cet email
$reqOrders = $pdo->prepare("SELECT * FROM commandes WHERE email = ? ORDER BY date_commande DESC");
$reqOrders->execute([$email]);
$commandes = $reqOrders->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Vite & Gourmand</title>
    <link rel="stylesheet" href="CSS-Accueil.css"> 
    <style>
        .profil-box { max-width: 900px; margin: 40px auto; padding: 20px; font-family: Arial, sans-serif; }
        .user-card { background: #f4f4f4; padding: 20px; border-radius: 10px; margin-bottom: 30px; border-left: 5px solid #e67e22; }
        .order-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .order-table th, .order-table td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        .order-table th { background-color: #e67e22; color: white; }
        .btn-logout { display: inline-block; margin-top: 20px; color: #d9534f; text-decoration: none; font-weight: bold; }
        .btn-logout:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="profil-box">
        <h1>Mon Espace Personnel</h1>

        <div class="user-card">
            <h2>Bonjour, <?= $prenom ?> !</h2>
            <p><strong>Nom complet :</strong> <?= $nom ?> <?= $prenom ?></p>
            <p><strong>Email :</strong> <?= $email ?></p>
            <p><strong>Membre depuis le :</strong> <?= $date_inscription ?></p>
            <a href="deconnexion.php" class="btn-logout">Se déconnecter</a>
        </div>

        <h3>Mes dernières commandes</h3>
        
        <?php if (count($commandes) > 0): ?>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>N° Commande</th>
                        <th>Date</th>
                        <th>Adresse de livraison</th>
                        <th>Total payé</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $cmd): ?>
                        <tr>
                            <td>#<?= $cmd['id'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($cmd['date_commande'])) ?></td>
                            <td><?= htmlspecialchars($cmd['adresse']) ?></td>
                            <td><strong><?= number_format($cmd['total'], 2, ',', ' ') ?> €</strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="background: #fff3cd; padding: 15px; border-radius: 5px; color: #856404;">
                Vous n'avez pas encore passé de commande. <a href="Menu.php">Découvrez nos menus !</a>
            </p>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>

</body>
</html>