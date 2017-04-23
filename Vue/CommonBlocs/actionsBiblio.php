<?php
function checkBiblio($idRevue,$idNumPublie,$idArticle,$authInfos,$action = null){
    $status = 0; // 0 = pas dedans; 1 = dedans
    if($idArticle == null){
        if(isset($authInfos['U'])
                && isset($authInfos['U']['HISTO_JSON']->biblio)
                && in_array($idNumPublie,$authInfos['U']['HISTO_JSON']->biblio)){
            $status = 1;
        }else if(isset($authInfos['G'])
                && isset($authInfos['G']['HISTO_JSON']->biblio)
                && in_array($idNumPublie,$authInfos['G']['HISTO_JSON']->biblio)){
            $status = 1;
        }
    }else{
        if(isset($authInfos['U'])
                && isset($authInfos['U']['HISTO_JSON']->biblio)
                && in_array($idArticle,$authInfos['U']['HISTO_JSON']->biblio)){
            $status = 1;
        }else if(isset($authInfos['G'])
                && isset($authInfos['G']['HISTO_JSON']->biblio)
                && in_array($idArticle,$authInfos['G']['HISTO_JSON']->biblio)){
            $status = 1;
        }
    }

    if($action == 'usermenu'){
        if($status == 0){?>
            <li id="addToBiblio<?= $idNumPublie ?>-<?= $idArticle ?>">
                <a
                    class="icon icon-usermenu-tools-bigger-char"
                    data-tooltip="Ajouter à ma bibliographie"
                    href="javascript:ajax.addToBiblio('<?= $idNumPublie ?>','<?= $idArticle ?>')"
                    data-webtrends="addToBiblio"
                    data-id_article="<?= $idArticle ?>"
                ></a>
            </li>
            <li id="removeFromBiblio<?= $idNumPublie ?>-<?= $idArticle ?>" style="display:none;">
                <a
                    class="icon icon-usermenu-tools-lower-chr"
                    data-tooltip="Supprimer de ma bibliographie"
                    href="javascript:ajax.removeFromBiblio('<?= $idNumPublie ?>','<?= $idArticle ?>')"
                ></a>
            </li>
        <?php }else{ ?>
            <li id="addToBiblio<?= $idNumPublie ?>-<?= $idArticle ?>" style="display:none;">
                <a
                    class="icon icon-usermenu-tools-bigger-char"
                    data-tooltip="Ajouter à ma bibliographie"
                    href="javascript:ajax.addToBiblio('<?= $idNumPublie ?>','<?= $idArticle ?>')"
                    data-webtrends="addToBiblio"
                    data-id_article="<?= $idArticle ?>"
                ></a>
            </li>
            <li id="removeFromBiblio<?= $idNumPublie ?>-<?= $idArticle ?>">
                <a
                    class="icon icon-usermenu-tools-lower-chr"
                    data-tooltip="Supprimer de ma bibliographie"
                    href="javascript:ajax.removeFromBiblio('<?= $idNumPublie ?>','<?= $idArticle ?>')"
                ></a>
            </li>
        <?php }
    }else if($action == 'remove'){?>
        <span class="AJBIB">
            <input type="image" class="icon del-panier" src="static/images/del.png" alt="Supprimer de votre sélection" onclick="ajax.removeFromBiblioPage('<?= $idNumPublie ?>','<?= $idArticle ?>')">
        </span>
    <?php }else{
        if($status == 0){ ?>
            <a
                id="addToBiblio<?= $idNumPublie ?>-<?= $idArticle ?>"
                href="javascript:ajax.addToBiblio('<?= $idNumPublie ?>','<?= $idArticle ?>')"
                class="icon icon-add-biblio"
                data-tooltip="Ajouter à ma bibliographie"
                data-webtrends="addToBiblio"
                data-id_article="<?= $idArticle ?>"
            ></a>
            <?php if($idArticle == null){ ?>
                <span style='display:none;' id='removeFromBiblio<?= $idNumPublie ?>-<?= $idArticle ?>' class="infoajout"><a class="icon icon-remove-biblio" href="./biblio.php" data-tooltip="Supprimer de ma bibliographie"></a></span>
            <?php }else{ ?>
                <span style='display:none;' id='removeFromBiblio<?= $idNumPublie ?>-<?= $idArticle ?>' class="infoajout">Ajout&eacute; &agrave; <a href="./biblio.php" class="yellow"><strong>ma bibliographie</strong></a></span>
            <?php }
        }else{
            if($idArticle == null){ ?>
                <a class="icon icon-remove-biblio" href="./biblio.php"></a>
            <?php }else{ ?>
                <span class="infoajout">Ajout&eacute; &agrave; <a href="./biblio.php" class="yellow"><strong>ma bibliographie</strong></a></span>
            <?php }
        }
    }
}
?>
