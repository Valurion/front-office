<?php
$this->titre = "Ma bibliographie";
include (__DIR__ . '/../CommonBlocs/tabs.php');
?>
<div id="body-content">
    <div class="biblio" id="free_text">
        <br>
        <div class="list_articles">
            <h1 class="main-title">Ma bibliographie</h1>

            <? /* Fenêtre modale pour l'envoi de bibliographie par mail */ ?>
            <div style="display: none;" class="window_modal" id="modal_mail">
                <div class="basic_modal">
                    <span onclick="cairn.close_modal();" class="close_modal"></span>
                    <h1>Envoyer par email.</h1>
                    <p style="font-size: 0.85em;">
                        Votre bibliographie sera envoyée par e-mail à votre
                        correspondant, accompagnée si vous le souhaitez d'un commentaire
                        de votre choix. Les coordonnées que vous indiquez dans cette page
                        ne sont pas conservées et sont à usage unique.
                    </p>
                    <br>

                    <form method="post" action="javascript:ajax.sendBiblioMail()" name="inscription" id="inscription">
                        <input id="biblioList" type="hidden" value="<?= $biblioList ?>" name="biblio">

                        <div class="wrapper">
                            <div class="blue_milk left w45">
                                <label for="userNames">
                                    Vos prénom et nom
                                    <span class="red">*</span>
                                </label>
                                <span class="flash"></span>
                                <?php
                                    if(!empty($authInfos['U'])){
                                        echo '<input id="userNames" type="text" value="'.$authInfos["U"]["PRENOM"] . ' ' . $authInfos['U']['NOM'].'" name="NOM" required="required" class="textInput">';
                                    }else{
                                        echo '<input id="userNames" type="text" value="" name="NOM" required="required" class="textInput">';
                                    }
                                ?>
                            </div>

                            <div class="blue_milk right w45">
                                <label for="emailUser"> Adresse e-mail de votre correspondant <span class="red">*</span>
                                </label> <span class="flash"></span> <input id="emailUser" type="email" name="email" required="required" class="textInput">

                            </div>
                        </div>
                        <div class="blue_milk" style="width: 97%; margin-top: 1em;">
                            <label for="commentaire"> Commentaire <span class="red">*</span> <br>
                            </label>
                            <textarea id="commentaire" style="" name="COMMENT" cols="40" rows="5" class="textInput custom_textarea_bm" required="required"></textarea>
                        </div>
                        <div style="overflow: hidden;" class="mt2">
                            <input type="submit" value="Envoyer" class="blue_button submit_button right">
                        </div>
                    </form>
                </div>
            </div>

            <div class="center link_font">
                <a target="_home" href="biblio_p.php" style="width: 4.5em;" class="link_custom_generic">Imprimer</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="#" onclick="cairn.show_modal('#modal_mail')" style="width: 4.5em;" class="link_custom_generic">Envoyer</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span class="boutonbib">Exporter vers</span> <span class="boitebib">&nbsp;&nbsp;</span>
                <select onchange="changeselect();" size="1" class="boitesel" name="outil" id="outil">&nbsp;&nbsp;
                    <option value="0">...</option>
                    <option value="REF">Refworks</option>
                    <option value="END">EndNote</option>
                    <option value="ZOT">Zotero</option>
                </select>
            </div>
            <form style="display: inline" action="biblio.php" name="FLINK" id="FLINK" method="post">
                <input type="hidden" value="http://www.refworks.com/express/ExpressImport.asp?vendor=Cairn&amp;filter=Refworks%20Tagged%20Format&amp;encoding=65001&amp;url=<?= urlencode(Configuration::get('refworks')) ?>%3FID_ARTICLE%3D<?= $biblioList ?>" id="LINKREFWORK" name="LINKREFWORK">
                <input type="hidden" value="<?= Configuration::get('endnote') ?>?ID_ARTICLE=<?= $biblioList ?>" id="LINKENDNOTE" name="LINKENDNOTE">
                <input type="hidden" value="<?= Configuration::get('zotero') ?>?ID_ARTICLE=<?= $biblioList ?>" id="LINKZOT" name="LINKZOT">
                <input type="hidden" value="ADDNOTE" name="OPERATE">
            </form>

            <br> <br>
            <?php if (!empty($numOuv)) { ?>
                <h2 class="section">
                    <span>Ouvrages</span>
                </h2>
                <?php
                $arrayForList = $numOuv;
                $arrayFieldsToDisplay = array('ID', 'COLL_TITLE', 'BIBLIO_AUTEURS', 'STATE_OUV', 'REMOVE_BIBLIO');
                include (__DIR__ . '/../CommonBlocs/liste_1col.php');
            }
            ?>
            <?php if (!empty($artOuv)) { ?>
                <br>
                <h2 class="section">
                    <span>Contributions d’ouvrages</span>
                </h2>
                <?php
                $arrayForList = $artOuv;
                $currentPage = 'contrib';
                $arrayFieldsToDisplay = array('ID', 'NUMERO_TITLE', 'BIBLIO_AUTEURS', 'STATE', 'REMOVE_BIBLIO');
                include (__DIR__ . '/../CommonBlocs/liste_1col.php');
            }
            ?>

            <?php if (!empty($artRev)) { ?>
                <br>
                <h2 class="section">
                    <span>Articles de revues</span>
                </h2>

                <?php
                $arrayForList = $artRev;
                $currentPage = 'contrib';
                $arrayFieldsToDisplay = array('ID', 'REVUE_TITLE', 'BIBLIO_AUTEURS', 'STATE', 'REMOVE_BIBLIO');
                include (__DIR__ . '/../CommonBlocs/liste_1col.php');
            }
            ?>

            <?php if (!empty($numRev)) { ?>
                <br>
                <h2 class="section">
                    <span>Numéros de revues</span>
                </h2>
    <?php
        $arrayForList = $numRev;
        $currentPage = 'numero';
        $arrayFieldsToDisplay = array('ID', 'REVUE_TITLE', 'BIBLIO_AUTEURS', 'STATE_OUV', 'REMOVE_BIBLIO');
        include (__DIR__ . '/../CommonBlocs/liste_1col.php');
    }
    ?>

    <?php if (!empty($artMag)) { ?>

    <br>
    <h2 class="section">
        <span>Articles de magazines</span>
    </h2>

    <?php
        $arrayForList = $artMag;
        $currentPage = 'contrib';
        $arrayFieldsToDisplay = array('ID', 'REVUE_TITLE', 'BIBLIO_AUTEURS', 'STATE', 'REMOVE_BIBLIO');
        include (__DIR__ . '/../CommonBlocs/liste_1col.php');
    }
    ?>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../CommonBlocs/invisible.php'; ?>



<?php

$this->javascripts[] = <<<'EOD'
    function changeselect() {
        var lasel = document.getElementById('outil');
        switch (lasel.options[lasel.selectedIndex].value)
        {
            case 'REF':
                javascript:window.open(document.getElementById('LINKREFWORK').value);
                break;
            case 'END':
                javascript:document.location.href = document.getElementById('LINKENDNOTE').value;
                break;
            case 'ZOT':
                javascript:document.location.href = document.getElementById('LINKZOT').value;
                break;
        }
    }
EOD;

?>
