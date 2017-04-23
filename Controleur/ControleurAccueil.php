<?php

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
require_once 'Modele/Manager.php';

class ControleurAccueil extends Controleur {

    private $content;
    private $manager;

    // transpose and pad a Matrix with an empty element, needed if you want to publish the data in a vertical table
    private function &transposeMatrix(&$contentArray, $nbColumns, &$padding) {
        $transposedMatrix = array();
        for ($i = 0, $c = count($contentArray[0]); $i < $c; $i++) {
            for ($si = 0; $si < $nbColumns; $si++) {
                $transposedMatrix[$i][$si] = (isset($contentArray[$si][$i]) ? $contentArray[$si][$i] : $padding);
            }
        }
        return $transposedMatrix;
    }

    // instantiate the Model Class
    public function __construct() {
        $this->content = new Content();
        $this->manager = new Manager();
    }

    // Controler method related to the Revues - Navigation
    public function index() {
        $statut = 1;
        $typePublication = 1;
        $nbColumns = 4; // @todo : get if from ConfigFile
        $disciplines = $this->content->getDisciplines($typePublication, $statut);
        $letters = $this->content->getTitleFirstLetters($typePublication, $statut);
        $lastPubs = $this->content->getLastNumPublished($typePublication, $statut);

        //Empty element, needed to pad the Matrix
        $discipline = array();
        $discipline['DISCIPLINE'] = '';

        $disciplinesMatrix = array_chunk($disciplines, ceil(count($disciplines) / $nbColumns));
        $disciplinesArr = $this->transposeMatrix($disciplinesMatrix, $nbColumns, $discipline);

        $countRevues = $this->content->countRevues($typePublication, $statut);

        $lastArts = array();
        if(Configuration::get('mode')=='cairninter'){
            $lastArts = $this->content->getLastArticleOfLastNums();
        }

        /*
         * Modification septembre 2016 : la page d'accueil peut être appelée avec un paramètre shib=1
         * Cela permet de déclencher une authentification sur la CorsUrl au retour d'un login Shibboleth...
         */
        $token = null;
        if($this->requete->existeParametre("shib") && $this->requete->getParametre("shib")==1){
            if($this->requete->existeParametre("cairn_token")){
                $token = $this->requete->getParametre("cairn_token");
            }
        }

        $headers = Service::get('Webtrends')->webtrendsHeaders('accueil-revue', $this->authInfos);

        if ($this->requete->existeParametre("TITRE")) {
            $LET = $this->requete->getParametre("TITRE");
            $revueFullList = $this->content->getRevuesByTitle('1', '1', $LET);
            $revues = array();
            $revuesAbo = array();
            foreach($revueFullList as $revue){
                $access = Service::get('ControleAchat')->hasAccessToRevue($this->authInfos,$revue,1,1);
                if($access){
                    $revuesAbo[] = $revue;
                }else{
                    $revues[] = $revue;
                }
            }
            $this->genererVue(array('disciplines' => $disciplines, 'letters' => $letters,
                'lastpubs' => $lastPubs, 'arrdisciplines' => $disciplinesArr, 'LET' => $LET, 'revues' => $revues, 'revuesAbo' => $revuesAbo,
                'countRevues' => $countRevues, 'token' => $token, 'corsURL' => Configuration::get('crossDomainUrl')), null, null, $headers);
        } else {
            $this->genererVue(array('disciplines' => $disciplines, 'letters' => $letters, 'lastArts' => $lastArts,
                'lastpubs' => $lastPubs, 'arrdisciplines' => $disciplinesArr, 'countRevues' => $countRevues,
                'token' => $token, 'corsURL' => Configuration::get('crossDomainUrl')), null, null, $headers);
        }
    }

    public function disciplines() {
        $statut = 1;
        $nbColumns = 4;
        $typePublication = 1;

        if ($this->requete->existeParametre("id")) {
            $disciplineFilter = $this->requete->getParametre("id");
            if($disciplineFilter != 'all'){
                $disciplinePos = $this->content->getPosDiscFromUrl($disciplineFilter);
            }else{
                $disciplinePos = null;
            }
        } elseif ($this->requete->existeParametre("POS")) {
            $disciplinePos = $this->requete->getParametre("POS");
            $disciplineFilter = $this->content->getUrlDiscFromPos($disciplinePos);
        }
        if ($this->requete->existeParametre("TITRE")) {
            $LET = $this->requete->getParametre("TITRE");
        }else{
            //par defaut, pas d'affichage de revue
            $LET = 'NOTHING';
            //$LET = 'ALL';
        }

        $disciplines = $this->content->getDisciplines("$typePublication", $statut);
        //Empty element, needed to pad the Matrix
        $discipline = array();
        $discipline['DISCIPLINE'] = '';
        $disciplinesMatrix = array_chunk($disciplines, ceil(count($disciplines) / $nbColumns));
        $disciplinesArr = $this->transposeMatrix($disciplinesMatrix, $nbColumns, $discipline);

        $letters = $this->content->getTitleFirstLetters("$typePublication", $statut, $disciplinePos);
        $lastPubs = $this->content->getLastNumPublished("$typePublication", $statut, $disciplinePos);
        $mostConsultated = $this->content->getMostConsultated("$typePublication", $statut, $disciplinePos);

        $countRevues = $this->content->countRevues($typePublication, $statut, $disciplinePos);

        $revues = $this->content->getRevuesByTitle($typePublication, '1', $LET, $disciplinePos);

        // Métadonnée webtrends
        $webtrendsService = Service::get('Webtrends');
        $webtrendsTags = $webtrendsService->getTagsForAllPages('disc-revue', $this->authInfos);
        if ($disciplinePos !== null) {
            $webtrendsTags =  array_merge(
                $webtrendsTags,
                $webtrendsService->getTagsForDisciplinePage($disciplines, $disciplinePos)
            );
        }
        $headers = $webtrendsService->webtrendsTagsToHeadersTags($webtrendsTags);

        if (isset($LET) && $LET != 'ALL') {
            $revueFullList = $revues;
            $revues = array();
            $revuesAbo = array();
            foreach ($revueFullList as $revue) {
                $access = Service::get('ControleAchat')->hasAccessToRevue($this->authInfos,$revue,1,1);
                if ($access) {
                    $revuesAbo[] = $revue;
                } else {
                    $revues[] = $revue;
                }
            }
            if (($LET == "NOTHING") && (Configuration::get('mode') !== 'cairninter')) {
                /*
                Sur cairn, la liste pour les disciplines (sans avoir préciser une lettre) ne doit contenir que les articles les plus consultés et les 4 dernières revues mis en ligne dans la discipline en question. Et pour avoir la liste complète des revues de cette discipline, il faut cliquer sur "Tous", dans les lettres.
                Sur cairn-int, la liste affiche toutes les revues, et seulement elle. Il n'y a pas de listes par lettres (en raison du volume beaucoup plus réduit sur cairn-int)
                */
                $this->genererVue(
                    array(
                        'disciplines' => $disciplines,
                        'letters' => $letters,
                        'lastpubs' => $lastPubs,
                        'arrdisciplines' => $disciplinesArr,
                        'mostconsultated' => $mostConsultated,
                        'curDiscipline' => $disciplineFilter,
                        'curDisciplinePos' => $disciplinePos,
                        'LET' => $LET,
                        /*'revues' => $revues, 'revuesAbo' => $revuesAbo,*/
                        'countRevues' => $countRevues
                    ),
                    null,
                    null,
                    $headers
                );
            } else {
                //traitement des lettres
                $this->genererVue(
                    array(
                        'disciplines' => $disciplines,
                        'letters' => $letters,
                        'lastpubs' => $lastPubs,
                        'arrdisciplines' => $disciplinesArr,
                        'mostconsultated' => $mostConsultated,
                        'curDiscipline' => $disciplineFilter,
                        'curDisciplinePos' => $disciplinePos,
                        'LET' => $LET,
                        'revues' => $revues,
                        'revuesAbo' => $revuesAbo,
                        'countRevues' => $countRevues
                    ),
                    null,
                    null,
                    $headers
                );
            }
        } else {
            //traitement $LET == ALL
            $revueFullList = $revues;
            $revues = array();
            $revuesAbo = array();
            foreach($revueFullList as $revue){
                $access = Service::get('ControleAchat')->hasAccessToRevue($this->authInfos,$revue,1,1);
                if($access){
                    $revuesAbo[] = $revue;
                }else{
                    $revues[] = $revue;
                }
            }

            if (Configuration::get('mode') == 'cairninter') {
                $this->genererVue(
                    array(
                        'disciplines' => $disciplines,
                        'letters' => $letters,
                        'lastpubs' => $lastPubs,
                        'arrdisciplines' => $disciplinesArr,
                        'mostconsultated' => $mostConsultated,
                        'curDiscipline' => $disciplineFilter,
                        'curDisciplinePos' => $disciplinePos,
                        'countRevues' => $countRevues,
                        'revues' => $revues,
                        'revuesAbo' => $revuesAbo
                    ),
                    null,
                    null,
                    $headers
                );
            } else {
                $this->genererVue(
                    array(
                        'disciplines' => $disciplines,
                        'letters' => $letters,
                        'lastpubs' => $lastPubs,
                        'arrdisciplines' => $disciplinesArr,
                        'mostconsultated' => $mostConsultated,
                        'curDiscipline' => $disciplineFilter,
                        'curDisciplinePos' => $disciplinePos,
                        'countRevues' => $countRevues,
                        'revues' => $revues,
                        'revuesAbo' => $revuesAbo
                    ),
                    null,
                    null,
                    $headers
                );
            }
        }
    }

    public function redirectOuvrages() {
        // Dans le futur, on utilisera peut-être https (en tout cas, je le souhaite)
        $protocol = (
            !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443
        ) ? "https:/" : "http:/";
        // On reconstruit l'url dynamiquement pour renvoyer sur la page ouvrage
        $url = [
            $protocol,
            trim($_SERVER['HTTP_HOST'], '/'),
            trim(Configuration::get('racineWeb'), '/'),
            'ouvrages.php',
        ];
        $url = implode('/', $url);
        // On renvoit aussi les paramètres utilisés par la page "normale" des disciplines
        $getQueryParameters = array();
        if ($this->requete->existeParametre("id")) {
            $getQueryParameters['id'] = $this->requete->getParametre("id");
        }
        if ($this->requete->existeParametre("POS")) {
            $getQueryParameters['POS'] = $this->requete->getParametre("POS");
        }
        if ($this->requete->existeParametre("TITRE")) {
            $getQueryParameters['TITRE'] = $this->requete->getParametre("TITRE");
        }
        if (!!$getQueryParameters) {
            $url .= '?'.http_build_query($getQueryParameters);
        }
        header("Location: ".$url, 301); // C'est une redirection permanente
        die();
    }

    public function ouvragesDisciplines() {
        $statut = 1;
        $typePublication = 3;
        $nbColumns = 4;
        $disciplinePos = null;

        if ($this->requete->existeParametre("id")) {
            $disciplineFilter = $this->requete->getParametre("id");
            $disciplinePos = $this->content->getPosDiscFromUrl($disciplineFilter);
        } elseif ($this->requete->existeParametre("POS")) {
            $disciplinePos = $this->requete->getParametre("POS");
            $disciplineFilter = $this->content->getUrlDiscFromPos($disciplinePos);
        }
        if ($this->requete->existeParametre("TITRE")) {
            $LET = $this->requete->getParametre("TITRE");
        }/*else{
            $LET = 'ALL';
        }*/
        $disciplines = $this->content->getDisciplines($typePublication, $statut);

        //Empty element, needed to pad the Matrix
        $discipline = array();
        $discipline['DISCIPLINE'] = '';
        $disciplinesMatrix = array_chunk($disciplines, ceil(count($disciplines) / $nbColumns));
        $disciplinesArr = $this->transposeMatrix($disciplinesMatrix, $nbColumns, $discipline);

        if ($disciplinePos != null) {
            if (($disciplinePosRoot = $this->content->getDsiciplineRoot($disciplinePos)) != 0) {
                $sousDisciplines = $this->content->getSousDisciplines($disciplinePosRoot);
            } else {
                $disciplinePosRoot = $disciplinePos;
                $sousDisciplines = $this->content->getSousDisciplines($disciplinePos);
            }

            if (count($sousDisciplines) > 0) {
                $SousDisciplinesMatrix = array_chunk($sousDisciplines, ceil(count($sousDisciplines) / 3));
                $sousDisciplinesArr = $this->transposeMatrix($SousDisciplinesMatrix, 3, $discipline);
            }
        }

        $countOuvrages = $this->content->countOuvrages($typePublication, $statut, $disciplinePos);

        $letters = $this->content->getOuvragesTitleFirstLetters("$typePublication", $statut, $disciplinePos);

        // Métadonnées webtrends
        $webtrendsService = Service::get('Webtrends');
        if (($disciplinePos === null) && ($LET === null)) {
            $headers = $webtrendsService->webtrendsHeaders('accueil-ouvrage', $this->authInfos);
        } else {
            $webtrendsTags = array_merge(
                $webtrendsService->getTagsForAllPages('disc-ouvrage', $this->authInfos),
                $webtrendsService->getTagsForDisciplinePage(
                    $disciplines,
                    $disciplinePosRoot,
                    $sousDisciplines,
                    $disciplinePos
                )
            );
            $headers = $webtrendsService->webtrendsTagsToHeadersTags($webtrendsTags);
        }

        //
        if (isset($LET)) {
            $revueFullList = $this->content->getOuvragesByTitle($typePublication, '1', $LET, $disciplinePos);
            //if(isset($this->authInfos['I'])){
                $revues = array();
                $revuesAbo = array();
                foreach($revueFullList as $revue){
                    $access = Service::get('ControleAchat')->hasAccessToRevue($this->authInfos,$revue,1,1);
                    if($access){
                        $revuesAbo[] = $revue;
                    }else{
                        $revues[] = $revue;
                    }
                }
            /*} else{
                $revues = $revueFullList;
                $revuesAbo = array();
            } */
            $this->genererVue(array('disciplines' => $disciplines, 'letters' => $letters,
                'lastpubs' => null, 'arrdisciplines' => $disciplinesArr,
                'curDiscipline' => $disciplineFilter, 'curDisciplinePos' => $disciplinePos, 'curDisciplinePosRoot' => $disciplinePosRoot, 'LET' => $LET, 'revues' => $revues, 'revuesAbo' => $revuesAbo
                , 'sousDisciplinesArr' => $sousDisciplinesArr, 'countOuvrages' => $countOuvrages), 'ouvrages.php', null, $headers);
        } else {
            $lastPubs = $this->content->getOuvragesLastNumPublished($typePublication, '1', $disciplinePos);
            $this->genererVue(array('arrdisciplines' => $disciplinesArr, 'lastpubs' => $lastPubs, 'letters' => $letters,
                'curDiscipline' => $disciplineFilter, 'curDisciplinePos' => $disciplinePos, 'curDisciplinePosRoot' => $disciplinePosRoot,
                'sousDisciplinesArr' => $sousDisciplinesArr, 'countOuvrages' => $countOuvrages), 'ouvrages.php', null, $headers);
        }
    }

    public function encyclopediesDisciplines() {
        $statut = 1;
        $nbColumns = 4;
        $typePublication = 6;

        $disciplinePos = null;
        $disciplineFilter = null;
        if ($this->requete->existeParametre("id")) {
            $disciplineFilter = $this->requete->getParametre("id");
            $disciplinePos = $this->content->getPosDiscFromUrl($disciplineFilter);
        } elseif ($this->requete->existeParametre("POS")) {
            $disciplinePos = $this->requete->getParametre("POS");
            $disciplineFilter = $this->content->getUrlDiscFromPos($disciplinePos);
        }
        if ($this->requete->existeParametre("TITRE")) {
            $LET = $this->requete->getParametre("TITRE");
        }/*else{
            $LET = 'ALL';
        }*/
        if ($this->requete->existeParametre("AUTEUR")) {
            $AUTEUR = $this->requete->getParametre("AUTEUR");
        }
        $disciplines = $this->content->getDisciplines($typePublication, $statut);

        $discipline = array();
        $discipline['DISCIPLINE'] = '';
        $disciplinesMatrix = array_chunk($disciplines, ceil(count($disciplines) / $nbColumns));
        $disciplinesArr = $this->transposeMatrix($disciplinesMatrix, $nbColumns, $discipline);

        $letters = $this->content->getOuvragesTitleFirstLetters("$typePublication", '1', $disciplinePos);
        $authorsLetters = $this->content->getOuvragesAuthorsFirstLetters("$typePublication", '1', $disciplinePos);

        $countOuvrages = $this->content->countOuvrages($typePublication, $statut, $disciplinePos);

        // Métadonnée webtrends
        $webtrendsService = Service::get('Webtrends');
        $webtrendsTags = $webtrendsService->getTagsForAllPages(
            ($disciplinePos === null) ? 'accueil-encyclopedie' : 'disc-encyclopedie',
            $this->authInfos
        );
        if ($disciplinePos !== null) {
            $webtrendsTags = array_merge(
                $webtrendsTags,
                $webtrendsService->getTagsForDisciplinePage($disciplines, $disciplinePos));
        }
        $headers = $webtrendsService->webtrendsTagsToHeadersTags($webtrendsTags);

        // SI on a un TITRE (LETTER) on ne veut pas les derniers ajoutés, mais on veut les titres
        if (isset($LET)) {
            $revueFullList = $this->content->getOuvragesByTitle($typePublication, '1', $LET, $disciplinePos);
            //if(isset($this->authInfos['I'])){
                $revues = array();
                $revuesAbo = array();
                foreach($revueFullList as $revue){
                    $access = Service::get('ControleAchat')->hasAccessToRevue($this->authInfos,$revue,1,1);
                    if($access){
                        $revuesAbo[] = $revue;
                    }else{
                        $revues[] = $revue;
                    }
                }
            /*} else{
                $revues = $revueFullList;
                $revuesAbo = array();
            }*/
            $this->genererVue(array('disciplines' => $disciplines, 'letters' => $letters,
                'arrdisciplines' => $disciplinesArr,
                'curDiscipline' => $disciplineFilter, 'curDisciplinePos' => $disciplinePos, 'LET' => $LET, 'revues' => $revues, 'revuesAbo' => $revuesAbo, 'lettersAuthor' => $authorsLetters, 'countOuvrages' => $countOuvrages), 'encyclopedies.php', null, $headers);
        } elseif (isset($AUTEUR)) {
            $revues = $this->content->getOuvragesByAuthor($typePublication, '1', $AUTEUR, $disciplinePos);
            $this->genererVue(array('disciplines' => $disciplines, 'letters' => $letters,
                'arrdisciplines' => $disciplinesArr,
                'curDiscipline' => $disciplineFilter, 'curDisciplinePos' => $disciplinePos, $letters, 'lettersAuthor' => $authorsLetters, 'revues' => $revues, 'AUTEUR' => $AUTEUR, 'countOuvrages' => $countOuvrages), 'encyclopedies.php', null, $headers);
        } elseif ($disciplinePos != null) {


            $this->genererVue(array('arrdisciplines' => $disciplinesArr, 'letters' => $letters, 'lettersAuthor' => $authorsLetters, 'curDiscipline' => $disciplineFilter, 'curDisciplinePos' => $disciplinePos, 'countOuvrages' => $countOuvrages), 'encyclopedies.php', null, $headers);
        } else {

            $lastPubs = $this->content->getOuvragesLastNumPublished($typePublication, '1', $disciplinePos);
            $this->genererVue(array('arrdisciplines' => $disciplinesArr, 'lastpubs' => $lastPubs, 'letters' => $letters, 'lettersAuthor' => $authorsLetters, 'curDiscipline' => $disciplineFilter, 'curDisciplinePos' => $disciplinePos, 'countOuvrages' => $countOuvrages), 'encyclopedies.php', null, $headers);
        }
    }

    public function magazines() {
        $statut = 1;
        $typePublication = 2;
        $disciplinePos = null;
        $revues = $this->content->getMagazines($typePublication);
        $revueFullList = $revues;
        $revues = array();
        $revuesAbo = array();
        foreach ($revueFullList as $revue) {
            $access = Service::get('ControleAchat')->hasAccessToRevue($this->authInfos,$revue,1,1);
            if ($access) {
                $revuesAbo[] = $revue;
            } else {
                $revues[] = $revue;
            }
        }

        $headers = Service::get('Webtrends')->webtrendsHeaders('accueil-magazine', $this->authInfos);
        $this->genererVue(array('revues' => $revues, 'revuesAbo' => $revuesAbo), 'magazines.php', null, $headers);
    }

    public function aideInstitutionsClientes(){
        $headers = Service::get('Webtrends')->webtrendsHeaders('corporate-*', $this->authInfos);
        $this->genererVue(null, null, null, $headers);
    }

    public function planDuSite(){
        $headers = Service::get('Webtrends')->webtrendsHeaders('corporate-*-plan-du-site', $this->authInfos);
        $this->genererVue(null, null, null, $headers);
    }

    public function raccourcisClavier(){
        $headers = Service::get('Webtrends')->webtrendsHeaders('corporate-*', $this->authInfos);
        $this->genererVue(null, null, null, $headers);
    }

    public function mesAlertes(){
        $revues = $this->content->getRevuesByType(1);
        $collections = $this->content->getRevuesByType(3);
        $headers = Service::get('Webtrends')->webtrendsHeaders('compte-*-alerte', $this->authInfos);

        if(!empty($this->authInfos['U']))
        {
            $revuesAlertes = $this->content->loadRevuesAlertes($this->authInfos['U']['EMAIL'])->fetchAll(PDO::FETCH_ASSOC);
            $collectionsAlertes = $this->content->loadCollectionAlertes($this->authInfos['U']['EMAIL'])->fetchAll(PDO::FETCH_ASSOC);
            $this->genererVue(array('revues' => $revues , 'collections' => $collections , 'revuesAlertes' => $revuesAlertes , 'collectionsAlertes' => $collectionsAlertes ), 'mesAlertes.php', null, $headers);
        }
        else
        {
            $this->genererVue(array('revues' => $revues , 'collections' => $collections), 'mesAlertes.php', null, $headers);
        }
    }


    public function setAlertes() {
        // Cette fonction est utilisée comme une api REST.
        // Mais elle n'en a pas le comportement, en particulier avec les messages d'erreurs.
        // Ce n'est pas la seule, et je n'ai pas le courage de reprendre toutes les fonctions
        // pour afficher correctement les messages d'erreurs et qui soit parsable.
        $id_user = $this->requete->getParametre("ID_USER", null);
        $id_alerte = $this->requete->getParametre("ID_ALERTE", null);
        $type = $this->requete->getParametre("TYPE", null);
        if (in_array(null, [$id_user, $id_alerte, $type])) {
            http_response_code(422);
            echo json_encode([
                'has-subscribe' => 0,
                'reason' => 'missing parameters',
            ]);
            return;
        }

        if ($this->manager->existsAlert($id_user, $id_alerte)) {
            // Bon, c'est pas terrible, on fait croire qu'on a ajouté une nouvelle alerte, alors
            // qu'elle existe déjà.
            // Faudrait améliorer ça un jour.
            echo json_encode(['has-subscribe' => 1]);
            return;
        }
        $this->manager->addAlerts($id_user,$id_alerte,$type);
        echo json_encode(['has-subscribe' => 1]);
    }


    public function deleteAlert() {
        $id_user = $this->requete->getParametre("id_user");
        $id_alerte = $this->requete->getParametre("id_alerte");

        $this->manager->deleteAlerts($id_user, $id_alerte);
    }

    public function numPublie(){
        $idRevue = $this->requete->getParametre("id_revue");
        $pubs = $this->content->getNumPublieByIdRevue($idRevue);
        echo json_encode($pubs[0]);
    }


    public function mesRecherches(){
        $headers = Service::get('Webtrends')->webtrendsHeaders('mes-recherches', $this->authInfos);
        $this->genererVue(null, 'mesRecherches.php', null, $headers);
    }

    public function monHistorique(){
        $headers = Service::get('Webtrends')->webtrendsHeaders('mon-historique', $this->authInfos);
        if(isset($this->authInfos['U'])){
            $historique = $this->authInfos['U']['HISTO_JSON']->articles;
        }else{
            $historique = $this->authInfos['G']['HISTO_JSON']->articles;
        }
        if(isset($historique)){
            $typesAchat = array("MODE" => Service::get('ControleAchat')->getModeAchat($this->authInfos));
            $articles = $this->content->getBiblioArticles($historique);
            $modeBoutons = Configuration::get('modeBoutons');
            if($modeBoutons == 'cairninter'){
                Service::get('ContentArticle')
                            ->setTypesAchat($typesAchat)
                            ->readButtonsForInter($articles, $this->authInfos);
            }else{
                Service::get('ContentArticle')
                        ->setTypesAchat($typesAchat)
                        ->readContentArticles($articles, '', $this->authInfos);
            }
            foreach($articles as $article){
                $historiqueDetail[$article['ARTICLE_ID_ARTICLE']] = $article;
            }

            $this->genererVue(array('historiqueList'=>$historique,
                                    'historiqueDetail'=>$historiqueDetail), 'monHistorique.php', null, $headers);
        }else{
            $this->genererVue(null, 'monHistorique.php', null, $headers);
        }
    }
}
