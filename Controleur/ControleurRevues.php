<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * CONTROLER - Control the navigation
 * @version 0.1
 * @author ©Pythagoria - www.pythagoria.com - Pierre-Yves THOMAS
 * @author Pierre-Yves THOMAS
 * @todo : Refactorization, Methods merging :encyclopedies => ouvrages<>ouvrageDisciplines and index<>disciplines (rename to revues )
 */
require_once 'Framework/Controleur.php';

// loading the related Model
require_once 'Modele/Content.php';

class ControleurRevues extends Controleur {

    private $content;

    // instantiate the Model Class
    public function __construct() {
        $this->content = new Content();
    }

    public function index() {
        $typePublication = 1;
        if ($this->requete->existeParametre("TYPEPUB")) {
            $typePublication = $this->requete->getParametre("TYPEPUB");
        }
        $anneeFilter = null;
        if ($this->requete->existeParametre("ANNEE")) {
            $anneeFilter = $this->requete->getParametre("ANNEE");
        }

        $revueFilter = null;
        if ($this->requete->existeParametre("REVUE")) {
            $revueFilter = $this->requete->getParametre("REVUE");
            $revues = $this->content->getRevuesByUrl($revueFilter, null, $typePublication);
        } elseif ($this->requete->existeParametre("ID_REVUE")) {
            $IdRevue = $this->requete->getParametre("ID_REVUE");
            $revues = $this->content->getRevuesById($IdRevue);
        }
        if (($revues[0]['STATUT'] == 0) || (!isset($revues[0])) || (!$revues[0])) {
            if (!Configuration::get('allow_backoffice', false)) {
                //header('Location: http://'.Configuration::get('urlSite', 'www.cairn.info'));
                header('Location: http://' . Configuration::get('urlSite', 'www.cairn.info') . '/error_id.php');
                die();
            } else if (!isset($revues[0]) || !$revues[0]) {
                echo ($typePublication == 2) ? "Ce magazine n'existe pas" : "Cette revue n'existe pas";
                die();
            }
        }
        if($revues[0]['REVUE_COURANTE'] != ''){
            $revuePrec = $revues[0];
            $revues = $this->content->getRevuesById($revues[0]['REVUE_COURANTE']);
        }
        $curDisc = $this->content->getCurDisciplineEn($revues[0]["ID_REVUE"]);
        $curDiscipline = $curDisc['URL_REWRITING_EN'];
        $filterDiscipline = $curDisc['DISCIPLINE_EN'];
        $numeros = $this->content->getNumeroRevuesById($revues[0]["ID_REVUE"], $anneeFilter);
        $revuePrecedente = $revues[0]['REVUE_PRECEDENTE'];
        $revuesPrec = array();
        while($revuePrecedente != ''){
            $numerosPrec = $this->content->getNumeroRevuesById($revuePrecedente, $anneeFilter);
            $revuePrec = $this->content->getRevuesById($revuePrecedente)[0];
            $titrePrec = $revuePrec['TITRE'];
            $libellePrec = $revuePrec['LIBELLE'];
            $revuesPrec[] = [
                "TITRE" => $titrePrec,
                "LIBELLE" => $libellePrec,
                "URL_REWRITING" => $revuePrec['URL_REWRITING'],
                "NUMEROS" => $numerosPrec
            ];
            $revuePrecedente = $revuePrec['REVUE_PRECEDENTE'];
        }

        // Récupération de la traduction (si elle existe)
        // Version normale de cairn
        if(Configuration::get('mode') == 'normal') {
            // Ajout des données récupérées dans le tableau $revues
            // Le tableau est exploité dans Revues/index.php + Revues/Blocs/indexRevue.php
            require_once 'Modele/ManagerIntPub.php';
            $managerIntPub          = new ManagerIntPub('dsn_int_pub');
            $idNumeroCairn          = $revues[0]["ID_REVUE"]; // ex.: $idNumPublie = RHS;
            $idsInt                 = $managerIntPub->checkIfRevueOnCairnInt($idNumeroCairn);

            // Insertion des données dans le résultat final
            $revues[0]["ID_REVUE_INT"] = $idsInt["ID_REVUE"]; // Ajout de l'ID de la revue INT dans les données de la revue
            $revues[0]["URL_REWRITING_INT"] = $idsInt["URL_REWRITING_EN"]; // Ajout de la valeur de l'URL REWRITING
            //var_dump($idsInt);
        }

        $efta = 0;
        if(Configuration::get('mode') == 'cairninter'){
            $efta = $this->content->countEnglishFullTextArticles($revues[0]["ID_REVUE"]);
        }

        $typesAchat = Service::get('ControleAchat')->checkAchats($this->authInfos,$revues[0]);


        if ($typePublication == 1) {
            $namePage = 'revue';
        } else if ($typePublication == 2) {
            $namePage = 'magazine';
        } else {
            $namePage = null;
        }

        //Partie webTrends.
        $webtrendsService = Service::get('Webtrends');
        if ($revues[0]['TYPEPUB'] == 1) {
            $dataDiscipline = $this->content->getDisciplinesOfRevue($revues[0]['ID_REVUE']);
        }
        $webtrendsTags = array();
        $webtrendsTags = array_merge(
            $webtrendsTags,
            $webtrendsService->getTagsForAllPages($namePage, $this->authInfos),
            $webtrendsService->getTagsForRevuePage($revues[0], $dataDiscipline)
        );
        $headers = $webtrendsService->webtrendsTagsToHeadersTags($webtrendsTags);


        if ($typePublication == 1) {
            $this->genererVue(array('revue' => $revues[0], 'numeros' => $numeros,
                                    'curDiscipline' => $curDiscipline, 'filterDiscipline' => $filterDiscipline,
                                    'revue_url_Rewriting' => $revueFilter, 'typesAchat' => $typesAchat,
                                    'revuesPrec' => $revuesPrec, 'efta' => $efta), null, null, $headers);
        } else if ($typePublication == 2) {
            //pour les magazines, on a besoin d'un référentiel des années, pour la navigation...
            $annees = $this->content->getMinMaxAnneeRevuesById($revues[0]["ID_REVUE"]);
            $refAnnees = [
                "current" => $anneeFilter ? $anneeFilter : $numeros[0]["NUMERO_ANNEE"],
                "last" => $annees["MAX"],
                "first" => $annees["MIN"]
            ];
            $this->genererVue(array('revue' => $revues[0], 'numeros' => $numeros,
                                    'revue_url_Rewriting' => $revueFilter, 'refAnnees' => $refAnnees,
                                    'typePub' => 'magazine', 'typesAchat' => $typesAchat), 'indexMag.php', null, $headers);
        }
    }

    public function numero() {
        $numeroFilter = null;
        $numeroIsbn = null;
        $oneTokenBAD = false;
        if ($this->requete->existeParametre("ID_NUMPUBLIE")){
            $idNumPublie = $this->requete->getParametre("ID_NUMPUBLIE");
            $numero = $this->content->getRevueNumeroFromId($idNumPublie);
            $typePublication = $numero["REVUE_TYPEPUB"];
            $idRevue = $numero["REVUE_ID_REVUE"];
            $revueFilter = $numero["REVUE_URL_REWRITING"];
            $numeroIsbn = $numero['NUMERO_ISBN'];
            $numeroFilter = $numero['NUMERO_URL_REWRITING'];
        }else if ($this->requete->existeParametre("ISBN")) {
            //On est dans le cadre d'un ouvrage ou une encyclopedie
            $numeroFilter = $this->requete->getParametre("REVUE");
            $numeroIsbn = $this->requete->getParametre("ISBN");
            $numero = $this->content->getRevueNumeroFromUrlAndIsbn($numeroFilter, $numeroIsbn);
            $typePublication = $numero["REVUE_TYPEPUB"];
            $idRevue = $numero["REVUE_ID_REVUE"];
            $revueFilter = $numero["REVUE_URL_REWRITING"];
            $idNumPublie = $numero["NUMERO_ID_NUMPUBLIE"];
        } else {
            //On est dans le cadre d'une revue ou d'un magazine
            if ($this->requete->existeParametre("REVUE")) {
                $revueFilter = $this->requete->getParametre("REVUE");
            } elseif ($this->requete->existeParametre("ID_REVUE")) {
                $IdRevue = $this->requete->getParametre("ID_REVUE");
                $revue = $this->content->getRevuesById($IdRevue);
                $revueFilter = $revue[0]["URL_REWRITING"];
            }
            if ($this->requete->existeParametre("ANNEE")) {
                $anneeNumero = $this->requete->getParametre("ANNEE");
            }
            if ($this->requete->existeParametre("NUMERO")) {
                $numeroNumero = $this->requete->getParametre("NUMERO");
            }else{
                $numeroNumero = '';
            }

            $typePublication = 1;
            if ($this->requete->existeParametre("TYPEPUB")) {
                $typePublication = $this->requete->getParametre("TYPEPUB");
            }

            $idRevue = $this->content->getIdRevueFromUrl($revueFilter, $typePublication);
            $numero = $this->content->getNumpublie($idRevue, $anneeNumero, $numeroNumero)[0];
            $idNumPublie = $numero["NUMERO_ID_NUMPUBLIE"];
            $numero ['NUMERO_AUTEUR'] = $this->content->getAuteursNum($numero ['NUMERO_ID_NUMPUBLIE']); // utilisé pour coordinateur
        }

        // Récupération de la traduction (si elle existe)
        // Ajout des données récupérées dans le tableau $numero
        // Le tableau est exploité dans Revues/numero.php
        // Version normale
        if(Configuration::get('mode') == 'normal') {
            require_once 'Modele/ManagerIntPub.php';
            $managerIntPub          = new ManagerIntPub('dsn_int_pub');
            $idNumeroCairn          = $idNumPublie; // ex.: $idNumPublie = RHS_652;
            $idsInt                 = $managerIntPub->checkIfNumeroOnCairnInt($idNumeroCairn);

            // Insertion des données dans le résultat final
            $numero["ID_REVUE_INT"] = $idsInt["ID_REVUE"]; // Ajout de l'ID de la revue INT dans les données du numéro
            $numero["ID_NUMERO_INT"]= $idsInt["ID_NUMPUBLIE"]; // Ajout de l'ID du numéro INT dans les données du numéro
            // var_dump($idsInt);
        }
        // Version INT
        if(Configuration::get('mode') == 'cairninter') {
            require_once 'Modele/ManagerCairn3Pub.php';
            $managerCairn3Pub           = new ManagerCairn3Pub('dsn_cairn3_pub');
            $idNumeroCairn              = $numero["NUMERO_ID_NUMPUBLIE_S"]; // ex.: $idNumPublie = RHS_652;
            $idsCairn                   = $managerCairn3Pub->getListArticleOnCairn3($idNumeroCairn);
            
            // Insertion des données dans le résultat final
            //$numero["ID_REVUE_CAIRN"]   = $idsCairn["ID_REVUE"]; // Ajout de l'ID de la revue CAIRN3 dans les données du numéro
            //$numero["ID_NUMERO_CAIRN"]  = $idsCairn["ID_NUMPUBLIE"]; // Ajout de l'ID du numéro CAIRN3 dans les données du numéro
            $numero["IDS_ARTICLE_CAIRN"]  = $idsCairn; // Ajout de l'ID du numéro CAIRN3 dans les données du numéro
            //var_dump($idNumeroCairn);
            //var_dump($idsCairn);
        } 

        // En production, les numéros désactivés NE DOIVENT PAS apparaitre
        // En interne, on affiche le numéro, mais on prévient les convertisseurs que le numéro est désactivé, via la vue
//      if (($numero['NUMERO_STATUT'] == '0') || (!isset($numero)) || (!$numero)) {
        if (($numero['NUMERO_STATUT'] == '0') || (!isset($numero['NUMERO_ID_REVUE']))) {
            if (!Configuration::get('allow_backoffice', false)) {
//                header('Location: http://'.Configuration::get('urlSite', 'www.cairn.info'));
                header('Location: http://' . Configuration::get('urlSite', 'www.cairn.info') . '/error_id.php');
                die();
            } else if (!isset($numero) || !$numero) {
                echo "Ce numéro n'existe pas";
                die();
            }
        }

        $countReferencedBy = $this->content->countNumReferencedBy($idNumPublie);
        $revues = $this->content->getRevuesByUrl($revueFilter, $idNumPublie, $typePublication);

        if ((!isset($revues[0])) || (!$revues[0]) || ($revues[0]['STATUT'] == '0')) {
            if (!Configuration::get('allow_backoffice', false)) {
                header('Location: http://'.Configuration::get('urlSite', 'www.cairn.info'));
                die();
            }
        }

        $articles = $this->content->getArticlesFromlNumero($idNumPublie);
        $curDisc = $this->content->getCurDisciplineEn($idRevue);
        $curDiscipline = $curDisc['URL_REWRITING_EN'];
        $filterDiscipline = $curDisc['DISCIPLINE_EN'];

        $efta = 0;
        if(Configuration::get('mode') == 'cairninter'){
            $efta = $this->content->countEnglishFullTextArticles($revues[0]["ID_REVUE"]);
        }

        //Post parsing pour déterminer les boutons à afficher
        switch ($typePublication) {
            case '1':
                $typePubLib = 'revue';
                break;
            case '2':
                $typePubLib = 'magazine';
                break;
            default:
                $typePubLib = '';
                break;
        }
        //On fait déjà la vérif de l'accès au numéro, pour ne pas la faire sur les articles.
        if($numero['NUMERO_PRIX_ELEC'] > 0){
            $accessElecOk = Service::get('ControleAchat')->hasAccessToNumero($this->authInfos,$numero,$revues[0],0,0,'E');
            $accessPapierOk = Service::get('ControleAchat')->hasAccessToNumero($this->authInfos,$numero,$revues[0],1,1,'P');
            $accessOk = ($accessElecOk || $accessPapierOk)?true:false;
        }else{
            $accessOk = Service::get('ControleAchat')->hasAccessToNumero($this->authInfos,$numero,$revues[0]);
            $accessPapierOk = $accessOk;
            $accessElecOk = false;
        }

        //Preprod only - vérification du token acces BàD
        if((Configuration::get('allow_preprod', false)) && isset($_GET['token']))
        {

            //traitement form
            if(isset($_POST['miseenligneAutorisation']))
            {
                if($_POST['correctionAEffectuer'] == "CAE")
                {
                    Service::get("BonADiffuser")->setInterfictionForBAD($numero['NUMERO_ID_NUMPUBLIE'], $_GET['token']);

                    $subject = "Mise en ligne automatique : err signalée(s).";
                    $body = "La mise en ligne de \"".$numero['NUMERO_TITRE']."\" () a été commentée. (". $numero['NUMERO_ID_NUMPUBLIE'] .")<br>
                             Commentaire : ". strip_tags($_POST['commentaires']) ."";
                    $to = "conversion@cairn.info";
                    $from = "noreply@cairn.info";
                    $fromName = "Preprod Cairn";
                    Service::get('Mailer')->sendMailFromParams($subject, $body, $to, $from, $fromName);
                }
                if ($_POST['miseenligneAutorisation'] == "MEL")
                {
                    if($_POST['miseenligne'] == "NOW") {
                        $dateTransfert = new DateTime(date('Y-m-d'));
                        $dateTransfert->add(new DateInterval("P1D"));
                        Service::get("BonADiffuser")->setDateTransfertForBAD($numero['NUMERO_ID_NUMPUBLIE'], $_GET['token'], $dateTransfert->format('Y-m-d'));
                    }
                    elseif( $_POST['miseenligne'] == "date") {
                        if(strptime($_POST['date'] , "%d-%d-%Y") != false)
                        {
                            $dateTransfert = new DateTime($_POST['date']);
                            Service::get("BonADiffuser")->setDateTransfertForBAD($numero['NUMERO_ID_NUMPUBLIE'], $_GET['token'], $dateTransfert->format('Y-m-d'));
                        }
                    }
                    elseif(isset($_POST['miseenlignemois']) ) {
                        $dateTransfert = new DateTime(date('Y-m-d'));
                        $dateTransfert->add(new DateInterval("P". intval($_POST['miseenlignemois']) ."M"));
                        Service::get("BonADiffuser")->setDateTransfertForBAD($numero['NUMERO_ID_NUMPUBLIE'], $_GET['token'], $dateTransfert->format('Y-m-d'));
                    }
                }

            }
            // affichagemenu + autorisation BAD
            $oneTokenBAD = Service::get("BonADiffuser")->checkTokenForBAD($numero['NUMERO_ID_NUMPUBLIE'],$_GET['token'], $numero['NUMERO_DATE_MISEENLIGNE'] );
            if ($oneTokenBAD != false) {
            	$accessOk = true;
            	$accessElecOk = $accessOk;
            }
        }

        $typesAchat = Service::get('ControleAchat')->checkAchats($this->authInfos,$revues[0],$numero);
        $modeBoutons = Configuration::get('modeBoutons');

        //génération de la liste des boutons de la liste des articles
        if($modeBoutons == 'cairninter'){
            Service::get('ContentArticle')
                    ->setTypesAchat($typesAchat)
                    ->readButtonsForInter($articles, $this->authInfos,($accessOk==true?1:0));
        }else{
            //var_dump($accessOk); //$accessOk = true;
            Service::get('ContentArticle')
                    ->setTypesAchat($typesAchat)
                    ->readContentArticlesFromNumero($articles, $typePubLib, $revues[0], $numero, $numeroFilter, $numeroIsbn, $this->authInfos,($accessOk==true?1:0),0);
        }

        // Taggage webtrends
        if (isset($numero['REVUE_TYPEPUB']) && in_array($numero['REVUE_TYPEPUB'], array(3, 6))) { //Cas des ouvrages et des Encyclopédies de poche.
            $dataDiscipline = $this->content->getDisciplinesOfRevue($numero['NUMERO_ID_NUMPUBLIE']);
        } elseif(isset($revues[0]['TYPEPUB']) && $revues[0]['TYPEPUB'] == 1) { //Cas des revues.
            $dataDiscipline = $this->content->getDisciplinesOfRevue($numero['NUMERO_ID_REVUE']);
        }

        //Partie WebTrends.
        $webtrendsService = Service::get('Webtrends');
        $webtrendsTags = array_merge(
            $webtrendsService->getTagsForNumeroPage($numero, $revues[0], $dataDiscipline),
            $webtrendsService->getTagsForAllPages(
                $webtrendsService->mappingTypepubDescPages[$revues[0]['TYPEPUB']] . '-numero',
                $this->authInfos
            )
        );
        $headers = $webtrendsService->webtrendsTagsToHeadersTags($webtrendsTags);

        // Ajout des métadonnées pour Google Scholar, qui parse le contenu de cairn pour le diffuser sur leur portail
        $googleScholarTags = [
            'citation_isbn' => $numero['NUMERO_ISBN'],
            'citation_issn' => $revues[0]['ISSN'],
        ];
        // On transforme les tags en forme normalisés pour le $headers
        foreach ($googleScholarTags as $key => $value) {
            if (!$value) { continue; }
            array_push($headers, array(
                'tagname' => 'meta',
                'attributes' => [
                    array('name' => 'name', 'value' => $key),
                    array('name' => 'content', 'value' => $value),
                ]
            ));
        }


        if ($typePublication == 1) {
            $this->genererVue(array('revue' => $revues[0], 'articles' => $articles,
                                    'numero' => $numero, 'revue_url_Rewriting' => $revueFilter,
                                    'curDiscipline' => $curDiscipline, 'filterDiscipline' => $filterDiscipline,
                                    'countReferencedBy' => $countReferencedBy, 'typesAchat' => $typesAchat, 'efta' => $efta,
                                    'oneTokenBAD' => $oneTokenBAD,
                                    'accessElecOk' => $accessElecOk, 'accessPapierOk' => $accessPapierOk), null, null, $headers);
        } else if ($typePublication == 2) {
            $this->genererVue(array('revue' => $revues[0], 'articles' => $articles,
                                    'numero' => $numero, 'revue_url_Rewriting' => $revueFilter,
                                    'typePub' => 'magazine', 'countReferencedBy' => $countReferencedBy,
                                    'typesAchat' => $typesAchat,
                                    'oneTokenBAD' => $oneTokenBAD,
                                    'accessElecOk' => $accessElecOk, 'accessPapierOk' => $accessPapierOk), 'numeroMag.php', null, $headers);
        } else if ($typePublication == 3 || $typePublication == 6) {
            $this->genererVue(array('revue' => $revues[0], 'articles' => $articles, 'numero' => $numero,
                                    'revue_url_Rewriting' => $revueFilter, 'typePub' => ($typePublication == 3 ? 'ouvrage' : 'encyclopedie'),
                                    'countReferencedBy' => $countReferencedBy, 'typesAchat' => $typesAchat,
                                    'oneTokenBAD' => $oneTokenBAD,
                                    'accessElecOk' => $accessElecOk, 'accessPapierOk' => $accessPapierOk), 'numeroOuv.php', null, $headers);
        }
    }

    public function apropos() {
        if ($this->requete->existeParametre("REVUE")) {
            $revueFilter = $this->requete->getParametre("REVUE");
        } elseif ($this->requete->existeParametre("ID_REVUE")) {
            $IdRevue = $this->requete->getParametre("ID_REVUE");
            $revue = $this->content->getRevuesById($IdRevue);
            $revueFilter = $revue[0]["URL_REWRITING"];
        } elseif ($this->requete->existeParametre("ID_JOURNAL")) {
            $IdRevue = $this->requete->getParametre("ID_JOURNAL");
            $revue = $this->content->getRevuesById($IdRevue);
            $revueFilter = $revue[0]["URL_REWRITING"];
        }
        $revue = $this->content->getAProposRevueFromUrl($revueFilter);
        if (!$revue) {
            // Si la revue n'existe pas, ça ne sert à rien d'afficher une page vide
//            header('Location: http://' . Configuration::get('urlSite', 'www.cairn.info'));
            header('Location: http://' . Configuration::get('urlSite', 'www.cairn.info') . '/error_id.php');
            die;
        }
        $numeros = $this->content->getNumeroRevuesById($revue["ID_REVUE"]);

        // Version INT
        if(Configuration::get('mode') == 'cairninter') {
            require_once 'Modele/ManagerCairn3Pub.php';
            $managerCairn3Pub           = new ManagerCairn3Pub('dsn_cairn3_pub');
            $idRevueCairn               = $revue["ID_REVUE_S"]; // ex.: $idNumPublie = RHS_652;
            $metasCairn                 = $managerCairn3Pub->getMetadataRevueOnCairn3($idRevueCairn);
            
            // Insertion des données dans le résultat final
            $revue["URL_REWRITING_CAIRN"]    = $metasCairn["URL_REWRITING"]; // Ajout de l'URL REWRITE sur cairn.info
        } 

        $curDisc = $this->content->getCurDisciplineEn($revue["ID_REVUE"]);
        $curDiscipline = $curDisc['URL_REWRITING_EN'];
        $filterDiscipline = $curDisc['DISCIPLINE_EN'];

        $headers = Service::get('Webtrends')->webtrendsHeaders('revue-en-savoir-plus', $this->authInfos);

        $this->genererVue(array('revue' => $revue, 'numeros' => $numeros,
                                    'curDiscipline' => $curDiscipline,
                                    'filterDiscipline' => $filterDiscipline), null, null, $headers);
    }

    public function fulltext() {
        $idRevue = $this->requete->getParametre("ID_REVUE");
        $revue = $this->content->getRevuesById($idRevue);
        $revueFilter = $revue[0]["URL_REWRITING"];
        $revues = $this->content->getRevuesByUrl($revueFilter);
        $numeros = $this->content->getNumeroRevuesById($idRevue);
        $articles = $this->content->getEnglishFullTextArticlesFromRevue($idRevue);

        $curDisc = $this->content->getCurDisciplineEn($idRevue);
        $curDiscipline = $curDisc['URL_REWRITING_EN'];
        $filterDiscipline = $curDisc['DISCIPLINE_EN'];

        $typesAchat = Service::get('ControleAchat')->checkAchats($this->authInfos,$revues[0]);
        $modeBoutons = Configuration::get('modeBoutons');
        if($modeBoutons == 'cairninter'){
            Service::get('ContentArticle')
                    ->setTypesAchat($typesAchat)
                    ->readButtonsForInter($articles, $this->authInfos,0);
        }

        $this->genererVue(array('revue' => $revues[0],  'numeros' => $numeros,
                        'articles' => $articles,
                        'revue_url_Rewriting' => $revueFilter,
                        'curDiscipline' => $curDiscipline,
                        'filterDiscipline' => $filterDiscipline,
                        'typesAchat' => $typesAchat));
    }

    /**
     * Récupération des informations de l'article, pour la partie : WebTrends.
     */
    public function getInfosAboutArticleForWebTrends() {

        $idArticle = $this->requete->getParametre("id_article");

        $infoWebTrends = $this->content->getDataTagWebTrends($idArticle);

        echo json_encode($infoWebTrends);
    }

    /**
     * Récupération des informations au niveau du numéro, pour la partie : WebTrends.
     */
    public function getInfosAboutNumeroForWebTrends() {

        $idNumero = $this->requete->getParametre("id_numero");

        $infoWebTrends = $this->content->getDataTagNumeroWebTrends($idNumero);

        echo json_encode($infoWebTrends);
    }

    /**
     * Récupération des informations au niveau de la revue, pour la partie : WebTrends.
     */
    public function getInfosAboutRevueForWebTrends() {

        $idRevue = $this->requete->getParametre("id_revue");
        $idNumero = $this->requete->getParametre("id_numero");

        $infoWebTrends = $this->content->getDataTagRevueWebTrends($idRevue, $idNumero);

        echo json_encode($infoWebTrends);
    }

}
