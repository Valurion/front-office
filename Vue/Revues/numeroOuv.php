<?php
$this->titre = $numero['NUMERO_TITRE'];
include (__DIR__ . '/../CommonBlocs/tabs.php');
?>

<?php if ($numero['NUMERO_STATUT'] == 0 || $revue['STATUT'] == 0): ?>
    <div class="danger backoffice article-desactivate">
        <?php if ($revue['STATUT'] == 0): ?>
            Cette collection est actuellement désactivée.<br />
        <?php endif; ?>
        <?php if ($numero['NUMERO_STATUT'] == 0): ?>
            Cet ouvrage est actuellement désactivé.<br />
        <?php endif; ?>
        Sur http://cairn.info, cet ouvrage <strong>n’apparaîtra pas</strong>. Il apparaît <strong>uniquement</strong> sur <?= Configuration::get('urlSite') ?>.
    </div>
<?php endif; ?>

<div id="breadcrump">
    <a href="/">Accueil</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="./<?php echo $typePub == 'ouvrage' ? 'ouvrages.php' : 'encyclopedies-de-poche.php' ?>.htm"><?php echo $typePub == 'ouvrage' ? 'Ouvrages' : 'Encyclopédies de poche'; ?></a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="./<?php echo $numero["NUMERO_URL_REWRITING"] ?>--<?php echo $numero["NUMERO_ISBN"]; ?>.htm"><?php echo $numero["NUMERO_TYPE_NUMPUBLIE"] == '1' ? 'Ouvrage collectif' : 'Ouvrage'; ?></a>
</div>

<div id="body-content">
    <div id="page_numero">
        <div class="grid-g grid-3-head" id="page_header">
            <div class="grid-u-1-4">
                <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $revue['REVUE_ID_REVUE'] ?>/<?= $revue['NUMERO_ID_NUMPUBLIE'] ?>_L204.jpg" alt="couverture de [REVUE_ID_NUMPUBLIE]" class="big_coverbis">

                <?php if($accessElecOk && $numero['NUMERO_EPUB'] == 1){?>
                <br/>
                <table border="0" cellpadding="0" cellspacing="0" style="text-align: left; cursor: pointer; border: none; width: 214px;" id="epub"><tr>
                    <td style="padding : 5px;" width="45"><img height="45" src="./static/images/epub.png"/></td>
                    <td style="padding : 5px;" ><span style="font: bold 14px Alegreya;"><a href="./load_epub.php?ID_NUMPUBLIE=<?= $revue['NUMERO_ID_NUMPUBLIE'] ?>">Télécharger la version EPUB du numero</a></span></td>
                </tr></table>
                <?php } ?>
            </div>

            <div class="grid-u-1-2 meta">
                <h1 class="title_big_blue revue_title"><?= $numero['NUMERO_TITRE'] ?></h1>
                <h3 class="text_medium title"><b><?= $revue['NUMERO_SOUS_TITRE'] ?></b></h3>


                <!-- Affichage auteurs du numéros -->
                <div class="authors">
                    <?php
                        $authors = explode(',', $numero['NUMERO_AUTEUR']);
                        $lengthAuthors = count($authors) - 1;
                    ?>
                    <?php foreach ($authors as $index => $author): ?>
                        <?php
                        $splitAuthor = explode(':', $author);
                        $authorNom = trim($splitAuthor[1]);
                        $authorPrenom= trim($splitAuthor[0]);
                        $authorId = trim($splitAuthor[2]);
                        $authorAttribut = trim($splitAuthor[3]);
                        ?>
                        <?php if ($authorAttribut): ?>
                            <span class="yellow2">
                                <?= $authorAttribut ?>
                            </span>
                        <?php elseif (($index == 0) and ($numero['NUMERO_TYPE_NUMPUBLIE'] == 1)): ?>
                            <span class="yellow2">
                                Sous la direction de
                            </span>
                        <?php endif; ?>
                        <a class="author" href="publications-de-<?= $authorNom ?>-<?= $authorPrenom ?>--<?= $authorId ?>.htm">
                            <?= $authorPrenom ?>
                            <?=$authorNom ?><?php if ($index < $lengthAuthors): ?><span class="comma">,</span><?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <!-- /Affichage auteurs du numéros -->


                <ul class="others">


                    <?php if (Configuration::get('allow_backoffice', false)): ?>
                        <!-- Lien vers le back-office de la revue -->
                        <li>
                            <span class="yellow id-revue">Id Collection : </span>
                            <?= $revue['REVUE_ID_REVUE'] ?>
                            (<a href="<?= Configuration::get('backoffice', '#') ?>?controleur=Revues&amp;action=index&amp;ID_REVUE=<?= $revue['REVUE_ID_REVUE'] ?>" class="bo-content" target="_blank">back-office</a>)
                        </li>
                        <!-- Lien vers le back-office du numéro -->
                        <li>
                            <span class="yellow id-revue">Id Numpublie : </span>
                            <?= $numero['NUMERO_ID_NUMPUBLIE'] ?>
                            (<a href="<?= Configuration::get('backoffice', '#') ?>?controleur=Revues&amp;action=numero&amp;ID_NUMPUBLIE=<?= $numero['NUMERO_ID_NUMPUBLIE'] ?>" class="bo-content" target="_blank">back-office</a>,
                            <!-- Lien vers le menu conversion -->
                            <a href="<?= Configuration::get('menu_conversion', '#').'?ID_NUMPUBLIE='.$revue['NUMERO_ID_NUMPUBLIE'].'&ID_REVUE='.$revue['REVUE_ID_REVUE'] ?>" class="bo-content" target="_blank">menu conversion</a>)
                        </li>
                    <?php endif; ?>


                    <?php if ($revue['NUMERO_ANNEE'] != '') { ?>
                        <li>
                            <span class="yellow nb_pages">Année : </span><?= $numero['NUMERO_ANNEE'] ?>
                        </li>
                    <?php } if ($revue['NUMERO_NB_PAGE'] != '') { ?>
                        <li>
                            <span class="yellow nb_pages">Pages : </span><?= $numero['NUMERO_NB_PAGE'] ?>
                        </li>
                    <?php } if ($revue['REVUE_TITRE'] != '') { ?>
                        <li>
                            <span class="yellow ">Collection : </span><a href="./collection.php?ID_REVUE=<?php echo $numero['NUMERO_ID_REVUE']; ?>"><?= $revue['REVUE_TITRE'] ?></a>
                        </li>
                    <?php } if ($revue['EDITEUR_NOM_EDITEUR'] != '') { ?>
                        <li>
                            <span class="yellow ">&#201;diteur : </span><a href="./editeur.php?ID_EDITEUR=<?php echo $revue['REVUE_ID_EDITEUR']; ?>"><?= $revue['EDITEUR_NOM_EDITEUR'] ?></a>
                        </li>
                    <?php } if ($revue['NUMERO_EAN'] != '') { ?>
                        <li>
                            <span class="yellow issn">ISBN : </span><?= $revue['NUMERO_EAN'] ?>
                        </li>
                    <?php } if ($numero['NUMERO_ISBN_NUMERIQUE'] != '') { ?>
                        <li>
                            <span class="yellow issn">ISBN version en ligne : </span><?= $numero['NUMERO_ISBN_NUMERIQUE'] ?>
                        </li>
                    <?php } if ($revue['REVUE_WEB'] != '') { ?>
                        <li>
                            <a target="_blank" href="<?= $revue['REVUE_WEB'] ?>" id="site-internet">Site internet</a>
                        </li>
                    <?php } ?>
                    <?php if ($numero['NUMERO_EDITION_PRECEDENTE'] != '') { ?>
                        <li>
                            <a href="<?php echo $numero['PREV_NUM_URL_REWRITING'] . '--' . $numero['PREV_NUM_ISBN'] . '.htm'; ?>">Edition précédente</a>
                        </li>
                    <?php }
                    if ($numero['NUMERO_DERNIERE_EDITION'] != '') { ?>
                        <li>
                            <a href="<?php echo $numero['LAST_NUM_URL_REWRITING'] . '--' . $numero['LAST_NUM_ISBN'] . '.htm'; ?>">Dernière édition</a>
                        </li>
                    <?php } ?>
                </ul>

                <form name="rechrevue" action="./resultats_recherche.php" method="get" class="search_inside">
                    <button type="submit">
                        <img src="./static/images/icon/magnifying-glass-black.png">
                    </button>
                    <input type="text" placeholder="Chercher dans cet ouvrage" name="searchTerm" />
                    <input type="hidden" name="ID_NUMPUBLIE" value="<?php echo $numero['NUMERO_ID_NUMPUBLIE']; ?>" />
                </form>

                <?php
                if(isset($typesAchat['DISPLAY_BLOC_ACHAT'])) {
                    include __DIR__."/../CommonBlocs/addToBasket.php";
                }
                ?>

            </div>
            <div class="grid-u-1-4">
                <?php if($oneTokenBAD != false)
                {
                    include (__DIR__ . '/../CommonBlocs/blocMiseEnLigneBAD.php');
                }
                else
                {
                    include (__DIR__ . '/../CommonBlocs/alertesEmail.php');
                }
                ?>
                <hr class="grey" style="margin-top:0;"/>
                <div class="article_menu">
                    <h1>Raccourcis</h1>
                    <ul id="shortcuts_links">
                        <?php if ($revue['NUMERO_MEMO'] != ""): ?><li><a href="<?php echo $numero["NUMERO_URL_REWRITING"]; ?>--<?php echo $numero["NUMERO_ISBN"]; ?>.htm#memo">Présentation<span class="unicon unicon-round-arrow-black-right right">&#10140;</span></a></li><?php endif; ?>
                        <li><a href="<?php echo $numero["NUMERO_URL_REWRITING"]; ?>--<?php echo $numero["NUMERO_ISBN"]; ?>.htm#summary">Sommaire <span class="unicon unicon-round-arrow-black-right right">&#10140;</span></a></li>
                        <li><a href="<?php echo $numero["NUMERO_URL_REWRITING"]; ?>--<?php echo $numero["NUMERO_ISBN"]; ?>.htm#about">Fiche technique <span class="unicon unicon-round-arrow-black-right right">&#10140;</span></a></li>
                    </ul>


                    <!-- Cité par -->
                    <?php if (count($countReferencedBy) > 0) { ?>
                        <h1>Cité par...</h1>
                        <ul>
                            <?php
                            $countRefByRevue = 0;
                            $countRefByOuvrage = 0;
                            foreach ($countReferencedBy as $refBy) {
                                if ($refBy['TYPEPUB'] == 1) {
                                    $countRefByRevue += $refBy['CNT'];
                                } else {
                                    $countRefByOuvrage += $refBy['CNT'];
                                }
                            }
                            ?>
                            <?php if ($countRefByRevue > 0): ?>
                                <li>
                                    <a href="./cite-par.php?ID_NUMPUBLIE=<?= $numero['NUMERO_ID_NUMPUBLIE'] ?>#cite-par-revue">
                                        <?= $countRefByRevue ?> articles de revues
                                        <span class="icon-arrow-black-right icon right"></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($countRefByOuvrage > 0): ?>
                                <li>
                                    <a href="./cite-par.php?ID_NUMPUBLIE=<?= $numero['NUMERO_ID_NUMPUBLIE'] ?>#cite-par-ouvrage">
                                        <?= $countRefByOuvrage ?> ouvrages
                                        <span class="icon-arrow-black-right icon right"></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    <?php } ?>
                    <!-- /Cité par -->


                </div>
            </div>
        </div>

        <?php
        if(isset($typesAchat['DISPLAY_BLOC_ACHAT'])){
            include __DIR__."/../CommonBlocs/blocAddToBasket.php";
        }
        ?>

        <?php if ($revue['NUMERO_MEMO'] != ""): ?>
            <hr class="grey">
            <div class="memo-numpublie">
                <h1 id="memo" class="main-title">Présentation</h1>
                <p><?= $revue['NUMERO_MEMO'] ?></p>
            </div>
        <?php endif; ?>
        <hr class="grey" />

        <h1 class="main-title" id="summary">
            Sommaire
            <?php
            require_once(__DIR__.'/../CommonBlocs/actionsBiblio.php');
            checkBiblio($numero['NUMERO_ID_REVUE'], $numero['NUMERO_ID_NUMPUBLIE'], null,$authInfos);
            ?>
        </h1>

        <div class="list_articles">

            <?php
            $done = -1;
            foreach ($articles as $article):
                $done++;
                if (($article['ARTICLE_STATUT'] == 0) && (!Configuration::get('allow_backoffice', false))) continue;
                ?>
                <?php if ($done == 0 || $article['ARTICLE_SECT_SOM'] != $articles[$done - 1]['ARTICLE_SECT_SOM']) : ?>
                    <h2 class="sect_som"><?= $article['ARTICLE_SECT_SOM'] ?></h2>
                <?php endif; ?>
                <?php if ($done == 0 || $article['ARTICLE_SECT_SSOM'] != $articles[$done - 1]['ARTICLE_SECT_SSOM']) : ?>
                    <h3 class="sect_ssom"><?= $article['ARTICLE_SECT_SSOM'] ?></h3>
                <?php endif; ?>

                <div class="article greybox_hover" id="<?= $article['ARTICLE_ID_ARTICLE'] ?>">
                    <a name="<?php echo $article["ARTICLE_ID_ARTICLE"]; ?>"></a>
                    <?php
                        $displayAuthors = $numero['NUMERO_TYPE_NUMPUBLIE'] == '1';
                        if (!$displayAuthors && isset($article['ARTICLE_AUTEUR'])) {
                            $displayAuthors = $article['ARTICLE_AUTEUR'] !== $numero['NUMERO_AUTEUR'];
                        }
                    ?>
                    <?php if ($displayAuthors): ?>
                    <div class="authors">
                        <span class="author">
                            <?php if (!!$article['ARTICLE_AUTEUR']): ?>
                                <?php
                                    $_authors = array();  // Utilisé pour le taggage coins, pour des raisons de performances
                                    $authors = explode(',', $article['ARTICLE_AUTEUR']);
                                    $lengthAuthors = count($authors);
                                ?>

                                <?php foreach ($authors as $index => $author): ?>
                                    <?php
                                        $author = explode(':', $author);
                                        $author = array(
                                            'PRENOM' => $author[0],
                                            'NOM' => $author[1],
                                            'UID' => $author[2],
                                            'ATTRIBUT' => $author[3],
                                        );
                                        $_authors[] = $author;
                                    ?>
                                    <?php if ($author['ATTRIBUT']) echo '<i>'.$author['ATTRIBUT'].'</i>'; ?>
                                    <a class="yellow" href="./publications-de-<?= $author['NOM'] ?>-<?= $author['PRENOM'] ?>--<?= $author['UID'] ?>.htm">
                                        <?= $author['PRENOM'] ?> <?= $author['NOM'] ?><!--
                                     --></a><!--
                                    --><?php if (($index + 1) < $lengthAuthors) echo ','; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <?php endif ?>
                    </div>
                    <?php endif ?>
                    <span class="surtitle"><?= $article['ARTICLE_SURTITRE'] ?></span>
                    <div class="wrapper_title">
                        <?php if(trim($article['ARTICLE_PAGE_DEBUT']) != '' && trim($article['ARTICLE_PAGE_FIN']) != ''){ ?>
                        <span class="nb_pages">Page <?= $article['ARTICLE_PAGE_DEBUT'] ?> &#224; <?= $article['ARTICLE_PAGE_FIN'] ?></span>
                        <?php } ?>
                        <?php if (($article['ARTICLE_STATUT'] == 0) && (Configuration::get('allow_backoffice', false))): ?>
                            <h2 class="title red"><i>(désactivé)</i> <?= $article['ARTICLE_TITRE'] ?></h2>
                        <?php else: ?>
                            <h2 class="title"><?= $article['ARTICLE_TITRE'] ?></h2>
                        <?php endif; ?>
                    </div>
                    <span class="subtitle"><?= $article['ARTICLE_SOUSTITRE'] ?></span>

                    <span class="mention_title"><?= $article['ARTICLE_MENTION_SOMMAIRE'] ?></span>

                    <div class="state">
                        <?php foreach ($article['LISTE_CONFIG_ARTICLE'] as $configArticle): ?>
                            <?php if (strpos($configArticle['CLASS'], 'wrapper_buttons_add-to-cart') === false): ?>
                            <a
                                href="<?= $configArticle['HREF'] ?>"
                                class="<?= ((!isset($configArticle['CLASS']) || $configArticle['CLASS']=='')?'button':$configArticle['CLASS']) ?>"
                                <?php if (strpos($configArticle['HREF'], 'load_pdf') !== false || (strpos($configArticle['HREF'], 'revues.org') !== false)): ?>
                                    <?php echo strpos($configArticle['HREF'], 'load_pdf') !== false ? 'data-webtrends="goToPdfArticle"' : 'data-webtrends="goToRevues.org"' ?>
                                    data-id_article="<?= $article['ARTICLE_ID_ARTICLE'] ?>"
                                    data-titre=<?=
                                        Service::get('ParseDatas')->cleanAttributeString($article['ARTICLE_TITRE'])
                                    ?>
                                    data-authors=<?=
                                        Service::get('ParseDatas')->cleanAttributeString(
                                            Service::get('ParseDatas')->stringifyRawAuthors(
                                                $article['ARTICLE_AUTEUR'],
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
                            ><?= $configArticle['LIB'] ?></a>
                            <?php elseif (!isset($numero['NUMERO_MOV_WALL_PPV']) || (date('Y-m-d') >= $numero['NUMERO_MOV_WALL_PPV'])) : ?>
                            <a
                                href="<?= $configArticle['HREF'] ?>"
                                class="<?= ((!isset($configArticle['CLASS']) || $configArticle['CLASS']=='')?'button':$configArticle['CLASS']) ?>"
                                <?php if (strpos($configArticle['HREF'], 'load_pdf') !== false || (strpos($configArticle['HREF'], 'revues.org') !== false)  || (strpos($configArticle['HREF'], 'mon_panier') !== false))  : ?>
                                    <?php
                                        if (strpos($configArticle['HREF'], 'load_pdf') !== false) {
                                            echo 'data-webtrends="goToPdfArticle"';
                                        } elseif (strpos($configArticle['HREF'], 'revues.org') !== false) {
                                            echo 'data-webtrends="goToRevues.org"';
                                        }  elseif (strpos($configArticle['HREF'], 'mon_panier') !== false) {
                                            echo 'data-webtrends="goToMonPanier" ';
                                            echo 'data-prix_article="' . number_format($article['ARTICLE_PRIX'], 2, '.', '') . '" ';
                                        }
                                    ?>
                                    data-id_article="<?= $article['ARTICLE_ID_ARTICLE'] ?>"
                                    data-titre=<?=
                                        Service::get('ParseDatas')->cleanAttributeString($article['ARTICLE_TITRE'])
                                    ?>
                                    data-authors=<?=
                                        Service::get('ParseDatas')->cleanAttributeString(
                                            Service::get('ParseDatas')->stringifyRawAuthors(
                                                $article['ARTICLE_AUTEUR'],
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
                            ><?= $configArticle['LIB'] ?></a>
                            <?php endif ?>
                        <?php endforeach; ?>
                        <?php
                        checkBiblio($revue['REVUE_ID_REVUE'], $numero['NUMERO_ID_NUMPUBLIE'], $article['ARTICLE_ID_ARTICLE'],$authInfos);
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

        <div class="about-numpublie">
            <h2 id="about">Fiche technique</h2>
            <p>
                <?php
                foreach(explode(',', $numero['NUMERO_AUTEUR']) as $index => $author) {
                    $author = explode(':', $author);
                    if ($author[3]) {
                        echo $author[3];
                    }
                    echo $author[0] . ' ' . $author[1] . ', ';
                }
                ?>
                <em><?= trim($numero['NUMERO_TITRE']) ?></em>,
                <?php if ($numero['NUMERO_VOLUME']): ?>
                    <?= $numero['NUMERO_VOLUME'] ?>,
                <?php endif; ?>
                <?php if ($revue['EDITEUR_VILLE']): ?>
                    <?= $revue['EDITEUR_VILLE']; ?>,
                <?php endif; ?>
                <?php if ($numero['NUMERO_NUMEROA']): ?>
                    -<?= $numero['NUMERO_NUMEROA'] ?>,
                <?php endif; ?>
                <?= $revue['EDITEUR_NOM_EDITEUR']; ?>,
                «&#160;<?= $revue['REVUE_TITRE'] ?>&#160;»,
                <?= $numero['NUMERO_ANNEE'] ?>,
                <?= $numero['NUMERO_NB_PAGE'] ?>&#160;pages.
                <?php if ($numero['NUMERO_EAN']): ?>
                    <br />
                    ISBN&#160;: <?= $numero['NUMERO_EAN'] ?>
                <?php endif; ?>
                <?php if ($numero['NUMERO_DOI']): ?>
                    <br />
                    DOI&#160;: <?= $numero['NUMERO_DOI'] ?>
                <?php endif; ?>
                <br />
                Lien&#160;: &lt;<u>http://www.cairn.info/<?= $numero['NUMERO_URL_REWRITING'] ?>--<?= $numero['NUMERO_ISBN'] ?>.htm</u>&gt;
            </p>
        </div>

    </div>

</div>

<?php include (__DIR__ . "/../CommonBlocs/invisible.php"); ?>
