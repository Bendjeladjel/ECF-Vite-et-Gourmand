<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vite & Gourmand - Accueil</title>
    <link rel="stylesheet" href="CSS-Accueil.css">
</head>
<body>
<?php include 'header.php'; ?>

    <main>
        <h1>Accueil</h1>
        <p>Bienvenue sur le site Vite & Gourmand</p>

        <nav>
            <p>Sommaire de la page :</p>
            <ul>
                <li><a href="#presentation">Qui nous sommes ?</a></li>
                <li><a href="#avis">Les Avis</a></li>
                <li><a href="#gage">Notre Gage de Qualité</a></li>
            </ul>
        </nav>

        <section id="presentation">
            <h2>Qui nous sommes ?</h2>
            <p>« Vite & Gourmand » est une entreprise constituée de deux personnes, Julie et José. Elle existe depuis 25 ans à Bordeaux...</p>
        </section>

        <section id="gage">
            <h2>Notre Gage de Qualité</h2>
            <p>Nous nous engageons à fournir des repas savoureux et de qualité...</p>
            <p>Liste des collaborateurs locaux :</p>
            <ul>
                <li>Ferme du Bonheur - Producteur de légumes</li>
                <li>Fromagerie des Délices - Producteur de fromages</li>
                <li>Boulangerie Artisanale - Fournisseur de pains</li>
            </ul>
        </section>

        <section id="avis">
            <h2>Les Avis</h2>
            <p>Vous pouvez via cette section laisser un avis* ou consulter les avis vérifiés.</p>
        </section>
    </main>

<?php include 'footer.php'; ?>
</body>
</html>



