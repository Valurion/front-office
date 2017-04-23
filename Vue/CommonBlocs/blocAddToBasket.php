<?php

    /*
        TODO: Normalement, ce qui suit devrait être dans le controleur. Mais pas le temps de me plonger dedans pour le moment
        TODO: Les commentaires
    */
    const PURCHASE_NUMERO_ELEC = 0;
    const PURCHASE_NUMERO_PAPER = 1;

    function getPurchasesArticle($descAchats) {
        $basket = array();
        $isPurchaseArticle = isset($descAchats['ARTICLE']);

        if ($isPurchaseArticle) {
            $achat = $descAchats['ARTICLE'][0];
            $isOuvrage = in_array($achat['REVUE_TYPEPUB'], [3, 5, 6]);
            array_push($basket, array(
                'url' => "mon_panier.php?ID_ARTICLE=".$achat['ARTICLE_ID_ARTICLE'],
                'title' => "Acheter ".($isOuvrage ? 'ce chapitre' : 'cet article')." en version électronique",
                'desc' => "La version électronique de " . ($isOuvrage ? 'ce chapitre' : "cet article")." sera immédiatement accessible en ligne sur votre compte \"Mon Cairn.info\".<br />Un lien vous sera transmis par email pour y accéder.",
                'price' => $achat['ARTICLE_PRIX'],
                'icon' => "icon-elec-reader",
            ));
        }
        return $basket;
    }


    function getPurchasesNumero($descAchats, $hasAccessToElec) {
        $basket = array();
        $isPurchaseNumero = isset($descAchats['NUMERO'])
            && ($descAchats['NUMERO'][0]['REVUE_ACHAT_PAPIER'] == 1)
            && (
                ($descAchats['MODE'] != 2) || (!isset($descAchats['ARTICLE']))
            )
            && ($descAchats['NUMERO'][0]['NUMERO_EPUISE'] != 1)
            && ($descAchats['NUMERO'][0]['NUMERO_PRIX'] > 0);
        $isPurchaseNumeroElec = isset($descAchats['NUMERO_ELEC'])
            && $descAchats['MODE'] != 2
            && !$hasAccessToElec
            && ($descAchats['NUMERO_ELEC'][0]['REVUE_ACHAT_ELEC'] == 1)
            && !$hasAccessToElec;

        //Modification du code le 22/01/2016. Par Dimitry (Cairn).
        $element = ($descAchats['NUMERO'][0]['REVUE_TYPEPUB'] == 3) ? 'cet ouvrage' : 'ce numéro';
        
        //Pour la partie papier ou électronique;
        $numeroPapierOrElectronique = '';
        $description = '';
        if ($descAchats['NUMERO'][0]['NUMERO_MOVINGWALL'] != '0000-00-00' && $descAchats['NUMERO'][0]['NUMERO_MOVINGWALL'] <= date('Y-m-d')) {
            $numeroPapierOrElectronique = 'papier';
            $description = "La version papier de " . $element . " vous sera envoyée par la poste à l'adresse de livraison que vous nous aurez fournie.";
        } else {
            $numeroPapierOrElectronique = 'papier + électronique';
            $description = "La version électronique de " . $element . " sera immédiatement accessible en ligne sur votre compte \"Mon Cairn.info\".<br />Un lien vous sera transmis par email pour y accéder.<br />La version papier vous sera envoyée par la poste à l'adresse de livraison que vous nous aurez fournie.";
        }
        
        if ($isPurchaseNumero) {
            $achat = $descAchats['NUMERO'][0];
            array_push($basket, array(
                'url' => "mon_panier.php?ID_NUMPUBLIE=" . $achat['NUMERO_ID_NUMPUBLIE'],
                'title' => "Acheter " . $element . " en version " . $numeroPapierOrElectronique ,
                'desc' => $description,
                'price' => $achat['NUMERO_PRIX'],
                'icon' => 'icon-book-elec-reader',
                'type' => PURCHASE_NUMERO_PAPER,
            ));
        }
        if ($isPurchaseNumeroElec) {
            $achat = $descAchats['NUMERO_ELEC'][0];
            array_push($basket, array(
                'url' => "mon_panier.php?VERSION=ELEC&ID_NUMPUBLIE=".$achat['NUMERO_ID_NUMPUBLIE'],
                'title' => "Acheter " . $element . " en version électronique",
                'desc' => ucfirst ($element) . " sera immédiatement accessible en ligne sur votre compte \"Mon Cairn.info\".<br />Il ne vous sera pas envoyé : un lien vous sera transmis par email pour y accéder.",
                'price' => $achat['NUMERO_PRIX_ELEC'],
                'icon' => 'icon-elec-reader',
                'type' => PURCHASE_NUMERO_ELEC,
            ));
        }
        return $basket;
    };


    function getPurchasesRevue($descAchats) {
        $basket = array();

        $yearNow = intval(date('Y'));
        $basePara = "Cet abonnement vous donne accès aux versions électronique et papier de cette revue.<br />L'ensemble des numéros et des articles de cette revue seront immédiatement accessible en ligne sur votre compte \"Mon Cairn.info\".<br />Les numéros papier compris dans cet abonnement vous seront envoyés par la poste à l'adresse de livraison que vous nous aurez fournie, au fur et à mesure de leur parution.";

        foreach ($descAchats['REVUE'] as $achat) {
            $baseUrl = 'mon_panier.php?ID_REVUE='.$achat['ID_REVUE'].'&ID_ABON='.$achat['ID_ABON'];
            if ($achat['TYPE'] == 0) {
                
                //Chargement de l'année -1
                if (str_replace(' ', '', $achat['NEXTANNEE']) == '-1') {
                   array_push($basket, array(
                        'url' => $baseUrl.'&ANNEE='.($yearNow - 1),
                        'title' => $achat['LIBELLE'].' '.($yearNow - 1),
                        'desc' => $basePara,
                        'price' => $achat['PRIX'],
                        'icon' => 'icon-book-elec-reader',
                    )); 
                }
                
                //Chargement de l'année en cours.
                array_push($basket, array(
                    'url' => $baseUrl.'&ANNEE='.$yearNow,
                    'title' => $achat['LIBELLE'].' '.($achat['TYPE'] == 0 ? $yearNow : ''),
                    'desc' => $basePara,
                    'price' => $achat['PRIX'],
                    'icon' => 'icon-book-elec-reader',
                ));
                
                //chargement de l'année n + 1.
                if (str_replace(' ', '', $achat['NEXTANNEE']) == '1') {
                    array_push($basket, array(
                        'url' => $baseUrl.'&ANNEE='.($yearNow + 1),
                        'title' => $achat['LIBELLE'].' '.($yearNow + 1),
                        'desc' => $basePara,
                        'price' => $achat['PRIX'],
                        'icon' => 'icon-book-elec-reader',
                    )); 
                } 
                
            } else {
                foreach ($achat['LAST_NUMS'] as $achatLastNumero) {
                    array_push($basket, array(
                        'url' => $baseUrl.'&ID_NUMPUBLIE='.$achatLastNumero['ID_NUMPUBLIE'],
                        'title' => $achat['LIBELLE'].' <span class="yellow" style="margin-left: 0.5em; font-size: 0.9em;">(à partir du n°'.$achatLastNumero['ANNEE'].'/'.$achatLastNumero['NUMERO'].')</span>',
                        'desc' => $basePara,
                        'price' => $achat['PRIX'],
                        'icon' => 'icon-book-elec-reader',
                    ));
                }
            }
        };
        return $basket;
    }


    $purchases = array(
        'article' => getPurchasesArticle($typesAchat),
        'numero' => getPurchasesNumero($typesAchat, $accessElecOk),
        'revue' => getPurchasesRevue($typesAchat),
    );
?>


<?php if (count($purchases['article'])): ?>
    <div id="add-to-cart-slider-purchase-article" class="grid-g grid-3-head add-to-cart-slider mt1" style="display: none">
        <form class="grid-u-3-4">
            <?php foreach ($purchases['article'] as $index => $achat): ?>
                <div class="grid-g add-to-cart-article<?= ($index > 0) ? ' mt2' : ''?>">
                    <input
                        type="radio"
                        class="grid-u-1-12"
                        id="purchase-article-<?= $index ?>"
                        value="<?= $achat['url'] ?>"
                        name="purchase-article"
                        <?php if (count($purchases['article']) <= 1): ?>
                            style="visibility: hidden;"
                        <?php endif; ?>
                        <?= ($index === 0) ? 'checked' : ''?>
                    >
                    <label
                        for="purchase-article-<?= $index ?>"
                        class="grid-u-11-12"
                        <?php if (count($purchases['article']) <= 1): ?>
                            style="cursor: default;"
                        <?php endif; ?>
                    >
                        <span class="frame-grey grid-g block">
                            <span class="grid-u-3-4">
                                <span class="title block">
                                    <?= $achat['title'] ?>
                                </span>
                                <span class="paragraph block">
                                    <?= $achat['desc'] ?>
                                </span>
                            </span>
                            <span class="grid-u-1-8" style="position: relative; top: 2.7em;">
                                <span class="price"><?= $achat['price']; ?> €</span>
                            </span>
                            <span class="grid-u-1-8" style="position: relative; top: 2.2em;">
                                <span class="icon <?= $achat['icon'] ?>"></span>
                            </span>
                        </span>
                    </label>
                </div>
            <?php endforeach; ?>
            <div class="grid-g mt2 add-to-cart">
                <div class="grid-u-1-12"></div>
                <div class="grid-u-11-12">
                    <a class="left"  id="achat-article" href="#" onclick="
                        window.location = $('#add-to-cart-slider-purchase-article input[type=\'radio\']:checked').attr('value')
                    ">
                        <span class="icon icon-add-to-cart-big"></span>
                        Ajouter au panier
                    </a>
                    <?php if (count($purchases['numero'])): ?>
                        <a class="alternative right" href="#open-purchase-revue" onclick="$('#add-to-cart-trigger-numero').click()">Acheter plutôt le numéro (<?= $purchases['numero'][0]['price'] ?> €) ?</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
        <div class="grid-u-1-4 mention">
            <h2>Attention :</h2>
            <p>Cette offre est accessible par login et mot de passe sur votre compte "Mon Cairn.info", pour un seul accès simultané.</p>
            <p>Les prix ici indiqués sont les prix TTC.</p>
            <p>Pour plus d'informations, veuillez consulter les <a href="./conditions-generales-de-vente.php">conditions générales de vente</a>.</p>
        </div>
    </div>
<?php endif; ?>


<?php if (count($purchases['numero'])): ?>
    <div id="add-to-cart-slider-purchase-numero" class="grid-g grid-3-head add-to-cart-slider mt1" style="display: none">
        <form class="grid-u-3-4">
            <?php foreach ($purchases['numero'] as $index => $achat): ?>
                <div class="grid-g add-to-cart-numero<?= ($index > 0) ? ' mt2' : ''?>">
                    <input
                        type="radio"
                        class="grid-u-1-12"
                        id="purchase-numero-<?= $index ?>"
                        value="<?= $achat['url'] ?>"
                        name="purchase-numero"
                        <?php if (count($purchases['numero']) <= 1): ?>
                            style="visibility: hidden;"
                        <?php endif; ?>
                        <?= ($index === 0) ? 'checked' : ''?>
                    >
                    <label
                        for="purchase-numero-<?= $index ?>"
                        class="grid-u-11-12"
                        <?php if (count($purchases['numero']) <= 1): ?>
                            style="cursor: default;"
                        <?php endif; ?>
                    >
                        <span class="frame-grey grid-g block">
                            <span class="grid-u-3-4">
                                <span class="title block">
                                    <?= $achat['title'] ?>
                                </span>
                                <span class="paragraph block">
                                    <?= $achat['desc'] ?>
                                </span>
                            </span>
                            <span class="grid-u-1-8" style="position: relative; top: 2.7em;">
                                <span class="price"><?= $achat['price']; ?> €</span>
                            </span>
                            <span class="grid-u-1-8" style="position: relative; top: 2.2em;">
                                <span class="icon <?= $achat['icon'] ?>"></span>
                            </span>
                        </span>
                    </label>
                </div>
            <?php endforeach; ?>
            <div class="grid-g mt2 add-to-cart">
                <div class="grid-u-1-12"></div>
                <div class="grid-u-11-12">
                    <a class="block" href="#" onclick="
                        window.location = $('#add-to-cart-slider-purchase-numero input[type=\'radio\']:checked').attr('value')
                    ">
                        <span class="icon icon-add-to-cart-big"></span>
                        Ajouter au panier
                    </a>
                    <?php if (count($purchases['revue'])): ?>
                        <a  class="alternative block mt2"
                            <?php if (!count($purchases['article'])): ?>
                                href="#"
                                onclick="$('#add-to-cart-trigger-revue').click()"
                            <?php else: ?>
                                href="revue-<?= $revue['REVUE_URL_REWRITING'] ?>.htm#open-purchase-revue-slider"
                            <?php endif; ?>
                        >
                            <span class="icon icon-info"></span>
                            Acheter plutôt un abonnement à <?= $purchases['revue'][0]['price'] ?>&#160;€ pour :<br />
                            <span style="margin-left: 40px"></span>numéros papier + tous les articles de la revue en ligne
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
        <div class="grid-u-1-4 mention">
            <h2>Attention :</h2>
            <?php
                // Si achat du numéro uniquement en format électronique
                $isPurcharsesPaper = false;
                foreach ($purchases['numero'] as $key => $value) {
                    if ($value['type'] === PURCHASE_NUMERO_PAPER) {
                        $isPurcharsesPaper = true;
                        break;
                    }
                }
                if ($isPurcharsesPaper):
                // Si un jour, on utilise php > 5.4, on pourra décommenter ça....
                // if (!in_array(
                //     PURCHASE_NUMERO_PAPER,
                //     array_column($purchases['numero'], 'type')
                // )):
            ?>
                <p>Cette offre est accessible par login et mot de passe sur votre compte "Mon Cairn.info", pour un seul accès simultané.</p>
                <p>Les prix ici indiqués sont les prix TTC.</p>
            <?php else: ?>
                <p>Les prix ici indiqués sont les prix TTC, hors frais de livraison.<p>
            <?php endif; // Si achat du numéro au moins en version papier ?>

            <p>Pour plus d'informations, veuillez consulter les <a href="./conditions-generales-de-vente.php">conditions générales de vente</a>.<p>
        </div>
    </div>
<?php endif; ?>


<?php if (count($purchases['revue'])): ?>
    <div id="add-to-cart-slider-purchase-revue" class="grid-g grid-3-head add-to-cart-slider mt1" style="display: none">
<!--         <span onclick="cairn.close_menu();" class="close">
            <img src="./static/images/icon/lightbox-close.png">
        </span> -->
        <form class="grid-u-3-4">
            <?php foreach ($purchases['revue'] as $index => $achat): ?>
                <div class="grid-g add-to-cart-revue-<?= ($index > 0) ? ' mt2' : ''?>">
                    <input
                        type="radio"
                        class="grid-u-1-12"
                        id="purchase-revue-<?= $index ?>"
                        value="<?= $achat['url'] ?>"
                        name="purchase-revue"
                        <?php if (count($purchases['revue']) <= 1): ?>
                            style="visibility: hidden;"
                        <?php endif; ?>
                        <?= ($index === 0) ? 'checked' : ''?>
                    >
                    <label
                        for="purchase-revue-<?= $index ?>"
                        class="grid-u-11-12"
                        <?php if (count($purchases['revue']) <= 1): ?>
                            style="cursor: default;"
                        <?php endif; ?>
                    >
                        <span class="frame-grey grid-g block">
                            <span class="grid-u-3-4">
                                <span class="title block">
                                    <?= $achat['title'] ?>
                                </span>
                                <span class="paragraph block">
                                    <?= $achat['desc'] ?>
                                </span>
                            </span>
                            <span class="grid-u-1-8" style="position: relative; top: 2.7em;">
                                <span class="price"><?= $achat['price']; ?> €</span>
                            </span>
                            <span class="grid-u-1-8" style="position: relative; top: 2.2em;">
                                <span class="icon <?= $achat['icon'] ?>"></span>
                            </span>
                        </span>
                    </label>
                </div>
            <?php endforeach; ?>
            <div class="grid-g mt2 add-to-cart">
                <div class="grid-u-1-12"></div>
                <div class="grid-u-11-12">
                    <a href="#" id="achat-abonnement" onclick="
                        window.location = $('#add-to-cart-slider-purchase-revue input[type=\'radio\']:checked').attr('value')
                    ">
                        <span class="icon icon-add-to-cart-big"></span>
                        Ajouter au panier
                    </a>
                </div>
            </div>
        </form>
        <div class="grid-u-1-4 mention">
            <h2>Attention :</h2>
            <p>Cette offre est exclusivement réservée aux particuliers.</p>
            <p>Si vous souhaitez abonner votre institution, veuillez vous adresser à votre libraire ou à votre fournisseur habituel.</p>
            <p>Les prix ici indiqués sont les prix TTC.</p>
            <p>Pour plus d'informations, veuillez consulter les <a href="./conditions-generales-de-vente.php">conditions générales de vente</a>.</p>
        </div>
    </div>
<?php endif; ?>
