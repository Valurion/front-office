<?php

$ParseDatas = Service::get('ParseDatas');
/**
 * Converts all accent characters to ASCII characters.
 *
 * If there are no accent characters, then the string given is just returned.
 *
 * @param string $string Text that might have accent characters
 * @return string Filtered string with replaced "nice" characters.
 */


function formatrewriting($chaine) {
    //les accents
    //$chaine=  strtolower(trim($chaine));

    $chaine = remove_accents($chaine);
    $chaine = strtolower(trim($chaine));
    //les caracètres spéciaux (aures que lettres et chiffres en fait)
    $chaine = preg_replace('/([^.a-z0-9]+)/i', '-', $chaine);
    if (substr($chaine, 0, 6) == 'revue-')
        $chaine = substr($chaine, 6);
    return $chaine;
}

/**
 * Convertit un tableau en metadonnées coins
 * On utilise une balise span invisible avec une classe Z3988 auquel on fournit en attribut `title` les métadonnées sous la forme d'une query GET encodées correctement en encodage-pourcentage
 * Voir http://ocoins.info/
 *
 * TODO:: Vérifier toutes les métadonnées disponible
 *      Je n'ai fais que remplacer/corriger les balises non inteprétés déjà existante. Et je me suis rendu compte que les noms des métadonnées envoyés avant la refonte étaient fausse pour la plupart (joie...)
 *      Il faut aussi insérer les coins là où ils ne sont pas encore. Par exemple, les articles de revues
 */
function arrayToCoins($array, $type='article') {
    $array = array_map('trim', $array);
    $array = array_filter($array);
    $array['ctx_ver'] = 'Z39.88-2004';
    $array['rft_val_fmt'] = ($type === 'book') ? 'info:ofi/fmt:kev:mtx:book' : 'info:ofi/fmt:kev:mtx:journal';
    $query = http_build_query($array, null, '&', PHP_QUERY_RFC3986);
    return '<span class="Z3988" title="'.$query.'"></span>';
}

?>

<style>
    .pth_gray
    {
        background-color: graytext;
    }
    .contexte b {
        background-color:yellow;
    }

    #associated_keywords {
        margin-bottom : 1em;
    }
    #associated_keywords li {
        display : inline-block;
    }
    #associated_keywords .white_button {
        padding : 0 0.2em;
    }


</style>
<?php

$this->javascripts[] = <<<'EOD'
    var facettes = {"dp": "filter_year", "id_r": "filter_revue", "dr": "filter_disc", "tp": "filter_type"};
    $(document).ready(function() {

        $(".checkme").change(function() {
            $("input:checkbox", $(this).parent().parent()).prop('checked', $(this).prop("checked"));
        });
        $('form', $('#filter_search')).bind('submit', function() {


            for (var k in advanceJson) {
                console.log(k, advanceJson[k]);
                $(this).append($('<input>', {
                    type: 'hidden',
                    name: k,
                    value: advanceJson[k]
                }))
            }
            ;
            for (var k in facettes)
            {
                var node = $(this);
                var currId = $(node).attr("id");
                if (!(currId == facettes[k]))
                    $(this).append($('#' + k + '_hidden'));
            }

            for (var k in facettesJson) {
                // on ajoute l'historique des facettes, celle de la première requête

                var node = $(this);
                var currId = $(node).attr("id");
                if (!(currId == facettes[k]))
                {


                    for (var t in facettesJson[k])
                    {

                        $(this).append($('<input>', {
                            type: 'hidden',
                            name: k + '[]',
                            value: facettesJson[k][t]
                        }))
                    }
                    ;
                }

            }
            return true;
        });
        /*
         $('#allfacettes').bind('submit', function(e, data) {
         var formElement = document.getElementById("filter_type");
         formData = new FormData(formElement);
         console.log(formData);
         $('form', $('#filter_search')).each(function(index2) {
         $('input[type=hidden],input:checked', $(this)).each(function(index) {
         console.log(index + ": " + $(this).prop("tagName") + '::' + $(this).attr("name") + '::' + $(this).val());
         $('#allfacettes').append($('<input>', {
         type: 'hidden',
         name: $(this).attr("name"),
         value: $(this).val()
         }));
         });
         });
         return true;
         });

         */

    });
    function cairn_search_deploy_pertinent_articles(idNumPublie, node)
    {
        if (idNumPublie != "0")
        {
            /*
            $.get("index.php?controleur=Recherche&action=pertinent&searchTerm=" + $('#searchTerm').val() + "&ID_NUMPUBLIE=" + idNumPublie, function(data) {
                $('#__pertinent_' + idNumPublie).html(data);
                $('#__pertinent_' + idNumPublie).slideToggle(400);
            });
            */

            var fd = new FormData();
            fd.append('ID_NUMPUBLIE',idNumPublie);
            if($("#searchTerm").val() !='')
            {
             fd.append('searchTerm',$("#searchTerm").val());
            }
             fd.append('ID_NUMPUBLIE',idNumPublie);

            for (var k in advanceJson) {
                console.log(k, advanceJson[k]);
                fd.append(k, advanceJson[k]);
            }
            /*
            for (var k in facettes)
            {
                $('#allfacettes').append($('#' + k + '_hidden'));
            }*/

            for (var k in facettesJson) {
                {
                    for (var t in facettesJson[k])
                    {
                        fd.append(k+'[]', facettesJson[k][t]);

                    }

                }

            }

            $.ajax({
                url: 'index.php?controleur=Recherche&action=pertinent',
                data: fd,
                processData: false,
                contentType: false,
                type: 'POST',
                success: function(data) {

                    //alert(data);
                    $('#__pertinent_' + idNumPublie).html(data);
                    $('#__pertinent_' + idNumPublie).slideToggle(400);
                }
            });

            /*
             $.post("index.php?controleur=Recherche&action=pertinent",
             {
             name: "Donald Duck",
             city: "Duckburg"
             },
             function(data, status) {
             alert("Data: " + data + "\nStatus: " + status);
             });
             */


            $(node).attr("onclick", "$(this).toggleClass('active');cairn_search_toggle_pertinent_articles('" + idNumPublie + "')");

        }
        else
            $('#__pertinent_' + idNumPublie).slideToggle(400);
    }

    function cairn_search_toggle_pertinent_articles(idNumPublie)
    {
        $('#__pertinent_' + idNumPublie).slideToggle(400);
    }

    function cairn_search_modify(node)
    {
        $(node).attr("onclick", "$('#wrapper_modify_search').slideToggle(400);");
        {
            $('#wrapper_modify_search').slideToggle(400);
            $.get("index.php?controleur=Recherche&action=getAjaxAdvancedForm", function(data) {

                $('#wrapper_modify_search').html(data);
            });
        }

    }


    function filter_online_since() {

        // we pick the date range
        var dateRange = $('#filter_online-since').val();


        $('#allfacettes').append($('<input>', {
            type: 'hidden',
            name: 'periode',
            value: dateRange
        }));

        for (var k in advanceJson) {
            if (!(k == 'periode'))
                $('#allfacettes').append($('<input>', {
                    type: 'hidden',
                    name: k,
                    value: advanceJson[k]
                }))
        }
        ;
        for (var k in facettes)
        {
            $('#allfacettes').append($('#' + k + '_hidden'));
        }

        for (var k in facettesJson) {
            // on ajoute l'historique des facettes, celle de la première requête


            {


                for (var t in facettesJson[k])
                {

                    $('#allfacettes').append($('<input>', {
                        type: 'hidden',
                        name: k + '[]',
                        value: facettesJson[k][t]
                    }))
                }
                ;
            }

        }

        // on ajoute les avancées

        $('#allfacettes').submit();


    }

    function move(LIMIT) {

        /*   var formElement = document.getElementById("filter_type");
         $('form', $('#filter_search')).each(function(index2) {
         $('input[type=hidden],input:checked', $(this)).each(function(index) {
         console.log(index + ": " + $(this).prop("tagName") + '::' + $(this).attr("name") + '::' + $(this).val());
         $('#allfacettes').append($('<input>', {
         type: 'hidden',
         name: $(this).attr("name"),
         value: $(this).val()
         }));
         });
         });*/
        $('#allfacettes').append("<input type='hidden' name='START' value='" + LIMIT + "' />");
        for (var k in advanceJson) {
            console.log(k, advanceJson[k]);
            $('#allfacettes').append($('<input>', {
                type: 'hidden',
                name: k,
                value: advanceJson[k]
            }))
        }
        ;
        for (var k in facettes)
        {
            $('#allfacettes').append($('#' + k + '_hidden'));
        }

        for (var k in facettesJson) {
            {
                for (var t in facettesJson[k])
                {

                    $('#allfacettes').append($('<input>', {
                        type: 'hidden',
                        name: k + '[]',
                        value: facettesJson[k][t]
                    }))
                }
                ;
            }

        }

        // on ajoute les avancées

        $('#allfacettes').submit();
    }

    //Modification WebTrends, Envoi des tags.
    function searchTermes(node) {
        window.wtCairn.sendEvent({
            'action': 'clicOnRelatedSearches'
        });
        var conc = $("#searchTerm").val() + " \"" + $(node).text() + "\"";
        $('#compute_search_field').val(conc);
        $('#main_search_form').submit();
    }
    window.JSON || document.write('<script src="http://cdnjs.cloudflare.com/ajax/libs/json3/3.2.4/json3.min.js"><\/script>');
EOD;
?>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//var_dump($results);
?>
<?php $this->titre = "Résultats de recherche"; ?>
<div id="body-content" class="searchResult">
    <div id="search_header">
        <form method="POST" name="mel2" id="mel2" class="left">
            <input name="OPERATE" value="T" type="hidden">
            <input type="hidden" name="TRI" value="DMEL">
            <input type="hidden" name="periode">
            <!-- Le select ci-dessous est "transformé" par le script override_select.js. Il récupère les ids et classe, et les préfixe par un alter_ -->
            <span id="label_filter_online-since">En ligne depuis</span>
            <select id="filter_online-since" onchange="filter_online_since()" name="periode">
                <option value="ALL" <?php if ($periode == 'ALL') echo ' selected '; ?> >toujours</option>
                <option value="SEM" <?php if ($periode == 'SEM') echo ' selected '; ?>>Une semaine</option>
                <option value="MOIS" <?php if ($periode == 'MOIS') echo ' selected '; ?>>Un mois</option>
                <option value="DEUX" <?php if ($periode == 'DEUX') echo ' selected '; ?>>Deux mois</option>
                <option value="SIX" <?php if ($periode == 'SIX') echo ' selected '; ?>>Six mois</option>
            </select>
            <?php
                $this->javascripts[] = "var advanceJson = $advancedJson;";
                $this->javascripts[] = "var facettesJson = $facettesJson;";
                $this->javascripts[] = <<<'EOD'
                    // A utiliser avec le script override_select.js.
                    // Cette fonction ajoute dynamiquement une fonction à l'attribut onclick de chaque option.
                    $(function() {
                        $('#filter_online-since').children('option').attr('onclick',
                                '$("#alter_filter_online-since .container-option").hide();'
                                );
                    });
                    //alert(JSON.stringify(advanceJson));
EOD;
            ?>
        </form>
        <h1>Résultats de recherche</h1>
        <a href="<?php echo '/resultats_recherche.php?'.$_SERVER[QUERY_STRING].'&format=rss'; ?>" onclick="rssrech();">
<!--                 <span class="icon-rss icon" style="margin-left:0.6em;"></span> -->
            <img src="./static/images/icon/rss-grey.png" alt="rss logo grey" style="margin-left:0.6em; vertical-align:top;">
        </a>
        <span id="trigger_filtering" class="search_button right" style="margin-left:1.5em">Préciser</span>

        <a  class="search_button right" onclick="cairn_search_modify(this);"  style="margin-left:1.5em">Modifier</a>

    </div>
    <div id="search_navbar">
        <span>&nbsp;</span>
        <span style="float:left;">Votre recherche :</span>
        <span class="title_little_blue" style="margin-left:0.5em; font-size:17px; float:left;">
            <span id="librechav" style="float:left; width:640px; max-height:35px; overflow:hidden;"><?= str_replace('~','',htmlentities($searchTerm)) . $TRA ?></span>
        </span>
        <span style="margin-left:0.5em" class="right"><?php
        //echo number_format((int) $stats->TotalFiles,0,'.',' ') . " (" .  number_format((int) ((int) $stats->TotalFiles + (int) $stats->rejected),0,'.',' ') . " ) résultat";
        echo number_format((int) $stats->TotalFiles,0,'.',' ') . " résultat";
            if ($stats->TotalFiles > 1)
                echo "s ";
            ?></span>
    </div>
    <?php if(isset($evidensse) && !empty($evidensse)){?>
        <div id="search_navbar">
            <div class="state right">
                <input type="image" class="icon del-panier" src="static/images/del.png" alt="Supprimer la configuration" onclick="ajax.clearRechConfig()">
            </div>
        <span>Avec la configuration spécifique :</span><?php foreach($evidensse as $key => $value){ echo '<br/>'.$key.': <span class="title_little_blue">'.str_replace(',',', ',$value).'</span>'; } ?>
        </div>
    <?php } ?>
    <div id="wrapper_modify_search" style="display:none">

    </div>
    <div id="wrapper_filter_search" style="display:none">
        <span class="close" onclick="$('#wrapper_filter_search').toggle();">
            <img src="./static/images/icon/lightbox-close.png">
        </span>
        <div id="filter_search" class="filter_search" >




            <?php
            foreach ($hiddenFacettes as $hiddenFacetteK => $hiddenFacetteV) {
                echo "<input type='hidden' id='$hiddenFacetteK" . "_hidden'  name='$hiddenFacetteK" . "_hidden' value='$hiddenFacetteV'  >\n";
            }
            ?>



            <?php foreach ($facettes as $key => $value): ?>

                <form id="filter_<?php
                switch ($key) {
                    case 'Disciplines': $dsp = 'disc';
                        break;
                    case 'Types': $dsp = 'type';
                        break;
                    case 'Revues/collect.': $dsp = 'revue';
                        break;
                    case 'Dates de parution': $dsp = 'year';
                        break;
                } echo $dsp;
                ?>"  action='resultats_recherche.php?searchTerm=<?php echo urlencode($searchTerm); ?>' method="post">

                    <?php if ($key == 'Types') : ?>
                        <div id="associated_keywords"><h2>Termes associés</h2>
                            <ul>
                                <?php foreach ($concepts as $concept) : ?>
                                    <li><a  onclick="searchTermes(this);" class="white_button"> <?= $concept ?> </a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php
                    echo "<input type='hidden' name='searchTerm' value=\"" . htmlspecialchars($searchTerm) . "\" />\n";
                    echo "<input type='hidden' name='" . $label2facette[$key] . "_hidden' value='" . $hiddenFacettes[$label2facette[$key]] . "'  >\n";
                    ?>
                    <h2><?= $key ?></h2>
                    <ul>
                        <li><input type="checkbox"  name="p" class="checkme" id="bt_<?= $label2facette[$key] ?>" value="Tout cocher/décocher" />
                            <label for="bt_<?= $label2facette[$key] ?>">Tout cocher/décocher</label></li>


                        <?php foreach ($value as $key1 => $value1): ?>
                            <li>
                                <input type="checkbox" checked id="<?= $label2facette[$key] . '_' . $value1['mkey'] ?>" name='<?= $label2facette[$key] ?>[]' value="<?= $value1['mkey'] ?>" />
                                <?php if (!($dsp == 'revue')) : ?>
                                <label for="<?= $label2facette[$key] . '_' . $value1['mkey'] ?>"><?= $key1 . "(" . number_format($value1['nb'],0,'.',' ') . ')' ?></label><br/>
                                <?php else : ?>
                                    <label for="<?= $label2facette[$key] . '_' . $value1['mkey'] ?>"><?= $facettesRevues[$key1] . "(" . number_format($value1['nb'],0,'.',' ') . ')' ?></label><br/>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                        <?php
                        $missing = false;
                        foreach (explode(',', $hiddenFacettes[$label2facette[$key]]) as $hiddenFacetteV) {
                            $namef = $label2facette[$key];
                            if ($key == 'Disciplines') {
                                if (!(isset($facettes[$key])) || !(isset($facettes[$key][(isset($disciplines[$hiddenFacetteV])?$disciplines[$hiddenFacetteV]:'')]))) {
                                    $missing = true;
                                    echo "<li><input type='checkbox' id='$namef" . '_' . "$hiddenFacetteV'  name='$namef" . "[]' value='$hiddenFacetteV'  /><label for='$namef" . '_' . "$hiddenFacetteV'>" . (isset($disciplines[$hiddenFacetteV])?$disciplines[$hiddenFacetteV]:'Sans discipline') . "</li></label>\n";
                                }
                            } elseif ($key == 'Types') {
                                if (!(isset($facettes[$key][$typepub[$hiddenFacetteV]]))) {
                                    echo "<li><input type='checkbox'  id='$namef" . '_' . "$hiddenFacetteV'  name='$namef" . "[]' value='$hiddenFacetteV'  /><label for='$namef" . '_' . "$hiddenFacetteV'>" . $typepub[$hiddenFacetteV] . "</li>\n";
                                    $missing = true;
                                }
                            } elseif ($key == 'Revues/collect.') {
                                if (!(isset($facettes[$key][$hiddenFacetteV]))) {
                                    echo "<li><input type='checkbox'  id='$namef" . '_' . "$hiddenFacetteV'  name='$namef" . "[]' value='$hiddenFacetteV'  /><label for='$namef" . '_' . "$hiddenFacetteV'>" . $facettesRevues[$hiddenFacetteV] . "</li></label>\n";
                                    $missing = true;
                                }
                            } elseif ($key == 'Dates de parution')
                                if (!(isset($facettes[$key][$hiddenFacetteV]))) {
                                    echo "<li><input type='checkbox'  id='$namef" . '_' . "$hiddenFacetteV'  name='$namef" . "[]' value='" . str_replace('avant ', '~~', $hiddenFacetteV) . "'  /><label for='$namef" . '_' . "$hiddenFacetteV'>" . $hiddenFacetteV . "</label></li>\n";
                                    $missing = true;
                                }
                        }
                        ?>
                    </ul>
                    <br/>
                    <?php if (!$missing) : ?>
                        <?php
                            $this->javascripts[] = '$("#bt_' . $label2facette[$key] . '").attr("checked", "checked")';
                        ?>
                    <?php endif; ?>
                    <input type="submit" name="refine<?= $label2facette[$key] ?>" value="Rafraîchir" class="refresh"/>
                </form>


            <?php endforeach; ?>
          <!--  <input  type="button" name="refine" value="Tout rafraîchir"  onclick="$('#allfacettes').submit();"/>-->
        </div>
    </div>
    <div >
        <form style="display:none" id="allfacettes"  action="index.php?controleur=Recherche" method="post">
            <?php echo "<input id=\"searchTerm\" type='hidden' name='searchTerm' value=\"" . htmlspecialchars($searchTerm) . "\" />\n"; ?>
        </form>
    </div>





    <div class="results_list list_articles">

        <?php foreach ($results as $result) : ?>
            <?php
            //recup variables
            if (isset($result->item->packed) && (int) $result->item->packed == '1') {
                $pack = 1;
            } else {
                $pack = 0;
            }
            if ((int) $result->userFields->tp == 3) {
                $offset = (int) $result->userFields->tp + 2 * (int) $result->userFields->tnp;
            } else {
                $offset = (int) $result->userFields->tp;
            }
            $typePubTitle = $typeDocument[$pack][$offset];
            $typePub = $result->userFields->tp;
            $typeNumPublie = $result->userFields->tnp;
            $ARTICLE_ID_ARTICLE = $result->userFields->id;
            $ARTICLE_ID_REVUE = $result->userFields->id_r;
            $NUMERO_ID_REVUE = $ARTICLE_ID_REVUE;
            $ARTICLE_PRIX = $result->userFields->px;

            $ARTICLE_ID_NUMPUBLIE = $result->userFields->np;
            $NUMERO_ID_NUMPUBLIE = $ARTICLE_ID_NUMPUBLIE;
            $ARTICLE_TITRE = $result->userFields->tr;
            $NUMERO_TITRE = $result->userFields->titnum;
            $NUMERO_SOUS_TITRE = $metaNumero[$NUMERO_ID_NUMPUBLIE]['SOUS_TITRE'];
            $ARTICLE_HREF = "revue-" . $metaNumero[$NUMERO_ID_NUMPUBLIE]['REVUE_URL_REWRITING'] . '-' . $result->userFields->an . '-' . $result->userFields->NUM0 . '-page-' . $result->userFields->pgd . ".htm";

            //$dateParution=$metaNumero[$NUMERO_ID_NUMPUBLIE]['DATE_PARUTION'];
            //echo"<p>$dateParution</p>";
            $REVUE_ID = $result->userFields->id_r;

            //Correctif WebTrends, pour les auteurs.
            $auteurTemp = $result->userFields->auth0;
            $result->userFields->auth0 = '';
            foreach(explode('|', $auteurTemp) as $auteur) {
                list($vide ,$nom, $prenom, $id) = explode('#', $auteur);
                $result->userFields->auth0 .= '#' . $prenom . '#' . $nom . '#' . $id . '|';
            }
            $result->userFields->auth0 = trim($result->userFields->auth0, '|');
            //Fin du correctif webTrends des auteurs.

            $authors = explode('|', $result->userFields->auth0);
            $NUMERO_ANNEE = $result->userFields->an;
            $NUMERO_NUMERO = $result->userFields->NUM0;
            $NUMERO_VOLUME = $result->userFields->vol;
            $ARTICLE_PAGE = $result->userFields->pgd;
            if(isset($result->userFields->idp)){
                $PORTAIL = $result->userFields->idp;
            }
            //echo $result->item->hits;
            $arrHits = explode(' ',$result->item->hits);
            $arrHits = array_slice($arrHits,(count($arrHits)-250));
            $hitsStr = implode(' ',$arrHits);
            //echo '<br/>'.$hitsStr;
            $getDocUrlParameters = '&DocId=' . $result->item->docId . '&hits=' . urlencode($hitsStr);
            //$getDocUrlParameters = '&DocId=' . $result->item->docId . '&hits=' . urlencode($result->item->hits);
            $isPdf = (stripos($result->item->Filename, '.pdf') > 0);



            $NOM_EDITEUR = $metaNumero[$NUMERO_ID_NUMPUBLIE]['NOM_EDITEUR'];
            $REVUE_TITRE = $result->userFields->rev0;
            $cfgaArr = explode(',', $result->userFields->cfg0);

            if($metaNumero[$NUMERO_ID_NUMPUBLIE]['MEMO'] != '' && strlen($metaNumero[$NUMERO_ID_NUMPUBLIE]['MEMO'])){
                $NUMERO_MEMO = substr($metaNumero[$NUMERO_ID_NUMPUBLIE]['MEMO'], 0, strpos($metaNumero[$NUMERO_ID_NUMPUBLIE]['MEMO'], " ", 200));
            }else{
                $NUMERO_MEMO = $metaNumero[$NUMERO_ID_NUMPUBLIE]['MEMO'];
            }
            $NUMERO_MEMO = strip_tags($NUMERO_MEMO);
            $CONTEXTE = strip_tags($result->item->Synopsis, '<b>');

            $ARTICLE_HREF = '';
            $NUMERO_HREF = '';
            $REVUE_HREF = "";
            switch ($typePub) {
                case "1":
                    $ARTICLE_HREF = "./article.php?ID_ARTICLE=$ARTICLE_ID_ARTICLE" . $getDocUrlParameters;
                    $NUMERO_HREF = "./revue-" . $metaNumero[$NUMERO_ID_NUMPUBLIE]['REVUE_URL_REWRITING'] . "-$NUMERO_ANNEE-$NUMERO_NUMERO.htm";
                    $REVUE_HREF;
                    $REVUE_HREF = "./revue-" . $metaNumero[$NUMERO_ID_NUMPUBLIE]['REVUE_URL_REWRITING'] . ".htm";
                    break;
                case "2":
                    $ARTICLE_HREF = "./magazine-" . $metaNumero[$NUMERO_ID_NUMPUBLIE]['REVUE_URL_REWRITING'] . "-" . $NUMERO_ANNEE . "-" . $NUMERO_NUMERO . "-page-" . $ARTICLE_PAGE . ".htm";
                    $ARTICLE_HREF = "./article.php?ID_ARTICLE=".urlencode($ARTICLE_ID_ARTICLE).$getDocUrlParameters;
                    $NUMERO_HREF = "./magazine-" . $metaNumero[$NUMERO_ID_NUMPUBLIE]['REVUE_URL_REWRITING']. "-$NUMERO_ANNEE-$NUMERO_NUMERO.htm";
                    $REVUE_HREF;
                    $REVUE_HREF = "./magazine-" . $metaNumero[$NUMERO_ID_NUMPUBLIE]['REVUE_URL_REWRITING'] . ".htm";
                    break;
                case "3":

                    $ARTICLE_HREF = "./" . $metaNumero[$NUMERO_ID_NUMPUBLIE]['URL_REWRITING'] . "--" . $metaNumero[$NUMERO_ID_NUMPUBLIE]['ISBN'] . '-page-' . $ARTICLE_PAGE . ".htm";
                    $ARTICLE_HREF = "./article.php?ID_ARTICLE=$ARTICLE_ID_ARTICLE" . $getDocUrlParameters;
                    $NUMERO_HREF = "./" . $metaNumero[$NUMERO_ID_NUMPUBLIE]['URL_REWRITING'] . "--" . $metaNumero[$NUMERO_ID_NUMPUBLIE]['ISBN'] . ".htm";
                    $REVUE_HREF = "./collection-" . $metaNumero[$NUMERO_ID_NUMPUBLIE]['REVUE_URL_REWRITING'] . ".htm";
                    break;

                case "6":

                    $ARTICLE_HREF = "./" . $metaNumero[$NUMERO_ID_NUMPUBLIE]['URL_REWRITING'] . "--" . $metaNumero[$NUMERO_ID_NUMPUBLIE]['ISBN'] . '-page-' . $ARTICLE_PAGE . ".htm";
                    $ARTICLE_HREF = "./article.php?ID_ARTICLE=$ARTICLE_ID_ARTICLE" . $getDocUrlParameters;
                    $NUMERO_HREF = "./" . $metaNumero[$NUMERO_ID_NUMPUBLIE]['URL_REWRITING'] . "--" . $metaNumero[$NUMERO_ID_NUMPUBLIE]['ISBN'] . ".htm";
                    $REVUE_HREF = "./collection-" . $metaNumero[$NUMERO_ID_NUMPUBLIE]['REVUE_URL_REWRITING'] . ".htm";
                    break;
            }


            $BLOC_AUTEUR = '';
            $BLOC_AUTEUR_PACK = '';
            if (sizeof($authors) > 2) {
                $authors2 = explode('#', $authors[0]);
                $BLOC_AUTEUR = "<a class=\"yellow\" href=\"publications-de-" . trim($authors2[1]) . '-' . $authors2[2] . '--' . $authors2[3] . '.htm' . '">' . $authors2[0] . ' ' . $authors2[1] . ' ' . $authors2[2] . "</a> et al.";
            } else if (sizeof($authors) == 2) {
                $authors2 = explode('#', $authors[0]);
                $BLOC_AUTEUR = "<a class=\"yellow\" href=\"publications-de-" . $authors2[1] . '-' . $authors2[2] . '--' . $authors2[3] . '.htm' . '">' . $authors2[0] . ' ' . $authors2[1] . ' ' . $authors2[2] . "</a> et ";
                $authors2 = explode('#', $authors[1]);
                $BLOC_AUTEUR .= "<a class=\"yellow\" href=\"publications-de-" . trim($authors2[1]) . '-' . $authors2[2] . '--' . $authors2[3] . '.htm' . '">' . $authors2[0] . ' ' . $authors2[1] . ' ' . $authors2[2] . "</a> ";
            } else if (sizeof($authors) == 1 && $authors[0] != ' - ') {
                $authors2 = explode('#', $authors[0]);
                $BLOC_AUTEUR = "<a class=\"yellow\" href=\"publications-de-" . $authors2[1] . '-' . $authors2[2] . '--' . $authors2[3] . '.htm' . '">' . $authors2[0] . ' ' . $authors2[1] . ' ' . $authors2[2] . "</a>";
            }

            $BLOC_AUTEUR = trim($BLOC_AUTEUR);

            if($BLOC_AUTEUR != ''){
                $BLOC_AUTEUR_PACK = $BLOC_AUTEUR;
            }else{
                $numeroAuteurs = $metaNumero[$NUMERO_ID_NUMPUBLIE]['NUMERO_AUTEUR'];
                $authors = explode('|',$numeroAuteurs);
                if (sizeof($authors) > 2) {
                    $authors2 = explode(':', $authors[0]);
                    $BLOC_AUTEUR_PACK = "<a class=\"yellow\" href=\"publications-de-" . trim($authors2[1]) . '-' . $authors2[0] . '--' . $authors2[2] . '.htm' . '">' . $authors2[0] . ' ' . $authors2[1] . "</a> et al.";
                } else if (sizeof($authors) == 2) {
                    $authors2 = explode(':', $authors[0]);
                    $BLOC_AUTEUR_PACK = "<a class=\"yellow\" href=\"publications-de-" . $authors2[1] . '-' . $authors2[0] . '--' . $authors2[2] . '.htm' . '">' . $authors2[0] . ' ' . $authors2[1] . "</a> et ";
                    $authors2 = explode(':', $authors[1]);
                    $BLOC_AUTEUR_PACK .= "<a class=\"yellow\" href=\"publications-de-" . trim($authors2[1]) . '-' . $authors2[0] . '--' . $authors2[2] . '.htm' . '">' . $authors2[0] . ' ' . $authors2[1] . "</a> ";
                } else if (sizeof($authors) == 1 && $authors[0] != ' - ') {
                    $authors2 = explode(':', $authors[0]);
                    $BLOC_AUTEUR_PACK = "<a class=\"yellow\" href=\"publications-de-" . $authors2[1] . '-' . $authors2[0] . '--' . $authors2[2] . '.htm' . '">' . $authors2[0] . ' ' . $authors2[1] . "</a>";
                }
                $BLOC_AUTEUR_PACK = trim($BLOC_AUTEUR_PACK);
            }
            //if($BLOC_AUTEUR == '- ')
            //  $BLOC_AUTEUR='';
            ?>

            <?php if ($typePub == 6) : ?>
                <?php if (!$pack) : ?>
                    <!-- RECHERCHE D'ENCYCLOPÉDIE DE POCHE -->

                    <div class="result article encyclopedie" id="<?= $ARTICLE_ID_ARTICLE ?>">
                        <h2><?= $typePubTitle ?> </h2>
                        <div class="wrapper_meta">
                            <a href="<?= $ARTICLE_HREF ?>">
                                <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $ARTICLE_ID_REVUE ?>/<?= $ARTICLE_ID_NUMPUBLIE ?>_L61.jpg" alt="" class="small_cover"/>
                            </a>
                            <div class="meta">
                                <a href="<?= $ARTICLE_HREF ?>">
                                    <div class="title"><strong><?= $ARTICLE_TITRE ?></strong></div>
                                </a>
                                <div class="authors">
                                    <span class="author">
                                        <?= $BLOC_AUTEUR ?>
                                    </span>
                                </div>
                                <div class="revue_title">Dans <a href="<?= $NUMERO_HREF ?>" class="title_little_blue"><span class="title_little_blue"><?= $NUMERO_TITRE ?></span></a> <strong><?= $NOM_EDITEUR ?>, (<?= $NUMERO_ANNEE ?>)</strong></div>
                            </div>
                        </div>
                        <div class="contexte"><?= $CONTEXTE ?></div>
                        <!--
                        <div class="state">
                            [LISTE_CONFIG_ARTICLE]
                            <a href="[ARTICLE_LIBELLE_HREF]" class="button" [BLOC_CONFIG_ARTICLE_PDF] onclick="javascript:trackPDFViewer('[ARTICLE_ID_ARTICLE]', '[ARTICLE_TITRE_JVSCT]', '[BLOC_AUTEURS][AUTEUR_PRENOM_JVSCT] [AUTEUR_NOM_JVSCT][BLOC_DEUX];[AUTEUR_PRENOM_JVSCT] [AUTEUR_NOM_JVSCT][/BLOC_DEUX][/BLOC_AUTEURS]');" [/BLOC_CONFIG_ARTICLE_PDF]>
                               [ARTICLE_LIBELLE_LIBELLE]
                        </a>
                        [/LISTE_CONFIG_ARTICLE]

                        [BLOC_CREDIT_INST]
                        [BLOC_ARTICLE_ACHAT]
                        <a href="mes_demandes.php?ID_ARTICLE=[ARTICLE_ID_ARTICLE]" class="button">Demander cet article</a>
                        [/BLOC_ARTICLE_ACHAT]
                        [/BLOC_CREDIT_INST]

                        [BLOC_CAIRN_INST_ACHAT]
                        [BLOC_CREDIT_INST_OFF]
                        [BLOC_ARTICLE_ACHAT]
                        <a href="mon_panier.php?ID_ARTICLE=[ARTICLE_ID_ARTICLE]" class="wrapper_buttons_add-to-cart">
                            <span class="button first">Consulter</span>
                            <span class="icon icon-add-to-cart"></span>
                            <span class="button last">[ARTICLE_PRIX] €</span>
                        </a>
                        [/BLOC_ARTICLE_ACHAT]
                        [/BLOC_CREDIT_INST_OFF]
                        [/BLOC_CAIRN_INST_ACHAT]

                        [BLOC_ARTICLE_BIBLIO_AJOUT_ON]
                        <a href="[URL]&amp;AJOUTBIBLIO=[ARTICLE_ID_ARTICLE]#[ARTICLE_ID_ARTICLE]" class="icon icon-add-biblio">&#160;</a>
                        [/BLOC_ARTICLE_BIBLIO_AJOUT_ON]
                        [BLOC_ARTICLE_BIBLIO_AJOUT_OFF]
                        <span class="infoajout">Ajout&eacute; &agrave; <a href="./biblio.php" class="yellow"><strong>ma bibliographie</strong></a> <span class="icon icon-remove-biblio"></span></span>
                        [/BLOC_ARTICLE_BIBLIO_AJOUT_OFF]
                    </div>
                        -->

                <?php
                    $meta = $metaNumero[$NUMERO_ID_NUMPUBLIE];
                    $firstAuthor = explode(':', explode('|', $meta['NUMERO_AUTEUR'])[0]);
                    $coins = [
                        'rft.atitle' => $ARTICLE_TITRE,
                        'rft.jtitle' => $NUMERO_TITRE,
                        'rft.title' => $NUMERO_TITRE,
                        'rft.volume' => preg_replace('/\s*n°\s*/', '', $NUMERO_VOLUME),
                        'rft.issue' => $NUMERO_ANNEE.'/'.$NUMERO_NUMERO,
                        'rft.isbn' => $metaNumero[$NUMERO_ID_NUMPUBLIE]['ISBN'],
                        'rft.aulast' => $firstAuthor[1],
                        'rft.aufirst' => $firstAuthor[0],
                        'rft.au' => $firstAuthor[1] . ' ' .$firstAuthor[0],
                        'rft.pub' => $meta['NOM_EDITEUR'],
                        'rft.date' => $NUMERO_ANNEE,
                        'rft.issn' => null,
                        'rft.eissn' => null,
                        'rft.genre' => 'article',
                    ];
                    echo arrayToCoins($coins);
                ?>
                        <div class="state">

                            <?php if ($cfgaArr[0] > 0) : ?>
                                <a href="resume.php?ID_ARTICLE=<?= $ARTICLE_ID_ARTICLE ?>" class="button">
                                    <?php if ($cfgaArr[0] == 1) echo "Résumé"; else if ($cfgaArr[0] == 2) echo "Première page"; else if ($cfgaArr[0] == 3) echo "Premières lignes"; ?>
                                </a>
                            <?php endif; ?>
                            <?php
                            if (isset($articlesButtons[$ARTICLE_ID_ARTICLE]) && $articlesButtons[$ARTICLE_ID_ARTICLE]['STATUT'] == 1) {
                                ?>
                                <?php if ($cfgaArr[1] > 0) : ?>
                                    <a href="<?= $ARTICLE_HREF ?>" class="button">
                                        Version HTML
                                    </a>
                                <?php endif; ?>
                                <?php if ($cfgaArr[2] > 0) : ?>
                                    <?php if ($isPdf) : ?>
                                        <a href="feuilleter.php?ID_ARTICLE=<?= $ARTICLE_ID_ARTICLE . $getDocUrlParameters ?>&ispdf=<?= $isPdf ?>" class="button">
                                        <?php else: ?>
                                            <a href="feuilleter.php?ID_ARTICLE=<?= $ARTICLE_ID_ARTICLE ?>&searchTerm=<?= urlencode($searchTerm) ?>" class="button">
                                            <?php endif; ?>
                                            Feuilleter en ligne
                                        </a>

                                    <?php endif; ?>

                                    <?php if ($cfgaArr[3] > 0) : ?>
                                        <a
                                            href="load_pdf.php?ID_ARTICLE=<?= $ARTICLE_ID_ARTICLE ?>"
                                            class="button"
                                            data-webtrends="goToPdfArticle"
                                            data-id_article="<?= $ARTICLE_ID_ARTICLE ?>"
                                            data-titre=<?=
                                                $ParseDatas->cleanAttributeString($ARTICLE_TITRE)
                                            ?>
                                            data-authors=<?=
                                                $ParseDatas->cleanAttributeString(
                                                    $ParseDatas->stringifyRawAuthors(
                                                        str_replace(
                                                            '#',
                                                            $ParseDatas::concat_name,
                                                            implode($ParseDatas::concat_authors, $authors)
                                                        ), 0, ';'
                                                    )
                                                )
                                            ?>
                                        >
                                            Version PDF
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($cfgaArr[5] > 0) : ?>
                                        <a href="<?= $portalInfo[$ARTICLE_ID_ARTICLE]["URL_PORTAIL"] ?>" class="button">
                                            <?= $portalInfo[$ARTICLE_ID_ARTICLE]["NOM_PORTAIL"] ?>
                                        </a>
                                        <?php
                                    endif;
                                }else {
                                    if (isset($articlesButtons[$ARTICLE_ID_ARTICLE]) && $articlesButtons[$ARTICLE_ID_ARTICLE]['STATUT'] == 2) {
                                        //WebTrends : "tracking sur les boutons d'ajout au panier"
                                        if (strpos($articlesButtons[$ARTICLE_ID_ARTICLE]['CLASS'], 'wrapper_buttons_add-to-cart') !== false) {
                                            echo '<a href="' . $articlesButtons[$ARTICLE_ID_ARTICLE]['HREF'] . '" '
                                                    . (isset($articlesButtons[$ARTICLE_ID_ARTICLE]['CLASS']) ? ('class="' . $articlesButtons[$ARTICLE_ID_ARTICLE]['CLASS'] . '"') : '')
                                                    . 'data-webtrends="goToMonPanier" '
                                                    . 'data-prix_article="' . number_format($ARTICLE_PRIX, 2, '.', '') . '" '
                                                    . 'data-id_article="' . $ARTICLE_ID_ARTICLE . '" '
                                                    . 'data-titre=' . $ParseDatas->cleanAttributeString($ARTICLE_TITRE) . ' '
                                                    . 'data-authors=' . $ParseDatas->cleanAttributeString(
                                                    $ParseDatas->stringifyRawAuthors(
                                                        str_replace(
                                                            '#',
                                                            $ParseDatas::concat_name,
                                                            implode($ParseDatas::concat_authors, $authors)
                                                        ), 0, ';'
                                                    )) . ' '
                                                    . '>'
                                                    . $articlesButtons[$ARTICLE_ID_ARTICLE]['LIB']
                                                    . '</a>';
                                        } else {
                                            echo '<a href="' . $articlesButtons[$ARTICLE_ID_ARTICLE]['HREF'] . '" ' . (isset($articlesButtons[$ARTICLE_ID_ARTICLE]['CLASS']) ? ('class="' . $articlesButtons[$ARTICLE_ID_ARTICLE]['CLASS'] . '"') : '') . '>' . $articlesButtons[$ARTICLE_ID_ARTICLE]['LIB'] . '</a>';
                                        }
                                    }
                                }
                                require_once(__DIR__ . '/../CommonBlocs/actionsBiblio.php');
                                checkBiblio($ARTICLE_ID_REVUE, $ARTICLE_ID_NUMPUBLIE, $ARTICLE_ID_ARTICLE, $authInfos);
                                ?>
                        </div>
                    </div>

                <?php else: ?>

                    <div class="result article encyclopedie" id="<?= $ARTICLE_ID_ARTICLE ?>">
                        <h2><?= $typePubTitle ?></h2>
                        <div class="wrapper_meta">
                            <a href="<?= $NUMERO_HREF ?>">
                                <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $ARTICLE_ID_REVUE ?>/<?= $ARTICLE_ID_NUMPUBLIE ?>_L61.jpg" alt="" class="small_cover"/>
                            </a>
                            <div class="meta">
                                <div class="revue_title">
                                    <a href="<?= $NUMERO_HREF ?>" class="title_little_blue">
                                        <span class="title_little_blue"><?= $NUMERO_TITRE ?></span>
                                    </a>
                                    <strong>(<?= $NOM_EDITEUR ?>, <?= $NUMERO_ANNEE ?>)</strong>
                                </div>
                                <!-- <a href="resultats_recherche.php?MOV=0&amp;SESS=[NUM_SESSION]&amp;BLOC=[NUMERO_HREF]&amp;ID_REVUE=&amp;ID_NUMPUBLIE=[NUMERO_ID_NUMPUBLIE]"> -->
                                <div class="title">dans <a href="./encyclopedies-de-poche.php?ID_REVUE=<?= $REVUE_ID ?>"><strong><?= $REVUE_TITRE ?></strong></a></div>
                                <!-- </a> -->
                                <div class="authors">
                                    <?= $BLOC_AUTEUR_PACK ?>
                                </div>
                            </div>
                        </div>
                        <div class="contexteMemo"><?= $NUMERO_MEMO ?></div>
                        <div class="state">

                            <a href="<?= $NUMERO_HREF ?>"  class="button">Présentation/Sommaire</a>
                            <a href="#" class="button" onclick="$(this).toggleClass('active');
                                                cairn_search_deploy_pertinent_articles('<?= $NUMERO_ID_NUMPUBLIE ?>', this);">Articles les plus pertinents</a>
                            <?php
                            require_once(__DIR__ . '/../CommonBlocs/actionsBiblio.php');
                            checkBiblio($ARTICLE_ID_REVUE, $ARTICLE_ID_NUMPUBLIE, null, $authInfos);
                            ?>
                        </div>

                        <div class="pertinent_articles" id="__pertinent_<?= $NUMERO_ID_NUMPUBLIE ?>">

                            <div class="meta">
                                <div>
                                    <img src="img/pert_[ARTICLE_PERTI].png" alt="niveau de pertinence évalué à [ARTICLE_PERTI]" class="pertinence"/>
                                    <span class="pages">page [ARTICLE_PAGE_DEBUT] &agrave; [ARTICLE_PAGE_FIN]</span>

                                    <div class="title">[ARTICLE_TITRE][BLOC_ARTICLE_SOUSTITRE]. [ARTICLE_SOUSTITRE][/BLOC_ARTICLE_SOUSTITRE]</div>
                                </div>
                                <div class="contexte">[CONTEXTE]</div>
                                <div class="state">
                                    [LISTE_CONFIG_ARTICLE]
                                    <a href="[ARTICLE_LIBELLE_HREF]" class="button" [BLOC_CONFIG_ARTICLE_PDF] onclick="javascript:trackPDFViewer('[ARTICLE_ID_ARTICLE]', '[ARTICLE_TITRE_JVSCT]', '[BLOC_AUTEURS][AUTEUR_PRENOM_JVSCT] [AUTEUR_NOM_JVSCT][BLOC_DEUX];[AUTEUR_PRENOM_JVSCT] [AUTEUR_NOM_JVSCT][/BLOC_DEUX][/BLOC_AUTEURS]');" [/BLOC_CONFIG_ARTICLE_PDF]>
                                       [ARTICLE_LIBELLE_LIBELLE]
                                </a>
                                [/LISTE_CONFIG_ARTICLE]

                                [BLOC_CREDIT_INST]
                                [BLOC_ARTICLE_ACHAT]
                                <a href="mes_demandes.php?ID_ARTICLE=[ARTICLE_ID_ARTICLE]" class="button">Demander cet article</a>
                                [/BLOC_ARTICLE_ACHAT]
                                [/BLOC_CREDIT_INST]

                                [BLOC_CAIRN_INST_ACHAT]
                                [BLOC_CREDIT_INST_OFF]
                                [BLOC_ARTICLE_ACHAT]
                                <a href="mon_panier.php?ID_ARTICLE=[ARTICLE_ID_ARTICLE]" class="wrapper_buttons_add-to-cart">
                                    <span class="button first">Consulter</span>
                                    <span class="icon icon-add-to-cart"></span>
                                    <span class="button last">[ARTICLE_PRIX] €</span>
                                </a>
                                [/BLOC_ARTICLE_ACHAT]
                                [/BLOC_CREDIT_INST_OFF]
                                [/BLOC_CAIRN_INST_ACHAT]
                            </div>
                        </div>
                        <hr class="grey" />
                    </div>
                </div>

                <!-- FIN DE RECHERCHE D'ENCYCLOPÉDIE DE POCHE -->

            <?php endif; ?>
        <?php endif; ?>







        <?php if ($typePub == 3) : ?>
            <?php if (!$pack) : ?>
                <!-- RECHERCHE D'OUVRAGE -->

                <div class="result article ouvrage" id="<?= $ARTICLE_ID_ARTICLE ?>">
                    <h2><?= $typePubTitle ?></h2>
                    <div class="wrapper_meta">
                        <a href="<?= $ARTICLE_HREF ?>">
                            <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $ARTICLE_ID_REVUE ?>/<?= $ARTICLE_ID_NUMPUBLIE ?>_L61.jpg" alt="" class="small_cover"/>
                        </a>
                        <div class="meta">
                            <a href="<?= $ARTICLE_HREF ?>">
                                <div class="title"><strong><?= $ARTICLE_TITRE ?></strong></div>
                            </a>
                            <div class="authors">

                                <?= $BLOC_AUTEUR ?>

                            </div>
                            <div class="revue_title">Dans <a href="<?= $NUMERO_HREF ?>" class="title_little_blue"><span class="title_little_blue"><?= $NUMERO_TITRE ?><?php if ($NUMERO_SOUS_TITRE != '') echo '. ' . $NUMERO_SOUS_TITRE; ?></span></a>  <strong>(<?= $NOM_EDITEUR ?>, <?= $NUMERO_ANNEE ?>)</strong></div>
                        </div>
                    </div>
                    <div class="contexte"><?= $CONTEXTE ?></div>
                    <!--
                    <div class="state">

                        <a href="[ARTICLE_LIBELLE_HREF]" class="button">
                            [ARTICLE_LIBELLE_LIBELLE]
                        </a>
                        [/LISTE_CONFIG_ARTICLE]

                        [BLOC_CREDIT_INST]
                        [BLOC_ARTICLE_ACHAT]
                        <a href="mes_demandes.php?ID_ARTICLE=[ARTICLE_ID_ARTICLE]" class="button">Demander cet article</a>
                        [/BLOC_ARTICLE_ACHAT]
                        [/BLOC_CREDIT_INST]

                        [BLOC_CAIRN_INST_ACHAT]
                        [BLOC_CREDIT_INST_OFF]
                        [BLOC_ARTICLE_ACHAT]
                        <a href="mon_panier.php?ID_ARTICLE=[ARTICLE_ID_ARTICLE]" class="wrapper_buttons_add-to-cart">
                            <span class="button first">Consulter</span>
                            <span class="icon icon-add-to-cart"></span>
                            <span class="button last">[ARTICLE_PRIX] €</span>
                        </a>
                        [/BLOC_ARTICLE_ACHAT]
                        [/BLOC_CREDIT_INST_OFF]
                        [/BLOC_CAIRN_INST_ACHAT]

                        [BLOC_ARTICLE_BIBLIO_AJOUT_ON]
                        <a href="[URL]&amp;AJOUTBIBLIO=[ARTICLE_ID_ARTICLE]#[ARTICLE_ID_ARTICLE]" class="icon icon-add-biblio">&#160;</a>
                        [/BLOC_ARTICLE_BIBLIO_AJOUT_ON]
                        [BLOC_ARTICLE_BIBLIO_AJOUT_OFF]
                        <span class="infoajout">Ajout&eacute; &agrave; <a href="./biblio.php" class="yellow"><strong>ma bibliographie</strong></a> <span class="icon icon-remove-biblio"></span></span>
                        [/BLOC_ARTICLE_BIBLIO_AJOUT_OFF]
                    </div>
                    -->
                    <div class="state">

                        <?php if ($cfgaArr[0] > 0) : ?>
                            <a href="resume.php?ID_ARTICLE=<?= $ARTICLE_ID_ARTICLE ?>" class="button">
                                <?php if ($cfgaArr[0] == 1) echo "Résumé"; else if ($cfgaArr[0] == 2) echo "Première page"; else if ($cfgaArr[0] == 3) echo "Premières lignes"; ?>
                            </a>
                        <?php endif; ?>
                        <?php
                        if (isset($articlesButtons[$ARTICLE_ID_ARTICLE]) && $articlesButtons[$ARTICLE_ID_ARTICLE]['STATUT'] == 1) {
                            ?>
                            <?php if ($cfgaArr[1] > 0) : ?>
                                <a href="<?= $ARTICLE_HREF ?>" class="button">
                                    Version HTML
                                </a>
                            <?php endif; ?>
                            <?php if ($cfgaArr[2] > 0) : ?>
                                <?php if ($isPdf) : ?>
                                    <a href="feuilleter.php?ID_ARTICLE=<?= $ARTICLE_ID_ARTICLE . $getDocUrlParameters ?>&ispdf=<?= $isPdf ?>" class="button">
                                    <?php else: ?>
                                        <a href="feuilleter.php?ID_ARTICLE=<?= $ARTICLE_ID_ARTICLE ?>&searchTerm=<?= urlencode($searchTerm) ?>" class="button">
                                        <?php endif; ?>
                                        Feuilleter en ligne
                                    </a>

                                <?php endif; ?>

                                <?php if ($cfgaArr[3] > 0) : ?>
                                    <a
                                        href="load_pdf.php?ID_ARTICLE=<?= $ARTICLE_ID_ARTICLE ?>"
                                        class="button"
                                        data-webtrends="goToPdfArticle"
                                        data-id_article="<?= $ARTICLE_ID_ARTICLE ?>"
                                        data-titre=<?=
                                            $ParseDatas->cleanAttributeString($ARTICLE_TITRE)
                                        ?>
                                        data-authors=<?=
                                            $ParseDatas->cleanAttributeString(
                                                $ParseDatas->stringifyRawAuthors(
                                                    str_replace(
                                                        '#',
                                                        $ParseDatas::concat_name,
                                                        implode($ParseDatas::concat_authors, $authors)
                                                    ), 0, ';'
                                                )
                                            )
                                        ?>
                                    >
                                        Version PDF
                                    </a>
                                <?php endif; ?>
                                <?php if ($cfgaArr[5] > 0) : ?>
                                    <a href="<?= $portalInfo[$ARTICLE_ID_ARTICLE]["URL_PORTAIL"] ?>" class="button">
                                        <?= $portalInfo[$ARTICLE_ID_ARTICLE]["NOM_PORTAIL"] ?>
                                    </a>
                                    <?php
                                endif;
                            }else {
                                if (isset($articlesButtons[$ARTICLE_ID_ARTICLE]) && $articlesButtons[$ARTICLE_ID_ARTICLE]['STATUT'] == 2) {
                                    //WebTrends : "tracking sur les boutons d'ajout au panier"
                                    if (strpos($articlesButtons[$ARTICLE_ID_ARTICLE]['CLASS'], 'wrapper_buttons_add-to-cart') !== false) {
                                        echo '<a href="' . $articlesButtons[$ARTICLE_ID_ARTICLE]['HREF'] . '" '
                                            . (isset($articlesButtons[$ARTICLE_ID_ARTICLE]['CLASS']) ? ('class="' . $articlesButtons[$ARTICLE_ID_ARTICLE]['CLASS'] . '"') : '')
                                            . 'data-webtrends="goToMonPanier" '
                                            . 'data-prix_article="' . number_format($ARTICLE_PRIX, 2, '.', '') . '" '
                                            . 'data-id_article="' . $ARTICLE_ID_ARTICLE . '" '
                                            . 'data-titre=' . $ParseDatas->cleanAttributeString($ARTICLE_TITRE) . ' '
                                            . 'data-authors=' . $ParseDatas->cleanAttributeString(
                                            $ParseDatas->stringifyRawAuthors(
                                                str_replace(
                                                    '#',
                                                    $ParseDatas::concat_name,
                                                    implode($ParseDatas::concat_authors, $authors)
                                                ), 0, ';'
                                            )) . ' '
                                            . '>'
                                            . $articlesButtons[$ARTICLE_ID_ARTICLE]['LIB']
                                            . '</a>';
                                    } else {
                                        echo '<a href="' . $articlesButtons[$ARTICLE_ID_ARTICLE]['HREF'] . '" ' . (isset($articlesButtons[$ARTICLE_ID_ARTICLE]['CLASS']) ? ('class="' . $articlesButtons[$ARTICLE_ID_ARTICLE]['CLASS'] . '"') : '') . '>' . $articlesButtons[$ARTICLE_ID_ARTICLE]['LIB'] . '</a>';
                                    }
                                }
                            }
                            require_once(__DIR__ . '/../CommonBlocs/actionsBiblio.php');
                            checkBiblio($ARTICLE_ID_REVUE, $ARTICLE_ID_NUMPUBLIE, $ARTICLE_ID_ARTICLE, $authInfos);
                            ?>
                    </div>

                </div>

            <?php else: ?>

                <div class="result numero ouvrage" id="<?= $NUMERO_ID_NUMPUBLIE ?>">
                    <h2><?= $typePubTitle ?></h2>
                    <div class="wrapper_meta">
                        <a href="<?= $NUMERO_HREF ?>">
                            <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $NUMERO_ID_REVUE ?>/<?= $NUMERO_ID_NUMPUBLIE ?>_L61.jpg" alt="" class="small_cover"/>
                        </a>
                        <div class="meta">
                            <div class="revue_title"><a href="<?= $metaNumero[$NUMERO_ID_NUMPUBLIE]['URL_REWRITING'] ?>--<?= $metaNumero[$NUMERO_ID_NUMPUBLIE]['ISBN'] ?>.htm" class="title_little_blue"><span class="title_little_blue"><?= $NUMERO_TITRE . '. ' . $NUMERO_SOUS_TITRE ?> </span></a> <strong>(<?= $NOM_EDITEUR ?>, <?= $NUMERO_ANNEE ?>)</strong></div>
                            <div class="title">dans
                                <a href="<?= $REVUE_HREF ?>"><strong><?= $REVUE_TITRE ?></strong></a>
                            </div>

                            <div class="authors">
                                <?php if ($typeNumPublie == 1 && trim($BLOC_AUTEUR) <> '') : ?>
                                    Sous la direction de
                                <?php endif; ?>
                                <?= $BLOC_AUTEUR_PACK ?>
                            </div>
                        </div>
                    </div>

                    <div class="contexteMemo"><?= $NUMERO_MEMO ?> ...</div>
                    <div class="state">
                        <a href="<?= $NUMERO_HREF ?>"  class="button">Présentation/Sommaire</a>
                        <a href="#" class="button" onclick="$(this).toggleClass('active');
                                            cairn_search_deploy_pertinent_articles('<?= $NUMERO_ID_NUMPUBLIE ?>', this);">Articles les plus pertinents</a>
                        <?php
                        require_once(__DIR__ . '/../CommonBlocs/actionsBiblio.php');
                        checkBiblio($ARTICLE_ID_REVUE, $ARTICLE_ID_NUMPUBLIE, null, $authInfos);
                        ?>
                    </div>
                    <div class="pertinent_articles" id="__pertinent_<?= $NUMERO_ID_NUMPUBLIE ?>">
                        [LISTE_RESULTAT_ARTICLES_CONTRIB_OUVRAGE]
                        <div class="meta">
                            <div>
                                <img src="img/pert_[ARTICLE_PERTI].png" alt="niveau de pertinence évalué à [ARTICLE_PERTI]" class="pertinence"/>
                                <span class="pages">page [ARTICLE_PAGE_DEBUT] &agrave; [ARTICLE_PAGE_FIN]</span>
                                <div class="title">[ARTICLE_TITRE][BLOC_ARTICLE_SOUSTITRE]. [ARTICLE_SOUSTITRE][/BLOC_ARTICLE_SOUSTITRE]</div>
                            </div>
                            [BLOC_NUMERO_TYPE_NUMPUBLIE_1]
                            <div class="authors">
                                [BLOC_AUTEURS][AUTEUR_PRENOM] [AUTEUR_NOM][BLOC_PLUSDEDEUX] <em>et al.</em>[/BLOC_PLUSDEDEUX]
                                [BLOC_DEUX] et [AUTEUR_PRENOM] [AUTEUR_NOM][/BLOC_DEUX][/BLOC_AUTEURS]
                            </div>
                            [/BLOC_NUMERO_TYPE_NUMPUBLIE_1]
                            <div class="contexte">[CONTEXTE]</div>
                            <div class="state">
                                [LISTE_CONFIG_ARTICLE]
                                <a href="[ARTICLE_LIBELLE_HREF]" class="button" [BLOC_CONFIG_ARTICLE_PDF] onclick="javascript:trackPDFViewer('[ARTICLE_ID_ARTICLE]', '[ARTICLE_TITRE_JVSCT]', '[BLOC_AUTEURS][AUTEUR_PRENOM_JVSCT] [AUTEUR_NOM_JVSCT][BLOC_DEUX];[AUTEUR_PRENOM_JVSCT] [AUTEUR_NOM_JVSCT][/BLOC_DEUX][/BLOC_AUTEURS]');" [/BLOC_CONFIG_ARTICLE_PDF]>
                                   [ARTICLE_LIBELLE_LIBELLE]
                            </a>
                            [/LISTE_CONFIG_ARTICLE]

                            [BLOC_CREDIT_INST]
                            [BLOC_ARTICLE_ACHAT]
                            <a href="mes_demandes.php?ID_ARTICLE=[ARTICLE_ID_ARTICLE]" class="button">Demander cet article</a>
                            [/BLOC_ARTICLE_ACHAT]
                            [/BLOC_CREDIT_INST]

                            [BLOC_CAIRN_INST_ACHAT]
                            [BLOC_CREDIT_INST_OFF]
                            [BLOC_ARTICLE_ACHAT]
                            <a href="mon_panier.php?ID_ARTICLE=[ARTICLE_ID_ARTICLE]" class="wrapper_buttons_add-to-cart">
                                <span class="button first">Consulter</span>
                                <span class="icon icon-add-to-cart"></span>
                                <span class="button last">[ARTICLE_PRIX] €</span>
                            </a>
                            [/BLOC_ARTICLE_ACHAT]
                            [/BLOC_CREDIT_INST_OFF]
                            [/BLOC_CAIRN_INST_ACHAT]
                        </div>
                    </div>
                    <hr class="grey" />
                    [/LISTE_RESULTAT_ARTICLES_CONTRIB_OUVRAGE]
                </div>

                <?php
                    $meta = $metaNumero[$NUMERO_ID_NUMPUBLIE];
                    $firstAuthor = explode(':', explode('|', $meta['NUMERO_AUTEUR'])[0]);
                    $coins = [
                        'rft.btitle' => $NUMERO_TITRE,
                        'rft.title' => $NUMERO_TITRE,
                        'rft.isbn' => $metaNumero[$NUMERO_ID_NUMPUBLIE]['ISBN'],
                        'rft.aulast' => $firstAuthor[1],
                        'rft.aufirst' => $firstAuthor[0],
                        'rft.au' => $firstAuthor[1] . ' ' .$firstAuthor[0],
                        'rft.pub' => $meta['NOM_EDITEUR'],
                        'rft.date' => $NUMERO_ANNEE,
                        'rft.issn' => null,
                        'rft.genre' => 'book',
                    ];
                    echo arrayToCoins($coins, 'book');
                ?>
            </div>

            <!-- FIN DE RECHERCHE D'OUVRAGE -->

        <?php endif; ?>
    <?php endif; ?>



    <?php if ($typePub == 1) : ?>
        <?php if (!$pack) : ?>
            <!-- RECHERCHE DE REVUE -->

            <div class="result article revue" id="<?= $ARTICLE_ID_ARTICLE ?>">
                <h2><?= $typePubTitle ?></h2>
                <div class="wrapper_meta">
                    <a href="<?= $ARTICLE_HREF ?>">
                        <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $ARTICLE_ID_REVUE ?>/<?= $ARTICLE_ID_NUMPUBLIE ?>_L61.jpg" alt="" class="small_cover"/>
                    </a>
                    <div class="meta">
                        <div class="title"><a href="<?= $ARTICLE_HREF ?>"><strong><?= $ARTICLE_TITRE ?></strong></a></div>
                        <div class="authors">
                            <?= $BLOC_AUTEUR ?>
                        </div>
                        <div class="revue_title">Dans <a href="<?= $REVUE_HREF ?>" class="title_little_blue"><span class="title_little_blue"><?= $REVUE_TITRE ?></span></a> <strong><?= $NUMERO_ANNEE ?>/<?= $NUMERO_NUMERO ?> (<?= $NUMERO_VOLUME ?>)</strong></div>
                    </div>
                </div>
                <div class="contexte"><?= $CONTEXTE ?></div>
                <div class="state">
                    <!--
                        [LISTE_CONFIG_ARTICLE]
                        <a href="[ARTICLE_LIBELLE_HREF]" class="button">
                            [ARTICLE_LIBELLE_LIBELLE]
                        </a>
                        [/LISTE_CONFIG_ARTICLE]

                        [BLOC_CREDIT_INST]
                        [BLOC_ARTICLE_ACHAT]
                        <a href="mes_demandes.php?ID_ARTICLE=[ARTICLE_ID_ARTICLE]" class="button">Demander cet article</a>
                        [/BLOC_ARTICLE_ACHAT]
                        [/BLOC_CREDIT_INST]

                        [BLOC_CAIRN_INST_ACHAT]
                        [BLOC_CREDIT_INST_OFF]
                        [BLOC_ARTICLE_ACHAT]
                        <a href="mon_panier.php?ID_ARTICLE=[ARTICLE_ID_ARTICLE]" class="wrapper_buttons_add-to-cart">
                            <span class="button first">Consulter</span>
                            <span class="icon icon-add-to-cart"></span>
                            <span class="button last"><?= $ARTICLE_PRIX ?> €</span>
                        </a>
                        [/BLOC_ARTICLE_ACHAT]
                        [/BLOC_CREDIT_INST_OFF]
                        [/BLOC_CAIRN_INST_ACHAT]


                    <a href="<?= '$URL_BIBLIO' ?>" class="icon icon-add-biblio">&#160;</a>

                    [BLOC_ARTICLE_BIBLIO_AJOUT_OFF]
                    <span class="infoajout">Ajout&eacute; &agrave; <a href="./biblio.php" class="yellow"><strong>ma bibliographie</strong></a> <span class="icon icon-remove-biblio"></span></span>
                    [/BLOC_ARTICLE_BIBLIO_AJOUT_OFF]
                    -->

                </div>
                <div class="state">

                    <?php if ($cfgaArr[0] > 0) : ?>
                        <a href="resume.php?ID_ARTICLE=<?= $ARTICLE_ID_ARTICLE ?>" class="button">
                            <?php if ($cfgaArr[0] == 1) echo "Résumé"; else if ($cfgaArr[0] == 2) echo "Première page"; else if ($cfgaArr[0] == 3) echo "Premières lignes"; ?>
                        </a>
                    <?php endif; ?>
                    <?php
                    if (isset($articlesButtons[$ARTICLE_ID_ARTICLE]) && $articlesButtons[$ARTICLE_ID_ARTICLE]['STATUT'] == 1) {
                        ?>
                        <?php if ($cfgaArr[1] > 0) : ?>
                            <a href="<?= $ARTICLE_HREF ?>" class="button">
                                Version HTML
                            </a>
                        <?php endif; ?>
                        <?php if ($cfgaArr[2] > 0) : ?>
                            <?php if ($isPdf) : ?>
                                <a href="feuilleter.php?ID_ARTICLE=<?= $ARTICLE_ID_ARTICLE . $getDocUrlParameters ?>&ispdf=<?= $isPdf ?>" class="button">
                                <?php else: ?>
                                    <a href="feuilleter.php?ID_ARTICLE=<?= $ARTICLE_ID_ARTICLE ?>&searchTerm=<?= urlencode($searchTerm) ?>" class="button">
                                    <?php endif; ?>
                                    Feuilleter en ligne
                                </a>

                            <?php endif; ?>

                            <?php if ($cfgaArr[3] > 0) : ?>
                                <a
                                    href="load_pdf.php?ID_ARTICLE=<?= $ARTICLE_ID_ARTICLE ?>"
                                    class="button"
                                    data-webtrends="goToPdfArticle"
                                    data-id_article="<?= $ARTICLE_ID_ARTICLE ?>"
                                    data-titre=<?=
                                        $ParseDatas->cleanAttributeString($ARTICLE_TITRE)
                                    ?>
                                    data-authors=<?=
                                        $ParseDatas->cleanAttributeString(
                                            $ParseDatas->stringifyRawAuthors(
                                                str_replace(
                                                    '#',
                                                    $ParseDatas::concat_name,
                                                    implode($ParseDatas::concat_authors, $authors)
                                                ), 0, ';'
                                            )
                                        )
                                    ?>
                                >
                                    Version PDF
                                </a>
                            <?php endif; ?>

                            <!-- Partie webTrends, lien : "Consulter sur Revues.org" -->
                            <?php if (count($cfgaArr) > 5 && $cfgaArr[5] > 0) : ?>
                                <a href="<?= $portalInfo[$ARTICLE_ID_ARTICLE]["URL_PORTAIL"] ?>" class="button"
                                   data-webtrends="goToRevues.org"
                                   data-id_article="<?= $ARTICLE_ID_ARTICLE ?>"
                                   data-titre=<?= $ParseDatas->cleanAttributeString($ARTICLE_TITRE) ?>
                                   data-authors=<?=
                                        $ParseDatas->cleanAttributeString(
                                            $ParseDatas->stringifyRawAuthors(
                                                str_replace(
                                                    '#',
                                                    $ParseDatas::concat_name,
                                                    implode($ParseDatas::concat_authors, $authors)
                                                ), 0, ';'
                                            )
                                        )
                                    ?>
                                   >
                                    <?= $portalInfo[$ARTICLE_ID_ARTICLE]["NOM_PORTAIL"] ?>
                                </a>
                                <?php
                            endif;
                        }else {
                            if (isset($articlesButtons[$ARTICLE_ID_ARTICLE]) && $articlesButtons[$ARTICLE_ID_ARTICLE]['STATUT'] == 2) {
                                //WebTrends : "tracking sur les boutons d'ajout au panier"
                                if (strpos($articlesButtons[$ARTICLE_ID_ARTICLE]['CLASS'], 'wrapper_buttons_add-to-cart') !== false) {
                                    echo '<a href="' . $articlesButtons[$ARTICLE_ID_ARTICLE]['HREF'] . '" '
                                            . (isset($articlesButtons[$ARTICLE_ID_ARTICLE]['CLASS']) ? ('class="' . $articlesButtons[$ARTICLE_ID_ARTICLE]['CLASS'] . '"') : '')
                                            . 'data-webtrends="goToMonPanier" '
                                            . 'data-prix_article="' . number_format($ARTICLE_PRIX, 2, '.', '') . '" '
                                            . 'data-id_article="' . $ARTICLE_ID_ARTICLE . '" '
                                            . 'data-titre=' . $ParseDatas->cleanAttributeString($ARTICLE_TITRE)  . ' '
                                            . 'data-authors=' . $ParseDatas->cleanAttributeString(
                                            $ParseDatas->stringifyRawAuthors(
                                                str_replace(
                                                    '#',
                                                    $ParseDatas::concat_name,
                                                    implode($ParseDatas::concat_authors, $authors)
                                                ), 0, ';'
                                            )) . ' '
                                            . '>'
                                            . $articlesButtons[$ARTICLE_ID_ARTICLE]['LIB']
                                            . '</a>';
                                } else {
                                    echo '<a href="' . $articlesButtons[$ARTICLE_ID_ARTICLE]['HREF'] . '" ' . (isset($articlesButtons[$ARTICLE_ID_ARTICLE]['CLASS']) ? ('class="' . $articlesButtons[$ARTICLE_ID_ARTICLE]['CLASS'] . '"') : '') . '>' . $articlesButtons[$ARTICLE_ID_ARTICLE]['LIB'] . '</a>';
                                }
                            }
                        }
                        require_once(__DIR__ . '/../CommonBlocs/actionsBiblio.php');
                        checkBiblio($ARTICLE_ID_REVUE, $ARTICLE_ID_NUMPUBLIE, $ARTICLE_ID_ARTICLE, $authInfos);
                        ?>

                </div>
            </div>

        <?php else: ?>

            <div class="result numero revue" id="<?= $NUMERO_ID_NUMPUBLIE ?>">
                <h2><?= $typePubTitle ?></h2>
                <div class="wrapper_contexte wrapper_meta">
                    <a href="<?= $NUMERO_HREF ?>">
                        <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $ARTICLE_ID_REVUE ?>/<?= $ARTICLE_ID_NUMPUBLIE ?>_L61.jpg" alt="" class="small_cover"/>
                    </a>
                    <div class="meta">
                        <div class="revue_title">
                            <a href="<?= $REVUE_HREF ?>" class="title_little_blue"><span class="title_little_blue"><?= $REVUE_TITRE ?></span></a>
                            <strong><?= $NUMERO_ANNEE ?>/<?= $NUMERO_NUMERO ?> (<?= $NUMERO_VOLUME ?>)</strong>
                        </div>
                        <div class="numero_title">
                            <a href="<?= $NUMERO_HREF ?>">
                                <strong><?= $NUMERO_TITRE ?><?php if (trim($NUMERO_SOUS_TITRE) != '') echo ". $NUMERO_SOUS_TITRE"; ?></strong>
                            </a>
                        </div>
                        <div class="authors">

                        </div>
                    </div>
                    <div class="contexteMemo"><?= $NUMERO_MEMO ?></div>
                </div>

                <div class="state">
                    <a href="<?= $NUMERO_HREF ?>" class="button">Présentation/Sommaire</a>
                    <a href="#" class="button" onclick="$(this).toggleClass('active');
                                        cairn_search_deploy_pertinent_articles('<?= $NUMERO_ID_NUMPUBLIE ?>', this);">Articles les plus pertinents</a>
                    <?php
                    require_once(__DIR__ . '/../CommonBlocs/actionsBiblio.php');
                    checkBiblio($ARTICLE_ID_REVUE, $ARTICLE_ID_NUMPUBLIE, null, $authInfos);
                    ?>
                </div>
                <div class="pertinent_articles" id="__pertinent_<?= $NUMERO_ID_NUMPUBLIE ?>">

                    <div class="meta">
                        <div>
                            <img src="img/pert_[ARTICLE_PERTI].png" alt="niveau de pertinence évalué à [ARTICLE_PERTI]" class="pertinence"/>
                            <span class="pages">page [ARTICLE_PAGE_DEBUT] &agrave; [ARTICLE_PAGE_FIN]</span>
                            <div class="title">[ARTICLE_TITRE][BLOC_ARTICLE_SOUSTITRE]. [ARTICLE_SOUSTITRE][/BLOC_ARTICLE_SOUSTITRE]</div>
                        </div>
                        <div class="authors">
                            [BLOC_AUTEURS][AUTEUR_PRENOM] [AUTEUR_NOM][BLOC_PLUSDEDEUX] <em>et al.</em>[/BLOC_PLUSDEDEUX]
                            [BLOC_DEUX] et [AUTEUR_PRENOM] [AUTEUR_NOM][/BLOC_DEUX][/BLOC_AUTEURS]
                        </div>
                        <div class="contexte">[CONTEXTE]</div>
                        <div class="state">
                            [LISTE_CONFIG_ARTICLE]
                            <a href="[ARTICLE_LIBELLE_HREF]" class="button" [BLOC_CONFIG_ARTICLE_PDF] onclick="javascript:trackPDFViewer('[ARTICLE_ID_ARTICLE]', '[ARTICLE_TITRE_JVSCT]', '[BLOC_AUTEURS][AUTEUR_PRENOM_JVSCT] [AUTEUR_NOM_JVSCT][BLOC_DEUX];[AUTEUR_PRENOM_JVSCT] [AUTEUR_NOM_JVSCT][/BLOC_DEUX][/BLOC_AUTEURS]');" [/BLOC_CONFIG_ARTICLE_PDF]>
                               [ARTICLE_LIBELLE_LIBELLE]
                        </a>
                        [/LISTE_CONFIG_ARTICLE]

                        [BLOC_CREDIT_INST]
                        [BLOC_ARTICLE_ACHAT]
                        <a href="mes_demandes.php?ID_ARTICLE=[ARTICLE_ID_ARTICLE]" class="button">Demander cet article</a>
                        [/BLOC_ARTICLE_ACHAT]
                        [/BLOC_CREDIT_INST]

                        [BLOC_CAIRN_INST_ACHAT]
                        [BLOC_CREDIT_INST_OFF]
                        [BLOC_ARTICLE_ACHAT]
                        <a href="mon_panier.php?ID_ARTICLE=[ARTICLE_ID_ARTICLE]" class="wrapper_buttons_add-to-cart">
                            <span class="button first">Consulter</span>
                            <span class="icon icon-add-to-cart"></span>
                            <span class="button last">[ARTICLE_PRIX] €</span>
                        </a>
                        [/BLOC_ARTICLE_ACHAT]
                        [/BLOC_CREDIT_INST_OFF]
                        [/BLOC_CAIRN_INST_ACHAT]
                    </div>
                </div>
                <hr class="grey" />

            </div>
                <?php
                    $meta = $metaNumero[$NUMERO_ID_NUMPUBLIE];
                    $firstAuthor = explode(':', explode('|', $meta['NUMERO_AUTEUR'])[0]);
                    $coins = [
                        'rft.btitle' => $NUMERO_TITRE,
                        'rft.title' => $NUMERO_TITRE,
                        'rft.isbn' => $metaNumero[$NUMERO_ID_NUMPUBLIE]['ISBN'],
                        'rft.aulast' => $firstAuthor[1],
                        'rft.aufirst' => $firstAuthor[0],
                        'rft.au' => $firstAuthor[1] . ' ' .$firstAuthor[0],
                        'rft.pub' => $meta['NOM_EDITEUR'],
                        'rft.date' => $NUMERO_ANNEE,
                        'rft.issn' => null,
                        'rft.genre' => 'book',
                    ];
                    echo arrayToCoins($coins, 'book');
                ?>
            </div>

            <!-- FIN DE RECHERCHE DE REVUE -->

        <?php endif; ?>
    <?php endif; ?>

    <?php if ($typePub == 2) : ?>
        <?php if (!$pack) : ?>
            <!-- RECHERCHE DE MAGAZINE -->

            <div class="result article magazine" id="<?= $ARTICLE_ID_ARTICLE ?>">
                <h2><?= $typePubTitle ?></h2>
                <div class="wrapper_meta">
                    <a href="<?= $ARTICLE_HREF ?>">
                        <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $ARTICLE_ID_REVUE ?>/<?= $ARTICLE_ID_NUMPUBLIE ?>_L61.jpg" alt="" class="small_cover"/>
                    </a>
                    <div class="meta">
                        <a href="<?= $ARTICLE_HREF ?>">
                            <div class="title"><strong><?= $ARTICLE_TITRE ?></strong></div>
                        </a>
                        <div class="authors">
                            <?= $BLOC_AUTEUR ?>
                        </div>
                        <div class="revue_title">Dans <a href="<?= $REVUE_HREF ?>" class="title_little_blue"><span class="title_little_blue"><?= $REVUE_TITRE ?></span></a> <strong><?= $NUMERO_ANNEE ?>/<?= $NUMERO_NUMERO ?> (<?= $NUMERO_VOLUME ?>)</strong></div>
                    </div>
                </div>
                <div class="contexte"><?= $CONTEXTE ?></div>
                <!--
                <div class="state">
                    [LISTE_CONFIG_ARTICLE]
                    <a href="[ARTICLE_LIBELLE_HREF]" class="button">
                        [ARTICLE_LIBELLE_LIBELLE]
                    </a>
                    [/LISTE_CONFIG_ARTICLE]

                    [BLOC_CREDIT_INST]
                    [BLOC_ARTICLE_ACHAT]
                    <a href="mes_demandes.php?ID_ARTICLE=[ARTICLE_ID_ARTICLE]" class="button">Demander cet article</a>
                    [/BLOC_ARTICLE_ACHAT]
                    [/BLOC_CREDIT_INST]

                    [BLOC_CAIRN_INST_ACHAT]
                    [BLOC_CREDIT_INST_OFF]
                    [BLOC_ARTICLE_ACHAT]
                    <a href="mon_panier.php?ID_ARTICLE=[ARTICLE_ID_ARTICLE]" class="wrapper_buttons_add-to-cart">
                        <span class="button first">Consulter</span>
                        <span class="icon icon-add-to-cart"></span>
                        <span class="button last">[ARTICLE_PRIX] €</span>
                    </a>
                    [/BLOC_ARTICLE_ACHAT]
                    [/BLOC_CREDIT_INST_OFF]
                    [/BLOC_CAIRN_INST_ACHAT]

                    [BLOC_ARTICLE_BIBLIO_AJOUT_ON]
                    <a href="[URL]&amp;AJOUTBIBLIO=[ARTICLE_ID_ARTICLE]#[ARTICLE_ID_ARTICLE]" class="icon icon-add-biblio">&#160;</a>
                    [/BLOC_ARTICLE_BIBLIO_AJOUT_ON]
                    [BLOC_ARTICLE_BIBLIO_AJOUT_OFF]
                    <span class="infoajout">Ajout&eacute; &agrave; <a href="./biblio.php" class="yellow"><strong>ma bibliographie</strong></a> <span class="icon icon-remove-biblio"></span></span>
                    [/BLOC_ARTICLE_BIBLIO_AJOUT_OFF]
                </div>
                -->
                <div class="state">

                    <?php if ($cfgaArr[0] > 0) : ?>
                        <a href="resume.php?ID_ARTICLE=<?= $ARTICLE_ID_ARTICLE ?>" class="button">
                            <?php if ($cfgaArr[0] == 1) echo "Résumé"; else if ($cfgaArr[0] == 2) echo "Première page"; else if ($cfgaArr[0] == 3) echo "Premières lignes"; ?>
                        </a>
                    <?php endif; ?>
                    <?php
                    if (isset($articlesButtons[$ARTICLE_ID_ARTICLE]) && $articlesButtons[$ARTICLE_ID_ARTICLE]['STATUT'] == 1) {
                        ?>
                        <?php if ($cfgaArr[1] > 0) : ?>
                            <a href="<?= $ARTICLE_HREF ?>" class="button">
                                Version HTML
                            </a>
                        <?php endif; ?>
                        <?php if ($cfgaArr[2] > 0) : ?>
                            <?php if ($isPdf) : ?>
                                <a href="feuilleter.php?ID_ARTICLE=<?= $ARTICLE_ID_ARTICLE . $getDocUrlParameters ?>&ispdf=<?= $isPdf ?>" class="button">
                                <?php else: ?>
                                    <a href="feuilleter.php?ID_ARTICLE=<?= $ARTICLE_ID_ARTICLE ?>&searchTerm=<?= urlencode($searchTerm) ?>" class="button">
                                    <?php endif; ?>
                                    Feuilleter en ligne
                                </a>

                            <?php endif; ?>

                            <?php if ($cfgaArr[3] > 0) : ?>
                                <a
                                    href="load_pdf.php?ID_ARTICLE=<?= $ARTICLE_ID_ARTICLE ?>"
                                    class="button"
                                    data-webtrends="goToPdfArticle"
                                    data-id_article="<?= $ARTICLE_ID_ARTICLE ?>"
                                    data-titre=<?=
                                        $ParseDatas->cleanAttributeString($ARTICLE_TITRE)
                                    ?>
                                    data-authors=<?=
                                        $ParseDatas->cleanAttributeString(
                                            $ParseDatas->stringifyRawAuthors(
                                                str_replace(
                                                    '#',
                                                    $ParseDatas::concat_name,
                                                    implode($ParseDatas::concat_authors, $authors)
                                                ), 0, ';'
                                            )
                                        )
                                    ?>
                                >
                                    Version PDF
                                </a>
                            <?php endif; ?>

                            <?php if ($cfgaArr[5] > 0) : ?>
                                <a href="<?= $portalInfo[$ARTICLE_ID_ARTICLE]["URL_PORTAIL"] ?>" class="button">
                                    <?= $portalInfo[$ARTICLE_ID_ARTICLE]["NOM_PORTAIL"] ?>
                                </a>
                                <?php
                            endif;
                        }else {
                            if (isset($articlesButtons[$ARTICLE_ID_ARTICLE]) && $articlesButtons[$ARTICLE_ID_ARTICLE]['STATUT'] == 2) {
                                //WebTrends : "tracking sur les boutons d'ajout au panier"
                                if (strpos($articlesButtons[$ARTICLE_ID_ARTICLE]['CLASS'], 'wrapper_buttons_add-to-cart') !== false) {
                                    echo '<a href="' . $articlesButtons[$ARTICLE_ID_ARTICLE]['HREF'] . '" '
                                            . (isset($articlesButtons[$ARTICLE_ID_ARTICLE]['CLASS']) ? ('class="' . $articlesButtons[$ARTICLE_ID_ARTICLE]['CLASS'] . '"') : '')
                                            . 'data-webtrends="goToMonPanier" '
                                            . 'data-prix_article="' . number_format($ARTICLE_PRIX, 2, '.', '') . '" '
                                            . 'data-id_article="' . $ARTICLE_ID_ARTICLE . '" '
                                            . 'data-titre=' . $ParseDatas->cleanAttributeString($ARTICLE_TITRE)  . ' '
                                            . 'data-authors=' . $ParseDatas->cleanAttributeString(
                                            $ParseDatas->stringifyRawAuthors(
                                                str_replace(
                                                    '#',
                                                    $ParseDatas::concat_name,
                                                    implode($ParseDatas::concat_authors, $authors)
                                                ), 0, ';'
                                            )) . ' '
                                            . '>'
                                            . $articlesButtons[$ARTICLE_ID_ARTICLE]['LIB']
                                            . '</a>';
                                } else {
                                    echo '<a href="' . $articlesButtons[$ARTICLE_ID_ARTICLE]['HREF'] . '" ' . (isset($articlesButtons[$ARTICLE_ID_ARTICLE]['CLASS']) ? ('class="' . $articlesButtons[$ARTICLE_ID_ARTICLE]['CLASS'] . '"') : '') . '>' . $articlesButtons[$ARTICLE_ID_ARTICLE]['LIB'] . '</a>';
                                }
                            }
                        }
                        require_once(__DIR__ . '/../CommonBlocs/actionsBiblio.php');
                        checkBiblio($ARTICLE_ID_REVUE, $ARTICLE_ID_NUMPUBLIE, $ARTICLE_ID_ARTICLE, $authInfos);
                        ?>
                </div>

                <?php
                    $meta = $metaNumero[$NUMERO_ID_NUMPUBLIE];
                    $firstAuthor = explode(':', explode('|', $meta['NUMERO_AUTEUR'])[0]);
                    $coins = [
                        'rft.atitle' => $ARTICLE_TITRE,
                        'rft.jtitle' => $NUMERO_TITRE,
                        'rft.title' => $NUMERO_TITRE,
                        'rft.volume' => preg_replace('/\s*n°\s*/', '', $NUMERO_VOLUME),
                        'rft.issue' => $NUMERO_ANNEE.'/'.$NUMERO_NUMERO,
                        'rft.isbn' => $metaNumero[$NUMERO_ID_NUMPUBLIE]['ISBN'],
                        'rft.aulast' => $firstAuthor[1],
                        'rft.aufirst' => $firstAuthor[0],
                        'rft.au' => $firstAuthor[1] . ' ' .$firstAuthor[0],
                        'rft.pub' => $meta['NOM_EDITEUR'],
                        'rft.date' => $NUMERO_ANNEE,
                        'rft.issn' => null,
                        'rft.eissn' => null,
                        'rft.genre' => 'article',
                    ];
                    echo arrayToCoins($coins);
                ?>
            </div>

        <?php else: ?>

            <div class="result numero magazine" id="<?= $NUMERO_ID_NUMPUBLIE ?>">
                <h2><?= $typePubTitle ?></h2>
                <div class="wrapper_contexte wrapper_meta">
                    <a href="<?= $NUMERO_HREF ?>">
                        <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $ARTICLE_ID_REVUE ?>/<?= $ARTICLE_ID_NUMPUBLIE ?>_L61.jpg" alt="" class="small_cover"/>
                    </a>
                    <div class="meta">
                        <div class="revue_title">
                            <a href="<?= $REVUE_HREF ?>" class="title_little_blue">
                                <span class="title_little_blue"><?= $REVUE_TITRE ?></span>
                            </a>
                            <strong><?= $NUMERO_ANNEE ?>/<?= $NUMERO_NUMERO ?> (<?= $NUMERO_VOLUME ?>)</strong>
                        </div>
                        <div class="numero_title">
                            <a href="<?= $NUMERO_HREF ?>">
                                <strong><?= $NUMERO_TITRE ?><?php if (trim($NUMERO_SOUS_TITRE) != '') echo ". $NUMERO_SOUS_TITRE"; ?></strong>
                            </a>
                        </div>
                        <div class="authors">

                        </div>
                    </div>
                    <div class="contexteMemo"><?= $NUMERO_MEMO ?></div>
                </div>
                <div class="state">
                    <a href="<?= $NUMERO_HREF ?>"  class="button">Présentation/Sommaire</a>
                    <a href="#" class="button" onclick="$(this).toggleClass('active');
                                        cairn_search_deploy_pertinent_articles('<?= $NUMERO_ID_NUMPUBLIE ?>', this);">Articles les plus pertinents</a>
                    <?php
                    require_once(__DIR__ . '/../CommonBlocs/actionsBiblio.php');
                    checkBiblio($ARTICLE_ID_REVUE, $ARTICLE_ID_NUMPUBLIE, null, $authInfos);
                    ?>
                </div>
                <div class="pertinent_articles" id="__pertinent_<?= $NUMERO_ID_NUMPUBLIE ?>">
                    [LISTE_RESULTAT_ARTICLES_MAGAZINE]
                    <div class="meta">
                        <div>
                            <img src="img/pert_[ARTICLE_PERTI].png" alt="niveau de pertinence évalué à [ARTICLE_PERTI]" class="pertinence"/>
                            <span class="pages">page [ARTICLE_PAGE_DEBUT] &agrave; [ARTICLE_PAGE_FIN]</span>
                            <div class="title">[ARTICLE_TITRE][BLOC_ARTICLE_SOUSTITRE]. [ARTICLE_SOUSTITRE][/BLOC_ARTICLE_SOUSTITRE]</div>
                        </div>
                        <div class="authors">
                            [BLOC_AUTEURS][AUTEUR_PRENOM] [AUTEUR_NOM][BLOC_PLUSDEDEUX] <em>et al.</em>[/BLOC_PLUSDEDEUX]
                            [BLOC_DEUX] et [AUTEUR_PRENOM] [AUTEUR_NOM][/BLOC_DEUX][/BLOC_AUTEURS]
                        </div>
                        <div class="contexte">[CONTEXTE]</div>
                        <div class="state">
                            [LISTE_CONFIG_ARTICLE]
                            <a href="[ARTICLE_LIBELLE_HREF]" class="button" [BLOC_CONFIG_ARTICLE_PDF] onclick="javascript:trackPDFViewer('[ARTICLE_ID_ARTICLE]', '[ARTICLE_TITRE_JVSCT]', '[BLOC_AUTEURS][AUTEUR_PRENOM_JVSCT] [AUTEUR_NOM_JVSCT][BLOC_DEUX];[AUTEUR_PRENOM_JVSCT] [AUTEUR_NOM_JVSCT][/BLOC_DEUX][/BLOC_AUTEURS]');" [/BLOC_CONFIG_ARTICLE_PDF]>
                               [ARTICLE_LIBELLE_LIBELLE]
                        </a>
                        [/LISTE_CONFIG_ARTICLE]

                        [BLOC_CREDIT_INST]
                        [BLOC_ARTICLE_ACHAT]
                        <a href="mes_demandes.php?ID_ARTICLE=[ARTICLE_ID_ARTICLE]" class="button">Demander cet article</a>
                        [/BLOC_ARTICLE_ACHAT]
                        [/BLOC_CREDIT_INST]

                        [BLOC_CAIRN_INST_ACHAT]
                        [BLOC_CREDIT_INST_OFF]
                        [BLOC_ARTICLE_ACHAT]
                        <a href="mon_panier.php?ID_ARTICLE=[ARTICLE_ID_ARTICLE]" class="wrapper_buttons_add-to-cart">
                            <span class="button first">Consulter</span>
                            <span class="icon icon-add-to-cart"></span>
                            <span class="button last">[ARTICLE_PRIX] €</span>
                        </a>
                        [/BLOC_ARTICLE_ACHAT]
                        [/BLOC_CREDIT_INST_OFF]
                        [/BLOC_CAIRN_INST_ACHAT]
                    </div>
                </div>
                <hr class="grey" />
                [/LISTE_RESULTAT_ARTICLES_MAGAZINE]
            </div>

                <?php
                    $meta = $metaNumero[$NUMERO_ID_NUMPUBLIE];
                    $firstAuthor = explode(':', explode('|', $meta['NUMERO_AUTEUR'])[0]);
                    $coins = [
                        'rft.btitle' => $NUMERO_TITRE,
                        'rft.title' => $NUMERO_TITRE,
                        'rft.isbn' => $metaNumero[$NUMERO_ID_NUMPUBLIE]['ISBN'],
                        'rft.aulast' => $firstAuthor[1],
                        'rft.aufirst' => $firstAuthor[0],
                        'rft.au' => $firstAuthor[1] . ' ' .$firstAuthor[0],
                        'rft.pub' => $meta['NOM_EDITEUR'],
                        'rft.date' => $NUMERO_ANNEE,
                        'rft.issn' => null,
                        'rft.genre' => 'book',
                    ];
                    echo arrayToCoins($coins, 'book');
                ?>
            </div>

            <!-- FIN DE RECHERCHE DE MAGAZINE -->

        <?php endif; ?>
    <?php endif; ?>

    <?php if ($typePub == 4) : ?>
        <?php if (!$pack) : ?>
            <!-- RECHERCHE DE ETAT DU MONDE -->
            [BLOC_TYPEPUB_EDM]
            <div class="result article magazine" id="[ARTICLE_ID_ARTICLE]">
                <h2><?= $typePubTitle ?></h2>
                <div class="wrapper_meta">
                    <a href="[ARTICLE_HREF]">
                        <img src="./vign_rev/[ARTICLE_ID_REVUE]/[ARTICLE_ID_NUMPUBLIE]_L61.jpg" alt="" class="small_cover"/>
                    </a>
                    <div class="meta">
                        <a href="[ARTICLE_HREF]">
                            <div class="title"><strong>[ARTICLE_TITRE]</strong></div>
                        </a>
                        <div class="authors">
                            [BLOC_AUTEURS]
                            <span class="author">
                                [AUTEUR_PRENOM] [AUTEUR_NOM]
                                [BLOC_PLUSDEDEUX] <em>et al.</em> [/BLOC_PLUSDEDEUX]
                                [BLOC_DEUX] et [AUTEUR_PRENOM] [AUTEUR_NOM][/BLOC_DEUX]
                            </span>
                            [/BLOC_AUTEURS]
                        </div>
                        <div class="revue_title">Dans
                            <a href="[REVUE_HREF]" class="title_little_blue"><span class="title_little_blue">[REVUE_TITRE]</span></a>
                            <strong>([EDITEUR_NOM_EDITEUR], [BLOC_NUMERO_VOLUME][NUMERO_VOLUME] [/BLOC_NUMERO_VOLUME][NUMERO_ANNEE])</strong>
                        </div>
                    </div>
                </div>
                <div class="contexte">[CONTEXTE]</div>
                <div class="state">
                    [LISTE_CONFIG_ARTICLE]
                    <a href="[ARTICLE_LIBELLE_HREF]" class="button">
                        [ARTICLE_LIBELLE_LIBELLE]
                    </a>
                    [/LISTE_CONFIG_ARTICLE]

                    [BLOC_CREDIT_INST]
                    [BLOC_ARTICLE_ACHAT]
                    <a href="mes_demandes.php?ID_ARTICLE=[ARTICLE_ID_ARTICLE]" class="button">Demander cet article</a>
                    [/BLOC_ARTICLE_ACHAT]
                    [/BLOC_CREDIT_INST]

                    [BLOC_CAIRN_INST_ACHAT]
                    [BLOC_CREDIT_INST_OFF]
                    [BLOC_ARTICLE_ACHAT]
                    <a href="mon_panier.php?ID_ARTICLE=[ARTICLE_ID_ARTICLE]" class="wrapper_buttons_add-to-cart">
                        <span class="button first">Consulter</span>
                        <span class="icon icon-add-to-cart"></span>
                        <span class="button last">[ARTICLE_PRIX] €</span>
                    </a>
                    [/BLOC_ARTICLE_ACHAT]
                    [/BLOC_CREDIT_INST_OFF]
                    [/BLOC_CAIRN_INST_ACHAT]

                    [BLOC_ARTICLE_BIBLIO_AJOUT_ON]
                    <a href="[URL]&amp;AJOUTBIBLIO=[ARTICLE_ID_ARTICLE]#[ARTICLE_ID_ARTICLE]" class="icon icon-add-biblio">&#160;</a>
                    [/BLOC_ARTICLE_BIBLIO_AJOUT_ON]
                    [BLOC_ARTICLE_BIBLIO_AJOUT_OFF]
                    <span class="infoajout">Ajout&eacute; &agrave; <a href="./biblio.php" class="yellow"><strong>ma bibliographie</strong></a> <span class="icon icon-remove-biblio"></span></span>
                    [/BLOC_ARTICLE_BIBLIO_AJOUT_OFF]
                </div>

                <?php
                    $meta = $metaNumero[$NUMERO_ID_NUMPUBLIE];
                    $firstAuthor = explode(':', explode('|', $meta['NUMERO_AUTEUR'])[0]);
                    $coins = [
                        'rft.atitle' => $ARTICLE_TITRE,
                        'rft.jtitle' => $NUMERO_TITRE,
                        'rft.title' => $NUMERO_TITRE,
                        'rft.volume' => preg_replace('/\s*n°\s*/', '', $NUMERO_VOLUME),
                        'rft.issue' => $NUMERO_ANNEE.'/'.$NUMERO_NUMERO,
                        'rft.isbn' => $metaNumero[$NUMERO_ID_NUMPUBLIE]['ISBN'],
                        'rft.aulast' => $firstAuthor[1],
                        'rft.aufirst' => $firstAuthor[0],
                        'rft.au' => $firstAuthor[1] . ' ' .$firstAuthor[0],
                        'rft.pub' => $meta['NOM_EDITEUR'],
                        'rft.date' => $NUMERO_ANNEE,
                        'rft.issn' => null,
                        'rft.eissn' => null,
                        'rft.genre' => 'article',
                    ];
                    echo arrayToCoins($coins);
                ?>
            </div>
            [/BLOC_TYPEPUB_EDM]
        <?php else: ?>
            [BLOC_TYPEPUB_EDM_NUM]
            <div class="result numero magazine" id="[NUMERO_ID_NUMPUBLIE]">
                <h2>Dossier de l'État du monde</h2>
                <div class="wrapper_meta">
                    <a href="resultats_recherche.php?MOV=0&amp;SESS=[NUM_SESSION]&amp;BLOC=[NUMERO_HREF]&amp;ID_REVUE=&amp;ID_NUMPUBLIE=[NUMERO_ID_NUMPUBLIE]">
                        <img src="./vign_rev/[NUMERO_ID_REVUE]/[NUMERO_ID_NUMPUBLIE]_L61.jpg" alt="" class="small_cover"/>
                    </a>
                    <div class="meta">
                        <div class="numero_title">
                            <a href="[NUMERO_HREF]">
                                <strong>[NUMERO_TITRE][BLOC_NUMERO_SOUS_TITRE]. [NUMERO_SOUS_TITRE][/BLOC_NUMERO_SOUS_TITRE]</strong>
                            </a>
                        </div>
                        <div class="revue_title">dans
                            <a href="[REVUE_HREF]" class="title_little_blue">
                                <span class="title_little_blue">[REVUE_TITRE]</span>
                            </a>
                            <strong>([EDITEUR_NOM_EDITEUR], [BLOC_NUMERO_VOLUME][NUMERO_VOLUME] [/BLOC_NUMERO_VOLUME][NUMERO_ANNEE])</strong>
                        </div>
                        <div class="authors">
                            [BLOC_AUTEURS]
                            <span class="author">
                                [AUTEUR_PRENOM] [AUTEUR_NOM]
                                [BLOC_PLUSDEDEUX] <em>et al.</em> [/BLOC_PLUSDEDEUX]
                                [BLOC_DEUX] et [AUTEUR_PRENOM] [AUTEUR_NOM][/BLOC_DEUX]
                            </span>
                            [/BLOC_AUTEURS]
                        </div>
                    </div>
                </div>
                <div class="wrapper_contexte">
                    <div class="contexteMemo">[NUMERO_MEMO]</div>
                </div>
                <div class="state">
                    <a href="resultats_recherche.php?MOV=0&amp;SESS=[NUM_SESSION]&amp;BLOC=[NUMERO_HREF]&amp;ID_REVUE=&amp;ID_NUMPUBLIE=[NUMERO_ID_NUMPUBLIE]"  class="button">Présentation/Sommaire</a>
                    [BLOC_NUMERO_BIBLIO_AJOUT_ON]
                    <a href="[URL]&amp;AJOUTBIBLIO=[NUMERO_ID_NUMPUBLIE]" class="icon icon-add-biblio">&#160;</a>
                    [/BLOC_NUMERO_BIBLIO_AJOUT_ON]
                    <a href="#" class="button" onclick="cairn_search.deploy_pertinent_articles('resultats_recherche.php?MOV=0&amp;SESS=[NUM_SESSION]&amp;BLOC=LISTE_RESULTAT_ARTICLES_EDM&amp;ID_REVUE=&amp;ID_NUMPUBLIE=[NUMERO_ID_NUMPUBLIE]', '#__pertinent_[NUMERO_ID_NUMPUBLIE]', this);">Chapitres les plus pertinents</a>
                </div>
                <div class="pertinent_articles" id="__pertinent_[NUMERO_ID_NUMPUBLIE]">
                    [LISTE_RESULTAT_ARTICLES_EDM]
                    <div class="meta">
                        <div>
                            <img src="img/pert_[ARTICLE_PERTI].png" alt="niveau de pertinence évalué à [ARTICLE_PERTI]" class="pertinence"/>
                            <span class="pages">page [ARTICLE_PAGE_DEBUT] &agrave; [ARTICLE_PAGE_FIN]</span>
                            <div class="title">[ARTICLE_TITRE][BLOC_ARTICLE_SOUSTITRE]. [ARTICLE_SOUSTITRE][/BLOC_ARTICLE_SOUSTITRE]</div>
                        </div>
                        <div class="authors">
                            [BLOC_AUTEURS][AUTEUR_PRENOM] [AUTEUR_NOM][BLOC_PLUSDEDEUX] <em>et al.</em>[/BLOC_PLUSDEDEUX]
                            [BLOC_DEUX] et [AUTEUR_PRENOM] [AUTEUR_NOM][/BLOC_DEUX][/BLOC_AUTEURS]
                        </div>
                        <div class="contexte">[CONTEXTE]</div>
                        <div class="state">
                            [LISTE_CONFIG_ARTICLE]
                            <a href="[ARTICLE_LIBELLE_HREF]" class="button" [BLOC_CONFIG_ARTICLE_PDF] onclick="javascript:trackPDFViewer('[ARTICLE_ID_ARTICLE]', '[ARTICLE_TITRE_JVSCT]', '[BLOC_AUTEURS][AUTEUR_PRENOM_JVSCT] [AUTEUR_NOM_JVSCT][BLOC_DEUX];[AUTEUR_PRENOM_JVSCT] [AUTEUR_NOM_JVSCT][/BLOC_DEUX][/BLOC_AUTEURS]');" [/BLOC_CONFIG_ARTICLE_PDF]>
                               [ARTICLE_LIBELLE_LIBELLE]
                        </a>
                        [/LISTE_CONFIG_ARTICLE]

                        [BLOC_CREDIT_INST]
                        [BLOC_ARTICLE_ACHAT]
                        <a href="mes_demandes.php?ID_ARTICLE=[ARTICLE_ID_ARTICLE]" class="button">Demander cet article</a>
                        [/BLOC_ARTICLE_ACHAT]
                        [/BLOC_CREDIT_INST]

                        [BLOC_CAIRN_INST_ACHAT]
                        [BLOC_CREDIT_INST_OFF]
                        [BLOC_ARTICLE_ACHAT]
                        <a href="mon_panier.php?ID_ARTICLE=[ARTICLE_ID_ARTICLE]" class="wrapper_buttons_add-to-cart">
                            <span class="button first">Consulter</span>
                            <span class="icon icon-add-to-cart"></span>
                            <span class="button last">[ARTICLE_PRIX] €</span>
                        </a>
                        [/BLOC_ARTICLE_ACHAT]
                        [/BLOC_CREDIT_INST_OFF]
                        [/BLOC_CAIRN_INST_ACHAT]
                    </div>
                </div>
                <hr class="grey" />
                [/LISTE_RESULTAT_ARTICLES_EDM]
            </div>
            <!--<span class='Z3988' title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Ajournal&amp;rfr_id=info%3Asid%2Focoins.info%3Agenerator&amp;rft.genre=article&amp;rft.atitle=[ARTICLE_TITRE]&amp;rft.title=[REVUE_TITRE]&amp;rft.issn=[REVUE_ISSN]&amp;rft.date=[NUMERO_ANNEE]&amp;rft.volume=[NUMERO_VOLUME]&amp;rft.issue=[NUMERO_NUMERO]&amp;rft.spage=[ARTICLE_PAGE_DEBUT]&amp;rft.epage=[ARTICLE_PAGE_FIN]&amp;rft.au=[BLOC_AUTEURS][AUTEUR_PRENOM] [AUTEUR_NOM][/BLOC_AUTEURS]&amp;rft_id=info:doi/[ARTICLE_DOI]&amp;rft_id=[ARTICLE_HREF]"></span>-->
            </div>
            [/BLOC_TYPEPUB_EDM_NUM]
            <!-- FIN DE RECHERCHE DE ETAT DU MONDE -->
        <?php endif; ?>
    <?php endif; ?>

<?php endforeach; ?>
</div>




</div>

<?php
$nbPerPage = 20;
$nbAround = 2;
$jsPager = "move";
//$limit = 20;
$countNum = $stats->TotalFiles;
if ($countNum > $nbPerPage)
    require_once __DIR__ . '/../CommonBlocs/pager.php'; ?>
