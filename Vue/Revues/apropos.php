<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php
$this->titre = "À propos de " . $revue['TITRE'];
$typePub = 'revue';
include (__DIR__ . '/../CommonBlocs/tabs.php');
?>

<div id="breadcrump">
    <a href="./">Accueil</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="./revue-<?php echo $revue["URL_REWRITING"]; ?>.htm">Revue</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="./en-savoir-plus-sur-la-revue-<?php echo $revue["URL_REWRITING"]; ?>.htm">À propos de <?php echo $revue["TITRE"]; ?></a>
</div>

<div id="body-content">
    <div id="page_revue">
        <?php
        $modeIndex = 'apropos';
        require_once 'Vue/Revues/Blocs/indexRevue.php';
        ?>
        <hr class="grey">
        <div id="about_revue">
            <?php echo $revue["SAVOIR_PLUS2"]; ?>
        </div>
    </div>
</div>

