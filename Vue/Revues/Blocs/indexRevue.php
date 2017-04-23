<div class="grid-g grid-3-head" id="page_header">

    <div class="grid-u-1-4">
        <img
            src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $revue['ID_REVUE'] ?>/<?= $numeros[0]['NUMERO_ID_NUMPUBLIE'] ?>_L204.jpg"
            alt="<?php echo $revue['TITRE'] . " " . (isset($numero)?($numero['NUMERO_ANNEE'] . '/' . $numero['NUMERO_NUMERO'] - $numero['NUMERO_NUMEROA']):""); ?>"
            class="big_coverbis">


    </div>

    <div class="grid-u-1-2 meta">
        <h1 class="title_big_blue title">
            <?= $revue['TITRE'] ?>
        </h1>
        <h2 class="subtitle_medium_grey subtitle"><?= $revue['STITRE'] ?></h2>
        <ul class="others">
            <?php if (Configuration::get('allow_backoffice', false)): ?>
                <span class="yellow id-revue">Id Revue : </span>
                <?= $revue['ID_REVUE'] ?>
                (<a href="<?= Configuration::get('backoffice', '#') ?>?controleur=Revues&amp;action=index&amp;ID_REVUE=<?= $revue['ID_REVUE'] ?>" class="bo-content" target="_blank">back-office</a>)
            <?php endif; ?>
            <li>
                <span class="yellow editor">Éditeur :</span>
                <a href="./editeur.php?ID_EDITEUR=<?= $revue['ID_EDITEUR'] ?>" class="url">
                    <?= $revue['NOM_EDITEUR'] ?>
                </a>
            </li>
            <?php if ($revue['AFFILIATION'] != ""): ?>
                <li class="wrapper_affiliation">
                    <?= $revue['AFFILIATION'] ?>
                </li>
            <?php endif; ?>
            <?php if ($revue['ISSN']): ?>
                <li>
                    <span class="yellow issn">ISSN : </span>
                    <?= $revue['ISSN'] ?>
                </li>
            <?php endif; ?>
            <?php if ($revue['ISSN_NUM'] != ''): ?>
                <li>
                    <span class="yellow issn">ISSN en ligne :</span>
                    <?= $revue['ISSN_NUM'] ?>
                </li>
            <?php endif; ?>
            <?php if ($revue['PERIODICITE'] != ''): ?>
                <li>
                    <span class="yellow period">Périodicité : </span>
                    <?= $revue['PERIODICITE'] ?>
                </li>
            <?php endif ?>
            <?php if ($revue['WEB'] != ''): ?>
                <li>
                    <!--span class="yellow website">Site internet : </span>-->
                    <a target="_blank" href="<?= $revue['WEB'] ?>">Site internet</a>
                </li>
            <?php endif; ?>
        </ul>
        <?php if (!isset($modeIndex) || $modeIndex != 'apropos') { ?>
            <form name="rechrevue" action="./resultats_recherche.php"
                  method="get" class="search_inside">
                <button type="submit">
                    <img src="./static/images/icon/magnifying-glass-black.png">
                </button>
                <input type="text" placeholder="Chercher dans cette revue"
                       name="searchTerm" /> <input type="hidden" name="ID_REVUE"
                       value="<?= $revue['ID_REVUE'] ?>" />
            </form>
        <?php } ?>
        <?php
            if(isset($typesAchat['DISPLAY_BLOC_ACHAT'])){
                include __DIR__."/../../CommonBlocs/addToBasket.php";
            }
        ?>
    </div>
    <div class="grid-u-1-4">
        <?php
        $numero = $numeros[0];
        foreach ($numeros as $oneNumero) {
            if ($oneNumero['NUMERO_NB_ARTICLES'] != '0') {
                $numero = $oneNumero;
                break;
            }
        }
        
        include (__DIR__ . '/../../CommonBlocs/alertesEmail.php');?>

        <hr class="grey" />
        <div class="article_menu">
            <h1>Raccourcis</h1>
            <ul>
                <?php if (!isset($modeIndex) || $modeIndex != 'apropos') { ?>
                    <li><a
                            href="en-savoir-plus-sur-la-revue-<?= $revue['URL_REWRITING'] ?>.htm">À
                            propos de cette revue <span
                                class="icon-arrow-black-right icon right"></span>
                        </a></li>
                <?php } else { ?>
                    <li><a
                            href="revue-<?= $revue['URL_REWRITING'] ?>.htm">Liste des numéros <span
                                class="icon-arrow-black-right icon right"></span>
                        </a></li>
                <?php } ?>
            </ul>
            <?php if ($revue["ID_REVUE_INT"] != ''): ?>
            <?php /*<a href="http://cairn-int.info/revue.php?ID_REVUE=<?= $revue['ID_REVUE_INT'] ?>" class="cairn-int_link"><span class="icon icon-round-arrow-right"></span>English version</a>*/ ?>
            <a href="http://cairn-int.info/journal-<?php echo $revue["URL_REWRITING_INT"]; ?>.htm" class="cairn-int_link"><span class="icon icon-round-arrow-right"></span>English version</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
if(isset($typesAchat['DISPLAY_BLOC_ACHAT'])){
    include __DIR__."/../../CommonBlocs/blocAddToBasket.php";
}
?>

