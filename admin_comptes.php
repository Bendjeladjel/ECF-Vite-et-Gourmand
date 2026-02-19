<?php
session_start();
require_once 'db.php';

// 1. SÉCURITÉ : Vérifier si l'utilisateur est admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

// 2. TRAITEMENT DES ACTIONS (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);

    // ACTION : SUPPRIMER L'EMPLOYÉ
    if (isset($_POST['action']) && $_POST['action'] === 'supprimer') {
        $sql = "DELETE FROM utilisateurs WHERE email = ? AND role = 'employe'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $message = "<p style='color:green;'>✅ L'employé avec l'email <strong>$email</strong> a été supprimé.</p>";
        } else {
            $message = "<p style='color:orange;'>⚠️ Aucun employé trouvé avec l'email $email (ou c'est un compte admin).</p>";
        }
    } 
    // ACTION : CRÉER L'EMPLOYÉ
    else {
        $nom = htmlspecialchars($_POST['nom']);
        $prenom = htmlspecialchars($_POST['prenom']);
        $password_clair = $_POST['password'];
        $password_hash = password_hash($password_clair, PASSWORD_DEFAULT); // On hash toujours

        try {
            $sql = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, 'employe')";
            $pdo->prepare($sql)->execute([$nom, $prenom, $email, $password_hash]);
            $message = "<p style='color:green;'>✅ Employé <strong>$prenom $nom</strong> créé avec succès !</p>";
        } catch (PDOException $e) {
            // Gestion de l'erreur de doublon
            if ($e->getCode() == 23000) {
                $message = "<p style='color:red;'>❌ Erreur : L'adresse email <strong>$email</strong> est déjà utilisée.</p>";
            } else {
                $message = "<p style='color:red;'>❌ Erreur BDD : " . $e->getMessage() . "</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gestion Employés</title>
    <link rel="stylesheet" href="CSS-Accueil.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container" style="max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ffffff; background: #000000;">
        <h1>Gestion des Employés</h1>
        
        <?= $message ?>

        <form method="POST">
            <div style="margin-bottom: 10px;">
                <label>Prénom :</label><br>
                <input type="text" name="prenom" placeholder="Ex: Julie" style="width:100%;">
            </div>
            <div style="margin-bottom: 10px;">
                <label>Nom :</label><br>
                <input type="text" name="nom" placeholder="Ex: Gourmand" style="width:100%;">
            </div>
            <div style="margin-bottom: 10px;">
                <label>Email (Sert d'identifiant unique) :</label><br>
                <input type="email" name="email" required style="width:100%; border: 2px solid #3498db;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Mot de passe :</label><br>
                <input type="password" name="password" placeholder="Minimum 8 caractères" style="width:100%;">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" name="action" value="creer" 
                        style="flex: 1; background-color: #27ae60; color: white; padding: 10px; border: none; cursor: pointer;">
                    ➕ Créer l'employé
                </button>

                <button type="submit" name="action" value="supprimer" 
                        onclick="return confirm('Attention : Cette action est irréversible. Supprimer cet employé ?')"
                        style="flex: 1; background-color: #e74c3c; color: white; padding: 10px; border: none; cursor: pointer;">
                    🗑️ Supprimer (par email)
                </button>
            </div>
        </form>

        <p style="margin-top: 20px; text-align: center;">
            <a href="admin_dashboard.php" style="text-decoration: none; color: #34495e;">⬅ Retour au Dashboard</a>
        </p>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>