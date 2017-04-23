<?php
    $this->titre = $currentArticle["ARTICLE_TITRE"];

    require_once('Blocs/headerArticle.php');

    $this->javascripts[] = '<script type="text/javascript" src="./static/js/article.js"></script>';

    include (__DIR__ . '/../CommonBlocs/tabs.php');

    // On prépare les libellés, urls, ... etc
    $typeRev_suffixe = "";
    $typeNum_suffixe = "";
    $revue_url = "";
    $numero_url = "";
    if ($typePub == "revue" || $typePub == "magazine") {
        $article_libelle = "article";
        $article_det = "cet";
        $revue_url = $typePub . '-' . $revue["REVUE_URL_REWRITING"];
        $numero_url = $typePub . '-' . $revue["REVUE_URL_REWRITING"] . '-' . $revue["NUMERO_ANNEE"] . '-' . $revue["NUMERO_NUMERO"];
    } else {
        $article_libelle = "chapitre";
        $article_det = "ce";
        if ($typePub == "encyclopédie") {
            $typeRev_suffixe = " de poche";
        }
        $revue_url = $numero["NUMERO_URL_REWRITING"] . '--' . $numero["NUMERO_ISBN"];
        $numero_url = $numero["NUMERO_URL_REWRITING"] . '--' . $numero["NUMERO_ISBN"];
        
        if ($numero["NUMERO_TYPE_NUMPUBLIE"] == 1) {
            $typeNum_suffixe = " collectif";
        }
    }
    $typePub_url = null;
    switch ($typePub) {
        case 'revue':
            $typePub_url = 'Accueil_Revues.php';
            break;
        case 'encyclopédie':
            $typePub_url = 'encyclopedies-de-poche.php';
            break;
        default:
            $typePub_url = $typePub.'s.php';
            break;
    }
    
    // Définition de l'URL sur PREPROD / BON A TIRER
    $url_token = "";
	// Concervation du TOKEN dans l'URL
	if((Configuration::get('allow_preprod', false)) && isset($_GET['token']))
	{
	   	//$numero_url .= "?token=" . $_GET['token'];
	   	$url_token = "?token=" . $_GET['token'];
	}
?>

<?php if ($numero['NUMERO_STATUT'] == 0 || $revue['STATUT'] == 0): ?>
    <div class="danger backoffice article-desactivate">
        <?php if ($revue['STATUT'] == 0): ?>
            Cette revue est actuellement désactivé.<br />
        <?php endif; ?>
        <?php if ($numero['NUMERO_STATUT'] == 0): ?>
            Ce numéro est actuellement désactivé.<br />
        <?php endif; ?>
        <?php if ($currentArticle['ARTICLE_STATUT'] == 0): ?>
            Cet article est actuellement désactivé.<br />
        <?php endif; ?>
        Sur http://cairn.info, ce numéro <strong>n’apparaîtra pas</strong>. Il apparaît <strong>uniquement</strong> sur <?= Configuration::get('urlSite') ?>.
    </div>
<?php endif; ?>


<div id="breadcrump">
    <a href="/">Accueil</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="./<?php echo $typePub_url ?>"><?php echo ucfirst($typePub); ?>s<?php echo ($typeRev_suffixe != '' ? $typeRev_suffixe : ''); ?></a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="./<?php echo $revue_url; ?>.htm"><?php echo ucfirst($typePub=="encyclopédie"?"ouvrage":$typePub); ?><?php echo ($typeNum_suffixe != '' ? $typeNum_suffixe : ''); ?></a>
    <span class="icon-breadcrump-arrow icon"></span>
    <?php if ($typePub == "revue" || $typePub == "magazine") { ?>
        <a href="./<?php echo $numero_url; ?>.htm<?php echo $url_token; ?>">Num&#233;ro</a>
        <span class="icon-breadcrump-arrow icon"></span>
    <?php } ?>
    <a href="#"><?php echo ucfirst($article_libelle); ?></a>
</div>

<div id="body-content">
    <div id="page_article" class="lang-<?= $currentArticle['ARTICLE_LANGUE'] ?>">
        <input type="hidden" id="hits" value="<?= $hits ?>"/>
        <div id="page_header" class="grid-g grid-3-head">
            <div class="grid-u-1-4">
                <a href="./<?php echo $numero_url; ?>.htm<?php echo $url_token; ?>">
                    <img
                        class="big_coverbis"
                        id="numero-cover"
                        alt="<?php echo $revue["REVUE_TITRE"]; ?> <?php echo $revue["NUMERO_ANNEE"]; ?>/<?php echo $revue["NUMERO_NUMERO"]; ?>"
                        src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?php echo $revue["REVUE_ID_REVUE"]; ?>/<?= $revue['NUMERO_ID_NUMPUBLIE'] ?>_L204.jpg"
                        >
                </a>
            </div>

            <div class="grid-u-1-2 meta">
                <!-- DEBUT DES METADONNEES DE L'ARTICLE -->
                <?php
                if(isset($htmlDatas['METAS'])){
                //On commence par faire le pré-traitement sur les auteurs...
                $metasHtml = $htmlDatas["METAS"];
                //1 - Remplacement de [ARTICLE_SAME_AUTHOR_URL]
                if($currentArticle['ARTICLE_AUTEUR'] != ''){
                    $theAuthors = explode(",", $currentArticle['ARTICLE_AUTEUR']);
                    foreach ($theAuthors as $theAuthor) {
                        $theauthorParam = explode(':', $theAuthor);
                        $theAutheurPrenom = $theauthorParam[0];
                        $theAutheurNom = $theauthorParam[1];
                        $theAutheurId = $theauthorParam[2];
                        $theAutheurAttribut = $theauthorParam[3];
                        if (preg_match($patternIgnoreLinkOnAuthorContribution, $theAutheurAttribut)) {
                            // On zappe les liens des auteurs dont les contributions doivent être ignorés
                            $replaceStr = '#';
                            $replaceStr .= '" data-with-author-link="no"';
                        } else {
                            $replaceStr = 'publications-de-' . $theAutheurNom . '-' . $theAutheurPrenom . '--' . $theAutheurId . '.htm';
                            $replaceStr .= '" data-with-author-link="yes"';
                        }

                        $metasHtml = preg_replace('[\[ARTICLE_SAME_AUTHOR_URL\]]', $replaceStr, $metasHtml, 1);
                    }
                }
                echo $metasHtml;
                }?>
                <!-- FIN DES METADONNEES DE L'ARTICLE -->

                <!-- Récupération de la section et de la sous-section -->
                <?php
                    // Définition des variables
                    $article_sections       = "";
                    $article_section        = $currentArticle["ARTICLE_SECT_SOM"];
                    $article_sous_section   = $currentArticle["ARTICLE_SECT_SSOM"];

                    // Affichage
                    //if($article_section != "") {$article_sections .= "<span class=\"yellow\"><b>Section&nbsp;: </b></span> $article_section";}
                    //if($article_sous_section != "") {$article_sections .= "- <span class=\"yellow\"><b>Sous-Section&nbsp;: </b></span> $article_sous_section";}
                    //if($article_sections != "") {echo "<div>$article_sections</div>";}
                ?>
            </div>
            <div class="grid-u-1-4">
                <div class="article_menu">
                    <h1>Raccourcis</h1>
                    <ul id="article_shortcuts">
                        <li>
                            <a href="#anchor_abstract" id="link-abstract" style="display: none;">
                                Résumé
                                <span class="icon-arrow-black-right icon right"></span>
                            </a>
                        </li>
                        <li>
                            <a href="#anchor_plan" id="link-plan-of-article" style="display: none;">
                                Plan de l'article
                                <span class="icon-arrow-black-right icon right"></span>
                            </a>
                        </li>
                        <li>
                            <a href="#anchor_citation" id="link-cite-this-article">
                                Pour citer <?php echo $article_det . " " . $article_libelle; ?>
                                <span class="icon-arrow-black-right icon right"></span>
                            </a>
                        </li>
                        <?php if (Configuration::get('allow_backoffice', false)): ?>
                            <li>
                                <a class="bo-content" href="<?= Configuration::get('tires_a_part', '#') ?>?ID_ARTICLE=<?= $currentArticle['ARTICLE_ID_ARTICLE'] ?>">
                                    Tirés à part
                                </a>
                            </li>
                        <?php endif ?>
                    </ul>

                    <?php
                        // Affichage du lien vers la traduction SI la langue originale de l'article n'est pas EN
                        // et si une traduction existe bien
                        if ($numero["ID_ARTICLE_INT"] != null && $currentArticle["ARTICLE_LANGUE"] != "en") { ?>
                            <?php /*<a id="article-english-version" href="http://cairn-int.info/article.php?ID_ARTICLE=<?=$currentArticle['ID_ARTICLE_INT']?>" class="cairn-int_link"><span class="icon icon-round-arrow-right"></span>English version</a> */ ?>
                            <!-- Lien URL REWRITING -->
                            <a href="http://cairn-int.info/article-<?=$numero["ID_ARTICLE_INT"]?>--<?php echo $numero["URL_REWRITING_INT"]; ?>.htm" class="cairn-int_link"><span class="icon icon-round-arrow-right"></span>English version</a>

                    <?php } ?>
                    <?php if (count($countReferencedBy) > 0) { ?>
                        <h1>Cité par...</h1>
                        <ul id="article-cited-by">
                            <?php
                            foreach ($countReferencedBy as $refBy) {
                                $libRefBy = $refBy["TYPEPUB"] == 1 ? "Articles de revues" : "Ouvrages";
                                echo '<li><a class="cited-by" href="./cite-par.php?ID_ARTICLE=' . $currentArticle["ARTICLE_ID_ARTICLE"] . '&amp;T='.($refBy["TYPEPUB"] == 1 ?'R':'O').'">' . $libRefBy . ' [' . $refBy["CNT"] . '] <span class="icon-arrow-black-right icon right"></span></a></li>';
                            }
                            ?>
                        </ul>
                    <?php } ?>
                    <?php if($currentArticle['ARTICLE_SUJET_PROCHE'] == 1){ ?>
                        <h1>Voir aussi</h1>
                        <ul id="see_also_links">
                            <li>
                                <a id="see-also" href="./sur-un-sujet-proche.php?ID_ARTICLE=<?php echo $currentArticle["ARTICLE_ID_ARTICLE"]; ?>">
                                    Sur un sujet proche
                                    <span class="icon-arrow-black-right icon right"></span>
                                </a>
                            </li>
                        </ul>
                    <?php }
                    if($currentArticle['ARTICLE_EXTRAWEB_TITRE'] != '' && $currentArticle['ARTICLE_EXTRAWEB_NOM_FICHIER'] != ''){?>
                    <h1>Documents associés</h1>
                    <ul id="see_also_links">
                    <?php
                    $output_array = "";
                    preg_match("/^http/", $currentArticle["ARTICLE_EXTRAWEB_NOM_FICHIER"], $output_array);
                    if(empty($output_array)) { ?>
                    <li><a href="loadextraweb.php?ID_ARTICLE=<?php echo $currentArticle["ARTICLE_ID_ARTICLE"]; ?>"><?= $currentArticle['ARTICLE_EXTRAWEB_TITRE'] ?><span class="icon-arrow-black-right icon right"></span></a></li>
                    <?php }
                    else {?>
                    <li><a href="<?php echo $currentArticle['ARTICLE_EXTRAWEB_NOM_FICHIER']; ?>"><?= $currentArticle['ARTICLE_EXTRAWEB_TITRE'] ?><span class="icon-arrow-black-right icon right"></span></a></li>
                    <?php }?>

			<!--EndNoIndex-->
                        </ul>
                    <?php }?>
                </div>
            </div>
        </div>

        <div class="grid-g grid-4-article">
            <?php
            include(__DIR__ . '/Blocs/numeroMeta.php');
            ?>
            <div class="grid-u-3-4">
                <div id="article_content">
                    <hr class="grey">
                    <?php
                    include(__DIR__ . '/Blocs/navPage.php');
                    ?>
                    
                    <?php
	                    // Suppression de la boite à outil pour les bons à tirer
	                    if((Configuration::get('allow_preprod', true)) && !isset($_GET['token'])) {
		            ?>
                    <ul id="usermenu-tools" style="margin-top: 0px;">
                        <li>
                            <a
                                class="icon icon-usermenu-tools-zen"
                                data-tooltip="Mode zen"
                                href="./zen.php?ID_ARTICLE=<?php echo $currentArticle["ARTICLE_ID_ARTICLE"]; ?>"
                                data-webtrends="goToZenArticle"
                            ></a>
                        </li>
                        <?php
                        require_once(__DIR__.'/../CommonBlocs/actionsBiblio.php');
                        checkBiblio($revue['REVUE_ID_REVUE'], $numero['NUMERO_ID_NUMPUBLIE'], $currentArticle['ARTICLE_ID_ARTICLE'],$authInfos,'usermenu');
                        ?>
                        <?php
                        $configs_articles = explode(',',$currentArticle['ARTICLE_CONFIG_ARTICLE']);
                        if($configs_articles[3] == 1) {?>
                        <li>
                            <a
                                class="icon icon-usermenu-tools-pdf"
                                data-tooltip="Télécharger PDF"
                                target="_blank"
                                href="load_pdf.php?ID_ARTICLE=<?php echo $currentArticle["ARTICLE_ID_ARTICLE"]; ?>"
                                data-webtrends="goToPdfArticle"
                                data-id_article="<?= $currentArticle['ARTICLE_ID_ARTICLE'] ?>"
                                data-titre=<?=
                                    Service::get('ParseDatas')->cleanAttributeString($currentArticle['ARTICLE_TITRE'])
                                ?>
                                data-authors=<?=
                                    Service::get('ParseDatas')->cleanAttributeString(
                                        Service::get('ParseDatas')->stringifyRawAuthors(
                                            $currentArticle['ARTICLE_AUTEUR'],
                                            0,
                                            null,
                                            null,
                                            null,
                                            true,
                                            ',',
                                            ':'
                                        )
                                    )
                                ?>
                            ></a>
                        </li>
                        <?php }
                        if($configs_articles[2] == 1) { ?>
                        <li><a class="icon icon-usermenu-tools-summary" data-tooltip="Feuilleter" target="_blank" href="feuilleter.php?ID_ARTICLE=<?php echo $currentArticle["ARTICLE_ID_ARTICLE"]; ?>"></a></li>
                        <?php } ?>
                        <li><a class="icon icon-usermenu-tools-print" data-tooltip="Version imprimable" target="_blank" href="./article_p.php?ID_ARTICLE=<?php echo $currentArticle["ARTICLE_ID_ARTICLE"]; ?>"></a></li>

                        <?php if (Configuration::get('allow_backoffice', false)): ?>
                            <?php
                                /*
                                    Lien vers le menu conversion sur le back-office, permettant diverses actions sur les numéros/articles
                                */
                                $_link_edit_xml = Configuration::get('edit_xml', '#');
                                if ($_link_edit_xml !== '#') {
                                    $_link_edit_xml .= '#id_article='.$currentArticle['ARTICLE_ID_ARTICLE'];
                                }
                            ?>
                            <li>
                                <a
                                    class="icon icon-usermenu-tools-highlight"
                                    data-tooltip="Édition xml"
                                    target="_blank"
                                    href="<?= $_link_edit_xml ?>">
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <?php
	                    }
	                ?>
	                
                    <div class="col600" id="textehtml">
                        <!-- DEBUT DU CONTENU DE L'ARTICLE -->
                        <?php echo isset($htmlDatas["CONTENUS"])?$htmlDatas["CONTENUS"]:'';
                        if($typePub == "magazine" && ($revue['SOAP'] == null || $revue['SOAP'] == '')){
                            echo '<br><p class="copymag">&copy; '.$revue['EDITEUR_NOM_EDITEUR'].', '.$numero['NUMERO_ANNEE'].'</p>';
                        }

                        ?>
                        <!-- FIN DU CONTENU DE L'ARTICLE -->


                        <?php
                            $linktoInt = !empty($currentArticle["ARTICLE_ID_ARTICLE_S"]) ? $currentArticle["ARTICLE_ID_ARTICLE_S"] : $currentArticle["ARTICLE_URL_REWRITING_EN"];
                        ?>

                        <?php if ($currentArticle["ARTICLE_LANGUE"] != "en" && $numero["HAS_RESUME_INT"] != 0) { ?>
                            <p class="center" id="link_abstract_en">
                                <?php /*<a href="http://cairn-int.info/resume.php?ID_ARTICLE=<?=$numero["ID_ARTICLE_INT"]?>" class="link_custom_en" style="color:black;">
                                    <span class="icon icon-round-arrow-right black mr6"></span>
                                    English abstract on Cairn International Edition
                                </a> */ ?>
                                <!-- Lien URL REWRITING -->
                                <?php
                                    //var_dump($numero);
                                    // Création du lien vers l'abstract
                                    // Récupération des données de l'abstract
                                    if($numero["HAS_RESUME_ID"] != null) {
                                        $abstract_link = "abstract-".$numero["HAS_RESUME_ID"]."--".$numero["HAS_RESUME_URL"].".htm";
                                        echo "<a href=\"http://www.cairn-int.info/".$abstract_link."\" class=\"link_custom_en\" style=\"color:black;\">
                                                <span class=\"icon icon-round-arrow-right black mr6\"></span>
                                                English abstract on Cairn International Edition
                                              </a>";
                                    }
                                ?>
                                
                            </p>
                            <style>
                                #resume_en,.abstract.nb1.en > h3 { display : none; }
                            </style>
                        <?php } ?>

                    </div>
                    <?php include(__DIR__ . '/Blocs/citation.php'); ?>

                    <hr style="margin-top:3em;" class="grey">
                    <?php include(__DIR__ . '/Blocs/navPage.php'); ?>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include (__DIR__ . "/../CommonBlocs/invisible.php"); ?>
<?php
$this->javascripts[] = <<<'EOD'
    $(function() {
        $("#link_abstract_en").appendTo($('#from_xml_bottom .abstract'));
    });
EOD;
?>
