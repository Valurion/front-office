<?php


class ParseDatas {
    const concat_authors = "||";
    const concat_name = "&&";


    public static function cleanString($string) {
        return strip_tags(trim($string));
    }


    public static function cleanArrayString($array) {
        return array_filter(array_map('self::cleanString', $array));
    }


    public static function cleanAttributeString($string) {
        return json_encode(self::cleanString($string));
    }


    /***********************************************************************************************
     * Fonctions permettant de transformer et nettoyer des données
     * en vue d'une insertion dans un fichier csv
     **********************************************************************************************/
    public static function cleanCSVString($string) {
        if ($string === null) return $string;
        if (is_numeric($string)) {
            $string = str_replace('.', ',', $string);
        };
        $string = str_replace("\n", ' ', $string);
        $string = str_replace('"', "''", $string);
        $string = "\"$string\"";
        return $string;
    }


    public static function arrayToCSVRow($strings) {
        $row = array_values($strings);
        $row = array_map("self::cleanCSVString", $row);
        $row = implode(';', $row);
        return $row;
    }


    public static function arrayToCSVHeader($array, $beforeColumns=array(), $afterColumns=array()) {
        if (count($array) === 0) return $array;
        $header = array_keys($array[0]);
        $header = array_merge($beforeColumns, $header, $afterColumns);
        $header = array_map("self::cleanCSVString", $header);
        $header = implode(';', $header);
        return $header;
    }


    public static function arrayToCSV($array) {
        if (count($array) === 0) return $array;
        $csv = [self::arrayToCSVHeader($array)];
        foreach ($array as $row) {
            array_push($csv, self::arrayToCSVRow($row));
        }
        return $csv;
    }


    public static function formatCSVToHTML($csv) {
        // À l'arrache, mais suffisant pour debugger un CSV
        return "<table border='1'><tr><td>" . str_replace(
            ["\n", ";"],
            ["</td></tr>\n<tr><td>", "</td><td>"],
            $csv
        ) . "</td></tr></table>";
    }

    /**
    *   Concatène et transforme pour l'affichage une liste d'auteurs récupérés depuis sql et concatenée avec des caractères.
    *
    * @param array $rawAuthors  La liste des auteurs récupérés depuis sql
    * @param int $cut  À partir de quel auteur la concaténation doit être stoppé et doit être inséré $cutAlt.
    *                   Si inférieur à 1, ce paramètre est ignoré.
    * @param str $joinAuthors  La string qui servira de concaténation entre les différents auteurs. Par défaut à ', '
    * @param str $joinName  La string qui servira de concaténation entre le nom, prénom, etc. d'un auteur. Par défaut à ' '
    * @param str $cutAlt  La string insérée en fin de concaténation si $cut est défini. Par défaut à '<i>et al.</i>'
    * @param str $withIdAuthor  Si un id est inséré dans rawAuthors. Si à vrai, cet id sera supprimé lors de la normalisation
    * @param str $splitAuthorsOn  Le caractère qui sert de pivot entre les auteurs dans la string. Par défaut à self::concat_authors
    * @param str $splitNameOn  Le caractère qui sert de pivot entre les attributs d'un auteur dans la string. Par défaut à self::concat_name
    *
    * @return str
    **/
    public function stringifyRawAuthors($rawAuthors, $cut=0, $joinAuthors=null, $joinName=null, $cutAlt=null, $withIdAuthor=true, $splitAuthorsOn=null, $splitNameOn=null) {
        $joinName = $joinName ? $joinName : ' ';
        $joinAuthors = $joinAuthors ? $joinAuthors : ', ';
        $cutAlt = $cutAlt ? $cutAlt : '<i>et al.</i>';
        $splitAuthorsOn = $splitAuthorsOn ? $splitAuthorsOn : self::concat_authors;
        $splitNameOn = $splitNameOn ? $splitNameOn : self::concat_name;

        $authors = array();
        foreach (explode($splitAuthorsOn, $rawAuthors) as $index => $author) {
            if (!!$cutAlt && ($cut > 0) && ($index >= $cut)) {
                array_push($authors, $cutAlt);
                break;
            }
            $author = explode($splitNameOn, $author);
            $author = self::cleanArrayString($author);
            if ($withIdAuthor) {
                array_pop($author);
            }
            array_push($authors, implode($joinName, $author));
        }
        $authors = implode($joinAuthors, $authors);
        return $authors;
    }


    /**
    * À partir des données récupérés depuis la base de données, reconstruit l'url
    *
    * @param const $type        Le type de données (numéro, ouvrage, auteur, etc.)
    * @param const $typepub:    Le type de publication (revue, encyclopédie, collectifs, etc.)
    *                           Pour un auteur, n'est pas utilisé
    * @param array $data:       Les données formattés pour la transformation en url
    * @return string:           L'url
    *
    * TODO: utilisé les ID_* en dernier recours
    **/
    public function reconstructUrl($type, $typepub, $datas) {
        $CONSTANTS = Service::get('Constants');
        $url = array();
        if ($type === $CONSTANTS::IS_AUTEUR) {
            $url[] = 'publications-de';
            $url[] = $datas['nom'];
            $url[] = $datas['prenom'];
            $url[] = '-'.$datas['id_auteur'];
        }
        switch ($typepub) {
            case $CONSTANTS::TYPEPUB_REVUE:
            case $CONSTANTS::TYPEPUB_MAGAZINE:
                $url[] = $typepub === $CONSTANTS::TYPEPUB_MAGAZINE ? 'magazine' : 'revue';
                $url[] = $datas['url_rewriting'];
                if ($type === $CONSTANTS::IS_NUMERO || $type === $CONSTANTS::IS_ARTICLE) {
                    $url[] = $datas['annee'];
                    $url[] = $datas['numero'];
                }
                if ($type === $CONSTANTS::IS_ARTICLE) {
                    $url[] = 'page-'.$datas['page_debut'];
                }
                break;
            case $CONSTANTS::TYPEPUB_OUVRAGE:
            case $CONSTANTS::TYPEPUB_ENCYCLOPEDIE:
                if ($type === $CONSTANTS::IS_REVUE) {
                    $url[] = 'collection';
                    $url[] = $datas['url_rewriting'];
                }
                if ($type === $CONSTANTS::IS_NUMERO || $type === $CONSTANTS::IS_ARTICLE) {
                    $url[] = $datas['url_rewriting'];
                    $url[] = '-'.$datas['isbn'];
                }
                if ($type === $CONSTANTS::IS_ARTICLE) {
                    $url[] = 'page-'.$datas['page_debut'];
                }
                break;
            default:
                break;
        }
        return implode('-', $url) . '.htm';
    }
}
