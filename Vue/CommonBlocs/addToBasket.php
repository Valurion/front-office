<?php
    // Pour qu'une institution puisse acheter un ouvrage, il faut que les achats ne soient pas désactivés et qu'il y ait un crédit d'article
    $isAllowPurchaseForInstitution = ($typesAchat['MODE'] != 2) || (
        ($authInfos['I']['PARAM_INST']['A'] != 1)
        && (isset($authInfos['I']['PARAM_INST']['H']) && (intval($authInfos['I']['PARAM_INST']['H']) <= 1))
    );
    $isPurchaseArticle = isset($typesAchat['ARTICLE']);
    $isPurchaseNumero = isset($typesAchat['NUMERO'])
        && ($typesAchat['NUMERO'][0]['REVUE_ACHAT_PAPIER'] == 1)
        && $isAllowPurchaseForInstitution
        && ($typesAchat['NUMERO'][0]['NUMERO_EPUISE'] != 1)
        && ($typesAchat['NUMERO'][0]['NUMERO_PRIX'] > 0);
    // $isPurchaseNumero = isset($typesAchat['NUMERO']);
    $isPurchaseNumeroElec = isset($typesAchat['NUMERO_ELEC'])
        && $isAllowPurchaseForInstitution
        && ($typesAchat['NUMERO_ELEC'][0]['REVUE_ACHAT_ELEC'] == 1)
        && !$accessElecOk;
    $isPurchaseRevue = isset($typesAchat['REVUE'])
        && $isAllowPurchaseForInstitution;
    // Le verbe change suivant si on est connecté en institution
    $verbPurchase = $typesAchat['MODE'] == 2 ? 'Demander' : 'Acheter';
?>
<div class="add_to_cart_trigger">
    <div class="add-to-cart-bloc grid-g">
        <?php if ($isPurchaseArticle): ?>
            <?php if (
                ($typesAchat['MODE'] === 2) || (
                    // Quand on est connecté en tant qu'utilisateur ET en tant qu'institution ET que l'utilisateur a activé la possibilité d'acheter n'importe quel article, la demande a quand même la priorité
                    isset($authInfos['I'])
                    && isset($authInfos['U'])
                    && ($authInfos['U']['SHOWALL'] == 1)
                )
            ): ?>
                <a href="./mes_demandes.php?ID_ARTICLE=<?= $typesAchat['ARTICLE'][0]['ARTICLE_ID_ARTICLE'] ?>" class="add-to-cart-trigger grid-u-5-12" id="ask-to-inst-trigger-article" style="position: relative;">
                    <span class="icon icon-add-to-cart-big"></span>
                    <?=
                        in_array($typesAchat['ARTICLE'][0]['REVUE_TYPEPUB'], [3, 5, 6]) ?
                        'Demander ce chapitre' :
                        'Demander cet article'
                    ?>
                    <span class="icon icon-triangle-down"></span>
                </a>
            <?php else: ?>
                <a href="#" class="add-to-cart-trigger grid-u-5-12" id="add-to-cart-trigger-article" style="position: relative;">
                    <span class="icon icon-add-to-cart-big"></span>
                    <?=
                        in_array($typesAchat['ARTICLE'][0]['REVUE_TYPEPUB'], [3, 5, 6]) ?
                        'Acheter ce chapitre' :
                        'Acheter cet article'
                    ?>
                    <span class="icon icon-triangle-down"></span>
                </a>
            <?php endif ?>
        <?php endif ?>
        <?php if ($isPurchaseNumero || $isPurchaseNumeroElec): ?>
            <a href="#" class="add-to-cart-trigger grid-u-5-12" id="add-to-cart-trigger-numero" style="position: relative;">
                <span class="icon icon-add-to-cart-big"></span>
                <?php if ($typesAchat['NUMERO'][0]['REVUE_TYPEPUB'] == 3) : ?>
                    Acheter cet ouvrage
                <?php else : ?>
                    Acheter ce numéro
                <?php endif ?>
                <span class="icon icon-triangle-down"></span>
            </a>
        <?php endif; ?>
        <?php if ($isPurchaseRevue && !$isPurchaseArticle): ?>
            <a href="#" class="add-to-cart-trigger grid-u-5-12" id="add-to-cart-trigger-revue" style="width: 49%" style="position: relative;">
                <span class="icon icon-add-to-cart-big"></span>
                Acheter un abonnement
                <span class="icon icon-triangle-down"></span>
            </a>
        <?php endif; ?>
    </div>
</div>


<?php
$this->javascripts[] = <<<'EOD'
    $(function() {
        cairn.triggerMenu([
            {src: $('#add-to-cart-trigger-article'), dest: $('#add-to-cart-slider-purchase-article')},
            {src: $('#add-to-cart-trigger-numero'), dest: $('#add-to-cart-slider-purchase-numero')},
            {src: $('#add-to-cart-trigger-revue'), dest: $('#add-to-cart-slider-purchase-revue')},
        ]);
        // Si le flag est trouvé dans le hash de l'url, on ouvre le slide d'achat d'abonnement et on scroll jusqu'à lui
        if (window.location.hash.indexOf('open-purchase-revue-slider') >= 0) {
            $('#add-to-cart-trigger-revue').click();
            window.location.hash = window.location.hash.replace('open-purchase-revue-slider', '');
            $('html, body').animate({scrollTop: $('#page_header').offset().top}, 0);
        }
        });
EOD;
?>
