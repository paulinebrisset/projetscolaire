<?php

use App\Controllers\CategoriesController;

$instanceCatController = new CategoriesController;
$itemsDuneCat = $instanceCatController->afficherItemsDuneCategorie($categorie["id_categorie"]);

?>


<div class="container-fluid pageArticles">
    <div class="row page">
    <h2 class="titre"><?= $categorie["nom_categorie"] ?></h2>
    </div>
    <div class="row blanc">
    <p class="titre"><?= $categorie["description_categorie"] ?></p>
    </div>
    <div class="row">
        <?php foreach ($itemsDuneCat as $article) { ?>
            <div class="card col-12 col-sm-2 col-md article">
                <h3 class="titre"><?php echo ($article['titre']) ?></h3>
                <h4 class="prix"><?php echo ($article['prix'] . '€') ?></h4>
                <img class="img-fluid" src="<?php echo '/views/includes/assets/pictures/' . $article['image'] ?>" alt="cet article est à vendre">
                <p><?php echo ($article['description']) ?></p>
                <a class="enSavoirPlus" href="/items/lire/<?php echo $article["id_item"] ?>">En savoir plus</a>
            </div>
        <?php } ?>
    </div>
</div>