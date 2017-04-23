/****************************************************************************
 * Formatage de string
 * --------------------------------------------------------------------------
 * https://github.com/andrefarzat/javascript-string-format
 *
 * Formatage très simple d'une chaine de caractère.
 * Ex :
 *     console.log("Bonjour nom={0} prenom={1}".format('Machiavel', 'Nicolas'));
 *     // "Bonjour nom=Machiavel prenom=Nicolas"
 ***************************************************************************/
String.prototype.format = function() {
    var args = arguments;
    return this.replace(/\{(\d+)\}/g, function(a, b) {
        return typeof args[b] != 'undefined' ? args[b] : a;
    });
}
/****************************************************************************
 *   Fin de formatage de string
 * **************************************************************************/


$(function() {
    "use strict";
    $('a[onclick][href="#"]').click(function(event) {
        event.preventDefault();
    });
    // Permet d'afficher les tooltips au survol de la souris sur les élements ayant un attribut @data-tooltip
    if ($.fn.hasOwnProperty('tipTip')) {
        $('[data-tooltip]').tipTip({
            'attribute' : 'data-tooltip',
            'defaultPosition' : 'left'
        });
    }
});

cairn = {}

// Utilisé pour contourner les problèmes de balises. Principalement utiliser pour stocker les informations
// utilisateurs fourni par bloc_header
cairn.metadata = {}


cairn.show_menu = function(element, uid) {
    var menu = $(uid);
    var $element = $(element);

    if (!menu.data('height')) {
        menu.show();
        menu.data('height', menu.outerHeight());
        menu.hide();
    }

    if (!menu.is(':visible')) {
        cairn.close_menu($element);
        $element.addClass('active');
        menu.height(0);
        menu.show();
        menu.animate({'height': menu.data('height'), 'margin-bottom': '1em'});
    } else {
        cairn.close_menu($element);
    }
}
cairn.close_menu = function($elem) {
    $elem = $elem || $(this);
    $elem.removeClass('active'); /* On supprime la classe active qui met en surbrillance le menu actif */
    var menus = $('.menu');
    menus.each(function() {
        var that = $(this)
        if (that.is(':visible'))
            that.animate({'height': 0, 'margin-bottom': 0}, function() {
                that.hide()
            });
    });
}


cairn.triggerMenu = function(mapping, className) {

    "use strict";
    className = className || 'active';

    for (var index = 0, item; item = mapping[index]; index++) {

        var $src = item.src.first(), $dest = item.dest.first();
        if (!$src.length || !$dest.length) {
            continue;
        }
        if (!$dest.data('height')) {
            $dest.show();
            $dest.data('height', $dest.innerHeight());
            $dest.hide();
        }
        // Voir http://robertnyman.com/2008/10/09/explaining-javascript-scope-and-closures/
        var func = function($src, $dest, index) {
            return function(ev) {
                var isActive = $src.hasClass(className);
                if (isActive) {
                    $dest.animate({'height': 0, 'margin-bottom': 0}, function(){
                        $src.removeClass(className);
                        $dest.removeClass(className);
                        $dest.hide();
                    });
                } else {
                    var promise = $.Deferred();
                    for (var subindex = 0, subitem; subitem = mapping[subindex]; subindex++) {
                        if (subindex == index) { continue; }
                        var subfunc = function(subitem) {
                            return function() {
                                subitem.src.removeClass(className);
                                subitem.dest.animate({'height': 0, 'margin-bottom': 0}, function() {
                                    subitem.dest.removeClass(className);
                                    subitem.dest.hide();
                                })
                            };
                        }(subitem);
                        promise.done(subfunc);
                    }
                    promise.done(function() {
                        $src.addClass(className);
                        $dest.addClass(className);
                        $dest.height(0);
                        $dest.show();
                        $dest.animate({'height': $dest.data('height'), 'margin-bottom': '1em'});
                    });
                    promise.resolve();

                    //Ajustement au niveau de l'affichage. 08/09/2015
                    if (($dest.attr('id') == 'add-to-cart-slider-purchase-numero') || ($dest.attr('id') == 'add-to-cart-slider-purchase-revue')) {
                    	document.location.href="#add-to-cart-trigger-numero";
                    } else if (($dest.attr('id') == 'add-to-cart-slider-purchase-article')) {
                        document.location.href="#page_header";
                    }

                }
                ev.preventDefault();
                return false;
            }
        }($src, $dest, index);
        $src.click(func);
    };
};

/****************************************************************************
 * Boite modale
 * --------------------------------------------------------------------------
 * Pour l'utiliser :
 * - Créer une seule et unique <div id="blackground" /> pour permettre la coloration de fond.
 *   L'endroit où est situé cette div n'a aucune importance.
 * - Créer une div avec pour classe 'window_modal', avec un id et ne contenant
 *   qu'un seul enfant (pour conserver la mise en forme
 *   préétablie dans style.css).
 * - Placer un attribut onclick="show_modal(mon_id);" sur les'élements qui feront
 *   apparaitre la boite modale.
 * - Placer un attribut onclick="close_modal();" sur les élements qui feront dispaitre
 *   la boite modale.
 * S'utilise conjointement avec la feuille de style "style.css".
 *
 * 16/08/2013 (serge.kilimoff) : réécrite complète des boites modales.
 *  Bien plus facile d'utilisation, avec la majorité du code de style qui passe en css
 *  et on passe d'une quarantaine de ligne js à 6 lignes.
 ***************************************************************************/
cairn.show_modal = function(uid) {
    $('#blackground').fadeIn();
    $(uid).fadeIn();
}

cairn.close_modal = function() {
    $('.window_modal').fadeOut();
    $('#blackground').fadeOut();
}
/****************************************************************************
 *   Fin des fonctions de boite modale
 * **************************************************************************/



// Tirée de cairn-int.
// Fait apparaitre et disparaitre la flèche pour faire défiler la page tout en haut.
// FIX (serge.kilimoff): J'ai légèrement réécrit la fonction pour qu'elle soit plus lisible, et qu'elle ne lève pas d'erreurs si $trigger_jump n'est pas trouvé.
$(document).ready(function() {
    var $trigger_jump = $("#jump-top");
    if (!$trigger_jump)
        return false;
    var $window = $(window);
    var $body = $('body, html');

    if ($window.scrollTop() < 100)
        $trigger_jump.hide();

    $window.scroll(function() {
        if ($window.scrollTop() > 100)
            $trigger_jump.fadeIn();
        else
            $trigger_jump.fadeOut();
    });

    $trigger_jump.click(function() {
        $body.animate({scrollTop: 0}, 400);
        return false;
    });
});



/****************************************************************************
 * Parseur d'url
 * --------------------------------------------------------------------------
 * cairn.parse_GET récupère les paramètres GET de l'url actuelle.
 * cairn.parse_url_GET récupère les paramètres GET de l'url passée en argument.
 * cairn.parse_hash récupère les paramètres hash de l'url actuelle.
 *
 * Un objet est retournée, contenant comme clés le nom des paramètres et
 * comme valeurs les valeurs de paramètres.
 * Le résultat est mis en cache automatiquement.
 ****************************************************************************/
var _parse_query = function(query) {
    if (!query)
        return {};
    // Tirée de http://stackoverflow.com/questions/979975/how-to-get-the-value-from-url-parameter
    var query_string = {};
    var query = decodeURIComponent(query); // Les paramètres doivent être réencodée en "unicode".
    var vars = query.split("&");
    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split("=");
        // If first entry with this name
        if (typeof query_string[pair[0]] === "undefined") {
            query_string[pair[0]] = pair[1];
            // If second entry with this name
        } else if (typeof query_string[pair[0]] === "string") {
            var arr = [query_string[pair[0]], pair[1]];
            query_string[pair[0]] = arr;
            // If third or later entry with this name
        } else {
            query_string[pair[0]].push(pair[1]);
        }
    }
    return query_string
}
cairn.parse_url_GET = function(url) {
    return _parse_query(url.substring(url.indexOf('?') + 1))
}

cairn.parse_GET = function() {
    var params;
    if (!!cairn._parse_GET)
        return cairn._parse_GET;
    params = _parse_query(window.location.search.substring(1));
    cairn._parse_GET = params;
    return params;
}

cairn.parse_hash = function() {
    var params;
    if (!!cairn._parse_hash)
        return cairn._parse_hash;
    params = _parse_query(window.location.hash.substring(1));
    cairn._parse_GET = params;
    return params;
}
/****************************************************************************
 *   Fin des fonctions de parseur url
 * **************************************************************************/



/****************************************************************************
 * Complétion automatique d'un formulaire à partir du GET
 * --------------------------------------------------------------------------
 * Un contexte peut-être fourni à la méthode.
 * Ce contexte est un objet JS, avec comme clé possible:
 *      root => Le sélécteur JQUERY à partir duquel sera recherché les inputs
 *              du formulaire. Si cette clé n'est pas fourni,
 *              prend $(document) par défaut.
 *      params => Les paramètres issus du GET. Par défaut, prends ceux fourni
 *              par la fonction cairn.parse_GET.
 *
 * Pour des raisons de performances, il est vivement conseillé d'indiquer le
 * selecteur root le moins éloigné du formulaire que l'on souhaite remplir.
 * Si il le faut, ne pas hésiter à faire plusieurs passes.
 * Pour un exemple d'utilisation, voir recherche_avancee.php
 ****************************************************************************/
var CHECKBOX_TRUE_VALUES = ['on', 'true', true]
cairn.autofill_form_from_GET = function() {
    var context = arguments[0] || {};
    var $root = context['root'] || $(document);
    var params = context['params'] || cairn.parse_GET();
    if (!$root.length || $.isEmptyObject(params))
        return false;

    var $inputs = $root.find('input, select');
    var length = $inputs.length;
    if (!length)
        return false;

    var $input, name, type, old_value, new_value;
    for (var index = 0; index <= length; index++) {
        $input = $($inputs[index]);
        name = $input.prop('name');
        type = $input.prop('type') || 'text';
        old_value = $input.val();
        new_value = params[name] || null;

        if ((new_value == null || old_value == new_value) && type != 'radio')
            continue

        if (type == 'checkbox') {
            new_value = (CHECKBOX_TRUE_VALUES.indexOf(new_value) >= 0);
            $input.prop("checked", new_value);
        }
        else if (type == "radio") {
            $input.prop('checked', new_value == old_value);
        }
        else if (['text', 'email', 'select-one'].indexOf(type) >= 0) {
            $input.val(new_value);
        }
        else if (['submit', 'hidden'].indexOf(type) >= 0) {
            continue;
        }
        else {
            console.log('No supported for autofill form from GET parameters', name, type, old_value, new_value);
            continue;
        }
        $input.change();
        // $input.val(new_value);
    }
}
/****************************************************************************
 *   Fin de la complétion automatique depuis le GET
 * **************************************************************************/




/****************************************************************************
 * Conserve la position du viewport d'une page à l'autre
 * --------------------------------------------------------------------------
 * Pour enregistrer la position du viewport, placer l'attribut
 *      onclick="cairn.register_screenTop(this);"
 * sur une balise <a>.
 * Pour que cette valeur soit récupéré et utilisé, placer un
 *      <script>cairn.load_screenTop();</script>
 * à la fin de la page. Pour des raisons visuelles, il est préférable d'exécuter
 * cette fonction lors de sa rencontre par le DOM, et non au chargement complet
 * de la page (sinon, la page saute tout en haut le temps du chargement, et resaute
 * au viewport enregistré quand la page est totalement chargé).
 ****************************************************************************/
cairn.register_screenTop = function(elem) {
    var $this = $(elem);
    var href = $this.prop('href');
    if (!href)
        return true;
    $this.prop('href', href + '#window_top=' + $(window).scrollTop())
}

cairn.load_screenTop = function() {
    var top = cairn.parse_hash()['window_top'];
    if (!top)
        return;
    window.location.hash = window.location.hash.replace("window_top=" + top, '');
    $(window).scrollTop(top);
}
/****************************************************************************
 *   Fin de la conservation du viewport d'une page à l'autre
 * **************************************************************************/




/****************************************************************************
 * Aligne vers le bas un élement depuis son parent
 * --------------------------------------------------------------------------
 * Correspond à l'hypothétique vertical-align:bottom qui ne fonctionne pas avec
 * les blocks.
 *
 * $elem est le selecteur jquery à aligner vers le bas.
 * $parent est le selecteur qui sera considéré comme parent. Si il n'est pas indiqué,
 * alors le parent sera le parent réel de $elem.
 ****************************************************************************/
cairn.align_bottom = function($elem, $parent) {
    if (!$elem.length)
        return false;
    $parent = $parent || $elem.parent();
    var elem_y = $elem.offset().top + $elem.outerHeight(true);
    var parent_y = $parent.offset().top + $parent.outerHeight();

    if (elem_y < parent_y) {
        $elem.css({
            position: 'relative',
            top: Math.round(parent_y - elem_y)
        })
    }
}
/****************************************************************************
 *   Fin de l'alignement vers le bas d'un élement
 * **************************************************************************/




/****************************************************************************
 * Campagne éditeurs provisoire
 * --------------------------------------------------------------------------
 * Merci d'éviter de mettre trop de code dans cette partie.
 * Elle sert pour des campagnes ponctuelles suite à une demande éditeur.
 * Si c'est pour une campagne de longue durée, préférer une autre solution
 * que en JS.
 ****************************************************************************/
campaigns = {}
campaigns.shs_03_2014_stack = []

campaigns.shs_03_2014 = function() {
    if (cairn.metadata['inst_id_user'] != 'biblio_shs')
        return;
    var stack = campaigns.shs_03_2014_stack;
    var data, index, length = stack.length;

    $.get('./static/shs_campaign_test_2014.txt', function(data) {
        data = $.trim(data).split(';');
        for (index = 0; index < length; index++) {
            if (data.indexOf(stack[index][0]) < 0)
                continue;
            stack[index][1].css('display', 'block');
        }
    });
}
/****************************************************************************
 *   Fin de campagne éditeurs provisoire
 * **************************************************************************/

cairn.affichefact = function() {
    if (document.getElementById('checkidemadresse').checked) {
        document.getElementById('adressefact').style.display = 'none';
        document.getElementById('fact_nom').required = false;
        document.getElementById('fact_adr').required = false;
        document.getElementById('fact_cp').required = false;
        document.getElementById('fact_ville').required = false;
    } else {
        document.getElementById('adressefact').style.display = 'block';
        document.getElementById('fact_nom').required = true;
        document.getElementById('fact_adr').required = true;
        document.getElementById('fact_cp').required = true;
        document.getElementById('fact_ville').required = true;
    }
}

cairn.panierCredit = function() {
    var credits = document.getElementsByName('credit');
    var credit_value;
    for(var i = 0; i < credits.length; i++){
        if(credits[i].checked){
            credit_value = credits[i].value;
        }
    }
    window.location.href= './mon_panier.php?ID_CREDIT='+credit_value;
}



/****************************************************************************
* Confirmation pour stat_log
* --------------------------------------------------------------------------
* Pour faciliter la détection des robots, on envoi une requête ajax au serveur
* pour confirmer la prise en compte de javascript.
* On part du principe que la majorité des bots n'enclenchent pas le javascript
*****************************************************************************/
$(function() {
    // On renvoi une confirmation à stat_log
    var statLogToken = $("meta[name='id-cross-log']");
    if (statLogToken.length) {
        $.post('./?controleur=Pages&action=statLogCrossValidation', {
            'id-cross-log': statLogToken.attr('content'),
        });
    }
});

/****************************************************************************
* Inscriptions à la traduction d'un article
* --------------------------------------------------------------------------
* Cette fonction va automatiquement recherché les inscripteurs qui permettent
* l'inscription aux alertes de traduction d'un article.
* En cliquant sur cet inscripteur (typiquement, le point d'interrogation qui
* apparait sur le bouton grisé visuellement d'un article non traduit), cela
* va ouvrir une boite modale qui enverra un formulaire à la validation pour
* que l'utilisateur puisse être prevenu si une traduction est réalisé.
*
* Ces inscripteurs doivent avoir un attribut data-suscribe-on-translation, qui
* indiquera l'ID_ARTICLE, ainsi qu'un attribut data-article-title pour le titre
* de l'article, qui apparaitra dans la boite modale.
*
* Un exemple est disponible sur la page des résultats de recherche
*
* --------------------------------------------------------------------------
* Ce script est inséré dans le JS global, car il doit être utilisé sur plusieurs pages.
*
* TODO: Quand le formulaire est correctement envoyé, il devrait y avoir la non
* possibilité de se réinscrire à cette traduction.
***************************************************************************/
$(function() {
    var $inscriptors = $('[data-suscribe-on-translation]');
    // Si il n'y a aucun inscripteurs, on arrête le traitement.
    if (!$inscriptors.length)
    return false;
    // Les inputs nécéssaire au bon envoi du formulaire d'inscription
    var $register_id_article = $('input#id_article_translation');
    var $register_title_article = $('#article-title_translation');
    for (var i=0; i < $inscriptors.length; i++) {
    var $inscriptor = $($inscriptors[i]);
    $inscriptor.click(function() {
    // Quand on clique sur un inscripteur, il va remplir les inputs nécéssaires au bon envoi du formulaire.
    // Les données fourni dans les inputs correspondent à ce qui est indiqué dans les attributs datas.
    var $this = $(this);
    $register_id_article.val($this.data('suscribe-on-translation'));
    $register_title_article.html($this.data('article-title'));
    cairn.show_modal('#modal_why-not-article');
    });
    }
});

$(function() {
    $("#alert_on_translation").submit(function(event) {
        event.preventDefault();
        var $form = $(this);
        var email = $form.find('input[name="email_translation"]').val();
        var id_article = $form.find('input[name="id_article_translation"]').val();

        $.post($form.attr('action'), {email: email, id_article: id_article}, function() {
            $('#modal_why-not-article').hide();
            cairn.show_modal('#modal_confirm-why-not-article');
//                     $form.replaceWith($("&lt;p&gt;email registered.&lt;/p&gt;"));
        });
    });
});

function rssrech() {
        var lurl = document.URL;
        var tablurl =     lurl.split('?');
        var leparam = tablurl[1];
        var reg1=new RegExp("searchTerm","g");
        if (leparam.match(reg1))
        {
        leparam = leparam + '&chk_revue=on&chk_ouvcol=on&chk_ouvref=on&chk_mag=on&soumettre=Lancer+la+recherche';
        leparam = leparam.replace("searchTerm",'larech1');
        leparam = leparam.replace("BACK=1&", '').replace("BACK=1&", '').replace("BACK=1&", '').replace("BACK=1&", '').replace("BACK=1&", '').replace("BACK=1&", '').replace("BACK=1&", '').replace("_BACK=1&", '').replace("_BACK=1&", '').replace("_BACK=1&", '').replace("_BACK=1&", '').replace("_BACK=1&", '');
        }
        var lelien = 'static/includes/rss-recherche.php?'+ leparam.replace(/&_/g, '&').replace(/MOV=([0-9]*)&/g, '').replace(/&_/g, '&');
        window.location.href = lelien;
    }
