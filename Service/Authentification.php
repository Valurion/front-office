<?php

require_once 'Framework/Service.php';
require_once 'CairnToken.php';
require_once 'Modele/ContentCom.php';
require_once 'Modele/Content.php';
require_once 'Modele/ContentAbo.php';
require_once 'Modele/ManagerCom.php';
require_once 'Framework/Modele.php';
require_once 'Modele/RedisClient.php';
require_once 'Modele/Filter.php';

/**
 * Prend en charge la gestion de l'authentification:
 *  - création des tokens (particulier, institution, Guest)
 *  - lecture des tokens et des informations utilisateurs
 *  - calcul des droits d'accès
 *
 * @author ©Pythagoria - www.pythagoria.com
 * @author Benjamin HENNON
 */
class Authentification {
    /*
     * Le token prend la forme "userid"+"#"+md5(userid+salt)+"#"+time() de la première connexion
     */

    private $token = null;
    private $guestToken = null;
    private $userI = null;
    private $userU = null;
    private $userILog = null;
    private $userULog = null;
    private $userIPLog = null;
    private $contentCom = null;
    private $content = null;
    private $contentAbo = null;
    private $managerCom = null;
    private $redis = null;
    private $filter = null;

    /**
     * Constructeur
     *
     * Instancie les modèles nécessaires au service
     */
    function __construct() {
        $this->content = new Content();
        $this->contentCom = new ContentCom('dsn_com');
        $this->contentAbo = new ContentAbo('dsn_abo');
        $this->managerCom = new ManagerCom('dsn_com');

        $this->redis = new RedisClient(Configuration::get('redis_db_user'));

        $this->filter = new Filter();
    }

    /*
     * Crée un token sur base de la chaine fournie
     *
     * @param $userId string chaîne correspondant à l'utilisateur à tokeniser
     *
     * @return $token string représentation cryptée de l'utilisateur
     */

    public function createToken($userId) {
        if ($this->token != null) {
            $lastPart = explode('#', $this->token)[2];
        } else {
            $lastPart = time();
        }
        $this->token = $userId . "#" . CairnToken::encode($userId) . "#" . $lastPart;
        return $this->token;
    }

    /*
     * Mise à jour d'un token existant
     *
     * On prend comme postulat que l'authentification IP (automatique) est toujours la première,
     * donc cet update ne se fait qu'à partir d'un login via la plateforme
     *
     * Si le login via la plateforme est un login institution et que l'on est déjà connecté IP, on écrase la connexion IP
     *
     * TODO : il faudra faire pareil pour shibboleth ! (cfr. évolution 30813 dans le redmine Cairn)
     *
     * @param $other_userId string chaîne correspondant au second utilisateur à crypter
     * @param $other_userType char caractère permettant de connaître le type de l'utilisateur
     *
     * @return $token string représentation cryptée de l'utilisateur
     */

    public function updateToken($other_userId, $other_userType) {
        if ($this->validateToken(0)) {
            //Si le token I est existant et que le other est une institution, alors on l'écrase
            if ($other_userType == 'I' && $this->userI !== null) {
                $idToClose = $this->userI . '#' . explode('#', $this->token)[2];
                $this->removeTokenPart($this->userI);
                if (strpos($this->userI, 'IP$') !== FALSE) {
                    $this->managerCom->closeUserLogIP($idToClose);
                } else {
                    $this->managerCom->closeUserLog($idToClose);
                }
            }
            $clair = explode('#', $this->token)[0];
            $this->createToken(($clair != '' ? ($clair . "£") : '') . $other_userId);
        } else {
            $this->createToken($other_userId);
        }
        return $this->token;
    }

    /*
     * Permet de supprimer le token en cas de déconnection.
     * Attention, on ne se déconnecte jamais qu'en mode U !
     * Donc,
     *  - si on n'a qu'une auth et que l'on arrive ici, c'est qu'on se déconnecte d'un compte U => deleteToken
     *  - sinon, on supprime la partie U et on garde la partie I
     *
     * @return $token string la nouvelle version du token
     */

    public function dropToken() {
        if ($this->userI == null) {
            $this->token = null;
            $this->userU = null;
        } else {
            $this->createToken($this->userI);
            $this->userU = null;
        }
        return $this->token;
    }

    public function getToken() {
        return $this->token;
    }

    public function setToken($token) {
        $this->token = $token;
        return $this;
    }

    public function removeAllTokenParts() {
        $parts = explode('#', $this->token);
        $typeAuths = explode('£', $parts[0]);
        foreach ($typeAuths as $typeAuth) {
            if (strpos($typeAuth, 'IP$') !== FALSE) {
                $this->removeTokenPart($typeAuth);
                $this->managerCom->closeUserLogIP($typeAuth . '#' . $parts[2]);
            } else {
                $this->removeTokenPart($typeAuth);
                $this->managerCom->closeUserLog($typeAuth . '#' . $parts[2]);
            }
        }
    }

    /*
     * Permet d'enlever du token une partie correspondant à une des identifications (IP,I ou U)
     * C'est nécessaire au cas où une session expire
     *
     * @param la partie qui expire
     */

    public function removeTokenPart($part) {
        if ($this->token != null) {
            $parts = explode('#', $this->token);
            $typeAuths = explode('£', $parts[0]);
            $newToken = "";
            $onlyIP = 1;
            foreach ($typeAuths as $typeAuth) {
                if ($typeAuth != $part) {
                    $newToken .= ($newToken == '' ? '' : '£') . $typeAuth;
                    if (strpos($typeAuth, 'IP$') === false) {
                        $onlyIP = 0;
                    }
                }
            }
            if ($newToken == '') {
                //Si on a fait un remove de la dernière partie, on efface tout
                setcookie('cairn_token', $this->token, time() - 3600, '/', '', 0);
                unset($_COOKIE["cairn_token"]);
                $this->token = null;
            } else {
                //Sinon, on met à jour...
                $this->token = $this->createToken($newToken);
                if ($onlyIP == 1) {
                    setcookie('cairn_token', $this->token, strtotime('+' . Configuration::get('userIPSessionDuration') . ' ' . strtolower(Configuration::get('userIPSessionUnit')) . 's'));
                } else {
                    setcookie('cairn_token', $this->token, strtotime('+' . Configuration::get('userSessionDuration') . ' ' . strtolower(Configuration::get('userSessionUnit')) . 's'));
                }
            }
        }
    }

    /*
     * Méthode de validation du token, sur base de la comparaison
     * entre la partie claire et la partie cryptée
     *
     * return boolean
     */

    private function validateToken($validSessions = 1) {
        if ($this->token != null) {
            $parts = explode('#', $this->token);
            if (CairnToken::compare($parts[1], $parts[0])) {
                if ($validSessions == 0) {
                    return TRUE;
                } else {
                    //Le token est valide d'un point de vue cookie, on valide ensuite les sessions, pour chaque partie
                    $typeAuths = explode('£', $parts[0]);
                    foreach ($typeAuths as $typeAuth) {
                        if (strpos($typeAuth, 'IP$') !== FALSE) {
                            $session = $this->contentCom->checkSessionIP($typeAuth . '#' . $parts[2], Configuration::get('userIPInactivityDuration'), Configuration::get('userIPInactivityUnit'));
                            if ($session == FALSE) {
                                $this->removeTokenPart($typeAuth);
                                $this->managerCom->closeUserLogIP($typeAuth . '#' . $parts[2]);
                            }
                        } else {
                            $session = $this->contentCom->checkSession($typeAuth . '#' . $parts[2], Configuration::get('userInactivityDuration'), Configuration::get('userInactivityUnit'));
                            if ($session == FALSE) {
                                $ejectMode = $this->contentCom->isEjectMode($typeAuth . '#' . $parts[2]);
                                if ($ejectMode == 1) {
                                    echo "<script type=\"text/javascript\">setTimeout('cairn.show_modal(\'#modal_logouteject\')',2000);</script>";
                                }
                                $this->removeTokenPart($typeAuth);
                                $this->managerCom->closeUserLog($typeAuth . '#' . $parts[2]);
                            }
                        }
                    }
                    if ($this->token != null) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                }
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /*
     * Cette méthode fait appel au service d'Authentification IP afin de déterminer
     * si il est nécessaire de connecter automatiquement l'internaute à une institution
     *
     * @param $requete Requete objet requête complet
     * @return boolean
     */

    private function autoConnectByIpRange($requete) {
        $userIP = Service::get('AuthentificationIP')->loginByIP($requete);
        if ($userIP != null) {
            $user = $this->contentCom->getUserInfos($userIP['B_USER']);
            if ($this->token == null) {
                $this->createToken('IP$' . $userIP['ID']);
                if ($user['ROBOT'] != 1) {
                    setcookie('cairn_token', $this->token, strtotime('+' . Configuration::get('userIPSessionDuration') . ' ' . strtolower(Configuration::get('userIPSessionUnit')) . 's'));
                }
            } else {
                $this->updateToken('IP$' . $userIP['ID'], 'I');
                if ($user['ROBOT'] != 1) {
                    setcookie('cairn_token', $this->token, strtotime('+' . Configuration::get('userSessionDuration') . ' ' . strtolower(Configuration::get('userSessionUnit')) . 's'));
                }
            }
            if ($user['ROBOT'] != 1) {
                $this->managerCom->insertUserIP('IP$' . $userIP['ID'] . '#' . explode('#', $this->token)[2], $userIP['IP_USER'], $userIP['B_USER'], Configuration::get('userIPSessionDuration'), Configuration::get('userIPSessionUnit'));
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /*
     * Méthode de création d'un token guest
     *
     * Le guestToken permet d'accéder au record DB qui contient les dernières informations de l'utilisateur
     * Il n'y a pas de sécurité particulière donc on prend un time+random
     */

    private function createGuestToken() {
        $this->guestToken = date('Ymd') . time() . rand(0, 10000);
        setcookie('cairn_guest', $this->guestToken, strtotime('+' . Configuration::get('guestSessionDuration') . ' ' . strtolower(Configuration::get('guestSessionUnit')) . 's'));
        $this->managerCom->insertUserGuest($this->guestToken);
    }

    public function setGuestToken($token) {
        $this->guestToken = $token;
        return $this;
    }

    /*
     * Cette fonction vérifie si on est connecté (avec connection ouverte)
     *
     * Le principe est le suivant:
     *  On commence par regarder le token dans le cookie
     *  - si il est présent et valide, OK, on sort...
     *  - sinon on vérifie la connexion par IP
     *      - si on est en position de s'authentifier IP, on génère le token, on le stocke et OK, on sort.
     *      - sinon, on sors
     *
     *  A partir d'une connexion shiboleth, le mécanisme sera le même.
     *
     *  Comme la détection d'une authentification IP se fait dès l'entrée, elle est forcément dans le token si il existe.
     *
     * @return boolean
     */

    public function isConnected($requete, $validSessions = 1) {
        if ($this->validateToken($validSessions)) {
            /* if(strpos($this->token,'IP$') === FALSE){
              //Si il n'y a pas de session IP, on vérifie si il y a besoin d'en avoir une
              $this->autoConnectByIpRange($requete);
              } */
            return TRUE;
        } else if ($this->autoConnectByIpRange($requete)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /*
     * Cette méthode est appelée par le controleur principal et permet :
     *  - de vérifier la connexion
     *  - de déterminer le(s) mode(s) de connexion(s)
     *  - de lire les informations de l'(les) utilisateur(s) connecté(s)
     *
     * @param $requete Requete objet requête complet
     * @return $authInfos array l'ensemble des informations utilisateur(s)
     */

    public function readToken($requete, $validSessions = 1) {
        $authInfos = array();
        if ($this->isConnected($requete, $validSessions)) {
            $parts = explode('#', $this->token);
            $typeAuths = explode('£', $parts[0]);
            foreach ($typeAuths as $typeAuth) {
                if (strpos($typeAuth, 'IP$') !== FALSE) {
                    $userIP = $this->contentAbo->getUserInfos(substr($typeAuth, 3));
                    $user = $this->contentCom->getUserInfos($userIP['B_USER']);
                    $authInfos['I'] = array_merge($userIP, $user);
                    
                    $this->userI = 'IP$' . $userIP['ID'];
                    $this->userIPLog = 'IP$' . $userIP['ID'] . '#' . $parts[2];

                    $this->managerCom->touchLogIp($this->userIPLog);

                    if (!$this->redis->exists($userIP['B_USER'] . "AccessFilter")) {
                        $authInfos['I']['CACHE'] = 0;
                    } else {
                        $authInfos['I']['CACHE'] = 1;
                        $authInfos['I']['NBACCESS'] = $this->redis->scard($userIP['B_USER']);
                    }
                } else {
                    $user = $this->contentCom->getUserInfos($typeAuth);
                    $authInfos[$user["TYPE"]] = $user;

                    $localVar = 'user' . $user["TYPE"];
                    $this->$localVar = $user["ID_USER"];
                    $localVar.= 'Log';
                    $this->$localVar = $user["ID_USER"] . '#' . $parts[2];

                    $this->managerCom->touchLog($this->$localVar);

                    if (!$this->redis->exists($user["ID_USER"] . "AccessFilter")) {
                        $authInfos[$user["TYPE"]]['CACHE'] = 0;
                    } else {
                        $authInfos[$user["TYPE"]]['CACHE'] = 1;
                        $authInfos[$user["TYPE"]]['NBACCESS'] = $this->redis->scard($user["ID_USER"]);
                        //$redis = $this->redis->smembers($user["ID_USER"]);
                        //var_dump($authInfos[$user["TYPE"]]['NBACCESS']);
                        //var_dump($this->redis->smembers($user["ID_USER"]));
                    }
                }
            }
            //Si on est connecté, mais pas institution, on check quand meme l'IP
            if (!isset($authInfos['I'])) {
                $ip = $this->autoConnectByIpRange($requete);
                if ($ip) {
                    $parts = explode('#', $this->token);
                    $typeAuths = explode('£', $parts[0]);
                    foreach ($typeAuths as $typeAuth) {
                        if (strpos($typeAuth, 'IP$') !== FALSE) {
                            $userIP = $this->contentAbo->getUserInfos(substr($typeAuth, 3));
                            $user = $this->contentCom->getUserInfos($userIP['B_USER']);
                            $authInfos['I'] = array_merge($userIP, $user);
                            $this->userI = 'IP$' . $userIP['ID'];
                            $this->userIPLog = 'IP$' . $userIP['ID'] . '#' . $parts[2];

                            $this->managerCom->touchLogIp($this->userIPLog);

                            if (!$this->redis->exists($userIP['B_USER'] . "AccessFilter")) {
                                $authInfos['I']['CACHE'] = 0;
                            } else {
                                $authInfos['I']['CACHE'] = 1;
                                $authInfos['I']['NBACCESS'] = $this->redis->scard($userIP['B_USER']);
                            }
                        }
                    }
                }
            }
        }
        //Si on n'est pas loggué en mode particulier, on génère un token GUEST
        if (!isset($authInfos["U"])) {
            if ($this->guestToken == null) {
                $this->createGuestToken();
            }
            $guest = $this->contentCom->getGuestInfos($this->guestToken);
            $authInfos["G"] = $guest;
        } else {
            if ($this->guestToken != null) {
                //On le place dans l'array au cas où on en aurait besoin
                $authInfos["G"]['TOKEN'] = $this->guestToken;
            }
        }
        //Si on est connecté institution (IP ou autre), on va chercher certains CAIRN_PARAM_INST
        if (isset($authInfos['I']) && (!isset($authInfos['U']) || $authInfos['U']['SHOWALL'] != 1)) {
            $authInfos['I']['PARAM_INST'] = $this->content->getCairnParamsInst($authInfos['I']['ID_USER'], "'D','O','P','Y','S', 'A', 'H'");
            $authInfos['I']['PARAM_INST_WEBTRENDS'] = $this->content->getCairnParamsInstWebTrends($authInfos['I']['ID_USER']);//Récupération des informations pour la table de webTrends.
        }

        //On ajoute les informations globales utiles pour les stats (IP, tokens)
        $authInfos['IP'] = Service::get('AuthentificationIP')->getIpClient($requete);
        if ($this->token != null) {
            $authInfos['TOKEN'] = substr($this->token, 0, strpos($this->token, '#'));
        } else {
            $authInfos['TOKEN'] = $this->guestToken;
        }

        //Intégration d'informations de l'utilisateur pour la partie webTrends.
        if (isset($authInfos['U'])) {
            $isachatsPPV = $this->contentCom->isAchatsPPV($authInfos['U']['ID_USER']);
            $authInfos['U']['ACHAT-PPV'] = $isachatsPPV ? true : false ;
        }
        
        //On fait la lecture de l'historique
        Service::get('CairnHisto')->readHisto($authInfos);
        return $authInfos;
    }

    public function getUserLogId($var) {
        return $this->$var;
    }

    public function genFilter($idUser, $firstFilter = null,$force = 0){
        $firstName = '';
        if($firstFilter != null){
            $firstName = substr($firstFilter, strrpos($firstFilter,'/')+1);
            $firstName = '-'.substr($firstName,0, strpos($firstName,'.flt'));
        }

        if($this->redis->exists($idUser.$firstName."AccessFilter") && $force == 0){
            echo 'cache exists';
            return;
        }
        $request = array(
            "index" => Configuration::get('indexPath'),
            "userId" => $idUser,
            "filterPath" => Configuration::get('filterPath').'/'.$idUser.$firstName.'.flt',
            "firstFilterPath" => $firstFilter==null?Configuration::get('filterPath').'/cairnFreeArticles.flt':$firstFilter,
            "filterOperator" => "orWith"
        );
        $ok = $this->filter->genFilter($request);
        if($ok >= 0){
            $this->redis->setex($idUser.$firstName."AccessFilter",Configuration::get('filterPath')."/".$idUser.$firstName.'.flt');
        }
    }

}
