<?php
$this->titre = $currentArticle["ARTICLE_TITRE"];

    require_once('Vue/Pages/Blocs/headerArticle.php');

    $this->javascripts[] = '<script type="text/javascript" src="./static/js/article.js"></script>';

include (__DIR__ . '/../CommonBlocs/tabs.php');

?>
<div id="breadcrump">
    <a href="/">Home</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="disc-<?= $curDiscipline?>.htm"><?= $filterDiscipline?></a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="./journal-<?php echo $revue["URL_REWRITING_EN"] ?>.htm">Journal</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="./journal-<?php echo $revue["URL_REWRITING_EN"] ?>-<?php echo $numero["NUMERO_ANNEE"]; ?>-<?php echo $numero["NUMERO_NUMERO"]; ?>.htm">Issue</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="#">Article</a>
</div>

<div id="body-content">
    <div id="page_article">
        <input type="hidden" id="hits" value="<?= $hits ?>"/>
        <div id="page_header" class="grid-g grid-3-head">
            <div class="grid-u-1-4">
                <a href="./journal-<?php echo $revue["URL_REWRITING_EN"] ?>-<?php echo $numero["NUMERO_ANNEE"]; ?>-<?php echo $numero["NUMERO_NUMERO"]; ?>.htm">
                    <img
                        class="big_coverbis"
                        id="numero-cover"
                        alt="<?php echo $revue["REVUE_TITRE"]; ?> <?php echo $revue["NUMERO_ANNEE"]; ?>/<?php echo $revue["NUMERO_NUMERO"]; ?>"
                        src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?php echo $revue["REVUE_ID_REVUE"]; ?>/<?= $revue['NUMERO_ID_NUMPUBLIE'] ?>_H310.jpg"
                        >
                </a>
            </div>

            <div class="grid-u-1-2 meta">
                <!-- DEBUT DES METADONNEES DE L'ARTICLE -->
                <?php
                //On commence par faire le pré-traitement sur les auteurs...
                $metasHtml = $htmlDatas["METAS"];
                //1 - Remplacement de [ARTICLE_SAME_AUTHOR_URL]
                $theAuthors = explode(",", $currentArticle['ARTICLE_AUTEUR']);
                foreach ($theAuthors as $theAuthor) {
                    $theauthorParam = explode(':', $theAuthor);
                    $theAutheurPrenom = $theauthorParam[0];
                    $theAutheurNom = $theauthorParam[1];
                    $theAutheurId = $theauthorParam[2];
                    $replaceStr = 'publications-de-' . $theAutheurNom . '-' . $theAutheurPrenom . '--' . $theAutheurId . '.htm';

                    $metasHtml = preg_replace('[\[ARTICLE_SAME_AUTHOR_URL\]]', $replaceStr, $metasHtml, 1);
                }
                echo $metasHtml;
                ?>

                <!-- FIN DES METADONNEES DE L'ARTICLE -->
            </div>
            <div class="grid-u-1-4">
                <div class="article_menu">
                    <h1>Shortcuts</h1>
                    <ul id="article_shortcuts">
                        <li>
                            <a href="#anchor_abstract" id="link-abstract" style="display: none;">
                                Abstract
                                <span class="icon-arrow-black-right icon right"></span>
                            </a>
                        </li>
                        <li>
                            <a href="#anchor_plan" id="link-plan-of-article" style="display: none;">
                                Outline
                                <span class="icon-arrow-black-right icon right"></span>
                            </a>
                        </li>
                        <li>
                            <a href="#anchor_citation" id="link-cite-this-article">
                                To cite this article
                                <span class="icon-arrow-black-right icon right"></span>
                            </a>
                        </li>

                        <?php
                        $french = $currentArticle['LISTE_CONFIG_ARTICLE'][1];
                        if($french != ''){
                        ?>
                            <!--li>
                                <a href="http://www.cairn.info/article.php?ID_ARTICLE=<?= $currentArticle['ARTICLE_ID_ARTICLE_S']?>">Full text in French<span class="icon-arrow-black-right icon right"></span></a>
                            </li-->
                        <?php
                        }
                        ?>
                    </ul>
                    <?php if($numero["ID_ARTICLE_CAIRN"] != "") {  ?>
                    <div class="frenchVersion">
                                <a href="http://www.cairn.info/article.php?ID_ARTICLE=<?= $numero['ID_ARTICLE_CAIRN']?>" id="article-french-version">Full text in French</a>
                    </div>
                    <?php } ?>
                    <?php if ($currentArticle['ARTICLE_SUJET_PROCHE'] == 1){ ?>
                        <!-- <h1>See also</h1>
                        <ul id="see_also_links">
                            <li>
                                <a id="see-also" href="./see_also.php?ID_ARTICLE=<?php echo $currentArticle["ARTICLE_ID_ARTICLE"]; ?>">   You may be interested in
                                    <span class="icon-arrow-black-right icon right"></span>
                                </a>
                            </li>
                        </ul> -->
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
                    <ul id="usermenu-tools" style="margin-top: 0px;">
                        <!--li><a class="icon icon-usermenu-tools-zen" data-tooltip="Zen mode" href="./zen.php?ID_ARTICLE=<?php echo $currentArticle["ARTICLE_ID_ARTICLE"]; ?>"></a></li-->
                        <li><a class="icon icon-usermenu-tools-zen" data-tooltip="Zen mode" href="./focus-<?php echo $currentArticle["ARTICLE_ID_ARTICLE"]; ?>--<?php echo $currentArticle["ARTICLE_URL_REWRITING_EN"]; ?>.htm"></a></li>
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
                                data-tooltip="PDF"
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
                        <?php } ?>
                        <li><a class="icon icon-usermenu-tools-print" data-tooltip="Print" target="_blank" href="./article_p.php?ID_ARTICLE=<?php echo $currentArticle["ARTICLE_ID_ARTICLE"]; ?>"></a></li>

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
                    <div class="col600" id="textehtml">
                        <!-- DEBUT DU CONTENU DE L'ARTICLE -->
                        <?php echo $htmlDatas["CONTENUS"]; ?>
                        <!-- FIN DU CONTENU DE L'ARTICLE -->

                        <style>
                            #resume_en { display : block; }
                        </style>
                    </div>


                    <?php
                        /* Ce qui suite ne concerne que les articles de revues affiliés au CNRS */
                        if ($numero['NUMERO_TYPE_NUMPUBLIE'] === '5'):
                    ?>
                        <hr style="margin-top:3em;" class="grey">
                        <div class="section" style="clear:both;">
                            <b>The English version of this issue is published thanks to the support of the CNRS</b>
                        </div>
                        <hr style="margin-top:1em;" class="grey">
                    <?php endif; ?>


                    <?php

                    if(!empty($currentArticle['ARTICLE_MENTION_SOMMAIRE'])) { ?>
                        <hr class="grey" style="">
                        <section id="mention-sommaire" style="font-weight: bold;text-align: center;">
                        <?php echo $currentArticle['ARTICLE_MENTION_SOMMAIRE']; ?>
                        </section>
                        <hr class="grey" style="margin-top:0.5em;">
                        <?php
                    }

                    // Définition des métas données (from CAIRN3 ou INT si EN)
                    $metaArticle = $numero["META_ARTICLE_CAIRN"];
                    $metaNumero  = $numero["META_NUMERO_CAIRN"];
                    include(__DIR__ . '/Blocs/citation.php');
                    ?>
                    <hr style="margin-top:3em;" class="grey">
                    <?php
                    include(__DIR__ . '/Blocs/navPage.php');
                    ?>
                </div>
            </div>
        </div>
    </div>

</div>


<?php
    /* Ce qui suite ne concerne que les numéros de revues affiliés au CNRS */
    if ($numero['NUMERO_TYPE_NUMPUBLIE'] === '5') {
        $this->javascripts[] = <<<'EOD'
            $(function()  {
                $('#footer-logos-partner').append('<a href="#"><img src="./static/images/logo-CNRS.png" alt="logo CNRS" id="footer_logo_cnrs" /></a>');
            });
EOD;
    }
?>
