<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php
$this->titre = "About " . $revue['TITRE'];
$typePub = 'revue';
include (__DIR__ . '/../CommonBlocs/tabs.php');
?>

<div id="breadcrump">
    <a href="./">Home</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="disc-<?= $curDiscipline?>.htm"><?= $filterDiscipline?></a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="./journal-<?php echo $revue["URL_REWRITING_EN"]; ?>.htm">Journal</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="./about-the-journal-<?php echo $revue["URL_REWRITING_EN"]; ?>.htm">About <?php echo $revue["TITRE"]; ?></a>
</div>

<div id="body-content">
    <div id="page_revue">
        <?php
        $modeIndex = 'apropos';
        require_once __DIR__.'/Blocs/indexRevue.php';
        ?>
        <hr class="grey">
        <section id="about_revue" class="desc Clearfix desc-about-journal">
            <div><h3>Publisher</h3></div>
            <article>
                    <p><strong><?php echo $revue["NOM_EDITEUR"]; ?></strong></p>
            </article>
        </section>
        <div id="about_revue">
            <?php echo $revue["SAVOIR_PLUS2_EN"]; ?>
        </div>
    </div>
</div>

