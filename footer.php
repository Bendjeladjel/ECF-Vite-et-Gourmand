<link rel="stylesheet" href="CSS-Accueil.css">
<div class="footer">
    <h3>Nos Horaires</h3>
    <p>
        <?php
        require_once 'db.php';
        // On récupère les horaires
        $requete = $pdo->query("SELECT * FROM horaires ORDER BY id ASC");
        $tous_les_horaires = $requete->fetchAll();

        foreach ($tous_les_horaires as $h) {
            echo "<strong>" . htmlspecialchars($h['jour']) . " : </strong>";
            
            if ($h['est_ferme']) { 
                echo "Fermé";
            } else {
                // Formatage 10h00 - 19h00
                $debut = date("H\hi", strtotime($h['ouverture']));
                $fin = date("H\hi", strtotime($h['fermeture']));
                echo "$debut - $fin";
            }
            echo "<br>";
        }
        ?>
    </p>
    <p>12 rue de la Gourmandise, 33000 Bordeaux</p>
    <p><a href="mentions_legales.php">Mentions Légales</a></p>
    <p>© 2026 Vite & Gourmand - Tous droits réservés</p>
</div>