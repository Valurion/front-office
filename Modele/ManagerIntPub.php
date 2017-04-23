<?php

require_once 'Framework/Modele.php';

/**
 * Vérifie l'existance de traductions en ANGLAIS directement dans la base de données CairnINT_PUB
 * @author @cairn.info - www.cairn.info
 * @author Julien CADET
 */
class ManagerIntPub extends Modele {

    function __construct($dsn_name) {
        $this->selectDatabase($dsn_name);
    }

    // Vérification de l'existance d'une revue traduite et renvoi son ID
    public function checkIfRevueOnCairnInt($idRevue) {

        // Prépération de la requête
        // Récupération de l'ID de la revue sur CAIRN-INT selon la valeur de référence ($idRevue = ID de la revue sur CAIRN).
        $sql = "SELECT REVUE.ID_REVUE, REVUE.URL_REWRITING_EN
                FROM REVUE
                WHERE REVUE.ID_REVUE_S = ?
                LIMIT 1";
        $revueInt = $this->executerRequete($sql, array($idRevue));
        return $revueInt->fetch(PDO::FETCH_ASSOC);
    }

    // Vérification de l'existance d'un numéro traduit et renvoi son ID
    public function checkIfNumeroOnCairnInt($idNumero) {

        // Prépération de la requête
        // Récupération de l'ID du numéro sur CAIRN-INT selon la valeur de référence ($idNumero = ID du numéro sur CAIRN).
        $sql = "SELECT NUMERO.ID_REVUE, NUMERO.ID_NUMPUBLIE
                FROM NUMERO
                WHERE NUMERO.ID_NUMPUBLIE_S = ?
                LIMIT 1";
        $numeroInt = $this->executerRequete($sql, array($idNumero));
        return $numeroInt->fetch(PDO::FETCH_ASSOC);
    }

    // Vérification de l'existance d'un article traduit et renvoi son ID
    public function checkIfArticleOnCairnInt($idArticle) {

        // Prépération de la requête
        // Récupération de l'ID de l'article sur CAIRN-INT selon la valeur de référence ($idArticle = ID de l'article sur CAIRN).
        $sql = "SELECT ARTICLE.ID_REVUE, ARTICLE.ID_NUMPUBLIE, ARTICLE.ID_ARTICLE, ARTICLE.URL_REWRITING_EN
                FROM ARTICLE
                WHERE ARTICLE.ID_ARTICLE_S = ? AND ARTICLE.LANGUE_INTEGRALE = 'en'
                LIMIT 1";
        $articleInt = $this->executerRequete($sql, array($idArticle));
        return $articleInt->fetch(PDO::FETCH_ASSOC);
    }

    // Vérification de l'existence d'un résumé traduit et renvoi 1 (oui) ou 0 (non)
    public function checkIfResumeOnCairnInt($idResume) {

        // Prépération de la requête
        // Récupération de l'ID de l'article sur CAIRN-INT selon la valeur de référence ($idResume = ID de l'article sur CAIRN).
        /*$sql = "SELECT ARTICLE.ID_REVUE, ARTICLE.ID_NUMPUBLIE, ARTICLE.ID_ARTICLE
                FROM ARTICLE
                LEFT JOIN RESUMES 
                ON RESUMES.ID_ARTICLE = ARTICLE.ID_ARTICLE
                WHERE ARTICLE.ID_ARTICLE_S = ? AND RESUMES.RESUME_EN != ''
                LIMIT 1";*/
        $sql = "SELECT count(*) as count, ARTICLE.ID_ARTICLE, ARTICLE.URL_REWRITING_EN
                FROM ARTICLE
                LEFT JOIN RESUMES 
                ON RESUMES.ID_ARTICLE = ARTICLE.ID_ARTICLE
                WHERE ARTICLE.ID_ARTICLE_S = ? AND RESUMES.RESUME_EN != ''
                LIMIT 1";
        $resumeInt = $this->executerRequete($sql, array($idResume));
        return $resumeInt->fetch(PDO::FETCH_ASSOC);
    } 



    

}
