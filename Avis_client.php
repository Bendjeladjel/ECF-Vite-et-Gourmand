<?php
require_once 'db.php';
$msg = ""; // Initialisation pour éviter l'erreur "Undefined variable"

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom_client']);
    $note = intval($_POST['note']);
    $commentaire = htmlspecialchars($_POST['commentaire']);

    // Utilisation de 'est_valide' (colonne existante) au lieu de 'est_avis'
    $sql = "INSERT INTO avis (nom_client, note, commentaire, est_valide) VALUES (?, ?, ?, 0)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$nom, $note, $commentaire])) {
        $msg = "✅ Merci ! Votre avis est en attente de validation.";
    } else {
        $msg = "❌ Erreur lors de l'envoi.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis Client</title>
    <link rel="stylesheet" href="CSS-Accueil.css">
    <?php include 'header.php'; ?>
</head>
<body>
<div class="avis-form">
    <?php if(!empty($msg)) echo "<p>$msg</p>"; ?>
    <form method="POST">
        <p><input type="text" name="nom_client" placeholder="Votre nom" required></p>
       <p><select name="note">
            <option value="5">⭐⭐⭐⭐⭐</option>
            <option value="4">⭐⭐⭐⭐</option>
            <option value="3">⭐⭐⭐</option>
            <option value="2">⭐⭐</option>
            <option value="1">⭐</option>
        </select></p> 
       <p><textarea name="commentaire" placeholder="Votre message..."></textarea>
        <button type="submit">Envoyer</button></p> 
    </form>
</div>
</body>
<?php include 'footer.php'; ?>
</html>
