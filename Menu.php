<?php 
// 1. INDISPENSABLE : Toujours démarrer la session au tout début pour vérifier la connexion
session_start(); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vite & Gourmand - Notre Menu</title>
    <link rel="stylesheet" href="CSS-Accueil.css">
</head>
<body>
<?php include 'header.php'; ?>

    <main>
        <h1>Notre Menu</h1>
        <p>Découvrez les créations de Julie et José.</p>
        
        <?php if(!isset($_SESSION['user_id'])): ?>
            <p>Pour passer commande, merci de vous connecter 
               <button onclick="window.location.href='login.php'">Se connecter</button>
            </p>
        <?php endif; ?>

        <section id="liste-menu">
            <h2>Les Formules</h2>
            <ul>
                <li><strong>Menu Gourmand (25€) :</strong> Entrée + Plat + Dessert</li>
                <li><strong>Menu Vite (18€) :</strong> Plat + Dessert</li>
                <li><strong>Menu Enfant (12€) :</strong> Jusqu'à 12 ans</li>
            </ul>
        </section>

<?php
require_once 'db.php'; 
$requete = $pdo->query("SELECT * FROM menus"); 
$tous_les_menus = $requete->fetchAll(); 
?>

<section class="notre-carte">
    <h1>Nos Plateaux-Repas</h1>
    <div class="carte-menu">
        <?php foreach ($tous_les_menus as $menu) { ?>
            <div class="plat-card"> 
                <?php if (!empty($menu['image_url'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($menu['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($menu['titre']); ?>"
                         style="width:100%; height:200px; object-fit:cover;">
                <?php else: ?>
                    <img src="img/default.jpg" alt="Image non disponible" style="width:100%; height:200px; object-fit:cover;">
                <?php endif; ?>

                <div class="contenu-carte">
                    <h2><?php echo htmlspecialchars($menu['titre']); ?></h2>
                    <p><?php echo htmlspecialchars($menu['description']); ?></p>
                    <span class="prix"><?php echo number_format($menu['prix_ttc'], 2); ?> €</span>
                    
                    <div class="zone-allergenes">
                        <button type="button" class="btn-alergenes">⚠️ Allergènes</button>
                        <p class="liste-allergenes"><?php echo htmlspecialchars($menu['allergenes']); ?></p>
                    </div>
                    
                    <div style="margin-top: 15px;">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <button onclick="window.location.href='panier.php?action=ajout&id=<?php echo $menu['id']; ?>'">
                                🛒 Ajouter au panier
                            </button>
                        <?php else: ?>
                            <p style="color: #e67e22; font-size: 0.9em; font-weight: bold;">
                                🔒 Connectez-vous pour commander
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div> 
        <?php } ?>
    </div>
</section>

<?php include 'footer.php'; ?>
</body>
</html>