<?php 
if($tmpCmdIdFrom == ''){
$this->titre = 'My cart';
if($gabarit != 'gabaritAjax.php'){
    include (__DIR__ . '/../CommonBlocs/tabs.php');
}
}
$totalPrice = 0;
?>

<div class="biblio mon-panier" id="body-content">
    <div class="list_articles">
       <h1 class="main-title">My cart</h1>
        <hr class="grey">
        <div class="Clearfix"></div>
        <div id="wrapper_breadcrumb_cart">
            <ol id="breadcrumb_cart">
                <li class="black_button">My cart</li>
                <li>Billing Address</li>
                <li>Payment method</li>
                <li>Payment</li>
                <li>Get Access</li>
            </ol>
        </div>
        
        <?php if(!empty($credits)){ ?>
        <h2 class="section">
            <span>Article credits</span>
        </h2>
        <?php 
        foreach($credits as $credit){ 
            $totalPrice += $credit['PRIX'];
            ?>
            <div id="<?=$credit['PRIX'] ?>" class="specs article greybox_hover">
                <img class="small_cover" alt="couverture" src="./static/images/credit<?= $credit['PRIX'] ?>.png">
                <div class="meta">
                    <div class="title">
                        <strong>Article credits</strong>
                    </div>
                    <div class="revue_title">
                        Valid until 31-12-<?= $credit['EXPIRE']?>
                    </div>
                    <div class="prix">
                        <strong><span id="price-<?=$credit['PRIX'] ?>"><?= $credit['PRIX'] ?></span> €</strong>
                    </div>
                    <div class="state">
                        <input type="image" class="icon del-panier" src="static/images/del.png" alt="Supprimer de votre sélection" onclick="ajax.removeFromBasket('CREDIT','<?= $credit['PRIX'] ?>')">                        
                    </div>                    
                </div>
            </div>            
        <?php }?>
        <?php }?>

        <?php if(!empty($abos)){ ?>
        <h2 class="section">
            <span>Subscriptions</span>
        </h2>
        <?php foreach($abos as $abo){ 
            $totalPrice += $abo['ABO']['PRIX'];
            ?>
            <div id="<?=$abo['ABO']['ID_ABON'].'-'.$abo['ABO']['ID_REVUE'].'-'.(isset($abo["ANNEE"])?$abo["ANNEE"]:$abo["FIRSTNUM"]['NUMERO_ID_NUMPUBLIE'])?>" class="specs article greybox_hover">
                <img class="small_cover" alt="couverture" src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $abo['ABO']['ID_REVUE'] ?>/<?= $abo['ABO']['ID_NUMPUBLIE'] ?>_L61.jpg">
                <div class="meta">
                    <div class="title">
                        <strong><?= $abo['ABO']['TITRE'] ?> - <?= $abo['ABO']['LIBELLE'] ?></strong>
                    </div>
                    <div class="revue_title">
                        <?php
                        if(isset($abo['ANNEE'])){
                            echo 'Year '.$abo["ANNEE"].' ('.$abo["REVUE"]["PERIODICITE"].')';
                        }else if(isset($abo['FIRSTNUM'])){
                            echo 'From number '.$abo['FIRSTNUM']['NUMERO_ANNEE']."/".$abo['FIRSTNUM']['NUMERO_NUMERO'];
                        }
                        ?>
                    </div>
                    <div class="prix">
                        <strong><span id="price-<?=$abo['ABO']['ID_ABON']?>-<?=$abo['ABO']['ID_REVUE'].'-'.(isset($abo["ANNEE"])?$abo["ANNEE"]:$abo["FIRSTNUM"]['NUMERO_ID_NUMPUBLIE'])?>"><?= $abo['ABO']['PRIX'] ?></span> €</strong>
                    </div>
                    <div class="state">
                        <input type="image" class="icon del-panier" src="static/images/del.png" alt="Supprimer de votre sélection" onclick="ajax.removeFromBasket('ABO','<?= $abo['ABO']['ID_ABON'] ?>','<?= $abo['ABO']['ID_REVUE'] ?>','<?=(isset($abo["ANNEE"])?$abo["ANNEE"]:$abo["FIRSTNUM"]['NUMERO_ID_NUMPUBLIE'])?>')">                        
                    </div>                    
                </div>
            </div>            
        <?php }?>
        <?php }?>

        <?php if(!empty($numRev) || !empty($numRevElec)){ ?>
        <br>
        <h2 class="section">
            <span>Journals editions</span>
        </h2>
        <?php foreach($numRev as $rev){ 
            $totalPrice += $rev['NUMERO_PRIX'];
            ?>
            <div id="<?=$rev['NUMERO_ID_NUMPUBLIE']?>" class="specs article greybox_hover">
                <img class="small_cover" alt="couverture" src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $rev['NUMERO_ID_REVUE'] ?>/<?= $rev['NUMERO_ID_NUMPUBLIE'] ?>_L61.jpg">

                <div class="meta">
                    <div class="title">
                        <strong><?= $rev['NUMERO_TITRE'] ?></strong>
                    </div>
                    <div class="revue_title">
                        <span class="title_little_blue"><?= $rev['REVUE_TITRE'] ?></span> <strong><?= $rev['NUMERO_ANNEE'] ?>/<?= $rev['NUMERO_NUMERO'] ?>
                            <!--(n° 249) -->
                        </strong>
                    </div>

                    <div class="prix">
                        <strong><span id="price-<?=$rev['NUMERO_ID_NUMPUBLIE']?>"><?= $rev['NUMERO_PRIX'] ?></span> €</strong>
                    </div>
                    <div class="state">
                        <input type="image" class="icon del-panier" src="static/images/del.png" alt="Supprimer de votre sélection" onclick="ajax.removeFromBasket('NUM','<?= $rev['NUMERO_ID_NUMPUBLIE'] ?>')">                        
                    </div>
                </div>
            </div>
        <?php }?>
        
        <?php foreach($numRevElec as $rev){ 
            $totalPrice += $rev['NUMERO_PRIX_ELEC'];
            ?>
            <div id="ELEC-<?=$rev['NUMERO_ID_NUMPUBLIE']?>" class="specs article greybox_hover">
                <img class="small_cover" alt="couverture" src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $rev['NUMERO_ID_REVUE'] ?>/<?= $rev['NUMERO_ID_NUMPUBLIE'] ?>_L61.jpg">

                <div class="meta">
                    <div class="title">
                        <strong><?= $rev['NUMERO_TITRE'] ?></strong>
                    </div>
                    <div class="revue_title">
                        <span class="title_little_blue"><?= $rev['REVUE_TITRE'] ?></span> <strong><?= $rev['NUMERO_ANNEE'] ?>/<?= $rev['NUMERO_NUMERO'] ?>
                            <!--(n° 249) -->
                        </strong>
                        <br/><br/>
                        <strong>electronic format</strong>
                    </div>

                    <div class="prix">
                        <strong><span id="price-ELEC-<?=$rev['NUMERO_ID_NUMPUBLIE']?>"><?= $rev['NUMERO_PRIX_ELEC'] ?></span> €</strong>
                    </div>
                    <div class="state">
                        <input type="image" class="icon del-panier" src="static/images/del.png" alt="Supprimer de votre sélection" onclick="ajax.removeFromBasket('NUM','ELEC','<?= $rev['NUMERO_ID_NUMPUBLIE'] ?>')">                        
                    </div>
                </div>

            </div>
        <?php }?>
        <?php }?>
        
        <?php if(!empty($artOuv)){ ?>
        <br>
        <h2 class="section">
            <span>Books contribution</span>
        </h2>
        <?php 
            foreach($artOuv as $art){
                $totalPrice += $art['ARTICLE_PRIX'];
            }
            $arrayForList = $artOuv;
            $currentPage = 'contrib';
            $arrayFieldsToDisplay = array('ID', 'PRIX', 'NUMERO_TITLE', 'BIBLIO_AUTEURS', 'REMOVE_BASKET');
            include (__DIR__ . '/../CommonBlocs/liste_1col.php');
        
        } ?>

        <?php if(!empty($artRev)){ ?>
        <br>
        <h2 class="section">
            <span>Journal articles</span>
        </h2>
        <?php 
            foreach($artRev as $art){
                $totalPrice += $art['ARTICLE_PRIX'];
            }
            $arrayForList = $artRev;
            $currentPage = 'contrib';
            $arrayFieldsToDisplay = array('ID', 'PRIX', 'REVUE_TITLE', 'BIBLIO_AUTEURS', 'REMOVE_BASKET');
            include (__DIR__ . '/../CommonBlocs/liste_1col.php');
        
        } ?>
    
        <?php if(!empty($artMag)){ ?>
        <br>
        <h2 class="section">
            <span>Magazine articles</span>
        </h2>
        <?php 
            foreach($artMag as $art){
                $totalPrice += $art['ARTICLE_PRIX'];
            }
            $arrayForList = $artMag;
            $currentPage = 'contrib';
            $arrayFieldsToDisplay = array('ID', 'PRIX', 'NUMERO_TITLE', 'BIBLIO_AUTEURS', 'REMOVE_BASKET');
            include (__DIR__ . '/../CommonBlocs/liste_1col.php');
        
        } ?>
    
        &nbsp;<br> <span class="total"><strong>TOTAL </strong></span> <span class="prixtotal"><strong><span id="totalPrice"><?= $totalPrice ?></span> €</strong></span> <br> <br> <br>  


    <div class="checkout-bottom-section">
        <?php if($tmpCmdIdFrom != ''){?>
        <input type="hidden" id="tmpCmdIdFrom" value="<?= $tmpCmdIdFrom ?>"/>   
        <?php }
        if($totalPrice > 0){ 
        ?>
        <a class="continuer checkout-button" href="javascript:ajax.panierAchat()">Continue</a>
        <?php 
            //if($returnLink != null){
                //echo '<a class="payer checkin-button" href="./'.$returnLink.'">Back to page</a>';
                echo '<a class="payer checkin-button" href="javascript:history.back()">Back</a>';
            //}
        
        } ?>
    </div>


    <br>
</div>
