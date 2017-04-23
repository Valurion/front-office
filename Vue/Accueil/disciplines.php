<?php
/**
 *
 *
 * @version 0.1
 * @author ©Pythagoria - www.pythagoria.com - Pierre-Yves THOMAS
 * @todo : documentation du modèle transmis
 */
?>
<?php $this->titre = "Revues";
$typePub = 'revue';
include (__DIR__ . '/../CommonBlocs/tabs.php');

$arrayExcludeDisc = array();
if(isset($authInfos['I']) && $authInfos['I']['PARAM_INST'] !== false && isset($authInfos['I']['PARAM_INST']['D'])){
   $arrayExcludeDisc = explode(',', $authInfos['I']['PARAM_INST']['D']);
}
?>

<div id="body-content">
    <h1 class="main-title">Accès par discipline (<?php echo $countRevues . ' revues'; ?>)</h1>
    <div class="table-button-grey">
        <?php foreach ($arrdisciplines as $arrdiscipline): ?>
            <div class="grid-g grid-4 row">
                <?php foreach ($arrdiscipline as $ardisc): ?>
                    <div class="cell grid-u-1-4">
                        <?php if ($ardisc['DISCIPLINE'] == ''): ?>
                            <span class="empty">&nbsp;</span>
                        <?php else:
                            if(in_array($ardisc['POS_DISC'], $arrayExcludeDisc)){?>
                                <span class="desactivate grey_buttondis"><?= $this->nettoyer($ardisc['DISCIPLINE']) ?></span>
                            <?php }else{ ?>
                                <a <?php if ($curDiscipline == $ardisc['URL_REWRITING']): ?><?= "class='active'" ?><?php endif; ?> href="./disc-<?= $this->nettoyer($ardisc['URL_REWRITING']) ?>.htm" class=""><?= $this->nettoyer($ardisc['DISCIPLINE']) ?></a>
                            <?php }
                        endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        <div  class="grid-g grid-4 row">
            <div class="cell grid-u-1-4"></div>
            <div class="cell grid-u-1-4"></div>
            <div class="cell grid-u-1-4"></div>
            <div class="cell grid-u-1-4">
                <a href="./listerev.php" class="blue __all_collection">Toutes disciplines</a>
            </div>
        </div>
    </div>

    <hr class="grey"/>
    <h1 class="main-title">Accès par titre</h1>
    <ul class="pagination letters">
        <?php foreach ($letters as $letter): ?>
            <?php if ($letter['A'] == 1): ?>
                <?php if (isset($LET) && $LET == $letter['LET']): ?>
                    <li><span class='active'>
                            <?= $this->nettoyer($letter['LET']) ?></span>
                    </li>
                <?php else: ?>
                    <li><a href="discipline.php?POS=<?= $curDisciplinePos . '&TITRE=' . $this->nettoyer($letter['LET']) ?>">
                            <?= $this->nettoyer($letter['LET']) ?>
                        </a></li>
                <?php endif; ?>
            <?php else: ?>
                <li>
                    <span class="desactivate"><?= $this->nettoyer($letter['LET']) ?></span>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
        <li>
            <?php if (isset($LET) && $LET == 'ALL'): ?>
                <a href="#" class="active all">Tous</a>
            <?php else: ?>
                <a href="discipline.php?POS=<?= $curDisciplinePos . '&TITRE=ALL' ?>" class="all">Tous</a>
            <?php endif; ?>


        </li>
    </ul>
    <?php if (isset($revues) || isset($revuesAbo)): ?>
        <?php if (isset($revuesAbo) && count($revuesAbo) > 0){?>
            <br/><br/>
            <h1 class="main-title">Accès abonné</h1>
            <?php
            $arrayForList = $revuesAbo;
            $arrayFieldsToDisplay = array("NOM_EDITEUR");
            include (__DIR__ . '/../CommonBlocs/liste_2col.php');
            ?>
            <?php if (isset($revues) && count($revues) > 0){?>
                <hr class="grey">
                <h1 class="main-title">Autres revues</h1>
        <?php }
        }?>
        <?php if (isset($revues)): ?>
            <h1 class="main-title"></h1><br/><br/>

            <?php
            $arrayForList = $revues;
            $arrayFieldsToDisplay = array("NOM_EDITEUR");
            include (__DIR__ . '/../CommonBlocs/liste_2col.php');
            ?>
        <?php endif; ?>
    <?php else: ?>
        <hr class="grey"/>
        <h1 class="main-title">Récemment ajouté</h1>

        <div id="last_numeros">
            <div class="grid-g grid-4 last_numeros-1">

                <?php foreach ($lastpubs as $lastpub): ?>
                    <div class="grid-u-1-4 numero">
                        <a href="./<?= "revue-" . $this->nettoyer($lastpub['URL_REWRITING']) . '-' . $this->nettoyer($lastpub['ANNEE']) . '-' . $this->nettoyer($lastpub['NUMERO']) . '.htm' ?>">
                            <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $this->nettoyer($lastpub['ID_REVUE']) . '/' . $this->nettoyer($lastpub['ID_NUMPUBLIE']) . '_L204.jpg' ?>" class="big_cover" alt="couverture de <?= $lastpub['ID_NUMPUBLIE'] ?>">
                        </a>
                        <h2 class="title_big_blue revue_title"><?= $this->nettoyer($lastpub['TITRE_ABREGE']) ?></h2>
                        <h3><?= $this->nettoyer(strip_tags($lastpub['TITRE'])) ?></h3>
                        <div class="subtitle_little_grey reference"><?= $this->nettoyer($lastpub['ANNEE']) . '/' . $this->nettoyer($lastpub['NUMERO']) . ' ' . $this->nettoyer($lastpub['VOLUME']) ?></div>

                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    <?php endif; ?>
    <hr class="grey"/>
    <div id="articles_more_view" class="list_articles">
        <h1 class="main-title">Articles les plus consultés</h1>

        <?php foreach ($mostconsultated as $most): ?>
            <div class="article greybox_hover">
                <a href="./resume.php?ID_ARTICLE=<?= $most['ID_ARTICLE'] ?>" style="display: inline-block; vertical-align: top;">
                    <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= ($most["ID_REVUE"]) . '/' . $most["ID_NUMPUBLIE"] ?>_L61.jpg" alt="SR_037_0143" class="small_cover"></a>
                <div class="meta">
                    <div class="title"><a href="./resume.php?ID_ARTICLE=<?= $most['ID_ARTICLE'] ?>">
                            <?= $most['TITRE'] ?>
                        </a></div>
                    <h3 class="subtitle">
                        <?= $most['SOUSTITRE'] ?>
                    </h3>
                    <div class="authors">
                        <?= $most['AUTEUR'] ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>


<?php $this->javascripts[] = <<<'EOD'
    $(function() {
        // Permet de remonter l'accès à toutes les revues dans la grille, sur le dernier item vide.
        var $empty = $("#body-content .empty:last");
        var $all_collec = $(".__all_collection");
        if (!$empty.length || !$all_collec.length)
            return false;
        $empty[0].outerHTML = $all_collec[0].outerHTML;
        $all_collec.parent().parent().hide();
    });
EOD;
?>
