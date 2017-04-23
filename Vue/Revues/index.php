<?php
/**
 * Dedicated View [Coupled with the default method of the controler]
 *
 * @version 0.1
 * @author ©Pythagoria - www.pythagoria.com - Pierre-Yves THOMAS
 * @todo : documentation du modèle transmis
 */
?>
<?php $this->titre = "Revue " . $revue['TITRE'];
$typePub = 'revue';
include (__DIR__ . '/../CommonBlocs/tabs.php');
?>

<?php if ($revue['STATUT'] == 0): ?>
    <div class="danger backoffice article-desactivate">
        Cette revue/collection est actuellement désactivée.<br />
        Sur http://cairn.info, cette revue/collection <strong>n’apparaîtra pas</strong>. Elle apparaît <strong>uniquement</strong> sur <?= Configuration::get('urlSite') ?>.
    </div>
<?php endif; ?>

<div id="breadcrump">
    <a href="./">Accueil</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="#">Revue</a>
    <!-- <a href="[DISCIPLINE_HREF]">Discipline ([DISCIPLINE_DISCIPLINE])</a>
<span class="icon-breadcrump-arrow icon"></span> <a href="#">Revue</a> -->
</div>

<div id="body-content">
    <div id="page_revue">
        <?php require_once 'Vue/Revues/Blocs/indexRevue.php'; ?>

        <hr class="grey">

        <h1 class="main_title">Liste des num&#233;ros</h1>
        <div class="list_numeros">
            <?php $prev_portail = "" ?>
            <?php
            $done = 0;
            foreach ($numeros as $numero): $done++;
                $cur_portail = $numero['PORTAIL_NOM_PORTAIL'];
                ?>
                <?php if ($cur_portail != $prev_portail) : $prev_portail = $cur_portail; ?>
                    <?php if ((($done % 2) == 0) && $done > 1) : // we need to close and reset ?>
                    </div>
                    <?php $done = 1; ?>
                <?php endif; ?>
                <h1 class="main_title" style="font: bold 20px 'Alegreya SC';"><?= $cur_portail ?></h1>
            <?php endif; ?>
            <?php if (($done % 2) == 1) : ?>
                <div class="grid-g grid-2-list">
                <?php endif; ?>
                <div class="grid-u-1-2 greybox_hover numero">

                    <?php if ($numero['NUMERO_NB_ARTICLES'] != '0'): ?>
                        <a href="./<?= "revue-" . $numero['REVUE_URL_REWRITING'] . '-' . ($numero['NUMERO_ANNEE']) . '-' . ($numero['NUMERO_NUMERO']) . '.htm' ?>">
                            <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $numero['NUMERO_ID_REVUE'] . '/' . $numero['NUMERO_ID_NUMPUBLIE'] ?>_L61.jpg" alt="couverture de <?= $numero['NUMERO_TITRE'] ?>" class="small_cover">
                        </a>
                    <?php else : ?>
                        <span>
                            <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $numero['NUMERO_ID_REVUE'] . '/' . $numero['NUMERO_ID_NUMPUBLIE'] ?>_L61.jpg" alt="couverture de <?= $numero['NUMERO_TITRE'] ?>" class="small_cover">
                        </span>
                    <?php endif; ?>
                    <div class="meta">
                        <div class="subtitle_medium_grey numero_ref">
                            <?= $numero['NUMERO_ANNEE'] ?><!--
                            <?php if ($numero['NUMERO_NUMERO']): ?>
                                -->/<?= $numero['NUMERO_NUMERO'] ?><!--
                                <?php if ($numero['NUMERO_NUMEROA']): ?>
                                    -->-<?= $numero['NUMERO_NUMEROA'] ?><!--
                                <?php endif; ?>
                            <?php endif; ?>-->
                            (<?= $numero['NUMERO_VOLUME'] ?>)
                            <?php if (Configuration::get('allow_backoffice', false)): ?>
                                <span class="bo-content"><?= $numero['NUMERO_ID_NUMPUBLIE'] ?></span>
                            <?php endif; ?>
                        </div>
                        <h2 class="title_little_blue numero_title">

                            <?php if ($numero['NUMERO_NB_ARTICLES'] != '0'): ?>
                                <a href="./<?= "revue-" . $numero['REVUE_URL_REWRITING'] . '-' . ($numero['NUMERO_ANNEE']) . '-' . ($numero['NUMERO_NUMERO']) . '.htm' ?>"><?= $numero['NUMERO_TITRE'] ?></a>
                            <?php else: ?>
                                <?= $numero['NUMERO_TITRE'] ?>
                            <?php endif; ?>
                        </h2>

                        <h2 class="text_medium numero_subtitle"><?= $numero['NUMERO_SOUS_TITRE'] ?></h2>

                        <?php if ($numero['NUMERO_NB_ARTICLES'] == '0'): ?>
                            <div class="available_soon">Prochainement disponible</div>
                        <?php endif; ?>
                    </div>
                </div>
               <?php if (($done % 2) == 0) : ?>
                    </div>
                <?php endif; ?>
        <?php endforeach; ?>


        <?php
        foreach($revuesPrec as $revuePrec){
            $titrePrec = $revuePrec['TITRE'];
            $libPrec = $revuePrec['LIBELLE'];
            $numerosPrec = $revuePrec['NUMEROS'];
            if($titrePrec != '' && !empty($numerosPrec)){ ?>
                <?php if($titrePrec != $revue['TITRE'] || $libPrec != ''){?>
                    </div>
                    <h2 class="main_title"><?= $libPrec!=''?$libPrec:$titrePrec?></h2>
                    <div class="list_numeros">
                <?php
                    $done = 0;
                } ?>
                    <?php $prev_portail = "" ?>
                    <?php

                    foreach ($numerosPrec as $numero): $done++;
                        $cur_portail = $numero['PORTAIL_NOM_PORTAIL'];
                        ?>
                        <?php if ($cur_portail != $prev_portail) : $prev_portail = $cur_portail; ?>
                            <?php if ((($done % 2) == 0) && $done > 1) : // we need to close and reset ?>
                            </div>
                            <?php $done = 1; ?>
                        <?php endif; ?>
                        <h1 class="main_title" style="font: bold 20px 'Alegreya SC';"><?= $cur_portail ?></h1>
                    <?php endif; ?>
                    <?php if (($done % 2) == 1) : ?>
                        <div class="grid-g grid-2-list">
                        <?php endif; ?>
                        <div class="grid-u-1-2 greybox_hover numero">

                            <?php if ($numero['NUMERO_NB_ARTICLES'] != '0'): ?>
                                <a href="./<?= "revue-" . $revuePrec['URL_REWRITING'] . '-' . ($numero['NUMERO_ANNEE']) . '-' . ($numero['NUMERO_NUMERO']) . '.htm' ?>">
                                    <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $numero['NUMERO_ID_REVUE'] . '/' . $numero['NUMERO_ID_NUMPUBLIE'] ?>_L61.jpg" alt="couverture de <?= $numero['NUMERO_TITRE'] ?>" class="small_cover">
                                </a>
                            <?php else : ?>
                                <span>
                                    <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $numero['NUMERO_ID_REVUE'] . '/' . $numero['NUMERO_ID_NUMPUBLIE'] ?>_L61.jpg" alt="couverture de <?= $numero['NUMERO_TITRE'] ?>" class="small_cover">
                                </span>
                            <?php endif; ?>
                            <div class="meta">
                                <div class="subtitle_medium_grey numero_ref">
                                    <?= $numero['NUMERO_ANNEE'] ?>/<?= $numero['NUMERO_NUMERO'] ?><!--
                                    <?php if ($numero['NUMERO_NUMEROA'] != ''): ?>
                                        -->-<?= $numero['NUMERO_NUMEROA'] ?><!--
                                    <?php endif; ?>-->
                                    (<?= $numero['NUMERO_VOLUME'] ?>)
                                </div>
                                <h2 class="title_little_blue numero_title">

                                    <?php if ($numero['NUMERO_NB_ARTICLES'] != '0'): ?>
                                        <a href="./<?= "revue-" . $revuePrec['URL_REWRITING'] . '-' . ($numero['NUMERO_ANNEE']) . '-' . ($numero['NUMERO_NUMERO']) . '.htm' ?>"><?= $numero['NUMERO_TITRE'] ?></a>
                                    <?php else: ?>
                                        <?= $numero['NUMERO_TITRE'] ?>
                                    <?php endif; ?>
                                </h2>

                                <h2 class="text_medium numero_subtitle"><?= $numero['NUMERO_SOUS_TITRE'] ?></h2>

                                <?php if ($numero['NUMERO_NB_ARTICLES'] == '0'): ?>
                                    <div class="available_soon">Prochainement disponible</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (($done % 2) == 0) : ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach;
            }
        }?>
        <?php if (($done % 2) != 0) : ?>
            </div>
        <?php endif;?>
</div>
</div>
</div>

<?php
    /* Ce qui suite ne concerne que les numéros de revues affiliés au CNRS */
    $revuesCNRS = explode(',', Configuration::get('revuesCNRS', ''));
    if (in_array($revue['ID_REVUE'], $revuesCNRS)) {
        $this->javascripts[] = <<<'EOD'
            $(function()  {
                $('#footer-logos-partner').append('<a href="http://www.cnrs.fr/"><img src="./static/images/logo-CNRS.png" alt="logo CNRS" id="footer_logo_cnrs" /></a>');
            });
EOD;
    }
?>
