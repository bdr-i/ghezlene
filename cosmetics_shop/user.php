<?php
    session_start();
    // Connexion à la base de données
    if (file_exists('../config/dbConnect.php')) {
        require '../config/dbConnect.php';
    } else {
        echo "File not found";
        die();
    }; 

    $query = "SELECT * FROM products";
    $result = mysqli_query($link, $query);
    $produits = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $produits[] = $row; 
        }
    }

    // Fermer la connexion
    mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cosmetics Shop</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        #top {
            padding: 20px 0;
        }
        .navbar {
            transition: top 0.3s;
        }
        .sticky {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        footer {
            background-color: white;
            padding: 20px 0;
            text-align: center;
        }
        .s_cover {
            padding: 60px 0;
            background-color: white;
            animation: fadeIn 1.5s ease-in-out;
            text-align: center;
            color: #333;
        }
        .product-card {
            margin-bottom: 30px;
        }
        .mega-menu {
            min-width: 300px;
            padding: 20px;
        }
        .mega-menu h6 {
            margin-bottom: 20px;
        }
        .dropdown-item {
            padding: 5px 10px;
        }
    </style>
</head>
<body>
    <header id="top">
        <img class="img-responsive d-block mx-auto" src="Design_sans_titre__1_-removebg-preview.png" alt="" width="50px"/>
        <nav class="navbar navbar-expand-lg navbar-light bg-light" id="navbar">
            <div class="container">
                <a class="navbar-brand" href="#">Cosmetics Shop</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item"><a class="nav-link" href="user.php">Accueil</a></li>
                        <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="productsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Produits</a>
                            <div class="dropdown-menu mega-menu" aria-labelledby="productsDropdown">
                                <h6>Maquillage</h6>
                                <a class="dropdown-item" href="rouge-a-levres.html">Rouge à Lèvres</a>
                                <a class="dropdown-item" href="fonds-de-teint.html">Fonds de Teint</a>
                                <a class="dropdown-item" href="mascara.html">Mascara</a>
                                <div class="dropdown-divider"></div>
                                <h6>Parfums</h6>
                                <a class="dropdown-item" href="feminin.html">Parfums Féminins</a>
                                <a class="dropdown-item" href="masculin.html">Parfums Masculins</a>
                                <div class="dropdown-divider"></div>
                                <h6>Soins</h6>
                                <a class="dropdown-item" href="serum.html">Sérum</a>
                                <a class="dropdown-item" href="creme.html">Crème Hydratante</a>
                            </div>
                        </li>
                    </ul>
                    <form class="form-inline my-2 my-lg-0">
                        <input class="form-control mr-sm-2" type="search" placeholder="Rechercher" aria-label="Search">
                        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Rechercher</button>
                    </form>
                    <li class="nav-item"><a class="nav-link" href="panier.html"><i class="fas fa-shopping-cart"></i> Panier</a></li>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="s_cover text-center">
            <h1>Beauté sans frontières</h1>
            <p>Exprimez votre éclat unique</p>
            <a href="#produits" class="btn btn-primary btn-lg">Découvrir plus</a>
        </section>
        
        <section id="faq" class="s_faq_collapse pt-5 pb-5">
            <div class="container">
                <h3>Foire aux questions (FAQ)</h3>
                <div class="accordion" id="faqAccordion">
                    <div class="card">
                        <div class="card-header" id="headingOne">
                            <h5 class="mb-0">
                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Q1: Comment puis-je passer une commande ?
                                </button>
                            </h5>
                        </div>
                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#faqAccordion">
                            <div class="card-body">
                                Pour passer une commande, ajoutez les produits souhaités à votre panier, puis procédez à la validation de votre commande en suivant les instructions à l'écran.
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="headingTwo">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Q2: Quels modes de paiement acceptez-vous ?
                                </button>
                            </h5>
                        </div>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#faqAccordion">
                            <div class="card-body">
                                Nous acceptons plusieurs modes de paiement, y compris les cartes de crédit, PayPal et les virements bancaires.
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="headingThree">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Q3: Puis-je retourner un produit ?
                                </button>
                            </h5>
                        </div>
                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#faqAccordion">
                            <div class="card-body">
                                Oui, vous pouvez retourner un produit dans un délai de 30 jours suivant la réception, à condition qu'il soit dans son état d'origine.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="produits" class="s_dynamic_snippet_products pt-5 pb-5">
            <div class="container">
                <h4>Nos produits phares</h4>
                <div class="row">
                    <?php foreach ($produits as $produit): ?>
                    <div class="col-md-4 product-card">
                        <div class="card">
                        <img src="<?php echo '../images/' . $produit['image_url'] ; ?>" class="card-img-top" alt="<?php echo $produit['name']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $produit['name']; ?></h5>
                                <p class="card-text"><?php echo $produit['description']; ?></p>
                                <p class="card-text"><?php echo '€ ' . number_format($produit['price'], 2); ?></p>
                                <button class="btn btn-primary" onclick="ajouterAuPanier('<?php echo $produit['name']; ?>', <?php echo $produit['price']; ?>)">Ajouter au panier</button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <span>Copyright &copy; <span>Cosmoshop</span></span>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function ajouterAuPanier(nomProduit, prixProduit) {
            const panier = JSON.parse(localStorage.getItem('panier')) || [];
            const produit = {
                nom: nomProduit,
                prix: prixProduit,
                quantite: 1
            };

            const index = panier.findIndex(item => item.nom === nomProduit);
            if (index > -1) {
                panier[index].quantite += 1;
            } else {
                panier.push(produit);
            }

            localStorage.setItem('panier', JSON.stringify(panier));
            alert('Produit ajouté au panier !');
        }

        window.onscroll = function() {
            const navbar = document.getElementById("navbar");
            if (window.pageYOffset > navbar.offsetTop) {
                navbar.classList.add("sticky");
            } else {
                navbar.classList.remove("sticky");
            }
        };
    </script>
</body>
</html>
