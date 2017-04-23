<div id="anchor_citation" class="section">
    <h2>Pour citer <?php echo $article_det . " " . $article_libelle; ?></h2>
    <p>
        <?php
            // Init
            $reference = "";

            // Récupération et définition de(s) auteur(s)
            $liste_auteurs = "";
            if($currentArticle['ARTICLE_AUTEUR'] != ''){
                $theAuthors = explode(",", $currentArticle['ARTICLE_AUTEUR']);
                foreach ($theAuthors as $theAuthor) {
                    $theauthorParam = explode(':', $theAuthor);
                    $theAutheurPrenom = $theauthorParam[0];
                    $theAutheurNom = $theauthorParam[1];
                    $theAutheurContrib = (isset($theauthorParam[3])) ? $theauthorParam[3] : '';
                    $liste_auteurs .= '<em>' . $theAutheurContrib . '</em>' . '<span class="UpperCase">' . $theAutheurNom . '</span> ' . $theAutheurPrenom . ', ';
                }
                $liste_auteurs = rtrim($liste_auteurs, ", ");
            }

            // Formatage des données
            $article_titre  = trim($currentArticle["ARTICLE_TITRE"]);
            $article_stitre = trim($currentArticle["ARTICLE_SOUSTITRE"]);
            $revue_titre    = trim($revue["REVUE_TITRE"]);
            $revue_annee    = trim($numero["NUMERO_NUMERO"]);
            $revue_nro      = trim($numero["NUMERO_ANNEE"]);
            $revue_volume   = trim($numero["NUMERO_VOLUME"]);
            $numero_titre   = trim($currentArticle["NUMERO_TITRE"]);
            $numero_ville   = trim($currentArticle["EDITEUR_VILLE"]);
            $numero_editeur = trim($currentArticle["EDITEUR_NOM_EDITEUR"]);
            $page_debut     = trim($currentArticle["ARTICLE_PAGE_DEBUT"]);
            $page_fin       = trim($currentArticle["ARTICLE_PAGE_FIN"]);

            // On affiche pas de point si le titre se termine par un signe de ponctuation
            if (isset($currentArticle['ARTICLE_SOUSTITRE']) && $currentArticle['ARTICLE_SOUSTITRE']) { if (!in_array(substr(trim($currentArticle['ARTICLE_TITRE']), -1), ['.', '?', '!'])) { $article_titre .= "."; } }
                
            // Formatage du titre
            $article_full_titre = "";
            $article_titre = str_replace("«", "\"", $article_titre);
            $article_titre = str_replace("»", "\"", $article_titre);
            if($article_titre != "") {$article_full_titre .= $article_titre;}
            if($article_stitre != "") {$article_full_titre .= "&nbsp;".$article_stitre;}

            // Formatage des dates
            $numero_date = "";
            if($revue_annee != "") { $numero_date .= $revue_annee."/".$revue_nro; }
            else { $numero_date .= $revue_nro; }

            // Formatage du volume
            $volume = "";
            if($revue_volume != "") {$volume .= " (".$revue_volume.")";}

            // Construction de l'URL
            $url = "http://www.cairn.info/".$numero_url."-page-".$page_debut.".htm";


            // REVUES & MAGAZINES
            // Schéma : [Nom Auteur] [Prénom auteur], « [Titre article]. [Sous-titre article] », [Nom revue], [Année]/[n° année] ([Volume]), p. [début]-[fin].
            if ($typePub == "revue" || $typePub == "magazine") {                
                
                // Construction du schémpa
                if($liste_auteurs != "") {$reference .= $liste_auteurs.",&nbsp;";}          // Auteur(s)
                $reference     .= "«&nbsp;".$article_full_titre."&nbsp;»,&nbsp;";           // Titre & Sous-titre
                $reference     .= "<em>".$revue_titre."</em>,&nbsp;";                       // Nom de la revue
                $reference     .= $numero_date.$volume.",&nbsp;";                           // Année/N° Année & Volume
                $reference     .= "p.&nbsp;".$page_debut."-".$page_fin;                     // Pages
                $reference     .= ".";

                // URL & DOI
                $reference     .= "<br><br>";
                $reference     .= "URL : <a href=\"".$url."\">".$url."</a><br />";
                if($currentArticle["ARTICLE_DOI"] != "") {$reference     .= "DOI : <a class=\"lien\" href=\"http://dx.doi.org/".$currentArticle["ARTICLE_DOI"]."\">".$currentArticle["ARTICLE_DOI"]."</a>";}
            }
            // OUVRAGES
            // Schéma MONOGRAPHIE : [Nom Auteur] [Prénom auteur], « [Titre chapitre]. [Sous-titre article] », [Titre ouvrage], [Ville d'édition], [Éditeur], « [Collection] », [Année] ([N° édition]), p. [début]-[fin].
            // Schéma COLLECTIF   : [Nom Auteur] [Prénom auteur], « [Titre chapitre]. [Sous-titre article] », in [Nom Auteur principal] [Prénom auteur principal] et al., [Titre ouvrage], [Ville d'édition], [Éditeur], « [Collection] », [Année] ([N° édition]), p. [début]-[fin].
            else if ($typePub == "ouvrage") {

                // Construction du schémpa
                if($liste_auteurs != "") {$reference .= $liste_auteurs.",&nbsp;";}          // Auteur(s)
                $reference     .= "«&nbsp;".$article_full_titre."&nbsp;»,&nbsp;";           // Titre & Sous-titre
                $reference     .= "<em>".$numero_titre."</em>,&nbsp;";                      // Titre de l'ouvrage
                if($numero["NUMERO_TYPE_NUMPUBLIE"] == 1) {}                                // in et al.
                $reference     .= $numero_ville.",&nbsp;";                                  // Ville d'édition
                $reference     .= $numero_editeur.",&nbsp;";                                // Editeur
                $reference     .= "«&nbsp;".$revue_titre."&nbsp;»,&nbsp;";                  // Collection/Titre de la revue
                $reference     .= $numero_date.$volume.",&nbsp;";                           // Année/N° Année & Volume
                $reference     .= "p.&nbsp;".$page_debut."-".$page_fin;                     // Pages
                $reference     .= ".";

                // URL & DOI
                $reference     .= "<br><br>";
                $reference     .= "URL : <a href=\"".$url."\">".$url."</a><br />";
                if($currentArticle["ARTICLE_DOI"] != "") {$reference     .= "DOI : <a class=\"lien\" href=\"http://dx.doi.org/".$currentArticle["ARTICLE_DOI"]."\">".$currentArticle["ARTICLE_DOI"]."</a>";}
            }
            // AUTRES
            else {

            } 

            // Nettoyage
            $reference = str_replace("«&nbsp;&nbsp;»,&nbsp;", "", $reference);
            $reference = str_replace("&nbsp;,&nbsp;", "", $reference);

            // Rendu
            echo $reference;
        /*
        if($currentArticle['ARTICLE_AUTEUR'] != ''){
            $theAuthors = explode(",", $currentArticle['ARTICLE_AUTEUR']);
            foreach ($theAuthors as $theAuthor) {
                $theauthorParam = explode(':', $theAuthor);
                $theAutheurPrenom = $theauthorParam[0];
                $theAutheurNom = $theauthorParam[1];
                $theAutheurContrib = (isset($theauthorParam[3])) ? $theauthorParam[3] : '';
                echo '<em>' . $theAutheurContrib . '</em>' . '<span class="UpperCase">' . $theAutheurNom . '</span> ' . $theAutheurPrenom . ', ';
            }
        }

        if ($typePub == "revue" || $typePub == "magazine") {
            ?>
            «&nbsp;<?php
                echo $currentArticle["ARTICLE_TITRE"];
                // On affiche pas de point si le titre se termine par un signe de ponctuation
                if (isset($currentArticle['ARTICLE_SOUSTITRE']) && $currentArticle['ARTICLE_SOUSTITRE']) {
                    if (!in_array(substr(trim($currentArticle['ARTICLE_TITRE']), -1), ['.', '?', '!'])) {
                        echo '.';
                    }
                }
            ?>&nbsp;<?php if(isset($currentArticle["ARTICLE_SOUSTITRE"])) echo $currentArticle["ARTICLE_SOUSTITRE"].""; ?>»,&nbsp;<em><?php echo $revue["REVUE_TITRE"]; ?></em>
            <?php echo $revue["NUMERO_NUMERO"]; ?>/<?php echo $revue["NUMERO_ANNEE"] . " " . ($revue["NUMERO_VOLUME"] != '' ? '(' . $revue["NUMERO_VOLUME"] . ')' : ''); ?>
            <?php echo ($currentArticle["ARTICLE_PAGE_DEBUT"] > 0 ? (", p.&nbsp;" . $currentArticle["ARTICLE_PAGE_DEBUT"] . ($currentArticle["ARTICLE_PAGE_FIN"] > 0 ? '-' . $currentArticle["ARTICLE_PAGE_FIN"] : '')) : ''); ?>
            <br>URL : <a href="http://www.cairn.info/<?php echo $numero_url; ?>-page-<?php echo $currentArticle["ARTICLE_PAGE_DEBUT"]; ?>.htm" class="lien">www.cairn.info/<?php echo $numero_url; ?>-page-<?php echo $currentArticle["ARTICLE_PAGE_DEBUT"]; ?>.htm</a>.
            <?php if ($currentArticle["ARTICLE_DOI"] != '') { ?>
                <br/>DOI : <a class="lien" href="http://dx.doi.org/<?= $currentArticle["ARTICLE_DOI"] ?>"><?= $currentArticle["ARTICLE_DOI"] ?></a>.
                <?php
            }
        } else {
            ?>
            «&nbsp;<?php echo $currentArticle["ARTICLE_TITRE"] . ($currentArticle["ARTICLE_SOUSTITRE"] != '' ? ' ' . $currentArticle["ARTICLE_SOUSTITRE"] : ""); ?>&nbsp;»,&nbsp;
            <em><?= $numero["NUMERO_TITRE"] ?></em>, <?= ($revue['EDITEUR_VILLE'] != '' ? ($revue['EDITEUR_VILLE'] . ', ') : '') . $revue['EDITEUR_NOM_EDITEUR'] ?>
            <?= ', «' . $revue["REVUE_TITRE"] . '», ' . $numero["NUMERO_ANNEE"] . ', ' . $numero["NUMERO_NB_PAGE"] . ' pages' ?>
            <br>URL : <a href="http://www.cairn.info/<?php echo $numero_url; ?>-page-<?php echo $currentArticle["ARTICLE_PAGE_DEBUT"]; ?>.htm" class="lien">www.cairn.info/<?php echo $numero_url; ?>-page-<?php echo $currentArticle["ARTICLE_PAGE_DEBUT"]; ?>.htm</a>.
            <?php if ($currentArticle["ARTICLE_DOI"] != '') { ?>
                <br/>DOI : <a class="lien" href="http://dx.doi.org/<?= $currentArticle["ARTICLE_DOI"] ?>"><?= $currentArticle["ARTICLE_DOI"] ?></a>.
                <?php
            }
        }*/
        ?>


    </p>
</div>
