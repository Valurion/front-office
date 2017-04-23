<?php $this->titre = "Activation accès abonnés particuliers"; ?>

<?php include (__DIR__ . '/../CommonBlocs/tabs.php'); ?>

<div id="breadcrump">
    <div id="breadcrump_main">
        <a href="/">Accueil</a> <span class="icon-breadcrump-arrow icon"></span>
        <a href="code-abonnement-papier.php">Activation d'accès</a>
    </div>
</div>

<div id="body-content">
    <div id="free_text" class="biblio">

        <h1 class="main-title">Activation d'accès pour abonnés particuliers</h1>

        <p>Cette fonction est réservée aux abonnés à titre individuel à la revue, disposant d'un code d'abonné que l'éditeur leur a transmis.</p>

        <br />

        <div class="articleBody">

            <form id="ajoutalertes" method="post" name="ajoutalertes" action='code-abonnement-papier.php'>

                <h2 class="section">Précisez la revue à laquelle vous êtes abonné(e)</h2>
                <div>
                    <select name="ID_REVUE" id="ID_REVUE">
                        <option class="ital" selected value="">Choisir la revue...</option>
                        <?php foreach ($revues as $revue): ?>
                        <option value="<?php echo $revue['ID_REVUE'] ?>"><?php echo $revue['TITRE'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <br/>
                <div class="blue_milk left w45">
                    <label for="code_abonne">Indiquez votre code d'abonné</label>
                    <input type="text" name="code_abonne" id="code_abonne" value="">
                </div>
                <br/>&nbsp;<br/>
                <div class="right">
                    <input type='submit' class="button" value='Activer votre accès'>
                </div>
            </form>
        </div>
    </div>
</div>
