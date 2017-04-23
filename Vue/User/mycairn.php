<?php ?>

<div id="menu_mycairn" class="menu" style="display : none;">
    <div id="wrapper_menu_mycairn" class="grid-g">
        <span class="close_menu" onclick="cairn.close_menu();"></span>
        <div id="recently_searched" class="grid-u-1-3">
            <div class="mycairn_title_wrapper">
                <span class="title-like">Recherches récentes</span>
                <a href="./mes_recherches.php">Mes recherches</a>
            </div>
            <ul>
                <?php
                $recherches = array();
                if(isset($authInfos['U']) && isset($authInfos['U']['HISTO_JSON']->recherches)){
                    $recherches = $authInfos['U']['HISTO_JSON']->recherches;
                }else if(isset($authInfos['G']) && isset($authInfos['G']['HISTO_JSON']->recherches)){
                    $recherches = $authInfos['G']['HISTO_JSON']->recherches;
                }
                for($ind = 0 ; $ind < count($recherches) && $ind < 3 ; $ind++){
                    echo '<li><a href="resultats_recherche.php?searchTerm='.$recherches[$ind][0].'">'.htmlentities($recherches[$ind][0]).'</a></li>';
                }
                ?>
            </ul>
        </div>

        <div id="recently_viewed" class="grid-u-1-2">
            <div class="mycairn_title_wrapper">
                <span class="title-like">Récemment consultés</span>
                <a href="./mon_historique.php">Mon historique</a>
            </div>
            <ul>
                <?php
                $articles = array();
                if(isset($authInfos['U']) && isset($authInfos['U']['HISTO_JSON_ARTICLES'])){
                    $articles = $authInfos['U']['HISTO_JSON_ARTICLES'];
                }else if(isset($authInfos['G']) && isset($authInfos['G']['HISTO_JSON_ARTICLES'])){
                    $articles = $authInfos['G']['HISTO_JSON_ARTICLES'];
                }
                for($ind = 0 ; $ind < count($articles) && $ind < 3 ; $ind++){
                    echo '<li><a href="article.php?ID_ARTICLE='.$articles[$ind][0].'">'.$articles[$ind][1].'</a></li>';
                }
                ?>
            </ul>
        </div>

        <div id="mycairn_pannel" class="grid-u-1-6">
            <div class="mycairn_title_wrapper">
                <span class="title-like"><i>Créer un compte :</i></span>
            </div>
            <ul>
                <?php if(isset($authInfos["U"])){
                   echo '<li><a href="./mon_compte.php">Mon compte</a></li>';
                }?>
                <?php if($modeAchat == 1){?>
                    <li><a href="./mon_panier.php">Mon panier</a></li>
                <?php }else if($modeAchat == 2){ ?>
                    <li><a href="./mon_panier.php">Mon panier</a></li>
                    <li><a href="./mes_demandes.php">Mes demandes</a></li>
                <?php } ?>
                <?php if(isset($authInfos["U"])){
                   echo '<li><a href="./mes_achats.php">Mes achats</a></li>';
                }?>
                <li><a href="./biblio.php">Ma bibliographie</a></li>
                <li><a href="./mes_alertes.php">Mes alertes</a></li>
                <li><a href="./credit.php">Mon crédit d'articles</a></li>
            </ul>
            <?php if(!isset($authInfos["U"])){
                echo '<a href="./creer_compte.php" id="button_SignIn">M’inscrire</a>';
            }?>
        </div>
    </div>
</div>

