<?php
/**
 * Dedicated View [Coupled with the default method of the controler]
 *
 * @version 0.1
 * @author ©Pythagoria - www.pythagoria.com - Pierre-Yves THOMAS
 * @todo : documentation du modèle transmis
 */
?>
<?php $this->titre = "Accueil";
$typePub = 'revue';
include (__DIR__ . '/../CommonBlocs/tabs.php');

$arrayExcludeDisc = array();
if(isset($authInfos['I']) && isset($authInfos['I']['PARAM_INST']) && $authInfos['I']['PARAM_INST'] !== false && isset($authInfos['I']['PARAM_INST']['D'])){
   $arrayExcludeDisc = explode(',', $authInfos['I']['PARAM_INST']['D']);
}

/*
 * Modification septembre 2016 : la page d'accueil peut être appelée avec un paramètre shib=1
 * Cela permet de déclencher une authentification sur la CorsUrl au retour d'un login Shibboleth...
 */

if(isset($corsURL) && isset($token) && $token != null){
    echo '<img style="display:none;" src="http://'.$corsURL.'/index.php?controleur=User&action=loginCorsShib&token='.urlencode($token).'"/>';
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
                            <?php }else{  ?>
                                <a href="./disc-<?= $ardisc['URL_REWRITING'] ?>.htm" class=""><?= $ardisc['DISCIPLINE'] ?></a>
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
                    <li><a href="<?= "Accueil_Revues.php?TITRE=" . $letter['LET'] ?>">
                            <?= $letter['LET'] ?>
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
                <a href="Accueil_Revues.php?TITRE=ALL" class="all">Tous</a>
            <?php endif; ?>
        </li>
    </ul>
    <?php if (isset($revues) || isset($revuesAbo)): ?>
        <?php if (isset($revuesAbo) && count($revuesAbo) > 0){?>
            <br/><br/>
            <h1 class="main-title">Accès abonné</h1>
            <div id="list_revue_suscriber">
                <br/>
                <?php $x = 1; ?>
                <?php foreach ($revuesAbo as $revue): ?>
                    <?php $x++; ?>
                    <?php if (($x % 2) == 0): ?>
                        <div class="grid-g grid-2-list">
                    <?php endif; ?>
                    <div class="grid-u-1-2 greybox_hover revue">
                        <a  href="./revue-<?= $revue['URL_REWRITING'] ?>.htm">
                            <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $revue['ID_REVUE'] ?>/<?= $revue['ID_NUMPUBLIE'] ?>_L61.jpg" alt="couverture de <?= $revue['ID_NUMPUBLIE'] ?>" class="small_cover">
                        </a>
                        <div class="meta">
                            <h2 class="title_little_blue numero_title"><a  href="./revue-<?= $revue['URL_REWRITING'] ?>.htm"><?= $revue['TITRE'] ?></a></h2>
                            <div class="editeur">
                                <!-- Snippet pour la période de test SHS (début 01/04/2014) -->
                                <?= $revue['NOM_EDITEUR'] ?>
                            </div>
                        </div>
                    </div>
                    <?php if (($x % 2) == 1): ?>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if (($x % 2) == 0): ?>
                </div>
                <?php endif; ?>

            <?php if (isset($revues) && count($revues) > 0){?>
                <hr class="grey">
                <h1 class="main-title">Autres revues</h1>
        <?php }?>
             </div>
        <?php
        }
        ?>

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
                        <a  href="./revue-<?= $revue['URL_REWRITING'] ?>.htm">
                            <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $revue['ID_REVUE'] ?>/<?= $revue['ID_NUMPUBLIE'] ?>_L61.jpg" alt="couverture de [NUMERO_TITRE_ABREGE]" class="small_cover">
                        </a>
                        <div class="meta">
                            <h2 class="title_little_blue numero_title"><a  href="./revue-<?= $revue['URL_REWRITING'] ?>.htm"><?= $revue['TITRE'] ?></a></h2>
                            <div class="editeur">
                                <!-- Snippet pour la période de test SHS (début 01/04/2014) -->
                                <?= $revue['NOM_EDITEUR'] ?>
                            </div>
                        </div>
                    </div>
                    <?php if (($x % 2) == 1): ?>
                    </div>
                <?php endif; ?>

            <?php endforeach; ?>
            <?php if (($x % 2) == 0): ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    </div>
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
</div>
<div id="bloc_box">
    <div id="wrapper_bloc_box">
        <h1>Bienvenue sur cairn.info</h1>
        <div class="grid-g grid-3">
            <div class="grid-u-1-3">
                <h2>Comment accéder à cairn.info ?</h2>
                <p>
                    Cairn.info vous permet de consulter en ligne un nombre croissant de publications de sciences humaines et sociales de langue française, en texte intégral. Sans inscription ou abonnement préalable, tout internaute peut accéder gratuitement aux résumés des publications proposées sur ce portail, à leurs plans lorsque ceux-ci sont disponibles ainsi que, dans certains cas, à leur texte intégral.                </p>
                <a href="http://aide.cairn.info/">En savoir plus</a>
            </div>
            <div class="grid-u-1-3">
                <h2>Cairn.info dans le monde</h2>
                <p><a href="./aide-institutions-clientes.htm" style="display:block;"><img src="./static/images/mapworld.jpeg" alt="carte du monde" style="max-width : 100%;border : 1px solid white; border-radius : 3px;"></a></p>
            </div>
            <div class="grid-u-1-3">
                <h2>Pourquoi ouvrir un compte ?</h2>
                <p>Un compte n'est pas nécessaire pour consulter Cairn.info, mais donne accès à des services complémentaires susceptibles de faciliter la navigation, comme la conservation de l’historique, la constitution de bibliographies, la gestion d’alertes e-mail, etc. Pour avoir accès à ces outils, n’hésitez pas à créer gratuitement votre compte en quelques clics.</p>
                <a href="./creer_compte.php">Créer un compte</a>
            </div>
        </div>
    </div>
</div>
<div id="bloc_box2">
    <div class="grid-g grid-3">
        <div class="grid-u-1-3">
            <span class="icon-rss icon"></span><br>
            <a href="./abonnement_flux.php">Abonnez-vous aux flux RSS pour être informé en temps réel des nouveaux ouvrages, numéros de revue ou de magazines publiés sur Cairn.info.</a>
        </div>
        <div class="grid-u-1-3">
            <span class="icon-envelope icon"></span><br>
            <a href="./mes_alertes.php">Recevez les actualités de cairn.info en vous inscrivant à notre newsletter</a>
        </div>
        <div class="grid-u-1-3">
            <span class="icon-bell icon"></span><br>
            <a href="./mes_alertes.php">Recevez automatiquement par e-mail les annonces de nouvelles parutions des auteurs, revues et collections qui vous intéressent.</a>
        </div>
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
