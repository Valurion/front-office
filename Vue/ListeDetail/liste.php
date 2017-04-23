<?php
$this->titre = 'Liste des ' . $type;

if ($type == 'revues') {
    $script = 'listerev.php';
    $prefix = 'revue-';
} else if ($type == 'collections') {
    $script = 'collections.php';
    $prefix = 'collection-';
}
$toAdd = array();
$toAddTitres = array();
foreach ($revues as $row) {
    if(!in_array($row['ID_REVUE'], $toAddTitres)){
        $toAdd[] = $row;
        $toAddTitres[] = $row['ID_REVUE'];
    }
}
$typePub = ($type == "revues" ? 'revue' : 'ouvrage');
include (__DIR__ . '/../CommonBlocs/tabs.php');
?>
<div id="breadcrump">
    <a href="./">Accueil</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="./<?php echo $script; ?>">Liste des <?php echo $type; ?></a>
</div>

<div id="body-content">
    <div class="disciplineSwitcher boxHome">
        <form id="disciplineSwitcher" method="get" action="./<?php echo $script; ?>">
            <label for="editeur"><?php echo ucfirst($type); ?> de </label>&nbsp;
            <select onchange="this.form.submit()" id="editeur" name="editeur">
                <option value="">tous éditeurs</option>
                <?php
                foreach ($editeurs as $editeur) {
                    echo '<option ' . (($editeur["EDITEUR_ID_EDITEUR"] == $currentEditeur) ? "selected" : "") . ' value="' . $editeur["EDITEUR_ID_EDITEUR"] . '">' . $editeur["EDITEUR_NOM_EDITEUR"] . '</option>';
                }
                ?>
            </select>
        </form>
    </div>

    <div class="boxHome borderTop listerev">
        <h1 class="main-title">Les <?php echo $type; ?> (<?php echo count($toAdd); ?>)</h1>
        <?php
        $arrayForList = $toAdd;
        $arrayFieldsToDisplay = array();
        $currentPage = 'liste';
        if ($currentEditeur == '') {
            array_push($arrayFieldsToDisplay, 'NOM_EDITEUR');
        }
        $prefix = '';
        include (__DIR__ . '/../CommonBlocs/liste_2col.php');
        ?>
    </div>

    <?php include (__DIR__ . "/../CommonBlocs/invisible.php"); ?>

    <div class="CB"></div>
</div>


<?php
$this->javascripts[] = <<<'EOD'
    $(campaigns.shs_03_2014);
EOD;
?>
