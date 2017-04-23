<?php

/**
 * CONTROLER - Control the navigation for consultation pages:
 *  - LISTE des collections
 *  - LISTE des revues
 *  - DETAIL d'un éditeur avec la LISTE de ses publications
 *  - DETAIL d'un auteur avec la LISTE de ses publications
 *  - DETAIL d'une collection avec la LISTE de ses publications
 *
 * @author ©Pythagoria - www.pythagoria.com
 * @author benjamin
 */
require_once 'Framework/Controleur.php';

require_once 'Modele/Content.php';

class ControleurListeDetail extends Controleur {

    private $content;

    // instantiate the Model Classes
    public function __construct() {
        $this->content = new Content();
    }

    public function index() {
        $type = $this->requete->getParametre("TYPE");

        switch ($type) {
            case 'collections':
            case 'revues':
                $this->liste($type);
                break;
            case 'auteur':
            case 'editeur':
            case 'collection':
            case 'citepar':
                $this->listeDetail($type);
                break;
        }
    }

    private function liste($type) {
        //on récupère les données et on paramètre la vue et ses blocs
        $editeur = null;
        if ($this->requete->existeParametre("editeur")) {
            $editeur = $this->requete->getParametre("editeur");
        }
        switch ($type) {
            case 'collections':
                $typepub = 3;
                break;
            case 'revues':
                $typepub = 1;
                break;
        }
        $revues = $this->content->getRevuesByTitle($typepub, 1, 'ALL', null, $editeur, true);
        $editeurs = $this->content->getEditeurs(TRUE, $typepub);
        $headers = Service::get('Webtrends')->webtrendsHeaders('liste-'.$type, $this->authInfos);

        //appel à la vue
        $this->genererVue(array('editeurs' => $editeurs, 'revues' => $revues, 'currentEditeur' => $editeur, 'type' => $type), 'liste.php', null, $headers);
    }

    private function listeDetail($type) {
        $webtrendsService = Service::get('Webtrends');
        $webtrendsTags = $webtrendsService->getTagsForAllPages(
            $type !== 'collection' ? 'liste-'.$type : 'collection',
            $this->authInfos
        );
        //on récupère les données et on paramètre la vue et ses blocs
        switch ($type) {
            case 'auteur':
                $id = $this->requete->getParametre("ID");
                $nom = $this->requete->getParametre("NOM");
                $auteur = $this->content->getAuteurById($id);
                $ouvrages = $this->content->getAuteurOuvrages($id);
                $contribsOuvrage = $this->content->getAuteurArticles($id, '3,6', TRUE);
                $typesAchat = array("MODE" => Service::get('ControleAchat')->getModeAchat($this->authInfos));
                Service::get('ContentArticle')
                        ->setTypesAchat($typesAchat)
                        ->readContentArticles($contribsOuvrage, '', $this->authInfos);
                $articlesRev = $this->content->getAuteurArticles($id, 1);
                $modeBoutons = Configuration::get('modeBoutons');
                if($modeBoutons == 'cairninter'){
                    Service::get('ContentArticle')
                            ->setTypesAchat($typesAchat)
                            ->readButtonsForInter($articlesRev, $this->authInfos);
                }else{
                    Service::get('ContentArticle')
                            ->setTypesAchat($typesAchat)
                            ->readContentArticles($articlesRev, 'revue', $this->authInfos);
                }
                $articlesMag = $this->content->getAuteurArticles($id, 2);
                Service::get('ContentArticle')
                        ->setTypesAchat($typesAchat)
                        ->readContentArticles($articlesMag, 'magazine', $this->authInfos);
                $webtrendsTags = array_merge(
                    $webtrendsTags,
                    $webtrendsService->getTagsForAuteurPublications($auteur)
                );
                $webtrendsTags['numero-auteurs'] = $auteur['AUTEUR_PRENOM'] . ' ' . $auteur['AUTEUR_NOM']; //Ajout de l'auteur (Dimitry : Cairn, le 30/11/2015).
                $headers = $webtrendsService->webtrendsTagsToHeadersTags($webtrendsTags);
                $this->genererVue(array('auteur' => $auteur,
                    'ouvrages' => $ouvrages,
                    'contribs' => $contribsOuvrage,
                    'articlesRev' => $articlesRev,
                    'articlesMag' => $articlesMag,
                        ), 'auteur.php', null, $headers);
                break;
            case 'editeur':
                $id = $this->requete->getParametre("ID_EDITEUR");
                $editeur = $this->content->getEditeurById($id);
                $revues = $this->content->getRevuesByTitle(1, 1, '', null, $id, true);
                $countRev = count($revues);
                $colls = $this->content->getRevuesByTitle(3, 1, '', null, $id, true);
                $countColls = count($colls);
                $encycs = $this->content->getRevuesByTitle(6, 1, '', null, $id, true);
                $countEncycs = count($encycs);
                $mags = $this->content->getRevuesByTitle(2, 1, '', null, $id, true);
                $countMags = count($mags);
                $webtrendsTags = array_merge(
                    $webtrendsTags,
                    $webtrendsService->getTagsForEditeurPublications($editeur)
                );
                $headers = $webtrendsService->webtrendsTagsToHeadersTags($webtrendsTags);
                $this->genererVue(array('editeur' => $editeur,
                    'countRev' => $countRev, 'revues' => $revues,
                    'countColls' => $countColls, 'colls' => $colls,
                    'countEncycs' => $countEncycs, 'encycs' => $encycs,
                    'countMags' => $countMags, 'mags' => $mags,
                        ), 'editeur.php', null, $headers);
                break;
            case 'collection':
                if ($this->requete->existeParametre("ID_REVUE")) {
                    $revueId = $this->requete->getParametre("ID_REVUE");
                    $revue = $this->content->getRevuesById($revueId);
                    $revueFilter = $revue[0]["URL_REWRITING"];
                } else {
                    $revueFilter = $this->requete->getParametre("REVUE");
                }
                $revues = $this->content->getRevuesByUrl($revueFilter, null, '3,6');
                $limit = $this->requete->existeParametre("LIMIT") ? $this->requete->getParametre("LIMIT") : 0;
                $numeros = $this->content->getNumeroRevuesById($revues[0]["ID_REVUE"], null, $limit, 20);
                $countNum = $this->content->countNumeroRevuesById($revues[0]["ID_REVUE"]);
                $webtrendsTags = array_merge(
                    $webtrendsTags,
                    $webtrendsService->getTagsForCollection($revues[0])
                );
                $headers = $webtrendsService->webtrendsTagsToHeadersTags($webtrendsTags);
                $this->genererVue(array('revue' => $revues[0],
                    'numeros' => $numeros,
                    'limit' => $limit,
                    'countNum' => $countNum
                        ), 'collection.php', null, $headers);
                break;
            case 'citepar':
                /* Redmine 41866 - on montre les 2 en même temps...
                switch ($this->requete->getParametre("T")) {
                    case 'O':
                        $type = 'in (3,6)';
                        break;
                    default:
                        $type = ' = 1';
                }*/
                if($this->requete->existeParametre("ID_ARTICLE")){
                    $id = $this->requete->getParametre("ID_ARTICLE");
                    $article = $this->content->getArticleFromId($id);
                    $referencedByR = $this->content->getReferencedBy($id, 'B', ' = 1');
                    $referencedByO = $this->content->getReferencedBy($id, 'B', 'in (3,6)');
                    $auteurs = $this->content->getAuteursForReference($id);
                    $revue = $this->content->getRevuesById($article["ARTICLE_ID_REVUE"]);
                    $numero = $this->content->getNumpublieById($article["ARTICLE_ID_NUMPUBLIE"]);
                }else{
                    $id = $this->requete->getParametre("ID_NUMPUBLIE");
                    $referencedByR = $this->content->getNumReferencedBy($id, 'B', ' = 1');
                    $referencedByO = $this->content->getNumReferencedBy($id, 'B', 'in (3,6)');
                    $numero = $this->content->getNumpublieById($id);
                    $revue = $this->content->getRevuesById($numero[0]["NUMERO_ID_REVUE"]);
                    $article = array();
                }
                $webtrendsTags = array_merge(
                    $webtrendsTags,
                    $webtrendsService->getTagsForCitePar($article)
                );
                $headers = $webtrendsService->webtrendsTagsToHeadersTags($webtrendsTags);

                $this->genererVue(array('article' => $article,
                    'revue' => $revue[0],
                    'numero' => $numero[0],
                    'referencedByR' => $referencedByR,
                    'referencedByO' => $referencedByO,
                    'auteurs' => $auteurs,
                    'type' => $type
                        ), 'cite-par.php', null, $headers);
                break;
        }

        //appel à la vue
    }

    public function setAlertes(){
        $id_user = $this->requete->getParametre("ID_USER");
        $id_alerte = $this->requete->getParametre("ID_ALERTE");
        $type = $this->requete->getParametre("TYPE");

        $this->content->addAlerts($id_user,$id_alerte,$type);
    }

}
