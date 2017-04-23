<?php
$this->titre = "My list of articles";
?>
<div id="body-content">
    <div class="biblio" id="free_text">
        <br>
        <div class="list_articles">
            <h1 class="main-title">My list of articles</h1>

            <div style="display: none;" class="window_modal" id="modal_mail">
                <div class="basic_modal">
                    <h1>Send by mail.</h1>
                    <p>
                        Your bibliography will be sent by email to your correspondent, accompanied, if you wish to comment on your choices. The contact details you enter on this page are not saved and are for single use only.
                    </p>
                    <br>

                    <form method="post" action="javascript:ajax.sendBiblioMail()" name="inscription" id="inscription">
                        <input id="biblioList" type="hidden" value="<?= $biblioList ?>" name="biblio">

                        <div class="wrapper">
                            <div class="blue_milk left w45">
                                <label for="nom">
                                    Your name and surname
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
                                <label for="prenom"> Your email address <span class="red">*</span>
                                </label> <span class="flash"></span> <input id="emailUser" type="email" name="email" required="required" class="textInput">

                            </div>
                        </div>
                        <br>
                        <div class="blue_milk w80">
                            <label for="nom"> Comment <span class="red">*</span> <br>
                                <textarea id="commentaire" style="" name="COMMENT" cols="40" rows="10" class="textInput custom_textarea_bm" required="required"></textarea>
                            </label>
                        </div>
                        <br> <br> <input type="submit" value="Envoyer" class="blue_button submit_button right">
                    </form>
                    <br>
                    <button onclick="cairn.close_modal();" class="button-blue">Close</button>
                </div>
            </div>

            <div class="center link_font">
                <a target="_home" href="biblio_p.php" style="width: 4.5em;" class="link_custom_generic">Print</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="#" onclick="cairn.show_modal('#modal_mail')" style="width: 4.5em;" class="link_custom_generic">Send</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span class="boutonbib">Export to</span> <span class="boitebib">&nbsp;&nbsp;</span>
                <select onchange="changeselect();" size="1" class="boitesel" name="outil" id="outil">&nbsp;&nbsp;
                    <option value="0">...</option>
                    <option value="REF">Refworks</option>
                    <option value="END">EndNote</option>
                    <option value="ZOT">Zotero</option>
                </select>
            </div>
            <form style="display: inline" action="biblio.php" name="FLINK" id="FLINK" method="post">
                <input type="hidden" value="http://www.refworks.com/express/ExpressImport.asp?vendor=Cairn&amp;filter=Refworks%20Tagged%20Format&amp;encoding=65001&amp;url=http%3A//dedi.cairn.info/refworks/export.php%3FID_ARTICLE%3D<?= $biblioList ?>" id="LINKREFWORK" name="LINKREFWORK">
                <input type="hidden" value="http://dedi.cairn.info/endnote/expen.php?ID_ARTICLE=<?= $biblioList ?>" id="LINKENDNOTE" name="LINKENDNOTE">
                <input type="hidden" value="http://dedi.cairn.info/endnote/expenZ.php?ID_ARTICLE=<?= $biblioList ?>" id="LINKZOT" name="LINKZOT">
                <input type="hidden" value="ADDNOTE" name="OPERATE">
            </form>

            <br> <br>

            <?php if (!empty($artRev)) { ?>
                <br>
                <h2 class="section">
                    <span>Journal articles</span>
                </h2>

                <?php
                $arrayForList = $artRev;
                $currentPage = 'contrib';
                $arrayFieldsToDisplay = array('ID', 'NUMERO_TITLE', 'BIBLIO_AUTEURS', 'STATE_INTER', 'REMOVE_BIBLIO');
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
