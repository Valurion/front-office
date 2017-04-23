<?php
/**
 *
 *
 * @version 0.1
 * @author ©Pythagoria - www.pythagoria.com - Pierre-Yves THOMAS
 * @todo : documentation du modèle transmis
 */
?>
<?php $this->titre = "Encyclopédies de poche";
$typePub = 'encyclopedie';
include (__DIR__ . '/../CommonBlocs/tabs.php');

$arrayExcludeDisc = array();
if(isset($authInfos['I']) && $authInfos['I']['PARAM_INST'] !== false && isset($authInfos['I']['PARAM_INST']['D'])){
   $arrayExcludeDisc = explode(',', $authInfos['I']['PARAM_INST']['D']);
}
?>

<div id="body-content">
    <h1 class="main-title">Accès par discipline (<?php echo $countOuvrages . ' ouvrages'; ?>)</h1>
    <div class="table-button-grey">
        <?php foreach ($arrdisciplines as $arrdiscipline): ?>
            <div class="grid-g grid-4 row">
                <?php foreach ($arrdiscipline as $ardisc): ?>
                    <div class="cell grid-u-1-4">
                        <?php if ($ardisc['DISCIPLINE'] == ''): ?>
                            <span class="empty">&nbsp;</span>
                        <?php else :
                            if(in_array($ardisc['POS_DISC'], $arrayExcludeDisc)){?>
                                <span class="desactivate grey_buttondis"><?= $this->nettoyer($ardisc['DISCIPLINE']) ?></span>
                            <?php }else{  ?>
                                <a <?php if ($curDiscipline == $ardisc['URL_REWRITING']): ?><?= "class='active'" ?><?php endif; ?> href="./encyclopedies-de-poche-en-<?= ($ardisc['URL_REWRITING']) ?>.htm" class=""><?= ($ardisc['DISCIPLINE']) ?></a>
                            <?php }
                            endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <hr class="grey"/>
    <h1 class="main-title">Accès par titre</h1>
    <ul class="pagination letters">
        <?php foreach ($letters as $letter): ?>
            <?php if ($letter['A'] == 1): ?>
                <?php if (isset($LET) && $LET == $letter['LET']): ?>
                    <li><span class='active'>
                            <?= $letter['LET'] ?></span>
                    </li>
                <?php else: ?>
                    <li>
                        <?php if (isset($curDisciplinePos)) : ?>
                            <a href="encyclopedies-de-poche.php?POS=<?= $curDisciplinePos . '&TITRE=' . $letter['LET'] ?>">
                            <?php else: ?>
                                <a href="encyclopedies-de-poche.php?TITRE=<?= $letter['LET'] ?>">
                                <?php endif; ?>
                                <?= $letter['LET'] ?>
                            </a></li>
                <?php endif; ?>
            <?php else: ?>
                <li>
                    <span class="desactivate"><?= $letter['LET'] ?></span>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
            <?php if ($curDisciplinePos != ''){?>
                <li><a class="all" href="encyclopedies-de-poche.php?POS=<?= $curDisciplinePos ?>&TITRE=<?= $curDisciplinePos!=''?'ALL':'' ?>">Tous</a></li>
            <?php } ?>
    </ul>
    <?php if (isset($revuesAbo) && count($revuesAbo) > 0){?>
        <br/><br/>
        <h1 class="main-title">Accès abonné</h1>
        <div id="list_revue_suscriber">
        <?php $x = 1; ?>
            <?php foreach ($revuesAbo as $revue): ?>
                <?php $x++; ?>
                <?php if (($x % 2) == 0): ?>
                    <div class="grid-g grid-2-list">
                    <?php endif; ?>
                    <div class="grid-u-1-2 greybox_hover revue">
                        <a href="./<?= "" . ($revue['URL_REWRITING']) . '-' . '-' . ($revue['ISBN']) . '.htm' ?>">
                            <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= ($revue['ID_REVUE']) . '/' . ($revue['ID_NUMPUBLIE']) . '_L61.jpg' ?>" class="small_cover" />
                        </a>
                        <div class="meta">
                            <h2 class="title_little_blue numero_title"><a href="./<?= "" . ($revue['URL_REWRITING']) . '-' . '-' . ($revue['ISBN']) . '.htm' ?>" ><?= ($revue['NUMERO_TITRE_ABREGE']) ?></a></h2>
                            <?php
                            if (count(explode(',', $revue["NOM_AUTEURS"])) > 2) {
                                $etAl = " <em>et al.</em>";
                                $noms = explode(',', $revue["NOM_AUTEURS"]);
                                $nomParts = explode(':', $noms[0]);
                                $nom = '<a class="yellow" href="publications-de-'.$nomParts[1].'-'.$nomParts[0].'--'.trim($nomParts[2]).'.htm">'.$nomParts[0].' '.$nomParts[1].'</a>';
                            } else {
                                $etAl = "";
                                $noms = explode(',', $revue["NOM_AUTEURS"]);
                                $nomParts = explode(':', $noms[0]);
                                $nom = '<a class="yellow" href="publications-de-'.$nomParts[1].'-'.$nomParts[0].'--'.trim($nomParts[2]).'.htm">'.$nomParts[0].' '.$nomParts[1].'</a>';
                                if(count($noms) == 2){
                                    $nomParts = explode(':', trim($noms[1]));
                                    $nom .= ', <a class="yellow" href="publications-de-'.$nomParts[1].'-'.$nomParts[0].'--'.trim($nomParts[2]).'.htm">'.$nomParts[0].' '.$nomParts[1].'</a>';
                                }
                            }
                            echo '<div class="auteurs yellow">'.$nom.' '.$etAl.'</div>';
                            ?>
                            <h3><?= trim($revue['VOLUME'] . ' ' . $revue['ANNEE']) ?></h3>
                            <a class="editeur" href="editeur.php?ID_EDITEUR=<?=$revue['EDITEUR_ID_EDITEUR']?>"><?= $revue['EDITEUR_NOM_EDITEUR'] ?></a>
                        </div>
                    </div>
                    <?php if (($x % 2) == 1): ?>
                    </div>
                <?php endif; ?>

            <?php endforeach; ?>
            <?php if (($x % 2) == 0): ?>
            </div>
            <?php endif; ?>
        </div>
        <?php if (isset($revues) && count($revues) > 0){?>
            <hr class="grey">
            <h1 class="main-title">Autres ouvrages</h1>
    <?php }
    }?>
    <?php if (isset($revues)): ?>
        <div id="list_revue_suscriber">
            <br/><br/>
            <?php $x = 1; ?>
            <?php foreach ($revues as $revue): ?>
                <?php $x++; ?>
                <?php if (($x % 2) == 0): ?>
                    <div class="grid-g grid-2-list">
                    <?php endif; ?>
                    <div class="grid-u-1-2 greybox_hover revue">
                        <a href="./<?= "" . ($revue['URL_REWRITING']) . '-' . '-' . ($revue['ISBN']) . '.htm' ?>">
                            <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= ($revue['ID_REVUE']) . '/' . ($revue['ID_NUMPUBLIE']) . '_L61.jpg' ?>" class="small_cover" />
                        </a>
                        <div class="meta">
                            <h2 class="title_little_blue numero_title"><a href="./<?= "" . ($revue['URL_REWRITING']) . '-' . '-' . ($revue['ISBN']) . '.htm' ?>" ><?= ($revue['NUMERO_TITRE_ABREGE']) ?></a></h2>
                            <?php
                            if(isset($revue['NOM_AUTEURS'])){
                                if (count(explode(',', $revue["NOM_AUTEURS"])) > 2) {
                                    $etAl = " <em>et al.</em>";
                                    $noms = explode(',', $revue["NOM_AUTEURS"]);
                                    $nomParts = explode(':', $noms[0]);
                                    $nom = '<a class="yellow" href="publications-de-'.$nomParts[1].'-'.$nomParts[0].'--'.trim($nomParts[2]).'.htm">'.$nomParts[0].' '.$nomParts[1].'</a>';
                                } else {
                                    $etAl = "";
                                    $noms = explode(',', $revue["NOM_AUTEURS"]);
                                    $nomParts = explode(':', $noms[0]);
                                    $nom = '<a class="yellow" href="publications-de-'.$nomParts[1].'-'.$nomParts[0].'--'.trim($nomParts[2]).'.htm">'.$nomParts[0].' '.$nomParts[1].'</a>';
                                    if(count($noms) == 2){
                                        $nomParts = explode(':', trim($noms[1]));
                                        $nom .= ', <a class="yellow" href="publications-de-'.$nomParts[1].'-'.$nomParts[0].'--'.trim($nomParts[2]).'.htm">'.$nomParts[0].' '.$nomParts[1].'</a>';
                                    }
                                }
                                echo '<div class="auteurs yellow">'.$nom.' '.$etAl.'</div>';
                            }
                            ?>
                            <h3><?= trim($revue['VOLUME'] . ' ' . $revue['ANNEE']) ?></h3>
                            <?php if(isset($revue['EDITEUR_ID_EDITEUR'])){ ?>
                            <a class="editeur" href="editeur.php?ID_EDITEUR=<?=$revue['EDITEUR_ID_EDITEUR']?>"><?= $revue['EDITEUR_NOM_EDITEUR'] ?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if (($x % 2) == 1): ?>
                    </div>
                <?php endif; ?>

            <?php endforeach; ?>
            <?php if (($x % 2) == 0): ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
<h1 class="main-title">Accès par auteur</h1>
<ul class="pagination letters">
    <?php foreach ($lettersAuthor as $letter): ?>
        <?php if ($letter['A'] == 1): ?>
            <?php if (isset($AUTEUR) && $AUTEUR == $letter['LET']): ?>
                <li><span class='active'>
                        <?= $letter['LET'] ?></span>
                </li>
            <?php else: ?>
                <li>
                    <?php if (isset($curDisciplinePos)) : ?>
                        <a href="encyclopedies-de-poche.php?POS=<?= $curDisciplinePos . '&AUTEUR=' . $letter['LET'] ?>">
                        <?php else: ?>
                            <a href="encyclopedies-de-poche.php?AUTEUR=<?= $letter['LET'] ?>">
                            <?php endif; ?>
                            <?= $letter['LET'] ?>
                        </a></li>
            <?php endif; ?>
        <?php else: ?>
            <li>
                <span class="desactivate"><?= $letter['LET'] ?></span>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>
<?php if (isset($lastpubs)) : ?>
    <hr class="grey"/>
    <h1 class="main-title">Récemment ajouté</h1>

    <div id="last_numeros">
        <div class="grid-g grid-4 last_numeros-1">
            <?php foreach ($lastpubs as $lastpub): ?>
                <div class="grid-u-1-4 numero">
                    <a href="./<?= "" . ($lastpub['URL_REWRITING']) . '-' . '-' . ($lastpub['ISBN']) . '.htm' ?>">
                        <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= ($lastpub['ID_REVUE']) . '/' . ($lastpub['ID_NUMPUBLIE']) . '_L204.jpg' ?>" class="big_cover">
                    </a>
                    <h2 class="title_big_blue revue_title"><?= ($lastpub['NUMERO_TITRE_ABREGE']) ?></h2>
                    <h4>
                        <?php
                            echo $lastpub['ANNEE'];
                            if ($lastpub['VOLUME']) {
                                echo '/' . $lastpub['VOLUME'];
                            }
                        ?>
                    </h4>
                    <div class="subtitle_little_grey reference">
                        <?=
                            Service::get('ParseDatas')->stringifyRawAuthors($lastpub['NOM'], 2, null, null, null, false)
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
<?php endif; ?>

</div>
