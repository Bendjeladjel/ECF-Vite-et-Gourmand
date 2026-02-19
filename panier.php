<?php
session_start();

// SÉCURITÉ : Si l'utilisateur n'est pas connecté, on le redirige vers le login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?erreur=connexion_requise");
    exit();
}

require_once 'db.php';
// --- 1. LOGIQUE PHP (Traitement) ---
$action = $_GET['action'] ?? null;
$id_get = isset($_GET['id']) ? intval($_GET['id']) : null;
$erreur = $_GET['erreur'] ?? null;

// Vidage du panier
if ($action === 'vider') {
    $_SESSION['panier'] = array();
    header("Location: panier.php");
    exit();
}

// Mise à jour (POST via bouton ou auto-submit JS)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantite'])) {
    $trop_eleve = false;
    foreach ($_POST['quantite'] as $id_p => $qte) {
        $qte = intval($qte);
        if ($qte > 50) {
            $qte = 50;
            $trop_eleve = true;
        }
        if ($qte <= 0) unset($_SESSION['panier'][$id_p]);
        else $_SESSION['panier'][$id_p] = $qte;
    }
    $url = $trop_eleve ? "panier.php?erreur=limite" : "panier.php";
    header("Location: $url");
    exit();
}

// Ajout/Retrait 1 à 1 via les liens [+] et [-]
if ($id_get && $action) {
    if ($action === 'ajout') {
        $qte = $_SESSION['panier'][$id_get] ?? 0;
        if ($qte < 50) $_SESSION['panier'][$id_get] = $qte + 1;
        else { header("Location: panier.php?erreur=limite"); exit(); }
    } elseif ($action === 'retirer') {
        if (isset($_SESSION['panier'][$id_get])) {
            $_SESSION['panier'][$id_get]--;
            if ($_SESSION['panier'][$id_get] <= 0) unset($_SESSION['panier'][$id_get]);
        }
    }
    header("Location: panier.php");
    exit();
}

// Préparation des données pour l'affichage
$total_general = 0;
$produits_panier = [];

if (!empty($_SESSION['panier'])) {
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=vite_gourmand;charset=utf8', 'root', '');
        foreach ($_SESSION['panier'] as $id => $qte) {
            $req = $bdd->prepare('SELECT * FROM menus WHERE id = ?');
            $req->execute([$id]);
            $res = $req->fetch();
            if ($res) {
                $res['qte_panier'] = $qte;
                $res['sous_total'] = $res['prix_ttc'] * $qte;
                $total_general += $res['sous_total'];
                $produits_panier[] = $res;
            }
        }
    } catch (Exception $e) {
        die('Erreur BDD : ' . $e->getMessage());
    }
}

// --- 2. AFFICHAGE HTML ---
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier - Vite & Gourmand</title>
    <link rel="stylesheet" href="CSS-Accueil.css">
    <link rel="stylesheet" href="CSS-Panier.css">
</head>
<body style="display: flex; flex-direction: column; min-height: 100vh;">

    <?php include 'header.php'; ?>

    <main style="flex: 1;"> <div class="container">
            <h1 style="text-align:center; color:white; margin-top:20px;">Votre Panier</h1>

            <?php if (empty($produits_panier)): ?>
                <div style="text-align:center; color:white; padding: 50px 0;">
                    <p>Votre panier est tristement vide... 🛒</p>
                    <a href="Menu.php" style="color: #27ae60; font-weight:bold; text-decoration:none;">Retourner au menu</a>
                </div>
            <?php else: ?>
                <div class="panier-card">
                    
                    <?php if ($erreur === 'limite'): ?>
                        <div class="message-erreur" style="color:red; text-align:center; margin-bottom:15px; font-weight:bold;">
                            ⚠️ Quantité limitée à 50 articles par produit.
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="panier.php" id="form-qte">
                        <table class="table-panier" style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 2px solid #eee;">
                                    <th style="text-align:left; padding:10px;">Produit</th>
                                    <th style="text-align:center; padding:10px;">Quantité</th>
                                    <th style="text-align:right; padding:10px;">Prix</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produits_panier as $p): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding:15px;"><strong><?= htmlspecialchars($p['titre']) ?></strong></td>
                                    <td style="text-align:center;">
                                        <a href="panier.php?action=retirer&id=<?= $p['id'] ?>" style="color:red; text-decoration:none; font-weight:bold;">[-]</a>
                                        
                                        <input type="number" name="quantite[<?= $p['id'] ?>]" 
                                               value="<?= $p['qte_panier'] ?>" 
                                               min="1" max="50" class="input-qte">
                                        
                                        <a href="panier.php?action=ajout&id=<?= $p['id'] ?>" style="color:green; text-decoration:none; font-weight:bold;">[+]</a>
                                    </td>
                                    <td style="text-align:right; font-weight:bold;">
                                        <?= number_format($p['sous_total'], 2) ?> €
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </form>

                    <div class="total-panier" style="margin-top:20px; text-align:right;">
                        <h2 style="color:#000
#000000 ;">Total : <?= number_format($total_general, 2) ?> €</h2>
                    </div>

                    <div style="display:flex; justify-content: space-between; margin-top:30px;">
                        <a href="panier.php?action=vider" 
                           onclick="return confirm('Vider tout le panier ?');" 
                           style="color:#e74c3c; text-decoration:none;">🗑️ Vider le panier</a>
                        
                        <a href="commande.php" style="background:#27ae60; color:white; padding:10px 20px; border-radius:5px; text-decoration:none; font-weight:bold;">
                            Passer la commande
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        // Mise à jour automatique lors du changement manuel de l'input
        document.querySelectorAll('.input-qte').forEach(input => {
            input.addEventListener('change', function() {
                document.getElementById('form-qte').submit();
            });
        });
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>