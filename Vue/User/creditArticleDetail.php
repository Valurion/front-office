<?php
$this->titre = "Détail de mon crédit d'articles";
include (__DIR__ . '/../CommonBlocs/tabs.php');

function getAuteurs($auteur_string){
    $theAuthors = explode(",", $auteur_string);
    $str = "";
    foreach ($theAuthors as $theAuthor){
        $theauthorParam = explode(':', $theAuthor);
        $theAutheurPrenom = $theauthorParam[0];
        $theAutheurNom = $theauthorParam[1];
        $theAutheurId = $theauthorParam[2];
        $str .= ($str != '' ?', ':'');
        $str .= '<span class="author"><a class="yellow" href="publications-de-'.$theAutheurNom.'-'.$theAutheurPrenom.'--'.$theAutheurId.'.htm">'.$theAutheurPrenom.' '.$theAutheurNom.'</a></span>';
    } 
    return $str;
}
?>
<div class="biblio mon_credit mon-panier" id="body-content">
    <div id="free_text">
        <h1 class="main-title">Détail de mon crédit d'articles</h1>
        <h2 class="section"><span>Crédit d'articles en cours</span></h2>
        <p>
            <em>Crédit acheté le <?= date_format(new DateTime($credit['lastAchat']), 'd/m/Y')?> :</em> <?= $credit['prix'] ?> €<br>
            <em>Solde :</em> <?= $credit['solde'] ?> €
        </p>
        
        <br/><br/>
        
        <h2 class="section"><span>Historique de mes achats par crédit d'articles</span></h2>
        <p>
            <em>Montant total de vos achats par crédit d'articles :</em> <?= $credit['sumAchat'] ?> € (+ frais de port)<br>
        </p>
        <?php if(!empty($abos)){ ?>
        <h3 class="section"><span>Abonnements</span></h3>
        <div class="list_articles">
        <?php foreach($abos as $abo){?>
        <div class="article greybox_hover">
            <img class="small_cover" alt="couverture" src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $abo['ID_REVUE']?>/<?= $abo['details']['ID_NUMPUBLIE']?>_L61.jpg">
            <div class="meta">
                <div class="title_little_blue">
                    <a href="<?= $abo['details']['TYPEPUB']=='1'?'revue':($abo['details']['TYPEPUB']=='2'?'magazine':'collection')?>-<?= $abo['details']['URL_REWRITING']?>.htm"><strong><?= $abo['details']['TITRE']?></strong></a>
                </div>
                <div class="title"><?= $abo['details']['LIBELLE']?></div>
                <div class="date"><i>Acheté le </i><?= date_format(new DateTime($abo['DATE_ACHAT']), 'd/m/Y')?></div>
                <div class="prix"><strong><?= $abo['PRIX']?> €</strong></div>
            </div>
        </div>
        <?php } ?>
        </div>
        <?php 
        }
        if(!empty($artOuv)){ ?>
        <h3 class="section"><span>Contributions d’ouvrages</span></h3>
        <div class="list_articles">
        <?php foreach($artOuv as $art){?>
        <div class="article greybox_hover">
            <img class="small_cover" alt="couverture" src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $art['ID_REVUE'] ?>/<?= $art['ID_NUMPUBLIE']?>_L61.jpg">
            <div class="meta">
                <div class="title">
                    <a href="<?= $art['details']['NUMERO_URL_REWRITING'] ?>--<?= $art['details']['NUMERO_ISBN'] ?>-page-<?= $art['details']['ARTICLE_PAGE_DEBUT'] ?>.htm"><strong><?= $art['details']['ARTICLE_TITRE']?></strong></a><br>
                    <?= $art['details']['ARTICLE_SOUSTITRE']?>
                </div>
                <div class="authors"><?= getAuteurs($art['details']['ARTICLE_AUTEUR']) ?></div>
                <div class="revue_title">
                    Dans <span class="title_little_blue"><?= $art['details']['NUMERO_TITRE'] ?></span> <strong>(<?= $art['details']['EDITEUR_NOM_EDITEUR'] ?>, <?= $art['details']['NUMERO_ANNEE'] ?>)</strong>
                </div>
                <div class="date"><i>Acheté le <?= date_format(new DateTime($art['DATE']), 'd/m/Y')?>.</i></div>
                <div class="prix"><strong><?= $art['PRIX']?> €</strong></div>
            </div>
        </div>
        <?php } ?>
        </div>
        <?php }
        if(!empty($numRev)){ ?>
        <h3 class="section"><span>Numéros de revues</span></h3>
        <div class="list_articles">
        <?php foreach($numRev as $num){?>
        <div class="article greybox_hover">
            <img class="small_cover" alt="couverture" src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $num['ID_REVUE'] ?>/<?= $num['ID_NUMPUBLIE']?>_L61.jpg">
            <div class="meta">
                <div class="title">
                    <a href="revue-<?= $num['details']['REVUE_URL_REWRITING']?>-<?= $num['details']['NUMERO_ANNEE']?>-<?= $num['details']['NUMERO_NUMERO']?>.htm"><strong><?= $num['details']['NUMERO_TITRE'] ?></strong></a><br>
                    <i><?= $num['details']['NUMERO_SOUS_TITRE']?></i>
                </div>
                <div class="authors"></div>
                <div class="revue_title">
                    <a class="title_little_blue" href="./revue.php?ID_REVUE=<?= $num['ID_REVUE']?>"><span class="title_little_blue"><?= $num['details']['REVUE_TITRE']?></span></a>
                    <strong><?= $num['details']['NUMERO_ANNEE']?>/<?= $num['details']['NUMERO_NUMERO']?></strong>
                </div>
                <div class="date"><i>Acheté le <?= date_format(new DateTime($num['DATE']), 'd/m/Y')?>.</i></div>
                <div class="prix"><strong><?= $num['PRIX']?> €</strong></div>
            </div>
        </div>
        <?php }?>
        </div>
        <?php }
        if(!empty($artRev)){ ?>
        <h3 class="section"><span>Articles de revues</span></h3>
        <div class="list_articles">
        <?php foreach($artRev as $art){?>
        <div class="article greybox_hover">
            <img class="small_cover" alt="couverture" src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $art['ID_REVUE'] ?>/<?= $art['ID_NUMPUBLIE']?>_L61.jpg">
            <div class="meta">
                <div class="title">
                    <a href="revue-<?= $art['details']['REVUE_URL_REWRITING']?>-<?= $art['details']['NUMERO_ANNEE']?>-<?= $art['details']['NUMERO_NUMERO']?>-page-<?= $art['details']['ARTICLE_PAGE_DEBUT']?>.htm"><strong><?= $art['details']['ARTICLE_TITRE']?></strong></a><br>
                    <?= $art['details']['ARTICLE_SOUSTITRE']?>
                </div>
                <div class="authors"><?= getAuteurs($art['details']['ARTICLE_AUTEUR']) ?></div>
                <div class="revue_title">
                    Dans <a class="title_little_blue" href="revue-<?= $art['details']['REVUE_URL_REWRITING']?>.htm"><span class="title_little_blue"><?= $art['details']['REVUE_TITRE']?></span></a> <strong><?= $art['details']['NUMERO_ANNEE']?>/<?= $art['details']['NUMERO_NUMERO']?>
                        <?= $art['details']['NUMERO_VOLUME']!=''?('('.$art['details']['NUMERO_VOLUME'].')'):''?></strong>
                </div>
                <div class="date"><i>Acheté le <?= date_format(new DateTime($art['DATE']), 'd/m/Y')?>.</i></div>
                <div class="prix"><strong><?= $art['PRIX']?> €</strong></div>
            </div>
        </div>
        <?php }?>
        </div>
        <?php }
        if(!empty($artMag)){ ?>
        <h3 class="section"><span>Articles de magazines</span></h3>
        <div class="list_articles">
        <?php foreach($artMag as $art){?>
        <div class="article greybox_hover">
            <img class="small_cover" alt="couverture" src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $art['ID_REVUE'] ?>/<?= $art['ID_NUMPUBLIE']?>_L61.jpg">
            <div class="meta">
            <div class="title"><a href="magazine<?= $art['details']['REVUE_URL_REWRITING']?>-<?= $art['details']['NUMERO_ANNEE']?>-<?= $art['details']['NUMERO_NUMERO']?>-page-<?= $art['details']['ARTICLE_PAGE_DEBUT']?>.htm"><strong><?= $art['details']['ARTICLE_TITRE']?>
                <?= $art['details']['NUMERO_NUMERO']?>
                <?= $art['details']['NUMERO_ANNEE']?></strong></a>
            </div>
            <div class="authors"><?= getAuteurs($art['details']['ARTICLE_AUTEUR']) ?></div>
            <div class="date"><?= date_format(new DateTime($art['DATE']), 'd/m/Y')?>.</div>
            <div class="prix"><strong><?= $art['PRIX']?> €</strong></div>
            </div>            
        </div>
        <?php } ?>
        </div>
        <?php }?>     
    </div><!-- /col600 -->


</div>