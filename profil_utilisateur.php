<?php
session_start();
require_once 'db.php';

// Sécurité : Si l'utilisateur n'est pas connecté, on le renvoie à la connexion
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. On récupère les infos de l'utilisateur
$reqUser = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$reqUser->execute([$user_id]);
$infos = $reqUser->fetch();

// Sécurisation des variables d'affichage
$prenom = htmlspecialchars($infos['prenom'] ?? 'Client');
$nom = htmlspecialchars($infos['nom'] ?? '');

// 2. On récupère ses dernières commandes via son email (clé étrangère dans ta table)
$reqOrders = $pdo->prepare("SELECT * FROM commandes WHERE email = ? ORDER BY date_commande DESC");
$reqOrders->execute([$infos['email']]);
$commandes = $reqOrders->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil - Vite & Gourmand</title>
    <link rel="stylesheet" href="CSS-Accueil.css">
</head>
<body style="color: white;">
    <?php include 'header.php'; ?>

<?php if (isset($_GET['message']) && $_GET['message'] === 'annule'): ?>
    <div style="background: #e74c3c; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
        ✅ Votre commande a bien été annulée.
    </div>
<?php endif; ?>

    <div class="container" style="padding: 20px; max-width: 900px; margin: auto;">
        <h1>Bienvenue, <?= $prenom ?> !</h1>
        
        <div class="infos-client" style="background: rgba(0,0,0,0.8); color: #ffffff; padding: 15px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #333;">
            <h3>Mes Informations</h3>
            <p><strong>Nom :</strong> <?= $nom ?></p>
            <p><strong>Email :</strong> <?= htmlspecialchars($infos['email']) ?></p>
            <p><strong>Numéro :</strong> <?= htmlspecialchars($infos['numero'] ?? 'Non renseigné') ?></p>
            <p><strong>Statut du compte :</strong> <?= ucfirst($infos['role'] ?? 'client') ?></p>
        </div>

        <h3>Mes Commandes</h3>
        <?php if (!empty($commandes)): ?>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px; background: white; color: black;">
            <thead style="background: #333; color: white;">
                <tr>
                    <th style="padding: 10px; text-align: left;">Date</th>
                    <th style="padding: 10px; text-align: left;">Adresse</th>
                    <th style="padding: 10px; text-align: left;">Total</th>
                    <th style="padding: 10px; text-align: left;">Statut</th>
                    <th style="padding: 10px; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($commandes as $cmd): 
                $id_c = $cmd['numero_commande']; // Correction : on utilise numero_commande
                $statut = trim(strtolower($cmd['statut'] ?? 'en attente'));
                $estAnnule = ($statut === 'annulé' || $statut === 'annule');
            ?>
            <tr style="<?= $estAnnule ? 'opacity: 0.5; background: #eee;' : '' ?>">
                <td style="padding: 10px;"><?= date('d/m/Y H:i', strtotime($cmd['date_commande'])); ?></td>
                <td style="padding: 10px;"><?= htmlspecialchars($cmd['adresse'] ?? 'N/A') ?></td>
                <td style="padding: 10px;"><?= number_format($cmd['total'] ?? 0, 2, ',', ' '); ?> €</td>
                <td style="padding: 10px;">
                    <span style="font-weight: bold;"><?= ucfirst(htmlspecialchars($cmd['statut'])); ?></span>
                </td>
                <td style="padding: 10px;">
                    <a href="detail_commande.php?id=<?= $id_c ?>" style="color: #3498db;">[ Détails ]</a>

                    <?php if ($statut === 'en attente'): ?>
                        <a href="modifier_commande.php?id=<?= $id_c ?>" style="color: #27ae60; margin-left:10px;">[ Modifier ]</a>
                        <a href="annuler_commande.php?id=<?= $id_c ?>" 
                           onclick="return confirm('Annuler la commande ?')" 
                           style="color: #e74c3c; margin-left:10px;">[ Annuler ]</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>Vous n'avez pas encore passé de commande.</p>
        <?php endif; ?>

        <hr style="margin: 40px 0; border: 0; border-top: 1px solid #444;">

        <div class="user-card" style="border-left: 5px solid #3498db; padding: 15px; background: rgba(0,0,0,0.8); margin-bottom: 20px;">
            <h3>Modifier mes informations</h3>
            <form action="modifier_compte.php" method="POST">
                <label>Prénom :</label>
                <input type="text" name="prenom" value="<?= $prenom ?>" required style="width:100%; padding:8px; margin-bottom:15px; border-radius:5px; border:none;">
                
                <label>Nom :</label>
                <input type="text" name="nom" value="<?= $nom ?>" required style="width:100%; padding:8px; margin-bottom:15px; border-radius:5px; border:none;">

                <label>Numéro :</label>
                <input type="text" name="numero" value="<?= htmlspecialchars($infos['numero'] ?? '') ?>" required style="width:100%; padding:8px; margin-bottom:15px; border-radius:5px; border:none;">
                
                <button type="submit" style="background-color: #3498db; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px; font-weight: bold;">
                    Enregistrer les modifications
                </button>
            </form>
        </div>

        <div class="user-card" style="border-left: 5px solid #e74c3c; background: rgba(0,0,0,0.8); padding: 15px;">
            <h3 style="color: #e74c3c;">Zone de danger</h3>
            <p>La suppression de votre compte est définitive.</p>
            <a href="supprimer_compte.php" 
               onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ?')" 
               style="background-color: #e74c3c; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
               Supprimer mon compte définitivement
            </a>
        </div>

        <p style="margin-top: 40px; text-align: center;">
            <a href="deconnexion.php" style="color: #e74c3c; font-weight: bold; text-decoration: none; border: 1px solid #e74c3c; padding: 10px 25px; border-radius: 5px;">Se déconnecter</a>
        </p>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>