<?php
    $this->titre = "Download of PDF file";
    include (__DIR__ . '/../CommonBlocs/tabs.php');
?>

<div id="breadcrump">
    <a href="/">Home</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="./disc-<?= $curDiscipline ?>.htm"><?= $filterDiscipline?></a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="./journal-<?php echo $revue["URL_REWRITING_EN"] ?>.htm">Journal</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="./journal-<?php echo $revue["URL_REWRITING_EN"] ?>-<?php echo $numero["NUMERO_ANNEE"]; ?>-<?php echo $numero["NUMERO_NUMERO"]; ?>.htm">Issue</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="#">PDF</a>
</div>

<div id="body-content" class="articleBody">
    <h1 class="main-title">Article now downloading...</h1>
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
        In
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
            See the fulltext in HTML
        </a>
    </p>

<!--     <p>
        <a href="./sur-un-sujet-proche.php?ID_ARTICLE=<?= $currentArticle['ARTICLE_ID_ARTICLE'] ?>">
            You may be interested in
        </a>
    </p> -->
</div>

<?php

$this->javascripts[] = 'window.location = "./load_pdf.php?download=1&ID_ARTICLE=' . $currentArticle['ARTICLE_ID_ARTICLE'] . '";';
?>
