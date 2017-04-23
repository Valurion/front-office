<?php
/*
 * Quelques petits calculs...
 */

//Libellé "Pages x - y" en gérant le fait qu'il n'y aie qu'une page voire pas du tout
$libPage = ($currentArticle["ARTICLE_PAGE_DEBUT"] > 0 ? "Page" . ($currentArticle["ARTICLE_PAGE_FIN"] > 0 ? 's ' : ' ') . $currentArticle["ARTICLE_PAGE_DEBUT"] : '')
        . ($currentArticle["ARTICLE_PAGE_FIN"] > 0 ? (' - ' . $currentArticle["ARTICLE_PAGE_FIN"]) : '');

//Article précédent et suivant
for ($ind = 0; $ind < count($articles) && $articles[$ind]['ARTICLE_ID_ARTICLE'] != $currentArticle['ARTICLE_ID_ARTICLE']; $ind++) {    
}

$previousArticle = FALSE;
$modePrev = null;
$indPrev = $ind;
while($indPrev > 0 && $previousArticle == FALSE){
    $indPrev--;
    $candidat = $articles[$indPrev];
    if($candidat['LISTE_CONFIG_ARTICLE'][2] != ''){
        $previousArticle = $candidat;
        $modePrev = 'A';
    }else if($candidat['LISTE_CONFIG_ARTICLE'][0] != ''){
        $previousArticle = $candidat;
        $modePrev = 'R';
    }
}

$nextArticle = FALSE;
$modeNext = null;
while($ind != count($articles) - 1 && $nextArticle == FALSE){
    $ind++;
    $candidat = $articles[$ind];
    if($candidat['LISTE_CONFIG_ARTICLE'][2] != ''){
        $nextArticle = $candidat;
        $modeNext = 'A';
    }else if($candidat['LISTE_CONFIG_ARTICLE'][0] != ''){
        $nextArticle = $candidat;
        $modeNext = 'R';
    }
}

?>

<div class="article_navpages">
    <?php if ($previousArticle !== FALSE) { ?>
        <a class="left blue_button" href="./<?= $modePrev=='A'?'article':'abstract'?>-<?php echo $previousArticle['ARTICLE_ID_ARTICLE']; ?>--<?php echo $previousArticle['ARTICLE_URL_REWRITING_EN']; ?>.htm">
            <span class="icon-arrow-white-left icon"></span> Previous
        </a>
    <?php } ?>
    <span class="current_page">
        <?php echo $libPage; ?>
    </span>
    <?php if ($nextArticle !== FALSE) { ?>
        <a class="right blue_button" href="./<?= $modeNext=='A'?'article':'abstract'?>-<?php echo $nextArticle['ARTICLE_ID_ARTICLE']; ?>--<?php echo $nextArticle['ARTICLE_URL_REWRITING_EN']; ?>.htm">
            Next <span class="icon-arrow-white-right icon"></span>
        </a>
    <?php } ?>
</div>