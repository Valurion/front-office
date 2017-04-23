"use strict";
/*
    Script pour la recherche et sa fonction d'auto-complétion.
*/



var AJAX_URLS = {
    'pythagoria_sugg': window.location.protocol + '//dev.pythagoria.com/pth_cairn_labo',
    'json_revues_urls': './static/json/autocompletion-url-revues.json',
    'json_diacritics': './static/json/diacritics.json'
}

/*
    L'url permettant de rediriger vers la bonne page
*/
var SEARCH_URL = './index.php?controleur=Recherche&action=redirectFromAutocomplete&term={0}&category={1}';
/*
    Les réponses renvoyés par pythagoria contiennent le type de catégories.
    Par raison d'optimisation, cette catégorie est représenté par une lettre.
    La table qui suit réalise la correspondance entre cette lettre et la string
    qui sera affichée à l'utilisateur.
*/
var CATEGORIES = {
    'A': 'Auteurs',
    'R': 'Revues/Collections',
    'O': 'Titres',
    'E': 'Expressions'
};





/*
    Cette partie du script concerne la personalisation du widget autocomplete de jquery.
    Y est ajouté :
        * une catégorisation des données proposées.
        * une mise en emphase de la recherche pour chaque item.
*/
$(function() {
    // Table de translittération vers ASCII.
    // Sera chargé uniquement au besoin.
    var diacritics = null;

    function removeDiacritics(str) {
        /*
            Translittération vers une table ASCII (sans accents), en utilisant la table `diacritics`.

            @str: string
            @return: string

            Notes ::
                Pour des raisons de temps de charge, la table de translittération est chargée uniquement si besoin.
        */
        "use strict";
        if (diacritics === {}) {
            return str;
        }

        if (diacritics === null) {
            $.ajax({
                url: AJAX_URLS['json_diacritics'],
                async: false,
                ifModified: true,
                dataType: 'json',
                success: function(data, textStatus, jqXHR) {
                    diacritics = data;
                },
                error: function(jqXHR, textStatus, err) {
                    diacritics = {};
                }
            });
        }

        // Les lettres accentués sont ramenés à leurs équivalent ASCII. Par exemple, é->e
        var c;
        var str_length = str.length;
        var result = [];

        for (var i=0; i < str_length; i++) {
            c = str[i];
            // Pour des raisons de performances, on vérifie la présence du caractère dans la table diacritics uniquement si son codepoint est en dehors de la table ASCII.
            // 192 correspond au premier caractère accentué dans la table latin-1/utf-8, soit 'À'.
            if (c.charCodeAt() < 192) {
                result.push(c);
                continue;
            }
            result.push(diacritics.hasOwnProperty(c) ? diacritics[c] : c); // TODO: mesurer les perfs sur ça. Peut être moyen de micro-optimiser en zappant hasOwnProperty
        }
        return result.join('');
    }




    function hightlightSearchTerm(label, term) {
        // Pour chaque item, on met en surbrillance le terme recherché.
        // Par exemple, si on cherche le terme "Geo", cela donnera pour Géomorphologie : <strong>Géo</strong>morphologie
        // On ne tient pas compte des accents/casse/etc...

        // TODO: Pas encore audité ce code, hormis pour la variable globale termsReq qui a été supprimé et remplacé par un paramètre. La valeur de retour
        // a aussi été modifié, pour retourner une string en lieu et place d'une imbrication d'élements DOM.
        var termReq = removeDiacritics(term).trim();
        var termsReq = termReq.split(/[, \-'"]+/);
        var newitemLabel = " " + removeDiacritics(label) + " ";
        var newtermsReq = [];
        for (var key = 0; key < termsReq.length; key++) {
            if (termsReq[key].length > 0) {
                newtermsReq.push($.ui.autocomplete.escapeRegex(termsReq[key]));
            }
        }
        var myexpression = ((newtermsReq.join("\\b|\\b")));
        var patt1 = new RegExp("\\b" + myexpression, "ig");

        var myArray;
        var iii = 0;

        var marqueur1 = "<strong>";
        var marqueur2 = "</strong>";

        var pth_item_label = "";
        var last = 0;
        while ((myArray = patt1.exec(newitemLabel)) !== null) {
            pth_item_label = pth_item_label + label.substr(last, (patt1.lastIndex - myArray[0].length - last - 1)) + marqueur1 + label.substr(patt1.lastIndex - 1 - myArray[0].length, myArray[0].length) + marqueur2;
            last = patt1.lastIndex - 1;
        }
        return pth_item_label + label.substr(last);
    }


    // Personnalisation du widget d'autocomplétion.
    $.widget("custom.catcomplete", $.ui.autocomplete, {
        _renderMenu: function(ul, items) {
            var term = this.term,
                length = items.length,
                lastCategory, item;

            // Pour chaque item, on ajoute sa catégorie, si pas encore existante dans le menu d'autocomplétion.
            for (var i = 0; i < length; i++) {
                item = items[i];
                if (item.category !== lastCategory) {
                    ul.append(
                        $('<li class="ui-autocomplete-category">{0}</li>'.format(CATEGORIES[item.category]))
                    );
                    lastCategory = item.category;
                }
                ul.append(
                    $('<li><a>{0}</li></a>'.format(hightlightSearchTerm(item.label, term)))
                    .data('ui-autocomplete-item', item)
                );
            }
        }
    });

});





/*
    Cette partie concerne l'interaction entre l'utilisateur et l'autocomplétion.
    Ici sont effectués les requêtes vers pythagiora et la résolution des urls de résultats.
*/
$(function() {
    var $search_field = $('#compute_search_field');
    // sur certaines pages, jquery.ui est déjà défini et provoque des conflits de versions. En attendant de résoudre les problèmes de conflit sur toutes les pages, on vérifie si autocomplete est défini dans les prototypes de nodes jquery.
    if (!$search_field.autocomplete) {
        return false;
    }


    function resolveCairnUrl(term, category) {
        /*
            À partir des paramètres fournis, renvoie vers la page correspondante.

            @term: string
            @category: string
        */
        var location = document.location;
        term = term.replace(String.fromCharCode(160), ' '); // FIX: pour harmoniser les nbsp en blanc
        // TODO: harmoniser les termes renvoyés par pythagoria
        term = term.replace('%20', '+');

        location.href = SEARCH_URL.format(encodeURIComponent(term), category);
    }

    // Configuration du plugin d'auto-completion
    $search_field.catcomplete({
        delay: 100,
        minLength: 2,
        select: function(e, ui) {
            // Une sélection doit rediriger vers une page donnée
            return resolveCairnUrl(ui.item.value.trim(), ui.item.category);
        },
        source: function(req, add) {
            // Préparation de la requête à envoyer chez pythagorio
            var request = JSON.stringify({
                'params': [
                    req.term,
                ],
                'id': 1,
                'jsonrpc': '2.0',
                'method': 'myCpl',
            });

            $.post(AJAX_URLS['pythagoria_sugg'], request, function(response) {
                /*
                    Pour chaque item présent dans la réponse, on capitalise la première lettre
                    et on ajoute un @id
                    Ces items modifiés sont ensuite renvoyés vers le plugin d'autocompletion.
                */
                var suggestions = [];
                var result = response.result;
                var length = result.length;
                var suggestion;

                for (var i = 0; i < length; i++) {
                    suggestion = result[i];
                    suggestion.id = 'pth_' + i;
                    suggestion.label = suggestion.value.charAt(0).toUpperCase() + suggestion.value.slice(1);
                    suggestions.push(suggestion)
                }
                add(suggestions);
            }, "json");
        },
    });
});
