<?php

$delais = 60 * 60 * 4;   // 4 heures
header("Pragma: public");
header("Cache-Control: maxage=" . $delais);
header("Expires: " . gmdate('D, d M Y H:i:s', time() + $delais) . " GMT");

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function author_permut($string) {
    $string = trim(str_replace('  ', ' ', $string));
    $string = str_replace('  ', ' ', $string);
    $string = str_replace('  ', ' ', $string);
    $string = str_replace(' ', ' AND ', $string);

    return $string;
}

function unaccent_compare($a, $b) {
    return strcmp(trim(strtolower(remove_accents($a['TITRE']))), trim(strtolower(remove_accents($b['TITRE']))));
}

function seems_utf8($str) {
    $length = strlen($str);
    for ($i = 0; $i < $length; $i++) {
        $c = ord($str[$i]);
        if ($c < 0x80)
            $n = 0;# 0bbbbbbb
        elseif (($c & 0xE0) == 0xC0)
            $n = 1;# 110bbbbb
        elseif (($c & 0xF0) == 0xE0)
            $n = 2;# 1110bbbb
        elseif (($c & 0xF8) == 0xF0)
            $n = 3;# 11110bbb
        elseif (($c & 0xFC) == 0xF8)
            $n = 4;# 111110bb
        elseif (($c & 0xFE) == 0xFC)
            $n = 5;# 1111110b
        else
            return false;# Does not match any model
        for ($j = 0; $j < $n; $j++) { # n bytes matching 10bbbbbb follow ?
            if (( ++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
                return false;
        }
    }
    return true;
}

/**
 * Converts all accent characters to ASCII characters.
 *
 * If there are no accent characters, then the string given is just returned.
 *
 * @param string $string Text that might have accent characters
 * @return string Filtered string with replaced "nice" characters.
 */
function remove_accents($string) {
    if (!preg_match('/[\x80-\xff]/', $string))
        return $string;

    if (seems_utf8($string)) {
        $chars = array(
            // Decompositions for Latin-1 Supplement
            chr(195) . chr(128) => 'A', chr(195) . chr(129) => 'A',
            chr(195) . chr(130) => 'A', chr(195) . chr(131) => 'A',
            chr(195) . chr(132) => 'A', chr(195) . chr(133) => 'A',
            chr(195) . chr(135) => 'C', chr(195) . chr(136) => 'E',
            chr(195) . chr(137) => 'E', chr(195) . chr(138) => 'E',
            chr(195) . chr(139) => 'E', chr(195) . chr(140) => 'I',
            chr(195) . chr(141) => 'I', chr(195) . chr(142) => 'I',
            chr(195) . chr(143) => 'I', chr(195) . chr(145) => 'N',
            chr(195) . chr(146) => 'O', chr(195) . chr(147) => 'O',
            chr(195) . chr(148) => 'O', chr(195) . chr(149) => 'O',
            chr(195) . chr(150) => 'O', chr(195) . chr(153) => 'U',
            chr(195) . chr(154) => 'U', chr(195) . chr(155) => 'U',
            chr(195) . chr(156) => 'U', chr(195) . chr(157) => 'Y',
            chr(195) . chr(159) => 's', chr(195) . chr(160) => 'a',
            chr(195) . chr(161) => 'a', chr(195) . chr(162) => 'a',
            chr(195) . chr(163) => 'a', chr(195) . chr(164) => 'a',
            chr(195) . chr(165) => 'a', chr(195) . chr(167) => 'c',
            chr(195) . chr(168) => 'e', chr(195) . chr(169) => 'e',
            chr(195) . chr(170) => 'e', chr(195) . chr(171) => 'e',
            chr(195) . chr(172) => 'i', chr(195) . chr(173) => 'i',
            chr(195) . chr(174) => 'i', chr(195) . chr(175) => 'i',
            chr(195) . chr(177) => 'n', chr(195) . chr(178) => 'o',
            chr(195) . chr(179) => 'o', chr(195) . chr(180) => 'o',
            chr(195) . chr(181) => 'o', chr(195) . chr(182) => 'o',
            chr(195) . chr(182) => 'o', chr(195) . chr(185) => 'u',
            chr(195) . chr(186) => 'u', chr(195) . chr(187) => 'u',
            chr(195) . chr(188) => 'u', chr(195) . chr(189) => 'y',
            chr(195) . chr(191) => 'y',
            // Decompositions for Latin Extended-A
            chr(196) . chr(128) => 'A', chr(196) . chr(129) => 'a',
            chr(196) . chr(130) => 'A', chr(196) . chr(131) => 'a',
            chr(196) . chr(132) => 'A', chr(196) . chr(133) => 'a',
            chr(196) . chr(134) => 'C', chr(196) . chr(135) => 'c',
            chr(196) . chr(136) => 'C', chr(196) . chr(137) => 'c',
            chr(196) . chr(138) => 'C', chr(196) . chr(139) => 'c',
            chr(196) . chr(140) => 'C', chr(196) . chr(141) => 'c',
            chr(196) . chr(142) => 'D', chr(196) . chr(143) => 'd',
            chr(196) . chr(144) => 'D', chr(196) . chr(145) => 'd',
            chr(196) . chr(146) => 'E', chr(196) . chr(147) => 'e',
            chr(196) . chr(148) => 'E', chr(196) . chr(149) => 'e',
            chr(196) . chr(150) => 'E', chr(196) . chr(151) => 'e',
            chr(196) . chr(152) => 'E', chr(196) . chr(153) => 'e',
            chr(196) . chr(154) => 'E', chr(196) . chr(155) => 'e',
            chr(196) . chr(156) => 'G', chr(196) . chr(157) => 'g',
            chr(196) . chr(158) => 'G', chr(196) . chr(159) => 'g',
            chr(196) . chr(160) => 'G', chr(196) . chr(161) => 'g',
            chr(196) . chr(162) => 'G', chr(196) . chr(163) => 'g',
            chr(196) . chr(164) => 'H', chr(196) . chr(165) => 'h',
            chr(196) . chr(166) => 'H', chr(196) . chr(167) => 'h',
            chr(196) . chr(168) => 'I', chr(196) . chr(169) => 'i',
            chr(196) . chr(170) => 'I', chr(196) . chr(171) => 'i',
            chr(196) . chr(172) => 'I', chr(196) . chr(173) => 'i',
            chr(196) . chr(174) => 'I', chr(196) . chr(175) => 'i',
            chr(196) . chr(176) => 'I', chr(196) . chr(177) => 'i',
            chr(196) . chr(178) => 'IJ', chr(196) . chr(179) => 'ij',
            chr(196) . chr(180) => 'J', chr(196) . chr(181) => 'j',
            chr(196) . chr(182) => 'K', chr(196) . chr(183) => 'k',
            chr(196) . chr(184) => 'k', chr(196) . chr(185) => 'L',
            chr(196) . chr(186) => 'l', chr(196) . chr(187) => 'L',
            chr(196) . chr(188) => 'l', chr(196) . chr(189) => 'L',
            chr(196) . chr(190) => 'l', chr(196) . chr(191) => 'L',
            chr(197) . chr(128) => 'l', chr(197) . chr(129) => 'L',
            chr(197) . chr(130) => 'l', chr(197) . chr(131) => 'N',
            chr(197) . chr(132) => 'n', chr(197) . chr(133) => 'N',
            chr(197) . chr(134) => 'n', chr(197) . chr(135) => 'N',
            chr(197) . chr(136) => 'n', chr(197) . chr(137) => 'N',
            chr(197) . chr(138) => 'n', chr(197) . chr(139) => 'N',
            chr(197) . chr(140) => 'O', chr(197) . chr(141) => 'o',
            chr(197) . chr(142) => 'O', chr(197) . chr(143) => 'o',
            chr(197) . chr(144) => 'O', chr(197) . chr(145) => 'o',
            chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe',
            chr(197) . chr(148) => 'R', chr(197) . chr(149) => 'r',
            chr(197) . chr(150) => 'R', chr(197) . chr(151) => 'r',
            chr(197) . chr(152) => 'R', chr(197) . chr(153) => 'r',
            chr(197) . chr(154) => 'S', chr(197) . chr(155) => 's',
            chr(197) . chr(156) => 'S', chr(197) . chr(157) => 's',
            chr(197) . chr(158) => 'S', chr(197) . chr(159) => 's',
            chr(197) . chr(160) => 'S', chr(197) . chr(161) => 's',
            chr(197) . chr(162) => 'T', chr(197) . chr(163) => 't',
            chr(197) . chr(164) => 'T', chr(197) . chr(165) => 't',
            chr(197) . chr(166) => 'T', chr(197) . chr(167) => 't',
            chr(197) . chr(168) => 'U', chr(197) . chr(169) => 'u',
            chr(197) . chr(170) => 'U', chr(197) . chr(171) => 'u',
            chr(197) . chr(172) => 'U', chr(197) . chr(173) => 'u',
            chr(197) . chr(174) => 'U', chr(197) . chr(175) => 'u',
            chr(197) . chr(176) => 'U', chr(197) . chr(177) => 'u',
            chr(197) . chr(178) => 'U', chr(197) . chr(179) => 'u',
            chr(197) . chr(180) => 'W', chr(197) . chr(181) => 'w',
            chr(197) . chr(182) => 'Y', chr(197) . chr(183) => 'y',
            chr(197) . chr(184) => 'Y', chr(197) . chr(185) => 'Z',
            chr(197) . chr(186) => 'z', chr(197) . chr(187) => 'Z',
            chr(197) . chr(188) => 'z', chr(197) . chr(189) => 'Z',
            chr(197) . chr(190) => 'z', chr(197) . chr(191) => 's',
            // Euro Sign
            chr(226) . chr(130) . chr(172) => 'E',
            // GBP (Pound) Sign
            chr(194) . chr(163) => '');

        $string = strtr($string, $chars);
    } else {
        // Assume ISO-8859-1 if not UTF-8
        $chars['in'] = chr(128) . chr(131) . chr(138) . chr(142) . chr(154) . chr(158)
                . chr(159) . chr(162) . chr(165) . chr(181) . chr(192) . chr(193) . chr(194)
                . chr(195) . chr(196) . chr(197) . chr(199) . chr(200) . chr(201) . chr(202)
                . chr(203) . chr(204) . chr(205) . chr(206) . chr(207) . chr(209) . chr(210)
                . chr(211) . chr(212) . chr(213) . chr(214) . chr(216) . chr(217) . chr(218)
                . chr(219) . chr(220) . chr(221) . chr(224) . chr(225) . chr(226) . chr(227)
                . chr(228) . chr(229) . chr(231) . chr(232) . chr(233) . chr(234) . chr(235)
                . chr(236) . chr(237) . chr(238) . chr(239) . chr(241) . chr(242) . chr(243)
                . chr(244) . chr(245) . chr(246) . chr(248) . chr(249) . chr(250) . chr(251)
                . chr(252) . chr(253) . chr(255);

        $chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

        $string = strtr($string, $chars['in'], $chars['out']);
        $double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
        $double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
        $string = str_replace($double_chars['in'], $double_chars['out'], $string);
    }
    $string = str_replace("’", "'", $string);
    return $string;
}

function removeLig($string) {
    $chars = array('Œ' => 'OE', 'œ' => 'oe',
        'æ' => 'ae', 'Æ' => 'AE',
        'ĳ' => 'ij',
        'ﬀ' => 'ff',
        'ﬁ' => 'fi',
        'ﬂ' => 'fl',
        'ﬃ' => 'ffi',
        'ﬄ' => 'ffl',
        'ﬅ' => 'ft',
        'ﬆ' => 'st');
    $string = strtr($string, $chars);
    return $string;
}

require_once 'Framework/Controleur.php';
require_once 'Modele/Search.php';
require_once 'Modele/Content.php';
require_once 'Modele/ManagerStat.php';
require_once 'Modele/RedisClient.php';
require_once 'Modele/Filter.php';
require_once 'Modele/Translator.php';
require_once 'Modele/AnalyseRequest.php';
require_once 'Framework/include/JsonRpcPth.php';

class ControleurRecherche extends Controleur {

    private $content;
    private $contentdb;
    private $managerStat;
    private $redisClient;
    private $redisClientF;
    private $filter;
    private $analyser;
    private $expandSearch = true;
    private $urlService; //pour la recherche depuis le nt110

    private function getParametre2($parameter, $advanced = 0) {
        if ($this->requete->existeParametre($parameter)) {
            $str = trim($this->requete->getParametre($parameter));
            $str = removeLig($str);
            $str = str_replace(' et ', ' et~ ', $str);
            $str = str_replace(' ou ', ' ou~ ', $str);
            $str = str_replace(' SAUF ', ' ET PAS ', $str);
            if (substr($str, 0, 1) == '"' && substr($str, strlen($str) - 1) == '"') {
                $this->expandSearch = false;
                if (ctype_punct(substr($str, strlen($str) - 2)) && substr($str, strlen($str) - 2) != ')' && substr($str, strlen($str) - 2) != '*' ) {
                    $str = substr($str, 0, strlen($str) - 2) . '"';
                    return $str;
                }
            } else {
                if(strpos($str,'"') !== FALSE){
                    $this->expandSearch = false;
                }
                if ($advanced == 1) {
                    $str = str_replace(':', ' ', $str);
                    if (ctype_punct(substr($str, strlen($str) - 1)) && substr($str, strlen($str) - 1) != ')' && substr($str, strlen($str) - 1) != '*') {
                        $str = substr($str, 0, strlen($str) - 1);
                    }
                    /*if (strpos($str, ' ') !== FALSE) {
                        $str = '"' . $str . '"';
                    }*/
                    return $str;
                }
                if (ctype_punct(substr($str, strlen($str) - 1)) && substr($str, strlen($str) - 1) != ')' && substr($str, strlen($str) - 1) != '*') {
                    $str = substr($str, 0, strlen($str) - 1);
                    return $str;
                }
            }
            return $this->requete->getParametre($parameter);
        } else {
            return "";
        }
    }

    // instantiate the Model Class
    public function __construct() {
        $this->content = new Search();
        $this->contentdb = new Content();
        $this->managerStat = new ManagerStat('dsn_stat');
        $this->redisClient = new RedisClient(Configuration::get('redis_db_search'));
        $this->redisClientF = new RedisClient(Configuration::get('redis_db_user'));
        $this->filter = new Filter();
        $this->analyser = new AnalyseRequest();
        $this->urlService = Configuration::get('middleware_json_rpc', null);
    }

    public function booleanAdvancedFormKeywordsAnalyse(&$TRA, &$fnc_rech, $i, $field, $fieldTRA) {
        if ($this->getParametre2("etou$i") == 'OR' && $this->getParametre2("larech$i", 1) != '') {
            if ($fnc_rech != '') {
                $fnc_rech.= ' or ';
            }
            $fnc_rech.= "($field contains(" . $this->getParametre2("larech$i", 1) . '))';
            $TRA .= 'OU ' . $this->getParametre2("larech$i", 1) . ' (dans ' . $fieldTRA . ') ';
        } elseif ($this->getParametre2("etou$i") == 'AND' && $this->getParametre2("larech$i", 1) != '') {
            if ($fnc_rech != '') {
                $fnc_rech.= ' and ';
                $TRA .= 'ET ';
            }
            $fnc_rech.= "($field contains(" . $this->getParametre2("larech$i", 1) . '))';
            $TRA .= $this->getParametre2("larech$i", 1) . ' (dans ' . $fieldTRA . ') ';
        } elseif ($this->getParametre2("etou$i") == 'BUT' && $this->getParametre2("larech$i", 1) != '') {
            if ($fnc_rech != '') {
                $fnc_rech.= ' and ';
            }
            $fnc_rech.= ' not';
            $fnc_rech.= "($field contains(" . $this->getParametre2("larech$i", 1) . '))';
            $TRA .= 'SAUF ' . $this->getParametre2("larech$i", 1) . ' (pas dans ' . $fieldTRA . ') ';
        } elseif ($this->getParametre2("larech$i", 1) != '') {
            $TRA .= $this->getParametre2("larech$i", 1) . '(dans ' . $fieldTRA . ') ';
            $fnc_rech.= "($field contains(" . $this->getParametre2("larech$i", 1) . '))';
        }
    }

    private function advancedFormKeywordsAnalyse(&$TRA, &$fnc_rech, &$advanced) {
        if ($this->getParametre2('etou2') != '' || $this->getParametre2('larech1', 1) != '') {
            $advanced["etou2"] = $this->getParametre2("etou2");
            for ($i = 1; $i <= 6; $i++) {
                $ii = $i + 1;
                if ($this->getParametre2("larech$i", 1) != '') {
                    $advanced["larech$i"] = $this->getParametre2("larech$i", 1);
                    if ($this->getParametre2("dans$i") != '')
                        $advanced["dans$i"] = $this->getParametre2("dans$i");

                    if ($this->getParametre2("etou$i") != '')
                        $advanced["etou$i"] = $this->getParametre2("etou$i");
                }
                /*
                  if (trim($this->getParametre2("larech$i")) != '' && ($i>1)) {
                  if (!($fnc_rech == '')) {
                  switch ($this->getParametre2("etou$i")) {
                  case 'BUT' :
                  case 'AND' : $fnc_rech.= ' and ';
                  $TRA .= 'ET ';
                  break;
                  case 'OR' : $fnc_rech.= ' or ';
                  $TRA .= ' OU ' . $i . ' ';
                  break;
                  }
                  }

                  if ($this->getParametre2("etou$i") == 'BUT') {
                  $fnc_rech.= 'not';
                  $TRA .= 'PAS DANS ';
                  }
                  }
                 */

                switch ($this->getParametre2("dans$i")) {
                    case '':
                    case 'Tx':
                        if ($this->getParametre2("etou$i") == 'OR' && $this->getParametre2("larech$i", 1) != '') {
                            if ($fnc_rech != '') {
                                $fnc_rech.= ' or ';
                            }
                            $fnc_rech.= '(' . $this->getParametre2("larech$i", 1) . ')';
                            $TRA .= 'OU ' . $this->getParametre2("larech$i", 1) . ' (dans le texte intégral) ';
                        } elseif ($this->getParametre2("etou$i") == 'AND' && $this->getParametre2("larech$i", 1) != '') {
                            if ($fnc_rech != '') {
                                $fnc_rech.= ' and ';
                                $TRA .= 'ET ';
                            }
                            $fnc_rech.= '(' . $this->getParametre2("larech$i", 1) . ')';
                            $TRA .= $this->getParametre2("larech$i", 1) . ' (dans le texte intégral) ';
                        } elseif ($this->getParametre2("etou$i") == 'BUT' && $this->getParametre2("larech$i", 1) != '') {
                            if ($fnc_rech != '') {
                                $fnc_rech.= ' and ';
                            }
                            $fnc_rech.= ' not';
                            $fnc_rech.= '(' . $this->getParametre2("larech$i", 1) . ')';
                            $TRA .= 'SAUF ' . $this->getParametre2("larech$i", 1) . ' (pas dans le texte intégral) ';
                        } elseif ($this->getParametre2("larech$i", 1) != '') {
                            $TRA .= $this->getParametre2("larech$i", 1) . ' (dans le texte intégral) ';
                            $fnc_rech.= '(' . $this->getParametre2("larech$i", 1) . ')';
                        }
                        break;
                    case 'T':

                        $this->booleanAdvancedFormKeywordsAnalyse($TRA, $fnc_rech, $i, 'titre', "le titre de l'article");
                        // $fnc_rech.= '(titre contains(' . $this->getParametre2("larech$i") . '))';
                        // $TRA .= $this->getParametre2("larech$i") . ' (dans le titre de l\'article) ';
                        break;
                    case 'R':
                        $this->booleanAdvancedFormKeywordsAnalyse($TRA, $fnc_rech, $i, 'Resume', "le résume");

                        //                       $fnc_rech.= '(Resume contains(' . $this->getParametre2("larech$i") . '))';
                        //                     $TRA .= $this->getParametre2("larech$i") . ' (dans le résumé) ';
                        break;
                    case 'B':

                        $this->booleanAdvancedFormKeywordsAnalyse($TRA, $fnc_rech, $i, 'Biblio', "la bibliographie");
                        //$fnc_rech.= '(Biblio contains(' . $this->getParametre2("larech$i") . '))';
                        //                       $TRA .= $this->getParametre2("larech$i") . ' (dans la bibliographie) ';
                        break;
                    case 'Tr':
                        //$fnc_rech.= '(rev0 contains(' . $this->getParametre2("larech$i") . '))';
                        $this->booleanAdvancedFormKeywordsAnalyse($TRA, $fnc_rech, $i, 'rev0', "le titre de la revue");
                        //        $TRA .= $this->getParametre2("larech$i") . ' (dans le titre de la revue) ';
                        break;
                    case 'A':


                        if ($this->getParametre2("etou$i") == 'OR' && $this->getParametre2("larech$i") != '') {
                            if ($fnc_rech != '') {
                                $fnc_rech.= ' or ';
                                $TRA .= 'OU ';
                            }
                            $TRA .= $this->getParametre2("larech$i") . ' (dans le nom de l\'auteur) ';
                            $fnc_rech.= '(auteur contains(' . author_permut($this->getParametre2("larech$i")) . '))';
                        } elseif ($this->getParametre2("etou$i") == 'AND' && $this->getParametre2("larech$i") != '') {
                            if ($fnc_rech != '') {
                                $fnc_rech.= ' and ';
                                $TRA .= 'ET ';
                            }
                            $fnc_rech.= '(auteur contains(' . author_permut($this->getParametre2("larech$i")) . '))';
                            $TRA .= $this->getParametre2("larech$i", 1) . ' (dans le nom de l\'auteur) ';
                        } elseif ($this->getParametre2("etou$i") == 'BUT' && $this->getParametre2("larech$i") != '') {
                            if ($fnc_rech != '') {
                                $fnc_rech.= ' and ';
                            }
                            $fnc_rech.= ' not';
                            $fnc_rech.= '(auteur contains(' . author_permut($this->getParametre2("larech$i")) . '))';

                            $TRA .= 'SAUF ' . $this->getParametre2("larech$i") . ' (pas dans le nom de l\'auteur) ';
                        } elseif ($this->getParametre2("larech$i") != '') {
                            $TRA .= $this->getParametre2("larech$i") . ' (dans le nom de l\'auteur) ';
                            $fnc_rech.= '(auteur contains(' . author_permut($this->getParametre2("larech$i")) . '))';
                        }

                        break;
                    // Titouvr
                    case 'To':

                        $this->booleanAdvancedFormKeywordsAnalyse($TRA, $fnc_rech, $i, 'titrech', "le titre de l'ouvrage");

                        /*
                          if ($this->getParametre2("etou$i") == 'OR' && $this->getParametre2("larech$i") != '') {
                          if ($fnc_rech != '') {
                          $fnc_rech.= ' or ';
                          $TRA .= 'OU ' ;
                          }
                          $TRA .=  $this->getParametre2("larech$i") . ' (dans le titre de l\'ouvrage) ';
                          $fnc_rech.= '(titnum contains(' . $this->getParametre2("larech$i") . '))';




                          } elseif ($this->getParametre2("etou$i") == 'AND' && $this->getParametre2("larech$i") != '') {
                          if ($fnc_rech != '') {
                          $fnc_rech.= ' and ';
                          $TRA .= 'ET ';
                          }
                          $fnc_rech.= '(titnum contains(' . $this->getParametre2("larech$i") . '))';
                          $TRA .=  $this->getParametre2("larech$i") . ' (dans le titre de l\'ouvrage) ';
                          } elseif ($this->getParametre2("etou$i") == 'BUT' && $this->getParametre2("larech$i") != '') {
                          if ($fnc_rech != '') {
                          $fnc_rech.= ' and ';
                          }
                          $fnc_rech.= ' not';
                          $fnc_rech.= '(titnum contains(' . $this->getParametre2("larech$i") . '))';

                          $TRA .= 'ET ' . $this->getParametre2("larech$i") . ' (pas dans le titre de l\'ouvrage) ';
                          } elseif ($this->getParametre2("larech$i") != '') {
                          $TRA .= $this->getParametre2("larech$i") . ' (dans le titre de l\'ouvrage) ';
                          $fnc_rech.= '(titnum contains(' . $this->getParametre2("larech$i") . '))';

                          }
                         */

                        break;
                }
            }
        }
    }

    private function advancedFormTypePubAnalyse(&$TRA, &$fnc_rech, &$advanced) {

        $TRAP = '';
        $NBTRAP = 0;

        if ($this->getParametre2('etou2') != '') {
            if ($this->getParametre2('chk_revue') != 'on') {
                $advanced["chk_revue"] = "off";
                if ($fnc_rech != '') {
                    $fnc_rech.= ' and ';
                }
                $fnc_rech.= '(xfilter (notword "tp::1")) ';
            } else {
                $NBTRAP++;
                $TRAP .= ' les revues ';
                $advanced["chk_revue"] = "on";
            }

            if ($this->getParametre2('chk_edm') != 'on') {
                $advanced["chk_edm"] = "off";
                if ($fnc_rech != '') {
                    $fnc_rech.= ' and ';
                }
                $fnc_rech.= '(xfilter (notword "id_r::EDM")) ';
            } else {
                $NBTRAP++;
                if (!($TRAP == '')) {
                    $TRAP .= ' ou ';
                    $advanced["chk_edm"] = "on";
                }
                $TRAP .= 'l\'&eacute;tat du monde ';
            }
            if ($this->getParametre2('chk_ouvcol') != 'on') {
                $advanced["chk_ouvcol"] = "off";
                if ($fnc_rech != '') {
                    $fnc_rech.= ' and ';
                }
                $fnc_rech.= '(xfilter (notword "tp::3")) ';
            } else {
                $NBTRAP++;
                if ($TRAP != '') {
                    $TRAP .= ' ou ';
                }
                $TRAP .= 'les ouvrages collectifs ';
                $advanced["chk_ouvcol"] = "on";
            }

            if ($this->getParametre2('chk_ouvref') != 'on') {
                $advanced["chk_ouvref"] = "off";
                if ($fnc_rech != '') {
                    $fnc_rech.= ' and ';
                }
                $fnc_rech.= '(xfilter (notword "tp::6")) ';
            } else {
                $NBTRAP++;
                if ($TRAP != '') {
                    $TRAP .= ' ou ';
                }
                $TRAP .= 'les ouvrages de r&eacute;f&eacute;rence ';
                $advanced["chk_ouvref"] = "on";
            }

            if ($this->getParametre2('chk_mag') != 'on') {
                $advanced["chk_mag"] = "off";
                if ($fnc_rech != '') {
                    $fnc_rech.= ' and ';
                }
                $fnc_rech.= '(xfilter (notword "tp::2")) ';
            } else {
                $advanced["chk_mag"] = "on";
                $NBTRAP++;
                if ($TRAP != '') {
                    $TRAP .= ' ou ';
                }
                $TRAP .= 'les magazines ';
            }

            if ($NBTRAP == 4) {
                $TRA = trim($TRA) . ', dans tous les types de documents ';
            } else {
                $TRA = trim($TRA) . ", dans  $TRAP ";
            }
        }
    }

    private function advancedFormListAnalyse(&$TRA, &$fnc_rech, &$advanced) {
        /**
         * Discipline
         */
        if ($this->getParametre2('discipline') != '') {
            $advanced["discipline"] = $this->getParametre2('discipline');
            if ($fnc_rech != '') {
                $fnc_rech.= ' and ';
            }
            $fnc_rech.= '(xfilter (word "dr::' . $this->getParametre2('discipline') . '"))';
            $TRA = trim($TRA) . ", dans la discipline &lsquo;" . $this->contentdb->getUrlDiscFromPos($this->getParametre2('discipline')) . '&rsquo; ';
        }

        /**
         * Editeur
         */
        if ($this->getParametre2('editeur') != '') {
            $advanced["editeur"] = $this->getParametre2('editeur');
            if ($fnc_rech != '') {
                $fnc_rech.= ' and ';
            }

            /**
             * L'éditeur contient parfois un espace, dans ce cas, il faut spliter la requête en deux
             */
            $editeur = trim($this->getParametre2('editeur'));
            if (strpos($editeur, ' ') > 0) {
                list ($part1, $editeur) = explode(' ', $editeur);
                $fnc_rech.= '(xfilter (word "ed::' . $part1 . '")) and';
            }
            $fnc_rech.= '(xfilter (word "ed::' . $editeur . '"))';
            $TRA = trim($TRA) . ", chez l'&eacute;diteur &lsquo;" . $this->contentdb->getEditeurById($this->getParametre2('editeur'))['EDITEUR_NOM_EDITEUR'] . '&rsquo; ';
        }

        /**
         * Magazine
         */
        if ($this->getParametre2('revmag') != '') {
            $TRA = trim($TRA) . ", dans la revue/le magazine &lsquo;" . $this->contentdb->getRevuesById($this->getParametre2('revmag'))[0]['TITRE'] . '&rsquo; ';
            $advanced['revmag'] = $this->getParametre2('revmag');
        }

        /**
         * Collection
         */
        if ($this->getParametre2('recol') != '') {
            $advanced['recol'] = $this->getParametre2('recol');
            $TRA = trim($TRA) . ", dans la collection &lsquo;" . $this->contentdb->getRevuesById($this->getParametre2('recol'))[0]['TITRE'] . '&rsquo; ';
        }

        /**
         * Revue
         */
        if (($this->getParametre2('revmag') != '') || ($this->getParametre2('recol') != '') || ($this->getParametre2('revue') != '') || ($this->getParametre2('mag') != '')) {
            if (($this->getParametre2('revmag') != '') && (($this->getParametre2('chk_revue') == 'on') || ($this->getParametre2('[chk_mag') == 'on'))) {
                $fnc_rech2 = 'orword "id_r::' . strtolower($this->getParametre2('revmag')) . '"';
            }
            if (($this->getParametre2('recol') != '') && (($this->getParametre2('chk_ouvcol') == 'on') || ($this->getParametre2('chk_ouvref') == 'on'))) {
                $fnc_rech2.= ' orword "id_r::' . strtolower($this->getParametre2('recol')) . '"';
            }
            if (($this->getParametre2('revue') != '') && ($this->getParametre2('chk_revue') == 'on')) {
                $fnc_rech2.= ' orword "id_r::' . strtolower($this->getParametre2('revue')) . '"';
            }
            if (($this->getParametre2('mag') != '') && ($this->getParametre2('chk_mag') == 'on')) {
                $fnc_rech2.= ' orword "id_r::' . strtolower($this->getParametre2('mag')) . '"';
            }

            if ($fnc_rech2 != '') {
                if ($fnc_rech != '') {
                    $fnc_rech.= ' and ';
                }
                $fnc_rech.= ' (xfilter (' . $fnc_rech2 . '))';
            }
        }



        /**
         * Année
         */
        $an1 = $this->getParametre2("annee1") * 1;
        $an2 = $this->getParametre2("annee2") * 1;
        if ($an1 + $an2 > 0) {
            $advanced['annee1'] = $this->getParametre2('annee1');
            $advanced['annee2'] = $this->getParametre2('annee2');

            $TRA = trim($TRA) . ', p&eacute;riode: ';
            if ($an1 > $an2) {
                $tdiv = $an1;
                $an1 = $an2;
                $an2 = $tdiv;
            }

            if ($fnc_rech != '') {
                $fnc_rech.= ' and ';
            }
            if ($an1 < 1) {
                $an1 = '';
            }
            if ($an2 < 1) {
                $an2 = '';
            }
            $fnc_rech.= '(xfilter (word "dp::' . $an1 . '~~' . $an2 . '" ))';

            if ($an1 != '') {
                if ($an2 != '') {
                    $TRA.= 'de ' . $an1 . ' &agrave; ' . $an2 . ' ';
                } else {
                    $TRA.= 'apr&egrave;s ' . $an1 . ' ';
                }
            } else {
                $TRA.= 'avant ' . $an2 . ' ';
            }
        }
    }

    private function makeBooleanFacette(&$facettes, $name, $operator) {
        $f_bool = array();
        foreach ($facettes as $facette) {
            $f_bool[] = "(xfilter (word \"$name::$facette\"))";
        }
        return "(" . implode(")$operator(", $f_bool) . ")";
    }

    public function index() {
        $boolean = array();
        $cairnFacettes = explode(',', Configuration::get('cairnFacettes')); //array("tp", "dr", 'id_r', "dp");
        $cairnLabels = explode(',', Configuration::get('cairnLabels')); //array('tp' => 'Types', 'dp' => 'Dates de parution', 'dr' => 'Disciplines', 'id_r' => "Revues/collect.");

        $facette2labels = array_combine($cairnFacettes, $cairnLabels);
        $labels2facettes = array_flip($facette2labels);

        if ($this->requete->existeParametre('refinedr'))
            $is_refinedr = 1;
        else {
            $is_refinedr = 0;
        }

        $facetteshidden = array();
        $facetteshiddentmp = array();
        $myQuery = array();
        $facettesJson = array();

        foreach ($cairnFacettes as $facette) {

            if ($this->requete->existeParametre($facette . '_hidden')) {
                $facetteshidden[$facette] = $this->requete->getParametre($facette . '_hidden');
            }
            // we need a diff count in $_facettes and $facetteshidden
            if ($this->requete->existeParametre($facette)) {
                $_facettes = $this->requete->getParametre($facette);
                $myQuery[$facette] = array_flip($_facettes);
                if (count($_facettes) != count(explode(',', $facetteshidden[$facette]))) {
                    $facettesJson[$facette] = $_facettes;
                    $booleanLocal = $this->makeBooleanFacette($_facettes, $facette, 'OR');
                    /*if ($facette == 'efta') {
                        echo 'Facette efta:';
                        var_dump($booleanLocal);
                    }*/
                    $boolean[] = $booleanLocal;
                }
            }
        }

        if ($this->requete->existeParametre('type_search')) {
            if ($this->requete->getParametre('type_search') == 'english') {
                $boolean[] = '((xfilter (word "efta::2")))';
                $facetteshidden['efta'] = '0,1,2';
                $facettesJson['efta'] = '2';
                Service::get('CairnHisto')->addToHisto('searchMode', 'english', $this->authInfos);
            } else {
                Service::get('CairnHisto')->addToHisto('searchMode', 'all', $this->authInfos);
            }
        }

        $advanced = array();
        if ($this->requete->existeParametre("ID_NUMPUBLIE")) {
            $id_numpub = $this->requete->getParametre("ID_NUMPUBLIE");
            $boolean[] = "(xfilter (word \"np::$id_numpub\"))";
            $advanced['ID_NUMPUBLIE'] = $id_numpub;
        }




        if ($this->requete->existeParametre("ID_REVUE")) {
            $id_numpub = $this->requete->getParametre("ID_REVUE");
            $boolean[] = "(xfilter (word \"id_r::$id_numpub\"))";
            $advanced['ID_REVUE'] = $id_numpub;
        }



        $periode = "ALL";
        if ($this->requete->existeParametre("periode")) {
            $periode = $this->requete->getParametre("periode");
            $advanced["periode"] = $periode;
            if (!($periode == 'ALL')) {
                $periodeJours = '';
                switch ($periode) {
                    case 'SEM' : $periodeJours = 7;
                        break;
                    case 'MOIS' : $periodeJours = 30;
                        break;
                    case 'DEUX' : $periodeJours = 60;
                        break;
                    case 'SIX' : $periodeJours = 180;
                        break;
                }

                $boolean[] = "(xfilter (date \"T-$periodeJours~~T+0\"))";
            }
        }

    if ($this->requete->existeParametre("format")) // au format rss, il ne faut pas afficher les résultats trop vieux
        {
        $periode = 'DEUX';
        $periodeJours = 60;
        $advanced["periode"] = $periode;
    }

        /*
         * Profilage institution
         */
        if (isset($this->authInfos['I']['PARAM_INST']['S'])) {
            $notdrs = explode(',', $this->authInfos['I']['PARAM_INST']['S']);
            $allDisc = $this->contentdb->getDisciplines(null, 1);
            $boolDisc = "";
            foreach($allDisc as $disc){
                if(!in_array($disc['POS_DISC'], $notdrs)){
                    $boolDisc .= ($boolDisc==""?"":" OR ").'(xfilter (word \"dr::'.$disc['POS_DISC'].'\"))';
                }
            }
            if($boolDisc != ''){
                $boolean[] = "(".$boolDisc.")";
            }
            /*foreach ($notdrs as $notdr) {
                $boolean[] = "(xfilter (notword \"dr::$notdr\"))";
            }*/
        }
        if (isset($this->authInfos['I']['PARAM_INST']['Y'])) {
            $nottps = explode(',', $this->authInfos['I']['PARAM_INST']['Y']);
            foreach ($nottps as $nottp) {
                $boolean[] = "(xfilter (notword \"tp::$nottp\"))";
            }
        }

        $booleanCondition = implode(" AND ", $boolean);

        //var_dump($booleanCondition);

        /**
         * Construction de la requête "textuelle" en fonction des 6 critères possibles.
         * Chaque critère possède 3 attributs:
         *        etou = opérateur
         *        dans = choix du type de publication
         *        larech = texte à rechercher
         */
        $TRA = null;
        if ($this->getParametre2('etou2') != '' || $this->getParametre2('larech1') != '') {
            $fnc_rech = "";
            $TRA = "";

            $this->advancedFormKeywordsAnalyse($TRA, $fnc_rech, $advanced);
            $this->advancedFormTypePubAnalyse($TRA, $fnc_rech, $advanced);
            $this->advancedFormListAnalyse($TRA, $fnc_rech, $advanced);
        }

        /*
          if ($this->getParametre2('auteur') != '') {
          $advanced["auteur"] = $this->getParametre2("auteur");
          if ($fnc_rech != '') {
          $fnc_rech.= ' and ';
          }
          $fnc_rech.= '(auteur contains(' . author_permut($this->getParametre2('auteur')) . ')) ';
          $TRA = trim($TRA) . ', chez l\'auteur &lsquo;' . $this->getParametre2('auteur') . '&rsquo; ';
          } */



        $advanced_json = (json_encode($advanced));

        $indexes = array(Configuration::get("indexPath"));
        if ($this->requete->existeParametre("searchTerm")) {
            $searchTerm = $this->requete->getParametre("searchTerm");
            $searchTerm = str_replace('’', "'", $searchTerm);
            ;
            Service::get("CairnHisto")->addToHisto('recherches', $searchTerm, $this->authInfos);
        } /*else {
            $searchTerm = "voiture";
        }*/

        if ((isset($TEXTE_SEARCH) && $TEXTE_SEARCH != '') || (isset($fnc_rech) && $fnc_rech != '')) {
            $searchTerm = $TEXTE_SEARCH;

            if (!($booleanCondition == ''))
                $booleanCondition = "($booleanCondition) AND ($fnc_rech)";
            else
                $booleanCondition = $fnc_rech;
        }

        if ($searchTerm == "") {
            $searchMode = "boolean";
        } else {
            $searchMode = "triple";
            if(Configuration::get('modeRech') != null){
                $searchMode = Configuration::get('modeRech');
            }

            $str = trim($searchTerm);
            if (substr($str, 0, 1) == '"' && substr($str, strlen($str) - 1) == '"') {
                $this->expandSearch = false;
                if (ctype_punct(substr($str, strlen($str) - 2)) && substr($str, strlen($str) - 2) != ')' && substr($str, strlen($str) - 2) != '*') {
                    $searchTerm = substr($str, 0, strlen($str) - 2) . '"';
                }
            } else {
                if(strpos($str,'"') !== FALSE){
                    $this->expandSearch = false;
                }
                if (ctype_punct(substr($str, strlen($str) - 1)) && substr($str, strlen($str) - 1) != ')' && substr($str, strlen($str) - 1) != '*' ) {
                    $searchTerm = substr($str, 0, strlen($str) - 1);
                }
            }
        }
        $searchTerm = str_replace(' et ', ' et~ ', $searchTerm);
        $searchTerm = str_replace(' ou ', ' ou~ ', $searchTerm);
        $searchTerm = str_replace(' SAUF ', ' ET PAS ', $searchTerm);
        $searchTerm = removeLig($searchTerm);
        //var_dump($searchTerm);
        //var_dump($searchMode);
        //var_dump($booleanCondition);
        /* if ($this->requete->existeParametre("userid")) {
          $userid = $this->requete->getParametre("userid");
          if ($userid == 3) {
          $applyFilter = 'univ_paris7.flt';
          }
          if ($userid == 2) {
          $accessible_arts = $this->contentdb->getAccessibleArticles();
          }
          } */

        if ($this->requete->existeParametre("START")) {
            $startAt = (int) $this->requete->getParametre("START");
        } else {
            $startAt = 0;
        }

        $expander = array("family");
        if(Configuration::get('expansion') !== false){
            $expander = explode(',',Configuration::get('expansion'));
        }

        if (!($periode == "ALL")) {
            $sortMode = "mostRecent";
        } else {
            $sortMode = "";
        }

    if ($this->requete->existeParametre("format")) // au format rss, on affiche les plus recents
        {
        $sortMode = "mostRecent";
    }

        $fw = Configuration::get('fwRech');
        $pack = Configuration::get('packing');

        $proxyWindowWidth = 8;

        //Si on a une config spécifique, on surcharge...
        $evidensse = null;
        if ($this->requete->existeParametre('evidensse')) {
            $evidensse = $this->requete->getParametre('evidensse');
            foreach ($evidensse as $key => $value) {
                if ($key == 'expander') {
                    if (is_array($value)) {
                        $$key = $value;
                    } else {
                        $$key = explode(',', $value);
                    }
                } else {
                    $$key = $value;
                }
            }
        }

        if ($this->expandSearch == false) {
            $expander = array();
        }

        //On regarde si on a besoin d'un searchFilter (si il est dispo ou si on doit le générer
        $applyFilter = '';
        if((isset($this->authInfos['U']) && isset($this->authInfos['U']['HISTO_JSON']->searchModeInfo) && $this->authInfos['U']['HISTO_JSON']->searchModeInfo[0] == 'access')
            || (!isset($this->authInfos['U']) && isset($this->authInfos['G']) && isset($this->authInfos['G']['HISTO_JSON']->searchModeInfo) && $this->authInfos['G']['HISTO_JSON']->searchModeInfo[0] == 'access'))
        {
            if (isset($this->authInfos['U']) && $this->redisClientF->exists($this->authInfos['U']['ID_USER'] . "AccessFilter")) {
                $applyFilter = json_decode($this->redisClientF->get($this->authInfos['U']['ID_USER']."AccessFilter"));
            }else if (isset($this->authInfos['I']) && $this->redisClientF->exists($this->authInfos['I']['ID_USER'] . "AccessFilter")) {
                $applyFilter = json_decode($this->redisClientF->get($this->authInfos['I']['ID_USER']."AccessFilter"));
            }else {
                //Service::get('CairnHisto')->addToHisto('searchModeInfo', 'all', $this->authInfos);
                if(Configuration::get('filterEnabled') == 1 && (isset($this->authInfos['I']) || isset($this->authInfos['U']))){
                    $idUser = '';
                    if(isset($this->authInfos['I'])){
                        $idUser = $this->authInfos['I']['ID_USER'];
                        Service::get('Authentification')->genFilter($idUser);
                    }
                    if(isset($this->authInfos['U'])){
                        if($idUser != ''){
                            $firstFilter = $idUser;
                            $idUser = $this->authInfos['U']['ID_USER'];
                            Service::get('Authentification')->genFilter($idUser,Configuration::get('filterPath').'/'.$firstFilter.'.flt',1);
                        }else{
                            $idUser = $this->authInfos['U']['ID_USER'];
                            Service::get('Authentification')->genFilter($idUser);
                        }

                    }
                }else{
                    $applyFilter = Configuration::get('filterPath').'/cairnFreeArticles.flt';
                }
            }
            //echo 'Filtre utilisé:'.$applyFilter;
        }

        /* if(isset($this->authInfos['I'])){
          if($this->redisClient->exists($this->authInfos['I']['ID_USER']."AccessFilter")){
          $applyFilter = $this->redisClient->get($this->authInfos['I']['ID_USER']."AccessFilter");
          }else{
          if($this->redisClient->exists($this->authInfos['I']['ID_USER'])){
          $applyFilter = Configuration::get('filterPath')."/".$this->authInfos['I']['ID_USER'].'.flt';
          $request = array(
          "index" => Configuration::get('indexPath'),
          "filterPath" => $applyFilter,
          "docsId" => array_map('intval',$this->redisClient->smembers($this->authInfos['I']['ID_USER']))
          );
          //var_dump($request);
          $ok = $this->filter->genFilter($request);
          //var_dump($ok);
          $this->redisClient->setex($this->authInfos['I']['ID_USER']."AccessFilter",$applyFilter);
          }
          }
          } */


        /*
         *
         */

        if ($searchTerm <> '') {
            $request2analyse = strtolower($searchTerm);
            $request2analyse = str_replace('  ', ' ', $request2analyse);
            $request2analyse = trim($request2analyse);
            $request2analyse = trim($request2analyse);
            $request2analyse = trim($request2analyse);
            $reqArray = explode(" ", $request2analyse);
            if (sizeof($reqArray) < 5)
                $resultC4 = $this->analyser->doAnalyze($request2analyse); {

            }

            $booleanConditionL = "";
            foreach ($resultC4 as $C4) {
                //$booleanConditionL.= " orword \"C4::$C4\"  ";
				if($C4 != ''){
	                $booleanConditionL.= " andany(C4 contains($C4))  ";
				}
                //echo " <p>$C4</p>";
            }
            if ($booleanConditionL <> "") {
                if ($booleanCondition <> "")
                    $booleanCondition.="  $booleanConditionL";
                else {
                    $booleanCondition = "xfilter (word 'xlastword')   $booleanConditionL";
                }
            }
            $booleanCondition = trim($booleanCondition);
           // echo " <p>$booleanCondition</p>";
        }

        /*
         *
         */



        if (isset($advanced['ID_NUMPUBLIE']))
            $searchT = array('searchMode' => $searchMode, 'sort' => $sortMode, 'pack' => 0, 'fieldWeights' => $fw, 'request' => $searchTerm, 'applyFilter' => $applyFilter, 'method' => 'search', 'facettes' => $cairnFacettes, 'wantDetails' => 0, 'maxFiles' => 20, 'startAt' => $startAt, 'spell' => "fr", 'expander' => $expander, "index" => $indexes, "booleanCondition" => $booleanCondition);
        else{
            $searchT = array('searchMode' => $searchMode, 'autoStopLimit' => 350000, 'sort' => $sortMode, 'pack' => $pack, 'fieldWeights' => $fw, 'request' => $searchTerm, 'applyFilter' => $applyFilter, 'method' => 'search', 'facettes' => $cairnFacettes, 'wantDetails' => 0, 'maxFiles' => 20, 'startAt' => $startAt, 'spell' => "fr", 'expander' => $expander, "index" => $indexes, "booleanCondition" => $booleanCondition);
        }
        //if($proxyWindowWidth != null)
        {
            $searchT['proxyWindowWidth'] = 8;
        }

        // we check the cached mem
        //
        /* $redis = new Redis();
          $redis->connect(Configuration::get('redis_server'), 6379); */
        if ($this->redisClient->exists($searchT)) {
            $timeStart = microtime(true);
            $result = json_decode($this->redisClient->get($searchT));
            $timeEnd = microtime(true);
            // var_dump($result);
            $searchT['redis'] = 1;
        } else {
            $timeStart = microtime(true);

        if(!empty($this->urlService))
        {
            $clientS = new JsonRpcPth($this->urlService);
            $result = $clientS->doSearch($searchT);
        }
        else
        {
            $result = $this->content->doSearch($searchT);
        }

            $timeEnd = microtime(true);
            $this->redisClient->setex($searchT, $result);
            $searchT['redis'] = 0;
        }
        $searchT['execTime'] = $timeEnd - $timeStart;
        $searchT['totalFiles'] = $result->Stats->TotalFiles;
        $searchT['totalUnpacked'] = ((int) $result->Stats->TotalFiles + (int) $result->Stats->rejected);
        $this->managerStat->insertRecherche($searchTerm, $this->authInfos, $searchT);



        $disciplines = $this->contentdb->getAllDisciplinesLabels();
        $typepub = $this->contentdb->getAllTypePubLabels();

        $facettes = $result->Facettes;
        $myFacettes = array();

        foreach ($facettes as $key => $value) {
            foreach ($value as $key1 => $value1) { {
                    if (isset($myFacettes[$key][$key1])) {
                        $myFacettes[$key][$key1]+=$value1;
                    } else {
                        $myFacettes[$key][$key1] = $value1;
                    }
                }
            }
        }


        $myFacettes2view = array();
        foreach ($myFacettes as $key => $value) {
            foreach ($value as $key1 => $value1) {
                if ($key == 'dr' && $key1 != '-') {
                    if (!($is_refinedr) || (isset($myQuery['dr'][$key1]))) {
                        $myFacettes2view[$facette2labels[$key]][(isset($disciplines[$key1]) ? $disciplines[$key1] : "")] = array('nb' => $value1, 'mkey' => $key1);
                    }
                    $facetteshiddentmp['dr'][] = $key1;
                } elseif ($key == 'tp') {
                    $myFacettes2view[$facette2labels[$key]][$typepub[$key1]] = array('nb' => $value1, 'mkey' => $key1);
                    $facetteshiddentmp['tp'][] = $key1;
                } elseif ($key == 'dp') {
                    if ((int) $key1 < 2000) {
                        if (isset($myFacettes2view[$facette2labels[$key]]['avant 2000']['nb'])) {
                            $myFacettes2view[$facette2labels[$key]]['avant 2000']['nb'] += $value1;
                        } else {
                            $myFacettes2view[$facette2labels[$key]]['avant 2000']['nb'] = $value1;
                        }
                        //$facetteshiddentmp['dp'][]='~~2000';
                    } else {
                        $myFacettes2view[$facette2labels[$key]][$key1] = array('nb' => $value1, 'mkey' => $key1);
                        $facetteshiddentmp['dp'][] = $key1;
                    }
                } elseif ($key == 'id_r') {
                    $myFacettes2view[$facette2labels[$key]][$key1] = array('nb' => $value1, 'mkey' => $key1);
                    $facetteshiddentmp['id_r'][] = $key1;
                } elseif ($key == 'efta') {
                    $myFacettes2view[$facette2labels[$key]][$key1] = array('nb' => $value1, 'mkey' => $key1);
                    $facetteshiddentmp['efta'][] = $key1;
                }
            }
        }
        if (isset($myFacettes2view[$facette2labels['dp']]['avant 2000'])) {
            $myFacettes2view[$facette2labels[$key]]['avant 2000']['mkey'] = '~~2000';
            $facetteshiddentmp['dp'][] = 'avant 2000';
        }

        krsort($myFacettes2view[$facette2labels['dp']]);
        arsort($myFacettes2view[$facette2labels['dr']]);
        arsort($myFacettes2view[$facette2labels['id_r']]);
        if (Configuration::get('mode') == 'cairninter') {
            krsort($myFacettes2view[$facette2labels['efta']]);
        }

        array_splice($myFacettes2view[$facette2labels['dr']], 15);



        $collectionsFacettes = "'" . implode("','", array_keys($myFacettes2view[$facette2labels['id_r']])) . "','" . implode("','", explode(',', $facetteshidden['id_r'])) . "'";
        array_splice($myFacettes2view[$facette2labels['id_r']], 10);

        $facettesRevues = $this->contentdb->getCollectionNamesFromListId($collectionsFacettes);
        array_splice($facetteshiddentmp['dr'], 15);

        foreach ($cairnFacettes as $facette) {
            if (!(isset($facetteshidden[$facette]))) {
                if ($facette == 'dr') {
                    $dr_local = array();
                    foreach ($myFacettes2view[$facette2labels['dr']] as $key => $value)
                        $dr_local[] = $value['mkey'];
                    $facetteshidden[$facette] = implode(',', $dr_local);
                } elseif ($facette == 'id_r') {
                    $dr_local = array();
                    foreach ($myFacettes2view[$facette2labels['id_r']] as $key => $value)
                        $dr_local[] = $value['mkey'];
                    $facetteshidden[$facette] = implode(',', $dr_local);
                } else {
                    $facetteshidden[$facette] = implode(',', $facetteshiddentmp[$facette]);
                }
            }
        }


        $listId = array();
        $listNums = array();
        $listId2 = array();
        $listPortals = array();
        $portalInfo = '';
        foreach ($result->Items as $res) {
            $listNums[] = "'" . $res->userFields->np . "'";
            $listId2[] = "'" . trim($res->userFields->id) . "'";
            if ($res->userFields->pk0 == '1') {
                $listId[] = "'" . $res->userFields->np . "'";
            }
            if (isset($res->userFields->idp) && !($res->userFields->idp == '')) {
                $listPortals[] = "'" . trim($res->userFields->id) . "'";
            }
        }
        $concepts = array();
        if (sizeof($listId2) > 0) {

            if (sizeof($listPortals) > 0) {
                $portalInfo = $this->contentdb->getPortalInfoFromArticleId(implode(',', $listPortals));
            }
            /*
              $termesassocies = $this->contentdb->getTermesassocies(implode(',', $listId2));
              $myConcepts = array();
              foreach ($termesassocies as $key => $value) {
              $tt = explode(',', $value[0]);
              $done = 0;
              foreach ($tt as $t) {
              $done++;
              if ($done > 20)
              break;
              if($t != ''){
              $ttt = explode(';', $t);
              $valLocale = trim($ttt[1]);
              if ($valLocale <> ""){
              if(isset($myConcepts[$valLocale])){
              $myConcepts[$valLocale]+=(int) $ttt[0];
              }else{
              $myConcepts[$valLocale]=(int) $ttt[0];
              }
              }
              }
              }
              }

              arsort($myConcepts);
              array_splice($myConcepts, 20);

              foreach ($myConcepts as $key => $value) {
              $concepts[] = $key;
              }
             */
            $modeBoutons = Configuration::get('modeBoutons');
            if ($modeBoutons == 'cairninter') {
                $articlesButtons = Service::get('ContentArticle')->readButtonsForInterFromSearch(implode(',', $listId2), $this->authInfos);
            } else {
                $articlesButtons = Service::get('ControleAchat')->whichButtonsForArticles($this->authInfos, implode(',', $listId2));
            }
        }

        $concepts = $result->Concepts;
        $bidon = array_shift($concepts);
        //array_splice($concepts,20);



        if (sizeof($listNums) > 0) {
            $listNum = implode(',', $listNums);
            $metaNumero = $this->contentdb->getMetNumForRecherche($listNum);
        } else {
            $metaNumero = array();
        }

        $TRA = str_replace(' ET PAS ', ' SAUF ', $TRA);
        $searchTerm = str_replace(' ET PAS ', ' SAUF ', $searchTerm);
        $TRA = str_replace('~', '', $TRA);
        $searchTerm = str_replace('~', '', $searchTerm);

        $typeMicro = array(1 => "Article de Revue", 2 => "Article de Magazine", 3 => "Chapitre d'ouvrage", 4 => "L'état du Monde", 5 => "Contribution d'ouvrage", 6 => "Chapitre d'encyclopédie de poche");
        $typeMacro = array(1 => "Numéro de Revue", 2 => "Numéro de Magazine", 3 => "Ouvrage", 4 => "L'état du Monde", 5 => "Ouvrage collectif", 6 => "Encyclopédie de poche");
        $typeDocument = array(0 => $typeMicro, 1 => $typeMacro);

        // Métadonnées pour webtrends
        $webtrendsService = Service::get('Webtrends');
        $webtrendsTags = array_merge(
            $webtrendsService->getTagsForAllPages('resultats-recherche', $this->authInfos),
            $webtrendsService->getTagsForResearchPage($result->Stats->TotalFiles, $searchTerm)  // TODO: Il y a plusieurs nombres... Je ne sais pas lequel prendre
        );
        $headers = $webtrendsService->webtrendsTagsToHeadersTags($webtrendsTags);

        if ($this->requete->existeParametre("format")) // format rss, même param mais autre vue
        {
        $this->genererVue(array('evidensse' => $evidensse, 'facettesRevues' => $facettesRevues, 'portalInfo' => $portalInfo, 'periode' => $periode, 'facettesJson' => json_encode($facettesJson), 'advancedJson' => $advanced_json, 'metaNumero' => $metaNumero, 'results' => $result->Items, 'facettes' => $myFacettes2view, 'concepts' => $concepts, 'label2facette' => $labels2facettes, 'stats' => $result->Stats, 'hiddenFacettes' => $facetteshidden, 'disciplines' => $disciplines, 'typepub' => $typepub, 'searchTerm' => $searchTerm, 'TRA' => $TRA, 'typeDocument' => $typeDocument, 'limit' => $startAt, 'articlesButtons' => $articlesButtons), 'resultatRechercheRSS.php', 'none');
        }
        else
        {
            $this->genererVue(array('evidensse' => $evidensse, 'facettesRevues' => $facettesRevues, 'portalInfo' => $portalInfo, 'periode' => $periode, 'facettesJson' => json_encode($facettesJson), 'advancedJson' => $advanced_json, 'metaNumero' => $metaNumero, 'results' => $result->Items, 'facettes' => $myFacettes2view, 'concepts' => $concepts, 'label2facette' => $labels2facettes, 'stats' => $result->Stats, 'hiddenFacettes' => $facetteshidden, 'disciplines' => $disciplines, 'typepub' => $typepub, 'searchTerm' => $searchTerm, 'TRA' => $TRA, 'typeDocument' => $typeDocument, 'limit' => $startAt, 'articlesButtons' => $articlesButtons), 'resultatRecherche.php', null, $headers);
        }
    }

    public function pertinent() {
        $indexes = array(Configuration::get("indexPath"));
        if ($this->requete->existeParametre("searchTerm"))
            $searchTerm = $this->requete->getParametre("searchTerm");
        else {
            $searchTerm = "";
        }


        //$searchTerm = remove_accents($searchTerm);
        $boolean = array();
        if ($this->requete->existeParametre("ID_NUMPUBLIE")) {
            $id_numpub = $this->requete->getParametre("ID_NUMPUBLIE");
            $boolean[] = "(xfilter (word \"np::$id_numpub\"))";
        }

        $fnc_rech = "";
        $TRA = "";
        $advanced = array();
        $this->advancedFormKeywordsAnalyse($TRA, $fnc_rech, $advanced);





        $expander = array("family");
        if(Configuration::get('expansion') !== false){
            $expander = explode(',',Configuration::get('expansion'));
        }
        $booleanCondition = implode(" AND ", $boolean);

        if ($TEXTE_SEARCH != '' || $fnc_rech != '') {
            $searchTerm = $TEXTE_SEARCH;

            if (!($booleanCondition == ''))
                $booleanCondition = "($booleanCondition) AND ($fnc_rech)";
            else
                $booleanCondition = $fnc_rech;
        }

        if ($searchTerm == "") {
            $searchMode = "boolean";
        } else {
            $searchMode = "triple";
            if (substr($searchTerm, 0, 1) == '"' && substr($searchTerm, strlen($searchTerm) - 1) == '"') {
                $expander = array();
            }
        }

        //echo "boolean:$booleanCondition" . "\n" ."searchMode:$searchMode \nsearchTerm=$searchTerm";
        //var_dump($_POST);

        $fw = Configuration::get('fwPert');

        //Si on a une config spécifique, on surcharge...
        $evidensse = null;
        if ($this->requete->existeParametre('evidensse')) {
            $evidensse = $this->requete->getParametre('evidensse');
            foreach ($evidensse as $key => $value) {
                if ($key == 'expander') {
                    if (is_array($value)) {
                        $$key = $value;
                    } else {
                        $$key = explode(',', $value);
                    }
                } else {
                    $$key = $value;
                }
            }
        }

        //On regarde si on a besoin d'un searchFilter (si il est dispo ou si on doit le générer
        $applyFilter = '';
        if((isset($this->authInfos['U']) && isset($this->authInfos['U']['HISTO_JSON']->searchModeInfo) && $this->authInfos['U']['HISTO_JSON']->searchModeInfo[0] == 'access')
            || (!isset($this->authInfos['U']) && isset($this->authInfos['G']) && isset($this->authInfos['G']['HISTO_JSON']->searchModeInfo) && $this->authInfos['G']['HISTO_JSON']->searchModeInfo[0] == 'access'))
        {
            if (isset($this->authInfos['U']) && $this->redisClientF->exists($this->authInfos['U']['ID_USER'] . "AccessFilter")) {
                $applyFilter = json_decode($this->redisClientF->get($this->authInfos['U']['ID_USER']."AccessFilter"));
            }else if (isset($this->authInfos['I']) && $this->redisClientF->exists($this->authInfos['I']['ID_USER'] . "AccessFilter")) {
                $applyFilter = json_decode($this->redisClientF->get($this->authInfos['I']['ID_USER']."AccessFilter"));
            }else {
                //Service::get('CairnHisto')->addToHisto('searchModeInfo', 'all', $this->authInfos);
                $applyFilter = Configuration::get('filterPath').'/cairnFreeArticles.flt';
            }
            echo 'Filtre utilisé:'.$applyFilter;
        }

        $searchT = array('pack' => 0, 'fieldWeights' => $fw, 'request' => $searchTerm, 'applyFilter' => $applyFilter, 'method' => 'search', 'facettes' => '', 'wantDetails' => 0, 'maxFiles' => 3, 'startAt' => 0, 'spell' => "", 'expander' => $expander, "index" => $indexes, "booleanCondition" => $booleanCondition);

        if(!empty($this->urlService))
        {
            $clientS = new JsonRpcPth($this->urlService);
            $result = $clientS->doSearch($searchT);
        }
        else
        {
            $result = $this->content->doSearch($searchT);
        }

        $listPortals = array();
        $resultsToSort = array();
        $listId2 = array();
        foreach ($result->Items as $item) {
            $listId2[] = "'" . trim($item->userFields->id) . "'";
            $resultsToSort[(int) $item->userFields->pgd] = $item;
            if (!($item->userFields->idp == '')) {
                $listPortals[] = "'" . trim($item->userFields->id) . "'";
            }
        }


        if (sizeof($listPortals) > 0) {
            $portalInfo = $this->contentdb->getPortalInfoFromArticleId(implode(',', $listPortals));
        }

        $articlesButtons = array();
        if (sizeof($listId2) > 0) {
            $articlesButtons = Service::get('ControleAchat')->whichButtonsForArticles($this->authInfos, implode(',', $listId2));
        }

        //ksort($resultsToSort);



        $metaNumero = $this->contentdb->getMetNumForRecherche("'$id_numpub'");

        $typeMicro = array(1 => "Article de Revue", 2 => "Article de Magazine", 3 => "Chapitre d'ouvrage", 4 => "L'état du Monde", 5 => "Contribution d'ouvrage", 6 => "Chapitre d'encyclopédie de poche");
        $typeMacro = array(1 => "Numéro de Revue", 2 => "Numéro de Magazine", 3 => "Ouvrage", 4 => "L'état du Monde", 5 => "Ouvrage collectif", 6 => "Encyclopédie de poche");
        $typeDocument = array(0 => $typeMicro, 1 => $typeMacro);
        $this->genererVue(array('portalInfo' => $portalInfo, 'searchTerm' => $searchTerm, 'metaNumero' => $metaNumero, 'results' => $resultsToSort, 'stats' => $result->Stats, 'typepub' => $typepub, 'typeDocument' => $typeDocument, 'articlesButtons' => $articlesButtons), 'pertinent.php', 'gabaritAjax.php');

//        $this->genererVue(array('test' => $polo),'pertinent.php','gabaritAjax.php');
    }

    public function custom_sort($a, $b) {
        return strcoll($a['last_name'], $b['last_name']);
    }

    public function rechercheAvancee() {

        $revues = $this->contentdb->getRevuesByType(1, true);
        $mags = $this->contentdb->getRevuesByType(2, true);
        $revMags = array_merge($revues, $mags);

        usort($revMags, 'unaccent_compare');

        $collections = $this->contentdb->getRevuesByType(3, true);
        $collectionsEnc = $this->contentdb->getRevuesByType(6, true);
        $colls = array_merge($collections, $collectionsEnc);
        usort($colls, 'unaccent_compare');

        $editeurs = $this->contentdb->getEditeurs();

        $headers = Service::get('Webtrends')->webtrendsHeaders('recherche-avancee', $this->authInfos);

        $this->genererVue(array('revs' => $revues, 'mags' => $mags,
            'revMags' => $revMags, 'colls' => $colls, 'editeurs' => $editeurs), null, null, $headers);
    }

    public function getAjaxAdvancedForm() {
        $revues = $this->contentdb->getRevuesByType(1, true);
        $mags = $this->contentdb->getRevuesByType(2, true);
        $revMags = array_merge($revues, $mags);
        usort($revMags, 'unaccent_compare');

        $collections = $this->contentdb->getRevuesByType(3, true);
        $collectionsEnc = $this->contentdb->getRevuesByType(6, true);
        $colls = array_merge($collections, $collectionsEnc);
        usort($colls, 'unaccent_compare');

        $editeurs = $this->contentdb->getEditeurs();

        $this->genererVue(array('revs' => $revues, 'mags' => $mags,
            'revMags' => $revMags, 'colls' => $colls, 'editeurs' => $editeurs)
                , 'getAjaxAdvancedForm.php', 'gabaritAjax.php');
    }

    public function sujetProche() {
        // get id
        if ($this->requete->existeParametre("ID_ARTICLE")) {
            $ID_ARTICLE = $this->requete->getParametre("ID_ARTICLE");

            $indexes = array(Configuration::get("indexPath"));


            //$searchTerm = remove_accents($searchTerm);
            $boolean = array();
            if ($this->requete->existeParametre("ID_NUMPUBLIE")) {
                $id_numpub = $this->requete->getParametre("ID_NUMPUBLIE");
                $boolean[] = "(xfilter (word \"np::$id_numpub\"))";
            }

            $expander = array("family");


            //$booleanCondition = "(xfilter (word \"id::$ID_ARTICLE\"))";
            $booleanCondition = "(id contains ($ID_ARTICLE))";
            $searchT = array('pack' => 0, 'request' => 'xlastword', 'applyFilter' => '', 'method' => 'search', 'noFacettes' => '1', 'wantDetails' => 0, 'maxFiles' => 1, 'startAt' => 0, 'spell' => "", "index" => $indexes, "booleanCondition" => $booleanCondition);


            if(!empty($this->urlService))
            {
                $clientS = new JsonRpcPth($this->urlService);
                $result = $clientS->doSearch($searchT);
            }
            else
            {
                $result = $this->content->doSearch($searchT);
            }

            //$termesassocies = ($this->contentdb->getTermesassocies("'$ID_ARTICLE'"));


            $myConcepts = array();
            /*
              $C0a = array();
              foreach ($termesassocies as $key => $value) {
              //echo"<p>$value[0]</p>";
              $tt = explode(',', $value[0]);
              $done = 0;
              foreach ($tt as $t) {
              $done++;
              if ($done > 20)
              break;
              $ttt = explode(';', $t);
              //foreach($ttt as $keyt=>$valuet)
              //         echo "<p>".$ttt[0]."::". $ttt[1]."</p>";
              $valLocale = trim($ttt[1]);
              if ($valLocale <> "")
              $C0a[] = remove_accents($valLocale);
              }
              } */


            $C0 = ($result->Items[0]->userFields->C0);
            //echo "<hr/>";
            $C0a = explode('|', $C0);



            $C4 = ($result->Items[0]->userFields->C4);
            $np = ($result->Items[0]->userFields->np);

            $titre = $result->Items[0]->userFields->tr;
            $typePubCurrent = $result->Items[0]->userFields->tp;



            $C4 = str_replace("(", '', $C4);
            $C4 = str_replace(")", '', $C4);
            $C4a = explode('|', $C4);
            $bool = "";
            $bool2 = "";
            for ($x = 0; $x < sizeof($C0a); $x++) {
                $bool.=" andany(" . $C0a[$x] . ":" . (2 * 11 - 2 * $x) . ") ";
                if ($x > 9)
                    break;
            }

            for ($x = 0; $x < sizeof($C4a); $x++) {
                $bool2.=" andany(c4::" . $C4a[$x] . ":" . (10 - $x) . ") ";
                if ($x > 4)
                    break;
            }

            $bool3 = '';
            for ($x = 0; $x < sizeof($C4a); $x++) {
                if ($x == 0)
                    $bool3 = "(c4::" . trim($C4a[$x]) . ":" . (10 - $x) . ")";
                else {
                    $bool3.=" OR (c4::" . trim($C4a[$x]) . ":" . (10 - $x) . ")";
                }

                if ($x > 4)
                    break;
            }



            $booleanCondition = "(xfilter ($bool $bool2))  andany (np contains $np:100)";
            $booleanCondition = "(xfilter ($bool)) AND (($bool3))  andany (np contains $np:1000) AND NOT " . "(xfilter (word \"id::$ID_ARTICLE\"))";
            ;

            /*
             * Profilage institution
             */
            if (isset($this->authInfos['I']['PARAM_INST']['S'])) {
                $notdrs = explode(',', $this->authInfos['I']['PARAM_INST']['S']);
                foreach ($notdrs as $notdr) {
                    $booleanCondition .= " AND (xfilter (notword \"dr::$notdr\"))";
                }
            }
            if (isset($this->authInfos['I']['PARAM_INST']['Y'])) {
                $nottps = explode(',', $this->authInfos['I']['PARAM_INST']['Y']);
                foreach ($nottps as $nottp) {
                    $booleanCondition .= " AND (xfilter (notword \"tp::$nottp\"))";
                }
            }

            $searchT = array('pack' => 0, 'request' => 'xlastword', 'applyFilter' => '', 'method' => 'search', 'noFacettes' => '1', 'wantDetails' => 0, 'maxFiles' => 10, 'startAt' => 0, 'spell' => "", "index" => $indexes, "booleanCondition" => $booleanCondition, 'searchMode' => "boolean");

            if(!empty($this->urlService))
            {
                $clientS = new JsonRpcPth($this->urlService);
                $result = $clientS->doSearch($searchT);
            }
            else
            {
                $result = $this->content->doSearch($searchT);
            }

            $ouvrages = array();
            $revues = array();
            $magazines = array();
            $listId = array();
            $listId2 = array();
            $listNums = array();

            foreach ($result->Items as $res) {
                $listNums[] = "'" . $res->userFields->np . "'";
                $listId2[] = "'" . $res->userFields->id . "'";
                $typePub = $res->userFields->tp;
                if(Configuration::get('modeBoutons') == 'cairninter'){
                    $typePub = 1;
                }
                switch ($typePub) {
                    case "3":
                    case "5":
                    case "6":
                        $ouvrages[] = $res;
                        $listId[] = "'" . $res->userFields->np . "'";
                        break;
                    case "2":
                        $magazines[] = $res;
                        break;
                    case "1":
                        $revues[] = $res;
                        break;
                    case "4":
                        break;
                }

                if (isset($res->userFields->idp) && !($res->userFields->idp == '')) {
                    $listPortals[] = "'" . trim($res->userFields->id) . "'";
                }
            }
            if (sizeof($listNums) > 0) {
                $listNum = implode(',', $listNums);
                $metaNumero = $this->contentdb->getMetNumForRecherche($listNum);
            } else {
                $metaNumero = array();
            }

            if (isset($listPortals) && sizeof($listPortals) > 0) {
                $portalInfo = $this->contentdb->getPortalInfoFromArticleId(implode(',', $listPortals));
            } else {
                $portalInfo = array();
            }

            $articlesButtons = array();
            if (sizeof($listId2) > 0) {
                $modeBoutons = Configuration::get('modeBoutons');
                if ($modeBoutons == 'cairninter') {
                    $articlesButtons = Service::get('ContentArticle')->readButtonsForInterFromSearch(implode(',', $listId2), $this->authInfos);
                } else {
                    $articlesButtons = Service::get('ControleAchat')->whichButtonsForArticles($this->authInfos, implode(',', $listId2));
                }
            }

            $typeMicro = array(1 => "Article de Revue", 2 => "Article de Magazine", 3 => "Chapitre d'ouvrage", 4 => "L'état du Monde", 5 => "Contribution d'ouvrage", 6 => "Chapitre d'encyclopédie de poche");
            $typeMacro = array(1 => "Numéro de Revue", 2 => "Numéro de Magazine", 3 => "Ouvrage", 4 => "L'état du Monde", 5 => "Ouvrage collectif", 6 => "Encyclopédie de poche");
            $typeDocument = array(0 => $typeMicro, 1 => $typeMacro);

            // Metadonnées webtrends
            $headers = Service::get('Webtrends')->webtrendsHeaders('sur-un-sujet-proche', $this->authInfos);
            $this->genererVue(array('titre' => $titre, 'metaNumero' => $metaNumero, 'Ouvrages' => $ouvrages, 'Revues' => $revues, 'Magazines' => $magazines, 'label2facette' => $labels2facettes, 'stats' => $result->Stats, 'hiddenFacettes' => $facetteshidden, 'disciplines' => $disciplines, 'typepub' => $typepub, 'searchTerm' => $searchTerm, 'typeDocument' => $typeDocument, 'accessibleArticles' => $accessible_arts, 'limit' => $startAt, 'articlesButtons' => $articlesButtons, 'portalInfo' => $portalInfo, 'typePubCurrent' => $typePubCurrent), null, null, $headers);
        } else {
            // Affichage de la page d'erreur.
            header('Location: http://' . Configuration::get('urlSite', 'www.cairn.info') . '/error_no_id.php');
            die();
        }
    }

    public function redirectToFrench() {
        if ($this->requete->existeParametre('searchTerm')) {
            $searchTerm = $this->requete->getParametre('searchTerm');

            $translator = new Translator();
            $result = $translator->translate($searchTerm);

            $boolOperator = ') W/5 (';
            $searchTermTranslated = urlencode('(' . implode($boolOperator, $result) . ')');
            $newUrl = 'http://' . Configuration::get('crossDomainUrl') . '/resultats_recherche.php?searchTerm=' . $searchTermTranslated;

            header('Location: ' . $newUrl);
        }
    }

    /**
    * Redirige depuis les paramètres fournis dans l'url vers la bonne page.
    * Utilisé pour l'auto-complete.
    * Si le terme recherché n'est pas trouvé, ou si il y a plus d'un résultat, redirige vers la page de recherche
    **/
    public function redirectFromAutocomplete() {
        $CONSTANTS = Service::get('Constants');
        $ParseDatas = Service::get('ParseDatas');
        $term = $this->requete->existeParametre('term') ? $this->requete->getParametre('term') : null;
        $category = $this->requete->existeParametre('category') ? $this->requete->getParametre('category') : null;
        if ($category === null || $term === null) {
            http_response_code(422);
            return;
        }
        $term = trim($term);
        $datas = array();
        $type = null;
        switch ($category) {
            case $CONSTANTS::AUTOCOMPLETE_CATEGORY_OUVRAGE:
                $datas = $this->contentdb->getDatasForRedirectAutocomplete($category, $term);
                $type = $CONSTANTS::IS_NUMERO;
                break;
            case $CONSTANTS::AUTOCOMPLETE_CATEGORY_REVUE:
                $datas = $this->contentdb->getDatasForRedirectAutocomplete($category, $term);
                // Dirty-fix pour privilégier une revue par rapport à un autre contenu
                // Voir le ticket #70861
                // Je n'aime pas trop cette règle... J'aurais préféré un écran où l'on choissirait
                // le contenu que l'on souhaite vraiment voir.
                if (count($datas) === 2) {
                    if ($datas[0]['typepub'] !== $datas[1]['typepub']) {
                        if ($datas[0]['typepub'] == '1') {
                            $datas = [$datas[0]];
                        } elseif ($datas[1]['typepub'] == '1') {
                            $datas = [$datas[1]];
                        }
                    }
                }
                $type = $CONSTANTS::IS_REVUE;
                break;
            case $CONSTANTS::AUTOCOMPLETE_CATEGORY_AUTEUR:
                $term = trim($term, '.');  // Dirty-fix #68995  À voir avec pythagoria
                $datas = $this->contentdb->getDatasForRedirectAutocomplete($category, $term);
                $type = $CONSTANTS::IS_AUTEUR;
                break;
            default:
                break;
        }
        if ((count($datas) !== 1) || !$type) {
            $url = 'resultats_recherche.php?searchTerm="'
                .urlencode($term)
                .'"';
        } else {
            $url = $ParseDatas->reconstructUrl($type, intval($datas[0]['typepub']), $datas[0]);
        }
        http_response_code(303);
        header('Location: ' . $url);
    }
}
