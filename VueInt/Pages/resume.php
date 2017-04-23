<?php
$this->titre = $currentArticle["ARTICLE_TITRE"];

    require_once('Vue/Pages/Blocs/headerArticle.php');

    $this->javascripts[] = '<script type="text/javascript" src="./static/js/article.js"></script>';

include (__DIR__ . '/../CommonBlocs/tabs.php');
?>
<div id="breadcrump">
    <a href="/">Home</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="./disc-<?= $curDiscipline?>.htm"><?= $filterDiscipline?></a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="./journal-<?php echo $revue["URL_REWRITING_EN"] ?>.htm">Journal</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="./journal-<?php echo $revue["URL_REWRITING_EN"] ?>-<?php echo $numero["NUMERO_ANNEE"]; ?>-<?php echo $numero["NUMERO_NUMERO"]; ?>.htm">Issue</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="#">Abstract</a>
</div>

<div id="body-content">
    <div id="page_article">
        <div id="page_header" class="grid-g grid-3-head">
            <div class="grid-u-1-4">
                <a href="./journal-<?php echo $revue["URL_REWRITING_EN"] ?>-<?php echo $numero["NUMERO_ANNEE"]; ?>-<?php echo $numero["NUMERO_NUMERO"]; ?>.htm">
                    <img class="big_coverbis" alt="<?php echo $revue["REVUE_TITRE"]; ?> <?php echo $revue["NUMERO_ANNEE"]; ?>/<?php echo $revue["NUMERO_NUMERO"]; ?>" src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?php echo $revue["REVUE_ID_REVUE"]; ?>/<?= $revue['NUMERO_ID_NUMPUBLIE'] ?>_H310.jpg">
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
                        <li style="display:none" id="__article_shortcuts_template">
                            <a href="{link}">{title} <span class="icon-arrow-black-right icon right"></span></a>
                        </li>
                        <li><a href="#anchor_citation">To cite this article <span class="icon-arrow-black-right icon right"></span></a>
                        </li>
                        <?php
                        $french = $currentArticle['LISTE_CONFIG_ARTICLE'][1];
                        //if($french != ''){
                        if($numero["ID_ARTICLE_CAIRN"] != "") { 
                        ?>
                            <li>
                                <a href="http://www.cairn.info/article.php?ID_ARTICLE=<?= $numero["ID_ARTICLE_CAIRN"]?>">Full text in French<span class="icon-arrow-black-right icon right"></span></a>
                            </li>
                        <?php
                        }
                        $english = $currentArticle['LISTE_CONFIG_ARTICLE'][2];
                        if ($english != '' && strpos($english,'my_cart.php') !== FALSE){
                        ?>
                            <li class="add-to-cart">
                                <a href="./my_cart.php?ID_ARTICLE=<?= $currentArticle['ARTICLE_ID_ARTICLE']?>"><span class="add-to-cart-icon"></span><span class="add-to-cart-text-container"><span class="value-currency"><?= $currentArticle['ARTICLE_PRIX']?>&nbsp;€</span>Add to cart</span></a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="grid-g grid-4-article">
            <?php
            include(__DIR__ . '/Blocs/numeroMeta.php');
            ?>

            <div class="grid-u-3-4">
                <div id="article_content" class="content">
                    <hr class="grey">
                    <?php
                    include(__DIR__ . '/Blocs/navPage.php');
                    ?>
                    <ul id="usermenu-tools" style="margin-top: 0px;">
                        <?php
                        require_once(__DIR__.'/../CommonBlocs/actionsBiblio.php');
                        checkBiblio($revue['REVUE_ID_REVUE'], $numero['NUMERO_ID_NUMPUBLIE'], $currentArticle['ARTICLE_ID_ARTICLE'],$authInfos,'usermenu');
                        ?>
                        <li><a class="icon icon-usermenu-tools-print" data-tooltip="Print" target="_blank" href="./article_p.php?ID_ARTICLE=<?php echo $currentArticle["ARTICLE_ID_ARTICLE"]; ?>"></a></li>
                    </ul>
                    <div class="col600">
                        <!-- DEBUT DU CONTENU DE L'ARTICLE -->
                        <?php echo $htmlDatas["CONTENUS"]; ?>
                        <!-- FIN DU CONTENU DE L'ARTICLE -->

                        <style>
                            #resume_en { display : block; }
                        </style>
                        <br/>
                        <div class="add-to-cart">
                            <div style="display : table; text-align : center;">
                                <?php
                                $english = $currentArticle['LISTE_CONFIG_ARTICLE'][2];
                                if ($english != '') {
                                    if(strpos($english,'my_cart.php') !== FALSE){
                                    ?>
                                        <div style="display : table-cell;" class="frenchVersionCart w10">
                                            <a href="./my_cart.php?ID_ARTICLE=<?= $currentArticle['ARTICLE_ID_ARTICLE']?>">
                                            <span class="add-to-cart-text-container">
                                                <span class="value-currency"><?= $currentArticle['ARTICLE_PRIX']?>&nbsp;€</span>
                                                Add to cart
                                            </span>
                                            </a>
                                        </div>
                                    <?php }else{?>
                                        <div style="display : table-cell;" class="frenchVersion w10">
                                            <a href="./article-<?= $currentArticle['ARTICLE_ID_ARTICLE']?>--<?= $currentArticle['ARTICLE_URL_REWRITING_EN']?>.htm">Full text in English</a>
                                        </div>
                                    <?php }
                                }
                                $french = $currentArticle['LISTE_CONFIG_ARTICLE'][1];
                                //if($french != ''){
                                if($numero["ID_ARTICLE_CAIRN"] != "") { 
                                ?>
                                    <div style="display : table-cell;" class="frenchVersion w10">
                                        <a href="http://www.cairn.info/article.php?ID_ARTICLE=<?= $numero["ID_ARTICLE_CAIRN"]?>">Full text in French</a>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>


                    <?php
                        /* Ce qui suite ne concerne que les articles de revues affiliés au CNRS */
                        if ($numero['NUMERO_TYPE_NUMPUBLIE'] === '5'):
                    ?>
                        <div class="section" style="clear:both;">
                            <b>The English version of this issue is published thanks to the support of the CNRS</b>
                        </div>
                        <hr style="margin-top:1em;" class="grey">
                    <?php endif; ?>
                    <?php

                        // Définition des métas données (from CAIRN3 ou INT si EN)
                        $metaArticle = $numero["META_ARTICLE_CAIRN"];
                        $metaNumero  = $numero["META_NUMERO_CAIRN"];
                        include(__DIR__ . '/Blocs/citation.php');
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
