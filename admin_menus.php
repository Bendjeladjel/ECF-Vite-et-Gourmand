<?php
session_start();
require_once 'db.php';

// On vérifie si l'utilisateur est connecté ET s'il est soit admin, soit employe
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'employe')) {
    header("Location: login.php"); 
    exit();
}

$current_page = "admin_menus.php";
$plat_a_modifier = null;

// 1. SI ON CLIQUE SUR LE BOUTON ORANGE "MODIFIER" DANS LE TABLEAU
if (isset($_GET['modifier_id'])) {
    $id_edit = intval($_GET['modifier_id']);
    $stmt = $pdo->prepare("SELECT * FROM menus WHERE id = ?");
    $stmt->execute([$id_edit]);
    $plat_a_modifier = $stmt->fetch(); // On récupère les infos pour les mettre dans le formulaire
}

// 2. LOGIQUE D'ENREGISTREMENT (AJOUT OU MISE À JOUR)
if (isset($_POST['enregistrer_plat'])) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $titre = htmlspecialchars($_POST['titre']);
    $prix = floatval($_POST['prix_ttc']);
    $cat = htmlspecialchars($_POST['categorie']);
    $desc = htmlspecialchars($_POST['description']);
    $allerg = htmlspecialchars($_POST['allergenes']);
    $image_nom = $_POST['ancienne_image'] ?? ""; // On garde l'image actuelle par défaut

    // Si José télécharge une NOUVELLE image
    if (isset($_FILES['image_plat']) && $_FILES['image_plat']['error'] === 0) {
        $nom_fichier = time() . "_" . basename($_FILES['image_plat']['name']);
        if (move_uploaded_file($_FILES['image_plat']['tmp_name'], "uploads/" . $nom_fichier)) {
            $image_nom = $nom_fichier; // On remplace le nom de l'image
        }
    }

    if ($id) {
        // MISE À JOUR (UPDATE)
        $sql = "UPDATE menus SET titre=?, prix_ttc=?, categorie=?, description=?, image_url=?, allergenes=? WHERE id=?";
        $pdo->prepare($sql)->execute([$titre, $prix, $cat, $desc, $image_nom, $allerg, $id]);
    } else {
        // NOUVEL AJOUT (INSERT)
        $sql = "INSERT INTO menus (titre, prix_ttc, categorie, description, image_url, allergenes) VALUES (?,?,?,?,?,?)";
        $pdo->prepare($sql)->execute([$titre, $prix, $cat, $desc, $image_nom, $allerg]);
    }
    header("Location: $current_page"); exit();
}

// 3. SUPPRESSION
if (isset($_GET['supprimer_id'])) {
    $id_del = intval($_GET['supprimer_id']);
    $pdo->prepare("DELETE FROM menus WHERE id = ?")->execute([$id_del]);
    header("Location: $current_page"); exit();
}

$plats = $pdo->query("SELECT * FROM menus ORDER BY categorie, titre")->fetchAll(); // Tri par titre
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion de la Carte</title>
    <link rel="stylesheet" href="CSS-Accueil.css">
    <style>
        .admin-box { background: rgba(0,0,0,0.8); padding: 25px; border-radius: 10px; color: white; max-width: 900px; margin: 20px auto; border: 1px solid white; }
        input, select, textarea { width: 100%; padding: 10px; margin: 8px 0; border-radius: 5px; border: none; }
        .btn-save { background: #27ae60; color: white; padding: 12px; cursor: pointer; width: 100%; font-weight: bold; border: none; border-radius: 5px; margin-top: 10px; }
        .btn-cancel { background: #e74c3c; color: white; display: block; text-align: center; padding: 10px; text-decoration: none; border-radius: 5px; margin-top: 5px; }
        table { width: 100%; background: white; margin-top: 30px; border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #ddd; color: black; text-align: left; }
        th { background: #222; color: white; }
        .action-btns a { text-decoration: none; padding: 5px 10px; border-radius: 3px; font-weight: bold; font-size: 0.9em; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="admin-box">
        <h2>🍳 <?= $plat_a_modifier ? "Modifier : " . htmlspecialchars($plat_a_modifier['titre']) : "Ajouter un nouveau plat" ?></h2>
        
        <form method="POST" enctype="multipart/form-data">
            <?php if($plat_a_modifier): ?>
                <input type="hidden" name="id" value="<?= $plat_a_modifier['id'] ?>">
                <input type="hidden" name="ancienne_image" value="<?= $plat_a_modifier['image_url'] ?>">
            <?php endif; ?>

            <input type="text" name="titre" placeholder="Nom du plat" value="<?= $plat_a_modifier['titre'] ?? '' ?>" required>
            
            <input type="number" step="0.01" name="prix_ttc" placeholder="Prix (€)" value="<?= $plat_a_modifier['prix_ttc'] ?? '' ?>" required>
            
            <select name="categorie">
                <option value="entree" <?= (isset($plat_a_modifier) && $plat_a_modifier['categorie'] == 'entree') ? 'selected' : '' ?>>Entrée</option>
                <option value="plat" <?= (isset($plat_a_modifier) && $plat_a_modifier['categorie'] == 'plat') ? 'selected' : '' ?>>Plat Principal</option>
                <option value="dessert" <?= (isset($plat_a_modifier) && $plat_a_modifier['categorie'] == 'dessert') ? 'selected' : '' ?>>Dessert</option>
                <option value="boisson" <?= (isset($plat_a_modifier) && $plat_a_modifier['categorie'] == 'boisson') ? 'selected' : '' ?>>Boisson</option>
            </select>

            <textarea name="description" placeholder="Description..."><?= $plat_a_modifier['description'] ?? '' ?></textarea>
            
            <input type="text" name="allergenes" placeholder="Allergènes..." value="<?= $plat_a_modifier['allergenes'] ?? '' ?>">
            
            <label style="font-size: 0.8em;">Image du plat (laisser vide pour ne pas changer) :</label>
            <input type="file" name="image_plat" accept="image/*">
            
            <button type="submit" name="enregistrer_plat" class="btn-save">
                <?= $plat_a_modifier ? "💾 Sauvegarder les modifications" : "➕ Ajouter à la carte" ?>
            </button>

            <?php if($plat_a_modifier): ?>
                <a href="<?= $current_page ?>" class="btn-cancel">Annuler la modification</a>
            <?php endif; ?>
        </form>

        <table>
            <tr>
                <th>Aperçu</th>
                <th>Nom</th>
                <th>Prix</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($plats as $p): ?>
            <tr>
                <td>
                    <?php if(!empty($p['image_url'])): ?>
                        <img src="uploads/<?= $p['image_url'] ?>" width="50">
                    <?php else: ?>
                        <small>Pas d'image</small>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($p['titre']) ?></td>
                <td><?= number_format($p['prix_ttc'], 2, ',', ' ') ?> €</td>
                <td class="action-btns">
                    <a href="?modifier_id=<?= $p['id'] ?>" style="background: #f39c12; color: white;">✏️ Modifier</a>
                    <a href="?supprimer_id=<?= $p['id'] ?>" style="background: #e74c3c; color: white;" onclick="return confirm('Êtes-vous sûr ?');">🗑️ Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>

    <?php include 'footer.php'; ?>
</html>