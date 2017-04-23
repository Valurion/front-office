<!--
Ce template sert à l'affichage d'une liste à 2 colonnes.
Il s'attend à recevoir:
    - $arrayForList = l'array qui contient les données, comprenant le champ TYPEPUB
    - $arrayFieldsToDisplay = un array qui contient les champs à afficher. Par défaut, seul le titre et l'image s'affichent
    - $prefix = un prefix pour le nom des champs de l'array (par ex: 'NUMERO_')
-->
<?php

$alreadyAdded = array();
if(!isset($prefix)){
        $prefix = '';
    }

    if (isset($arrayForList)) {
?>
    <div id="list_revue_suscriber">
        <?php
        $x = 1;
        foreach ($arrayForList as $row) {
            if ($row[$prefix . 'TYPEPUB'] == '1') {
                $url = 'revue-' . $row[$prefix . 'URL_REWRITING'];
                $titre = $row[$prefix . 'TITRE'];
                $fingerprint = $titre;
            } else if ($row[$prefix . 'TYPEPUB'] == '3' || $row[$prefix . 'TYPEPUB'] == '6') {
                if (isset($currentPage) && $currentPage == 'liste') {
                    $url = 'collection-' . $row[$prefix . 'URL_REWRITING'];
                    $titre = $row[$prefix . 'TITRE'];
                    $fingerprint = $titre;
                } else if (isset($currentPage) && $currentPage == 'editeur') {
                    $url = 'collection-' . $row[$prefix . 'URL_REWRITING'];
                    $titre = 'Collection « ' . $row[$prefix . "TITRE"] . ' »';
                    $fingerprint = $titre;
                } else {
                    $url = $row[$prefix . 'URL_REWRITING'] . "--" . $row[$prefix . "ISBN"];
                    $titre = $row[$prefix . 'TITRE'];
                    $fingerprint = $titre . $row[$prefix . 'SOUS_TITRE'];
                }
            } else if ($row[$prefix . 'TYPEPUB'] == '2') {
                $url = 'magazine-' . $row[$prefix . 'URL_REWRITING'];
                $titre = $row[$prefix . 'TITRE'];
                $fingerprint = $titre;
            }

            if(!in_array($fingerprint, $alreadyAdded)){
                $alreadyAdded[] = $fingerprint;
                if (in_array("NOM_AUTEUR", $arrayFieldsToDisplay) || in_array("NOM_AUTEUR-ANNEE", $arrayFieldsToDisplay)) {
                    if (count(explode(',', $row[$prefix . "NOM"])) > 2) {
                        $etAl = " <em>et al.</em>";
                        $noms = explode(',', $row[$prefix . "NOM"]);
                        $nom = $noms[0];
                        //$nom = $row[$prefix."NOM"];
                    } else {
                        $etAl = "";
                        $nom = $row[$prefix . "NOM"];
                    }
                }
                $x++;
                if (($x % 2) == 0) {
                    echo '<div class="grid-g grid-2-list">';
                }
                ?>
                <div class="grid-u-1-2 greybox_hover revue">
                    <a  href="./<?= $url ?>.htm">
                        <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $row[$prefix . 'ID_REVUE'] ?>/<?= $row[$prefix . 'ID_NUMPUBLIE'] ?>_L61.jpg" alt="couverture de [NUMERO_TITRE_ABREGE]" class="small_cover">
                    </a>
                    <div class="meta">
                        <h2 class="title_little_blue numero_title"><a  href="./<?= $url ?>.htm"><?= $titre ?></a></h2>
                        <?php if (in_array("SOUS_TITRE", $arrayFieldsToDisplay) && $row[$prefix . 'SOUS_TITRE'] != '') { ?>
                            <h2 class="text_medium numero_subtitle"><?= $row[$prefix . 'SOUS_TITRE'] ?></h2>
                        <?php } ?>
                        <?php if (in_array("PERIODICITE", $arrayFieldsToDisplay) && $row[$prefix . 'PERIODICITE'] != '') { ?>
                            <div><?= $row[$prefix . 'PERIODICITE'] ?></div>
                        <?php } ?>
                        <?php if (in_array("ISSN", $arrayFieldsToDisplay) && $row[$prefix . 'ISSN'] != '') { ?>
                            <div>ISSN : <?= $row[$prefix . 'ISSN'] ?></div>
                        <?php } ?>
                        <?php if (in_array("ISSN_NUM", $arrayFieldsToDisplay) && $row[$prefix . 'ISSN_NUM'] != '') { ?>
                            <div>ISSN en ligne : <?= $row[$prefix . 'ISSN_NUM'] ?></div>
                        <?php } ?>
                        <?php if (in_array("NOM_AUTEUR", $arrayFieldsToDisplay) && $row[$prefix . 'NOM'] != '') {
                            ?>
                            <div class='auteurs yellow'><?= $nom . $etAl ?></div>
                        <?php } ?>
                        <?php if (in_array("NOM_AUTEUR-ANNEE", $arrayFieldsToDisplay) && $row[$prefix . 'NOM'] != '' && $row[$prefix . 'ANNEE'] != '') { ?>
                            <div class="yellow-bold"><?= $nom . $etAl ?><b style="color: black"> - <?= $row[$prefix . "ANNEE"] ?></b></div>
                        <?php } ?>
                        <?php if (in_array("NOM_EDITEUR", $arrayFieldsToDisplay) && $row[$prefix . 'NOM_EDITEUR'] != '') { ?>
                            <div class="editeur"><?= $row[$prefix . 'NOM_EDITEUR'] ?></div>
                        <?php } ?>
                    </div>
                </div>
                <?php
                if (($x % 2) == 1) {
                    echo '</div>';
                }
            }
        }
        if (($x % 2) == 0) {
            echo '</div>';
        }
        ?>
    </div>
    <?php
}
?>
