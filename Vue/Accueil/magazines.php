<?php
/**
 *
 *
 * @version 0.1
 * @author ©Pythagoria - www.pythagoria.com - Pierre-Yves THOMAS
 * @todo : documentation du modèle transmis
 */
?>
<?php $this->titre = "Magazines";
$typePub = 'magazine';
include (__DIR__ . '/../CommonBlocs/tabs.php');
?>

<div id="body-content">
    <?php if (isset($revuesAbo) && count($revuesAbo) > 0){?>
        <h1 class="main-title">Accès abonné</h1>
        <div id="last_numeros">
        <?php $x = 1; ?>
            <?php foreach ($revuesAbo as $revue): $done++; ?>
                <?php if ($done % 4 == 1) : ?>
                    <div class="grid-g grid-4 last_numeros-1">
                    <?php endif; ?>
                    <div class="grid-u-1-4 numero">
                        <a href="./magazine-<?= ($revue['URL_REWRITING']) . '.htm' ?>">
                            <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= ($revue['ID_REVUE']) . '/' . ($revue['ID_NUMPUBLIE']) . '_L204.jpg' ?>" class="big_cover">
                        </a>
                        <h2 class="title_big_blue revue_title"><a href="./magazine-<?= ($revue['URL_REWRITING']) . '-' . $revue['ANNEE'] . '-' . $revue['NUMERO'] . '.htm' ?>"><?= ($revue['REVUE_TITRE']) ?></a></h2>
                        <div class="subtitle_little_grey reference">
                        </div>
                    </div>
                    <?php if ($done % 4 == 0 && $done <> 1): ?>
                    </div>
                <?php endif; ?>

            <?php endforeach; ?>
            <?php if ($done % 4 != 0 && $done <> 1): ?>
        </div>
        <?php endif; ?>

        </div>
        <?php if (isset($revues) && count($revues) > 0){?>
            <hr class="grey">
            <h1 class="main-title">Autres magazines</h1>
    <?php }
    }?>
    <?php if(!isset($revuesAbo) || count($revuesAbo) == 0){ ?>
        <h1 class="main-title">Accès par titre (<?php echo count($revues) . ' magazines'; ?>)</h1>
    <?php } ?>
    <div id="last_numeros">
        <?php $done = 0; ?>
        <?php foreach ($revues as $revue): $done++; ?>
            <?php if ($done % 4 == 1) : ?>
                <div class="grid-g grid-4 last_numeros-1">
                <?php endif; ?>
                <div class="grid-u-1-4 numero">
                    <a href="./magazine-<?= ($revue['URL_REWRITING']) . '.htm' ?>">
                        <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= ($revue['ID_REVUE']) . '/' . ($revue['ID_NUMPUBLIE']) . '_L204.jpg' ?>" class="big_cover">
                    </a>
                    <h2 class="title_big_blue revue_title"><a href="./magazine-<?= ($revue['URL_REWRITING']) . '-' . $revue['ANNEE'] . '-' . $revue['NUMERO'] . '.htm' ?>"><?= ($revue['REVUE_TITRE']) ?></a></h2>
                    <div class="subtitle_little_grey reference">
                    </div>
                </div>
                <?php if ($done % 4 == 0 && $done <> 1): ?>
                </div>
            <?php endif; ?>

        <?php endforeach; ?>
        <?php if ($done % 4 != 0 && $done <> 1): ?>
    </div>
    <?php endif; ?>

</div>
