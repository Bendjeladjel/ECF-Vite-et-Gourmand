<?php
require_once 'db.php'; // Connexion à ta base vite_gourmand

$message_confirmation = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
$nom = htmlspecialchars($_POST['nom']);
$email = htmlspecialchars($_POST['email']);
$commentaire = htmlspecialchars($_POST['message']);

    // 2. Insertion en base de données (Table contact)
    // Utilisation d'une requête préparée pour la sécurité
    $sql = "INSERT INTO contact (nom, email, commentaire, date_envoi) VALUES (?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$nom, $email, $commentaire])) {
        $message_confirmation = "✅ Merci ! Votre message a bien été envoyé à Julie et José.";
    } else {
        $message_confirmation = "❌ Erreur lors de l'envoi. Veuillez réessayer.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Vite & Gourmand</title>
    <link rel="stylesheet" href="CSS-Accueil.css">
</head>
<body>
<?php include 'header.php'; ?>

    <main>
        <h1>Contactez-nous</h1>
        <p>Une question pour Julie ou José ? Un devis pour un événement ?</p>
<?php if (!empty($message_confirmation)): ?>

    <div style="padding: 15px; margin-bottom: 20px; border-radius: 5px; background-color: #d4edda; color: #155724; text-align: center; font-weight: bold;">
        <?= $message_confirmation ?>
    </div>
<?php endif; ?>

        <form action="#" method="post">
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required>
            </div>

            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="message">Message :</label>
                <textarea id="message" name="message" rows="5" required></textarea>
            </div>

            <button type="submit">Envoyer le message</button>
        </form>
    </main>

<?php include 'footer.php'; ?>
</body>
</html>




