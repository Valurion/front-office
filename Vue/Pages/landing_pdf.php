<?php
    $this->titre = "Téléchargement de fichier PDF";
    include (__DIR__ . '/../CommonBlocs/tabs.php');


    // On prépare les libellés, urls, ... etc
    $typeRev_suffixe = "";
    $typeNum_suffixe = "";
    $revue_url = "";
    $numero_url = "";
    if ($typePub == "revue" || $typePub == "magazine") {
        $article_libelle = "article";
        $article_det = "cet";
        $revue_url = $typePub . '-' . $revue["REVUE_URL_REWRITING"] . '.htm';
        $numero_url = $typePub . '-' . $revue["REVUE_URL_REWRITING"] . '-' . $revue["NUMERO_ANNEE"] . '-' . $revue["NUMERO_NUMERO"] . '.htm';
    } else {
        $article_libelle = "chapitre";
        $article_det = "ce";
        if ($typePub == "encyclopédie") {
            $typeRev_suffixe = " de poche";
        }
        $revue_url = $numero["NUMERO_URL_REWRITING"] . '--' . $numero["NUMERO_ISBN"];
        $numero_url = $numero["NUMERO_URL_REWRITING"] . '--' . $numero["NUMERO_ISBN"];

        if ($numero["NUMERO_TYPE_NUMPUBLIE"] == 1) {
            $typeNum_suffixe = " collectif";
        }
    }

        $typePub_url = null;
        switch ($typePub) {
            case 'revue':
                $typePub_url = 'Accueil_Revues.php';
                break;
            case 'encyclopédie':
                $typePub_url = 'encyclopedies-de-poche.php';
                break;
            default:
                $typePub_url = $typePub.'s.php';
                break;
        }
?>

<div id="breadcrump">
    <a href="/">Accueil</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="./<?php echo $typePub_url; ?>">
        <?php echo ucfirst($typePub); ?>s<?php echo ($typeRev_suffixe != '' ? $typeRev_suffixe : ''); ?></a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="./<?php echo $revue_url; ?>"><?php echo ucfirst($typePub=="encyclopédie"?"ouvrage":$typePub); ?><?php echo ($typeNum_suffixe != '' ? $typeNum_suffixe : ''); ?></a>
    <span class="icon-breadcrump-arrow icon"></span>
    <?php if ($typePub == "revue" || $typePub == "magazine") { ?>
        <a href="./<?php echo $numero_url; ?>">Num&#233;ro</a>
        <span class="icon-breadcrump-arrow icon"></span>
    <?php } ?>
    <a href="#">Pdf</a>
</div>

<div id="body-content" class="articleBody">
    <h1 class="main-title">Article en cours de téléchargement...</h1>
    <hr />
    <div>
        <a href="./article.php?ID_ARTICLE=<?= $currentArticle['ARTICLE_ID_ARTICLE'] ?>">
            <strong><?= $currentArticle['ARTICLE_TITRE'] ?></strong>
        </a>
        <?php if ($currentArticle['ARTICLE_SOUSTITRE']): ?>
            <i style="margin-left: 1em;"><?= $currentArticle['ARTICLE_SOUSTITRE'] ?></i>
        <?php endif; ?>
    </div>
    <div class="authors yellow">
        <?= Service::get('ParseDatas')->stringifyRawAuthors($currentArticle['ARTICLE_AUTEUR'], 0, null, null, null, true, ',', ':') ?>
    </div>
    <div class="revue_title">
        Dans
        <a href="<?= $revue_url ?>" class="title_little_blue">
            <span class="title_little_blue"><?= $revue['REVUE_TITRE'] ?></span>
        </a>
        <strong>
            <?= $numero['NUMERO_ANNEE'] ?><!--
            --><?= $numero['NUMERO_NUMERO'] ? '/'.$numero['NUMERO_NUMERO'] : '' ?><!--
            --><?= $numero['NUMERO_NUMEROA'] ? '-'.$numero['NUMERO_NUMEROA'] : '' ?>
            (<?= $numero['NUMERO_VOLUME'] ?>)
        </strong>
    </div>
    <hr />
    <p>
        <a href="./article.php?ID_ARTICLE=<?= $currentArticle['ARTICLE_ID_ARTICLE'] ?>">
            Voir le texte intégral de cet article en version HTML
        </a>
    </p>

    <p>
        <a href="./sur-un-sujet-proche.php?ID_ARTICLE=<?= $currentArticle['ARTICLE_ID_ARTICLE'] ?>">
            Voir d'autres contenus sur un sujet proche
        </a>
    </p>
</div>

<?php
$this->javascripts[] = 'setTimeout(function() {window.location = "./load_pdf_do_not_index.php?ID_ARTICLE='
    .$currentArticle['ARTICLE_ID_ARTICLE']
    .'"}, 1000);';
?>
