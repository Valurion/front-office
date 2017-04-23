<div id="anchor_citation" class="section" style="clear:both;">
    <h2>To cite this article</h2>
    <p>
        <?php
        $numero_url = $typePub . '-' . $revue["REVUE_URL_REWRITING"] . '-' . $revue["NUMERO_ANNEE"] . '-' . $revue["NUMERO_NUMERO"];
        $numero_url = str_replace('revue-revue-', 'revue-', $numero_url);
        $theAuthors = explode(",", $currentArticle['ARTICLE_AUTEUR']);
        foreach ($theAuthors as $theAuthor) {
            $theauthorParam = explode(':', $theAuthor);
            $theAutheurPrenom = $theauthorParam[0];
            $theAutheurNom = $theauthorParam[1];
            
            //Nouvelle approche 12/01/2016 : Dimitry (Cairn).
            if (!preg_match('/transl/i', $theauthorParam[3])) {
                if(!empty($theAutheurPrenom) && !empty($theAutheurNom)) { //Si pas d'auteur
                    echo '<span class="UpperCase">' . $theAutheurPrenom . '</span> ' . $theAutheurNom . ', ';
                }
            }
            
        }
 
        if ($currentArticle['ARTICLE_LANGUE'] == 'en') {
            ?>
            “&nbsp;<?php echo $currentArticle["ARTICLE_TITRE"]; ?>&nbsp;”,&nbsp;<em><?php echo $revue["REVUE_TITRE"]; ?></em>
            <?php echo $revue["NUMERO_NUMERO"]; ?>/<?php echo $revue["NUMERO_ANNEE"] . " " . ($revue["NUMERO_VOLUME"] != '' ? '(' . $revue["NUMERO_VOLUME"] . ')' : ''); ?>
            <?php echo ($currentArticle["ARTICLE_PAGE_DEBUT"] > 0 ? (", p.&nbsp;" . $currentArticle["ARTICLE_PAGE_DEBUT"] . ($currentArticle["ARTICLE_PAGE_FIN"] > 0 ? '-' . $currentArticle["ARTICLE_PAGE_FIN"] : '')) : ''); ?>
            <br>URL : <a href="http://www.cairn.info/<?php echo $numero_url; ?>-page-<?php echo $currentArticle["ARTICLE_PAGE_DEBUT"]; ?>.htm" class="lien">www.cairn.info/<?php echo $numero_url; ?>-page-<?php echo $currentArticle["ARTICLE_PAGE_DEBUT"]; ?>.htm</a>.
            <?php if ($currentArticle["ARTICLE_DOI"] != '') { ?>
                <br/>DOI : <a class="lien" href="http://dx.doi.org/<?= $currentArticle["ARTICLE_DOI"] ?>"><?= $currentArticle["ARTICLE_DOI"] ?></a>.
                <?php
            }
        } else {
            ?>
            “&nbsp;<?php echo $metaArticle["TITRE"]; ?>&nbsp;”,&nbsp;<em><?php echo $metaNumero["TITRE"]; ?></em>
            <?php echo $metaNumero["NUMERO"]; ?>/<?php echo $metaNumero["ANNEE"] . " " . ($metaNumero["VOLUME"] != '' ? '(' . $metaNumero["VOLUME"] . ')' : ''); ?>
            <?php echo ($metaArticle["PAGE_DEBUT"] > 0 ? (", p.&nbsp;" . $metaArticle["PAGE_DEBUT"] . ($metaArticle["PAGE_FIN"] > 0 ? '-' . $metaArticle["PAGE_FIN"] : '')) : ''); ?>
            <br>URL : <a href="http://www.cairn.info/<?php echo $numero_url; ?>-page-<?php echo $metaArticle["PAGE_DEBUT"]; ?>.htm" class="lien">www.cairn.info/<?php echo $numero_url; ?>-page-<?php echo $metaArticle["PAGE_DEBUT"]; ?>.htm</a>.
            <?php if ($metaArticle["DOI"] != '') { ?>
                <br/>DOI : <a class="lien" href="http://dx.doi.org/<?= $metaArticle["DOI"] ?>"><?= $metaArticle["DOI"] ?></a>.
                <?php
            }
        }
        ?>


    </p>
</div>
