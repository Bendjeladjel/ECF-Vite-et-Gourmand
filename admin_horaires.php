<?php
session_start();
require_once 'db.php';

// Sécurité : Admin ET Employé autorisés
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'employe')) {
    header("Location: login.php"); exit();
}

// Mise à jour des horaires
if (isset($_POST['save_horaires'])) {
    foreach ($_POST['h'] as $id => $data) {
        $ouvert = !empty($data['ouverture']) ? $data['ouverture'] : null;
        $ferme = !empty($data['fermeture']) ? $data['fermeture'] : null;
        $est_ferme = isset($data['est_ferme']) ? 1 : 0;

        $stmt = $pdo->prepare("UPDATE horaires SET ouverture=?, fermeture=?, est_ferme=? WHERE id=?");
        $stmt->execute([$ouvert, $ferme, $est_ferme, $id]);
    }
    $msg = "Horaires mis à jour avec succès !";
}

$horaires = $pdo->query("SELECT * FROM horaires ORDER BY id ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les Horaires - Vite & Gourmand</title>
    <link rel="stylesheet" href="CSS-Accueil.css">
    <style>
        .horaires-card { background: black; padding: 25px; margin: 30px auto; max-width: 800px; border-radius: 10px; color: white; border: 2px solid #27ae60; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 12px; border-bottom: 1px solid #000000; }
        input[type="time"] { padding: 5px; border: 1px solid #000000; border-radius: 4px; }
        .btn-save { background: #27ae60; color: black; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; margin-top: 15px; width: 100%; }
        .ferme-label { color: #e74c3c; font-weight: bold; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="horaires-card">
        <h1>🕒 Gérer les horaires d'ouverture</h1>
        <?php if(isset($msg)) echo "<p style='color:green'>$msg</p>"; ?>
        
        <form method="POST">
            <table>
                <tr>
                    <th>Jour</th>
                    <th>Ouverture</th>
                    <th>Fermeture</th>
                    <th>Fermé ?</th>
                </tr>
                <?php foreach ($horaires as $h): ?>
                <tr>
                    <td><strong><?= $h['jour'] ?></strong></td>
                    <td><input type="time" name="h[<?= $h['id'] ?>][ouverture]" value="<?= $h['ouverture'] ?>"></td>
                    <td><input type="time" name="h[<?= $h['id'] ?>][fermeture]" value="<?= $h['fermeture'] ?>"></td>
                    <td style="text-align:center;">
                    <?php $est_ferme = isset($_POST['h'][$h['id']]['est_ferme']) ? 1 : 0; ?>
                    <input type="checkbox" name="h[<?= $h['id'] ?>][est_ferme]" <?= $est_ferme ? 'checked' : '' ?>>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <button type="submit" name="save_horaires" class="btn-save">Enregistrer les horaires</button>
        </form>
    </div>
</body>
    <?php include 'footer.php'; ?>
</html>