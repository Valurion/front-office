<!--
Ce template sert à l'affichage d'une liste à 2 colonnes.
Il s'attend à recevoir:
    - $arrayForList = l'array qui contient les données, comprenant le champ TYPEPUB
    - $arrayFieldsToDisplay = un array qui contient les champs à afficher. Par défaut, seul le titre et l'image s'affichent
-->

<?php if (isset($arrayForList)) { ?>
    <?php
    foreach ($arrayForList as $row) {
        if ($row['REVUE_TYPEPUB'] == '1') {
            if($currentPage == 'numero'){
                $url = 'revue-' . $row['REVUE_URL_REWRITING'] . '-' . $row["NUMERO_ANNEE"] . '-' . $row["NUMERO_NUMERO"];
                $urlRev = 'revue-' . $row['REVUE_URL_REWRITING'];
                $titre = $row['NUMERO_TITRE'];
                $soustitre = $row['NUMERO_SOUS_TITRE'];
            }else{
                if(trim($row["ARTICLE_PAGE_DEBUT"]) != '')
                    $url = 'revue-' . $row['REVUE_URL_REWRITING'] . '-' . $row["NUMERO_ANNEE"] . '-' . $row["NUMERO_NUMERO"] . '-page-' . $row["ARTICLE_PAGE_DEBUT"];
                else
                    $url = 'article.php?ID_ARTICLE='.(isset($row['ARTICLE_ID_ARTICLE'])?$row['ARTICLE_ID_ARTICLE']:$row['ID_ARTICLE']);
                $urlRev = 'revue-' . $row['REVUE_URL_REWRITING'];
                $titre = $row['ARTICLE_TITRE'];
                $soustitre = $row['ARTICLE_SOUSTITRE'];
            }
        } else if ($row['REVUE_TYPEPUB'] == '3' || $row['REVUE_TYPEPUB'] == '6') {
            if ($currentPage == 'contrib') {
                if(trim($row["ARTICLE_PAGE_DEBUT"]) != '')
                    $url = $row['NUMERO_URL_REWRITING'] . "--" . $row["NUMERO_ISBN"] . "-page-" . $row["ARTICLE_PAGE_DEBUT"];
                else
                    $url = 'article.php?ID_ARTICLE='.(isset($row['ARTICLE_ID_ARTICLE'])?$row['ARTICLE_ID_ARTICLE']:$row['ID_ARTICLE']);
                $urlRev = $row['NUMERO_URL_REWRITING'] . "--" . $row["NUMERO_ISBN"];
                $titre = $row['ARTICLE_TITRE'];
                $soustitre = $row['ARTICLE_SOUSTITRE'];
            } else {
                $url = $row['NUMERO_URL_REWRITING'] . "--" . $row["NUMERO_ISBN"];
                $urlRev = $row['NUMERO_URL_REWRITING'] . "--" . $row["NUMERO_ISBN"];
                $titre = $row['NUMERO_TITRE'];
                $soustitre = $row['NUMERO_SOUS_TITRE'];
            }
        } else if ($row['REVUE_TYPEPUB'] == '2') {
            if(trim($row["ARTICLE_PAGE_DEBUT"]) != '')
                $url = 'magazine-' . $row['REVUE_URL_REWRITING'] . '-' . $row["NUMERO_ANNEE"] . '-' . $row["NUMERO_NUMERO"] . '-page-' . $row["ARTICLE_PAGE_DEBUT"];
            else
                $url = 'article.php?ID_ARTICLE='.(isset($row['ARTICLE_ID_ARTICLE'])?$row['ARTICLE_ID_ARTICLE']:$row['ID_ARTICLE']);
            $urlRev = 'magazine-' . $row['REVUE_URL_REWRITING'];
            $titre = $row['ARTICLE_TITRE'];
            $soustitre = $row['ARTICLE_SOUSTITRE'];
        }
        ?>
        <div <?= in_array("ID", $arrayFieldsToDisplay)?('id="'.$row['NUMERO_ID_NUMPUBLIE'].'-'.(isset($row['ARTICLE_ID_ARTICLE'])?$row['ARTICLE_ID_ARTICLE']:'').'"'):'' ?> class="greybox_hover article">
            <a  href="./<?= $urlRev ?>.htm">
                <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $row['REVUE_ID_REVUE'] ?>/<?= $row['NUMERO_ID_NUMPUBLIE'] ?>_L61.jpg" alt="couverture de [NUMERO_TITRE_ABREGE]" class="small_cover">
            </a>
            <div class="meta">
                <div class="title"><span class="bullet">
                        <a  href="./<?= $url.(strpos($url,'.php')===false?'.htm':'') ?>"><b><?= $titre ?><span class="subtitle"><?= $soustitre ?></span></b></a>
                    </span></div>

                <?php if (in_array("BIBLIO_AUTEURS", $arrayFieldsToDisplay)) {
                    if($row['BIBLIO_AUTEURS'] != ''){
                        $theAuthors = explode(',',$row['BIBLIO_AUTEURS']);
                        $str = "";
                        foreach ($theAuthors as $theAuthor){
                            $theauthorParam = explode(':', $theAuthor);
                            $theAutheurPrenom = $theauthorParam[0];
                            $theAutheurNom = $theauthorParam[1];
                            $theAutheurId = $theauthorParam[2];
                            $str .= ($str != '' ?', ':'');
                            $str .= '<span class="author"><a class="yellow" href="publications-de-'.$theAutheurNom.'-'.$theAutheurPrenom.'--'.$theAutheurId.'.htm">'.$theAutheurPrenom.' '.$theAutheurNom.'</a></span>';
                        }
                        echo '<div class="authors">'.$str.'</div>';
                    }
                }?>
                <?php if (in_array("COLL_TITLE", $arrayFieldsToDisplay)) { ?>
                    <div class="revue_title">Coll. <span class="title_little_blue"><a class="title_little_blue" href="./collection.php?ID_REVUE=<?= $row["REVUE_ID_REVUE"] ?>"><?= $row['REVUE_TITRE'] ?></a></span>
                        <b>(<?= $row['EDITEUR_NOM_EDITEUR'] ?>, <?= $row['NUMERO_ANNEE'] ?>)</b>
                    </div>
                <?php } ?>
                <?php if (in_array("NUMERO_TITLE", $arrayFieldsToDisplay)) { ?>
                    <div class="revue_title">Dans <span class="title_little_blue"><a class="title_little_blue" href="./<?= $urlRev ?>.htm"><?= $row['NUMERO_TITRE'] ?></a></span>
                        <b>(<?= $row['EDITEUR_NOM_EDITEUR'] ?>, <?= $row['NUMERO_ANNEE'] ?>)</b>
                    </div>
                <?php } ?>
                <?php if (in_array("REVUE_TITLE", $arrayFieldsToDisplay)) { ?>
                    <div class="revue_title">Dans <span class="title_little_blue"><a class="title_little_blue" href="./<?= $urlRev ?>.htm"><?= $row['REVUE_TITRE'] ?></a></span>
                        <?= $row['NUMERO_ANNEE'] ?>/<?= $row['NUMERO_NUMERO'] ?> (<?= $row['NUMERO_VOLUME'] ?>)
                    </div>
                <?php } ?>
                <?php if (in_array("PRIX", $arrayFieldsToDisplay)) {?>
                <div class="prix">
                    <strong><span id="price-<?= $row['NUMERO_ID_NUMPUBLIE'].'-'.(isset($row['ARTICLE_ID_ARTICLE'])?$row['ARTICLE_ID_ARTICLE']:'')?>"><?= $row['ARTICLE_PRIX'] ?></span> €</strong>
                </div>
                <?php } ?>
                <?php if (in_array("STATE_OUV", $arrayFieldsToDisplay)) { ?>
                    <div class="state">
                        <a class="button" href="<?= $url.(strpos($url,'.php')===false?'.htm':'') ?>">Présentation/Sommaire</a>
                        <?php if(in_array("REMOVE_BIBLIO", $arrayFieldsToDisplay)){
                            require_once(__DIR__.'/../CommonBlocs/actionsBiblio.php');
                            checkBiblio($row['REVUE_ID_REVUE'],$row['NUMERO_ID_NUMPUBLIE'], (isset($row['ARTICLE_ID_ARTICLE'])?$row['ARTICLE_ID_ARTICLE']:null),null,'remove');
                        } ?>
                    </div>
                <?php } ?>
                <?php if (in_array("STATE", $arrayFieldsToDisplay)) { ?>
                    <div class="state">
                        <?php foreach ($row["LISTE_CONFIG_ARTICLE"] as $listeConfigArt) { ?>
                            <a
                                class="<?= (!isset($listeConfigArt['CLASS'])?'button':$listeConfigArt['CLASS'])?>"
                                href="<?= $listeConfigArt["HREF"] ?>"
                                <?php if (strpos($listeConfigArt['HREF'], 'load_pdf') !== false || (strpos($listeConfigArt['HREF'], 'revues.org') !== false)  || (strpos($listeConfigArt['HREF'], 'mon_panier') !== false))  : ?>
                                    <?php
                                        if (strpos($listeConfigArt['HREF'], 'load_pdf') !== false) {
                                            echo 'data-webtrends="goToPdfArticle"';
                                        } elseif (strpos($listeConfigArt['HREF'], 'revues.org') !== false) {
                                            echo 'data-webtrends="goToRevues.org"';
                                        }  elseif (strpos($listeConfigArt['HREF'], 'mon_panier') !== false) {
                                            echo 'data-webtrends="goToMonPanier" ';
                                            echo 'data-prix_article="' . number_format($row['ARTICLE_PRIX'], 2, '.', '') . '" ';
                                        }
                                    ?>
                                    data-id_article="<?= $row['ARTICLE_ID_ARTICLE'] ?>"
                                    data-titre=<?=
                                        Service::get('ParseDatas')->cleanAttributeString($row['ARTICLE_TITRE'])
                                    ?>
                                    data-authors=<?=
                                        Service::get('ParseDatas')->cleanAttributeString(
                                            Service::get('ParseDatas')->stringifyRawAuthors(
                                                $row['BIBLIO_AUTEURS'],
                                                0,
                                                ';',
                                                null,
                                                null,
                                                true,
                                                ',',
                                                ':'
                                            )
                                        )
                                    ?>
                                <?php endif; ?>
                            ><?= $listeConfigArt["LIB"] ?></a>
                            <?php
                        }
                        ?>
                        <?php if(in_array("REMOVE_BIBLIO", $arrayFieldsToDisplay)){
                            require_once(__DIR__.'/../CommonBlocs/actionsBiblio.php');
                            checkBiblio($row['REVUE_ID_REVUE'],$row['NUMERO_ID_NUMPUBLIE'], (isset($row['ARTICLE_ID_ARTICLE'])?$row['ARTICLE_ID_ARTICLE']:null),null,'remove');
                        } else {
                            require_once(__DIR__.'/../CommonBlocs/actionsBiblio.php');
                            checkBiblio($row['REVUE_ID_REVUE'],$row['NUMERO_ID_NUMPUBLIE'], (isset($row['ARTICLE_ID_ARTICLE'])?$row['ARTICLE_ID_ARTICLE']:null),$authInfos);
                        } ?>
                    </div>
                <?php } ?>
                <?php if (in_array("REMOVE_BASKET", $arrayFieldsToDisplay)) { ?>
                    <div class="state">
                        <input type="image" class="icon del-panier" src="static/images/del.png" alt="Supprimer de votre sélection" 
                               onclick="ajax.removeFromBasket('ART','<?= $row['NUMERO_ID_NUMPUBLIE']?>','<?= $row['ARTICLE_ID_ARTICLE']?>')"
                               data-webtrends="removeFromCart" 
                               data-id_article="<?= $row['ARTICLE_ID_ARTICLE']?>" 
                               data-prix_article="<?= $row['ARTICLE_PRIX'] ?>" 
                               data-titre=<?= Service::get('ParseDatas')->cleanAttributeString($row['ARTICLE_TITRE'])?> 
                               data-authors=<?=
                                        Service::get('ParseDatas')->cleanAttributeString(
                                            Service::get('ParseDatas')->stringifyRawAuthors(
                                                $row['BIBLIO_AUTEURS'],
                                                0,
                                                ';',
                                                null,
                                                null,
                                                true,
                                                ',',
                                                ':'
                                            )
                                        )
                                    ?>
                               >
                    </div>
                <?php } ?>
                <?php if (in_array("REMOVE_BASKET_INST", $arrayFieldsToDisplay)) { ?>
                    <div class="state">
                        <input type="image" class="icon del-panier" src="static/images/del.png" alt="Supprimer de votre sélection" 
                               onclick="ajax.removeFromBasketInst('ART','<?= $row['NUMERO_ID_NUMPUBLIE']?>','<?= $row['ARTICLE_ID_ARTICLE']?>')" 
                               data-webtrends="removeFromCart" 
                               data-id_article="<?= $row['ARTICLE_ID_ARTICLE']?>" 
                               data-prix_article="<?= $row['ARTICLE_PRIX'] ?>" 
                               data-titre=<?= Service::get('ParseDatas')->cleanAttributeString($row['ARTICLE_TITRE'])?> 
                               data-authors=<?=
                                        Service::get('ParseDatas')->cleanAttributeString(
                                            Service::get('ParseDatas')->stringifyRawAuthors(
                                                $row['BIBLIO_AUTEURS'],
                                                0,
                                                ';',
                                                null,
                                                null,
                                                true,
                                                ',',
                                                ':'
                                            )
                                        )
                                    ?>
                                >
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php
    }
    ?>
    <?php
}
?>
