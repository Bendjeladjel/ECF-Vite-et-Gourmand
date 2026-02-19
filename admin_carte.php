<?php
session_start();
require_once 'db.php';

// Sécurité
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

// Action : Supprimer un plat
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM plats WHERE id = ?")->execute([$id]);
    header("Location: admin_carte.php");
}

// Action : Ajouter un plat
if (isset($_POST['add_plat'])) {
    $nom = htmlspecialchars($_POST['nom_plat']);
    $prix = $_POST['prix'];
    $desc = htmlspecialchars($_POST['description']);
    $pdo->prepare("INSERT INTO plats (nom, prix, description) VALUES (?, ?, ?)")->execute([$nom, $prix, $desc]);
}

$plats = $pdo->query("SELECT * FROM plats")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Carte</title>
    <link rel="stylesheet" href="CSS-Accueil.css">
    <?php include 'header.php'; ?>
</head>
<body>
    <h1>Administration de la Carte</h1>

    <h2>Ajouter un plat</h2>
    <form method="POST">
        <input type="text" name="nom_plat" placeholder="Nom du plat" required>
        <input type="number" name="prix" placeholder="Prix (€)" required step="0.01">
        <textarea name="description" placeholder="Description du plat"></textarea>
        <button type="submit" name="add_plat">Ajouter le plat</button>
    </form>

    <h2>Liste des plats</h2>
    <?php foreach ($plats as $plat): ?>
        <div class="plat-item">
            <strong><?= htmlspecialchars($plat['nom']) ?></strong> - <?= $plat['prix'] ?>€
            <p><?= htmlspecialchars($plat['description']) ?></p>
            <a href="?delete=<?= $plat['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce plat ?')">Supprimer</a>
        </div>
    <?php endforeach; ?>
</body>
</html>