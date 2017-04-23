<?php

/**
 * Converts all accent characters to ASCII characters.
 *
 * If there are no accent characters, then the string given is just returned.
 *
 * @param string $string Text that might have accent characters
 * @return string Filtered string with replaced "nice" characters.
 */

$eftaLabels = array("0"=>"French full-text",
                    "1"=>"French full-text with English abstract",
                    "2"=>"English full-text");

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
    var facettes = {"dp": "filter_year", "id_r": "filter_revue", "dr": "filter_disc", "tp": "filter_type", "efta" : "filter_text"};
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


    function searchTermes(node) {

        var conc = $("#searchTerm").val() + " \"" + $(node).text() + "\"";
        $('#compute_search_field').val(conc);
        $('#main_search_form2').submit();
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
<?php $this->titre = "Search results"; ?>
<div id="body-content" class="searchResult">
    <div id="search_header">
        <form method="POST" name="mel2" id="mel2" class="left">
            <input name="OPERATE" value="T" type="hidden">
            <input type="hidden" name="TRI" value="DMEL">
            <input type="hidden" name="periode">
            <!-- Le select ci-dessous est "transformé" par le script override_select.js. Il récupère les ids et classe, et les préfixe par un alter_ -->
            <span id="label_filter_online-since">Online since</span>
            <select id="filter_online-since" onchange="filter_online_since()" name="periode">
                <option value="ALL" <?php if ($periode == 'ALL') echo ' selected '; ?> >...</option>
                <option value="SEM" <?php if ($periode == 'SEM') echo ' selected '; ?>>One week</option>
                <option value="MOIS" <?php if ($periode == 'MOIS') echo ' selected '; ?>>One month</option>
                <option value="DEUX" <?php if ($periode == 'DEUX') echo ' selected '; ?>>Two months</option>
                <option value="SIX" <?php if ($periode == 'SIX') echo ' selected '; ?>>Six months</option>
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
        <h1>Search Results</h1>

        <span id="trigger_filtering" class="search_button right" style="margin-left:1.5em">Refine search</span>

        <!--a  class="search_button right" onclick="cairn_search_modify(this);"  style="margin-left:1.5em">Modifier</a-->

    </div>
    <div id="search_navbar">
        <span>Your search :</span>
        <span class="title_little_blue" style="margin-left:0.5em; font-size:17px;">
            <span id="librechav"><?= $searchTerm . $TRA ?></span>
        </span>
        <span style="margin-left:1.5em" class="right"><?php
        echo number_format((int) $stats->TotalFiles,0,'.',' ') . " Result";
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
    <!--div id="wrapper_modify_search" style="display:none">

    </div-->
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

            <?php $cntFacet = 0;
            foreach ($facettes as $key => $value):
                $cntFacet ++;
                ?>

                <form id="filter_<?php
                switch ($key) {
                    case 'Research areas': $dsp = 'disc';
                        break;
                    case 'Types': $dsp = 'type';
                        break;
                    case 'Journals': $dsp = 'revue';
                        break;
                    case 'Year': $dsp = 'year';
                        break;
                    case 'Text': $dsp = 'text';
                } echo $dsp;
                ?>"  action='resultats_recherche.php?searchTerm=<?php echo urlencode($searchTerm); ?>' method="post">

                    <?php
                    echo "<input type='hidden' name='searchTerm' value=\"" . htmlspecialchars($searchTerm) . "\" />\n";
                    echo "<input type='hidden' name='" . $label2facette[$key] . "_hidden' value='" . $hiddenFacettes[$label2facette[$key]] . "'  >\n";
                    ?>
                    <?php if($key != 'Text'){ ?>
                    <h2><?= $key ?></h2>
                    <?php } ?>

                        <?php if($key != 'Text'){ ?>
                        <ul>
                        <li><input type="checkbox"  name="p" class="checkme" id="bt_<?= $label2facette[$key] ?>" value="Tout cocher/décocher" />
                            <label for="bt_<?= $label2facette[$key] ?>">All/None</label></li>

                            <?php foreach ($value as $key1 => $value1): ?>
                                <li>
                                    <input type="checkbox" checked id="<?= $label2facette[$key] . '_' . $value1['mkey'] ?>" name='<?= $label2facette[$key] ?>[]' value="<?= $value1['mkey'] ?>" />
                                    <?php if (!($dsp == 'revue')) : ?>
                                    <label for="<?= $label2facette[$key] . '_' . $value1['mkey'] ?>"><?= $key1 . "(" . number_format($value1['nb'],0,'.',' ') . ')' ?></label><br/>
                                    <?php else : ?>
                                        <label for="<?= $label2facette[$key] . '_' . $value1['mkey'] ?>"><?= $facettesRevues[$key1] . "(" . number_format($value1['nb'],0,'.',' ') . ')' ?></label><br/>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach;
                        }else{?>
                            <?php foreach ($value as $key1 => $value1): ?>
                                <div id="filter_efta">
                                    <input type="checkbox" checked id="<?= $label2facette[$key] . '_' . $value1['mkey'] ?>" name='<?= $label2facette[$key] ?>[]' value="<?= $value1['mkey'] ?>" />
                                    <label for="<?= $label2facette[$key] . '_' . $value1['mkey'] ?>"><?= $eftaLabels[$key1] . "(" . number_format($value1['nb'],0,'.',' ') . ')' ?></label><br/>
                               </div>
                            <?php endforeach;
                        }?>
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
                            } elseif ($key == 'Dates de parution'){
                                if (!(isset($facettes[$key][$hiddenFacetteV]))) {
                                    echo "<li><input type='checkbox'  id='$namef" . '_' . "$hiddenFacetteV'  name='$namef" . "[]' value='" . str_replace('avant ', '~~', $hiddenFacetteV) . "'  /><label for='$namef" . '_' . "$hiddenFacetteV'>" . $hiddenFacetteV . "</label></li>\n";
                                    $missing = true;
                                }
                            } elseif ($key == 'Text'){
                                if (!(isset($facettes[$key][$hiddenFacetteV]))) {
                                    echo "<div id=\"filter_efta\"><input type='checkbox'  id='$namef" . '_' . "$hiddenFacetteV'  name='$namef" . "[]' value='$hiddenFacetteV'  /><label for='$namef" . '_' . "$hiddenFacetteV'>" . $eftaLabels[$hiddenFacetteV] . "</label></div>\n";
                                    $missing = true;
                                }
                            }
                        }
                        ?>

                    <?php if (!$missing) : ?>
                        <?php
                            $this->javascripts[] = '$("#bt_' . $label2facette[$key] . '").attr("checked", "checked")';
                        ?>
                    <?php endif; ?>
                    <?php if($key != 'Text'){ ?>
                        </ul>
                        <input type="submit" name="refine<?= $label2facette[$key] ?>" value="Refresh"/>
                    <?php }else{ ?>
                        <div id="filter_efta"><input type="submit" name="refine<?= $label2facette[$key] ?>" value="Refresh"/></div><hr class="grey" style="margin:20px 0;"/>
                    <?php } ?>
                </form>
                <?php if ($key == 'Text') : ?>
                    <div id="filter_type">
                        <div id="associated_keywords"><h2>Associated keywords</h2>
                            <ul>
                                <?php foreach ($concepts as $concept) : ?>
                                    <li><a  onclick="searchTermes(this);" class="white_button"> <?= $concept ?> </a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>

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
            /*if ((int) $result->userFields->tp == 3) {
                $offset = (int) $result->userFields->tp + 2 * (int) $result->userFields->tnp;
            } else {
                $offset = (int) $result->userFields->tp;
            }*/
            $offset = 1;
            $typePubTitle = $typeDocument[$pack][$offset];

            //$typePub = $result->userFields->tp;
            $typePub = 1;

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
            $getDocUrlParameters = 'DocId=' . $result->item->docId . '&hits=' . urlencode($hitsStr);
            //$getDocUrlParameters = 'DocId=' . $result->item->docId . '&hits=' . urlencode($result->item->hits);
            $isPdf = (stripos($result->item->Filename, '.pdf') > 0);



            $NOM_EDITEUR = $metaNumero[$NUMERO_ID_NUMPUBLIE]['NOM_EDITEUR'];
            $REVUE_TITRE = $result->userFields->rev0;
            $cfgaArr = explode(',', $result->userFields->cfg0);

            if($metaNumero[$NUMERO_ID_NUMPUBLIE]['MEMO'] != '' && strlen($metaNumero[$NUMERO_ID_NUMPUBLIE]['MEMO'])){
                $NUMERO_MEMO = substr($metaNumero[$NUMERO_ID_NUMPUBLIE]['MEMO'], 0, strpos($metaNumero[$NUMERO_ID_NUMPUBLIE]['MEMO'], " ", 200));
            }else{
                $NUMERO_MEMO = $metaNumero[$NUMERO_ID_NUMPUBLIE]['MEMO'];
            }
            $CONTEXTE = strip_tags($result->item->Synopsis, '<b>');

            $ARTICLE_HREF = '';
            $NUMERO_HREF = '';
            $REVUE_HREF = "";
            switch ($typePub) {
                case "1":
                    $english = $articlesButtons[$ARTICLE_ID_ARTICLE][2];
                    if($english == ''){
                        $ARTICLE_HREF = "./abstract.php?ID_ARTICLE=$ARTICLE_ID_ARTICLE&" . $getDocUrlParameters;
                    }else{
                        $ARTICLE_HREF = "./article.php?ID_ARTICLE=$ARTICLE_ID_ARTICLE&" . $getDocUrlParameters;
                    }
                    $NUMERO_HREF = "./journal-" . $metaNumero[$NUMERO_ID_NUMPUBLIE]['REVUE_URL_REWRITING'] . "-$NUMERO_ANNEE-$NUMERO_NUMERO.htm";
                    $REVUE_HREF;
                    $REVUE_HREF = "./journal-" . $metaNumero[$NUMERO_ID_NUMPUBLIE]['REVUE_URL_REWRITING'] . ".htm";
                    break;
            }


            $BLOC_AUTEUR = '';
            $BLOC_AUTEUR_PACK = '';
            if (sizeof($authors) > 2) {
                $authors2 = explode('#', $authors[0]);
                $BLOC_AUTEUR = "<a class=\"yellow\" href=\"publications-of-" . trim($authors2[1]) . '-' . $authors2[2] . '--' . $authors2[3] . '.htm' . '">' . $authors2[0] . ' ' . $authors2[1] . ' ' . $authors2[2] . "</a> and al.";
            } else if (sizeof($authors) == 2) {
                $authors2 = explode('#', $authors[0]);
                $BLOC_AUTEUR = "<a class=\"yellow\" href=\"publications-of-" . $authors2[1] . '-' . $authors2[2] . '--' . $authors2[3] . '.htm' . '">' . $authors2[0] . ' ' . $authors2[2] . ' ' . $authors2[1] . "</a> and ";
                $authors2 = explode('#', $authors[1]);
                $BLOC_AUTEUR .= "<a class=\"yellow\" href=\"publications-of-" . trim($authors2[1]) . '-' . $authors2[2] . '--' . $authors2[3] . '.htm' . '">' . $authors2[0] . ' ' . $authors2[2] . ' ' . $authors2[1] . "</a> ";
            } else if (sizeof($authors) == 1 && $authors[0] != ' - ') {
                $authors2 = explode('#', $authors[0]);
                $BLOC_AUTEUR = "<a class=\"yellow\" href=\"publications-of-" . $authors2[1] . '-' . $authors2[2] . '--' . $authors2[3] . '.htm' . '">' . $authors2[0] . ' ' . $authors2[2] . ' ' . $authors2[1] . "</a>";
            }

            $BLOC_AUTEUR = trim($BLOC_AUTEUR);

            if($BLOC_AUTEUR != ''){
                $BLOC_AUTEUR_PACK = $BLOC_AUTEUR;
            }else{
                $numeroAuteurs = $metaNumero[$NUMERO_ID_NUMPUBLIE]['NUMERO_AUTEUR'];
                $authors = explode('|',$numeroAuteurs);
                if (sizeof($authors) > 2) {
                    $authors2 = explode(':', $authors[0]);
                    $BLOC_AUTEUR_PACK = "<a class=\"yellow\" href=\"publications-of-" . trim($authors2[1]) . '-' . $authors2[2] . '--' . $authors2[3] . '.htm' . '">' . $authors2[0] . ' ' . $authors2[1] . ' ' . $authors2[2] . "</a> et al.";
                } else if (sizeof($authors) == 2) {
                    $authors2 = explode(':', $authors[0]);
                    $BLOC_AUTEUR_PACK = "<a class=\"yellow\" href=\"publications-of-" . $authors2[1] . '-' . $authors2[2] . '--' . $authors2[3] . '.htm' . '">' . $authors2[0] . ' ' . $authors2[2] . ' ' . $authors2[1] . "</a> et ";
                    $authors2 = explode(':', $authors[1]);
                    $BLOC_AUTEUR_PACK .= "<a class=\"yellow\" href=\"publications-of-" . trim($authors2[1]) . '-' . $authors2[2] . '--' . $authors2[3] . '.htm' . '">' . $authors2[0] . ' ' . $authors2[2] . ' ' . $authors2[1] . "</a> ";
                } else if (sizeof($authors) == 1 && $authors[0] != ' - ') {
                    $authors2 = explode(':', $authors[0]);
                    $BLOC_AUTEUR_PACK = "<a class=\"yellow\" href=\"publications-of-" . $authors2[1] . '-' . $authors2[2] . '--' . $authors2[3] . '.htm' . '">' . $authors2[0] . ' ' . $authors2[2] . ' ' . $authors2[1] . "</a>";
                }
                $BLOC_AUTEUR_PACK = trim($BLOC_AUTEUR_PACK);
            }
            //if($BLOC_AUTEUR == '- ')
            //  $BLOC_AUTEUR='';
            ?>

    <?php if ($typePub == 1) : ?>
        <?php if (!$pack) : ?>
            <!-- RECHERCHE DE REVUE -->

            <div class="result article revue" id="<?= $ARTICLE_ID_ARTICLE ?>">
                <!--h2><?= $typePubTitle ?></h2-->
                <div class="pages_article vign_small">
                    <a href="<?= $ARTICLE_HREF ?>">
                        <img src="http://<?= $vign_url ?>/<?= $vign_path ?>/<?= $ARTICLE_ID_REVUE ?>/<?= $ARTICLE_ID_NUMPUBLIE ?>_L62.jpg" alt="" class="small_cover"/>
                    </a>
                </div>
                <div class="metadata_article">
                    <div class="title"><a href="<?= $ARTICLE_HREF ?>"><strong><?= $ARTICLE_TITRE ?></strong></a></div>
                    <div class="authors">
                        <?= $BLOC_AUTEUR ?>
                    </div>
                    <div class="revue_title">in <a href="<?= $REVUE_HREF ?>" class="title_little_blue"><span class="title_little_blue"><?= $REVUE_TITRE ?></span></a> <strong><?= $NUMERO_ANNEE ?>/<?= $NUMERO_NUMERO ?> (<?= $NUMERO_VOLUME ?>)</strong></div>
                </div>

                <div class="state_article">
                    <?php
                    $abstract = $articlesButtons[$ARTICLE_ID_ARTICLE][0];
                    if($abstract == ''){
                        echo '<span class="button-grey2 w49 left">Abstract</span>';
                    }else{
                        echo '<a href="abstract-'.$ARTICLE_ID_ARTICLE.'--'.$articlesButtons[$ARTICLE_ID_ARTICLE][4].'.htm" class="button-blue2 w49 left">Abstract</a>';
                    }
                    $french = $articlesButtons[$ARTICLE_ID_ARTICLE][1];
                    if($french == ''){
                        echo '<span class="button-grey2 w49 right">French</span>';
                    }else{
                        echo '<a href="http://www.cairn.info/article.php?ID_ARTICLE='.$articlesButtons[$ARTICLE_ID_ARTICLE][5].'" class="button-blue2 w49 right">French</a>';
                    }
                    echo '<br>';
                    $english = $articlesButtons[$ARTICLE_ID_ARTICLE][2];
                    if($english == ''){
                        echo '<span class="button-grey2 w100">English
                                    <span data-article-title="'.$ARTICLE_TITRE.'" data-suscribe-on-translation="'.$ARTICLE_ID_ARTICLE.'" class="question-mark">
                                        <span class="tooltip">Why is this article not available in English?</span>
                                    </span>
                                </span>';
                    }else{
                        echo '<a href="'.$english.(strpos($english,'my_cart.php')===FALSE?('?'.$getDocUrlParameters):'').'" class="button-blue2 w100">';

                        if(strpos($english,'my_cart.php') !== FALSE){
                            echo 'English <span class="cart-icon">'.$articlesButtons[$ARTICLE_ID_ARTICLE][3].' € </span>';
                        }else{
                            if($articlesButtons[$ARTICLE_ID_ARTICLE][3] == 0){
                                echo 'English : Free';
                            }else{
                                echo 'English';
                            }
                        }
                        echo '</a>';
                    }?>





                    <?php /*if ($cfgaArr[0] > 0) : ?>
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
                            <a href="load_pdf.php?ID_ARTICLE=<?= $ARTICLE_ID_ARTICLE ?>" class="button">
                                Version PDF
                            </a>
                        <?php endif; ?>

                        <?php if (count($cfgaArr) > 5 && $cfgaArr[5] > 0) : ?>
                            <a href="<?= $portalInfo[$ARTICLE_ID_ARTICLE]["URL_PORTAIL"] ?>" class="button">
                                <?= $portalInfo[$ARTICLE_ID_ARTICLE]["NOM_PORTAIL"] ?>
                            </a>
                            <?php
                        endif;
                    }else {
                        if (isset($articlesButtons[$ARTICLE_ID_ARTICLE]) && $articlesButtons[$ARTICLE_ID_ARTICLE]['STATUT'] == 2) {
                            echo '<a href="' . $articlesButtons[$ARTICLE_ID_ARTICLE]['HREF'] . '" ' . (isset($articlesButtons[$ARTICLE_ID_ARTICLE]['CLASS']) ? ('class="' . $articlesButtons[$ARTICLE_ID_ARTICLE]['CLASS'] . '"') : '') . '>' . $articlesButtons[$ARTICLE_ID_ARTICLE]['LIB'] . '</a>';
                        }
                    }
                    require_once(__DIR__ . '/../CommonBlocs/actionsBiblio.php');
                    checkBiblio($ARTICLE_ID_REVUE, $ARTICLE_ID_NUMPUBLIE, $ARTICLE_ID_ARTICLE, $authInfos);
                    */?>
                </div>
                <div class="contexte"><?= $CONTEXTE ?></div>
            </div>

                    <!-- FIN DE RECHERCHE DE REVUE -->
        <?php endif; ?>
    <?php endif; ?>

<?php endforeach; ?>
</div>



<!--div class="right" style="float:right; padding-top:20px;"><a class="search_button" href="/redirect_to_french_research.php?searchTerm=<?= $searchTerm ?>">Extend your search on cairn.info</a></div-->
<div style="text-align:right;"><a class="search_button" href="/redirect_to_french_research.php?searchTerm=<?= $searchTerm ?>">Extend your search on cairn.info</a></div>
</div>

<?php
$nbPerPage = 20;
$nbAround = 2;
$jsPager = "move";
//$limit = 20;
$countNum = $stats->TotalFiles;
if ($countNum > $nbPerPage)
    require_once __DIR__ . '/../CommonBlocs/pager.php'; ?>
