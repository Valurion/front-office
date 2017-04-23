<?php
/**
 * Dedicated View [Coupled with the default method of the controler]
 *
 * @version 0.1
 * @author ©Pythagoria - www.pythagoria.com - Pierre-Yves THOMAS
 * @todo : documentation du modèle transmis
 */
?>
<?php $this->titre = $revue['TITRE']; ?>

<?php
include (__DIR__ . '/../CommonBlocs/tabs.php');
?>



<?php if ($revue['STATUT'] == 0): ?>
    <div class="danger backoffice article-desactivate">
        Ce magazine est actuellement désactivé.<br />
        Sur http://cairn.info, ce magazine <strong>n’apparaîtra pas</strong>. Il apparaît <strong>uniquement</strong> sur <?= Configuration::get('urlSite') ?>.
    </div>
<?php endif; ?>


<div id="breadcrump">
    <a href="./">Accueil</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="./magazines.php">Magazines</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="./magazine-<?php echo $revue["URL_REWRITING"]; ?>.htm"><?php echo $revue["TITRE"]; ?></a>
</div>

<div id="body-content">
    <div id="page_revue">
        <?php require_once 'Vue/Revues/Blocs/indexMagazine.php'; ?>

        <hr class="grey">

        <div class="magazines">

            <div class="main-title_wrapper">
                <h1 class="main-title">Numéros disponibles</h1>
                <div class="nav">
                    <a href="./magazine.php?ID_REVUE=<?php echo $revue["ID_REVUE"]; ?>&amp;ANNEE=<?php echo $refAnnees["last"]; ?>" class="first">|&#9664;</a>
                    <a href="./magazine.php?ID_REVUE=<?php echo $revue["ID_REVUE"]; ?>&amp;ANNEE=<?php echo $refAnnees["current"] < $refAnnees["last"] ? ($refAnnees["current"] == $refAnnees["first"] ? ($refAnnees["current"] + 2) : ($refAnnees["current"] + 1)) : $refAnnees["current"]; ?>" class="prev">&#9664;</a>
                    <span class="actu"><?php echo $refAnnees["current"] > $refAnnees["first"] ? ($refAnnees["current"] . '-' . ($refAnnees["current"] - 1)) : (($refAnnees["current"] + 1) . '-' . $refAnnees["current"]); ?></span>
                    <a href="./magazine.php?ID_REVUE=<?php echo $revue["ID_REVUE"]; ?>&amp;ANNEE=<?php echo $refAnnees["current"] > ($refAnnees["first"] + 1) ? $refAnnees["current"] - 1 : $refAnnees["current"]; ?>" class="next">&#9654;</a>
                    <a href="./magazine.php?ID_REVUE=<?php echo $revue["ID_REVUE"]; ?>&amp;ANNEE=<?php echo ($refAnnees["first"] + 1); ?>" class="last">&#9654;|</a>
                </div>
            </div>

            <?php
            $count = 0;
            $countAnnee = 0;
            $bclAnnee = '';
            foreach ($numeros as $numero) {
                if ($numero["NUMERO_ANNEE"] < $refAnnees["current"] - 1) {
                    echo '</div>';
                    continue;
                }
                if ($count == 0 || $numero["NUMERO_ANNEE"] != $bclAnnee) {
                    $countAnnee = 0;
                    $bclAnnee = $numero["NUMERO_ANNEE"];
                    echo ($count > 0 ? '</div>' : '') . '<h2 class="magazine_year"><hr class="before"/>' . $numero["NUMERO_ANNEE"] . '<hr class="after"/></h2>'
                    . '<div class="list_magazines">';
                }
                if ($countAnnee % 4 == 0) {
                    echo ($countAnnee > 0 ? '</div>' : '') . '<div class="grid-g grid-4 last_numeros-1">';
                }
                $count++;
                $countAnnee++;
                ?>
                <div class="grid-u-1-4 numero greybox_hover">
                    <a href="./magazine-<?php echo $revue["URL_REWRITING"]; ?>-<?php echo $numero["NUMERO_ANNEE"]; ?>-<?php echo $numero["NUMERO_NUMERO"]; ?>.htm">
                        <img class="big_cover" src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?php echo $revue["ID_REVUE"]; ?>/<?php echo $numero["NUMERO_ID_NUMPUBLIE"]; ?>_L204.jpg" alt="Consulter <?php echo $revue["TITRE"]; ?> <?php echo $numero["NUMERO_ANNEE"]; ?>/<?php echo $numero["NUMERO_NUMERO"]; ?>">
                    </a>
                    <div class="subtitle_little_grey reference"><?php echo $numero["NUMERO_VOLUME"]; ?> <?php echo $numero["NUMERO_NUMERO"]; ?>/<?php echo $numero["NUMERO_ANNEE"]; ?></div>
                    <h2 class="title_medium_blue revue_title"><a href="magazine-<?php echo $revue["URL_REWRITING"]; ?>-<?php echo $numero["NUMERO_ANNEE"]; ?>-<?php echo $numero["NUMERO_NUMERO"]; ?>.htm"><?php echo $numero["NUMERO_TITRE_ABREGE"]; ?></a></h2>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>
