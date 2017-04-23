<?php 
$this->titre = "Création de compte";
include (__DIR__ . '/../CommonBlocs/tabs.php');
?>
<div id="breadcrump">
    <a href="/">Accueil</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="#">Mon compte</a>
</div>
<div id="body-content">
    <div id="free_text">
        <h1 class="main-title"></h1>
        <a target="_blank" href="http://aide.cairn.info/le-compte-personnel/">
            <span style="bottom: 1em;" class="question-mark">
            <span class="tooltip">En savoir plus sur Mon Cairn.info</span>
            </span>
        </a>
        <p class="h_center">
            Déjà enregistré ? <a class="yellow italic" href="connexion.php">Connectez-vous</a>
        </p>

        <hr class="w50">

        <h1 class="main-title">Créer un compte Cairn.info</h1>
        <p>
            Si vous ne disposez pas encore d'un compte Cairn.info, veuillez
            entrer les informations suivantes<br>
            <em>(les champs suivis d'un astérisque <span class="red">*</span>
                sont obligatoires)
            </em>.
        </p>

        <form id="creer_compte" action="#" method="POST" name="creecompte">
            <h2><strong>Coordonnées</strong></h2><br>
            <div class="wrapper">
                <div class="blue_milk left w45">
                    <label for="email">Adresse e-mail (qui sera votre identifiant)
                        <span class="red">*</span>
                    </label> <span class="flash "></span>
                    <input type="email" required="required" value="<?= $email ?>" id="email" name="email">
                </div>
            </div><br>

            <div class="wrapper">
                <div class="blue_milk left w45">
                    <label for="nom">Votre nom <span class="red">*</span></label> <span class="flash"></span> <input type="text" required="required" value="<?= $nom ?>" id="nom" name="nom">
                </div>
                <div class="blue_milk right w45">
                    <label for="prenom">Votre prénom <span class="red">*</span></label>
                    <span class="flash"></span> <input type="text" required="required" value="<?= $prenom ?>" id="prenom" name="prenom">
                </div>
            </div><br>

            <div class="wrapper">
                <div class="blue_milk left w45">
                    <label for="mdp">Choisir votre mot de passe <span class="red">*</span></label>
                    <span class="flash"></span> <input type="password" required="required" value="" id="mdp" name="mdp">
                </div>
                <div class="blue_milk right w45">
                    <label for="cmdp">Confirmer votre mot de passe <span class="red">*</span></label>
                    <span class="flash "></span>
                    <input type="password" required="required" value="" id="cmdp" name="cmdp">
                </div>
            </div><br>

            <h2><strong>Profil</strong></h2><br>
            <label class="left " for="select">Activité</label> 
            <select class="right w50" id="activity" name="activity">
                <option value="0">Choisissez…</option>
                <option value="1">étudiant en premier cycle (licence - y. c. classes préparatoires)</option>
                <option value="2">étudiant en second cycle (maîtrise)</option>
                <option value="3">étudiant en troisième cycle (doctorat)</option>
                <option value="4">enseignant et/ou chercheur</option>
                <option value="5">documentaliste/bibliothécaire</option>
                <option value="6">autre salarié de la fonction publique</option>
                <option value="7">autre salarié dans le secteur associatif</option>
                <option value="8">autre salarié dans le secteur privé</option>
                <option value="9">profession libérale</option>
                <option value="10">sans emploi</option>
                <option value="11">retraité</option>
                <option value="12">autre</option>
            </select> <br>
            <br>

            <div class="wrapper">
                <label class="left w50" for="pos_disc">Discipline de prédilection</label>
                <select class="right w50" id="pos_disc" name="pos_disc">
                    <option value="0">Choisissez…</option> 
                    <?php                    
                    foreach($disciplines as $discipline){
                        echo '<option value="'.$discipline["POS_DISC"].'">'.$discipline["DISCIPLINE"].'</option>';
                    }
                    ?>
                </select>
            </div>

            <h2><strong>Code promotionnel</strong></h2><br>
            <div class="blue_milk left w45">
                <label for="codepromo">Indiquez le code qui vous a été transmis</label>
                <input type="text" onchange="ajax.promotion('code=' + this.value + '&amp;user=' + document.getElementById('email').value)" value="" id="codepromo" name="codepromo">
            </div><br><br><br>

            <h2><strong>Conditions d'utilisation</strong></h2><br>
            <input type="checkbox" required="required" id="accept_conditions" name="checkconditions">
            <label for="accept_conditions">J'accepte les <a class="" target="_blank" href="./conditions.php"><span style="text-decoration: underline;">conditions d'utilisation</span></a> du
                site Cairn.info. <span class="red">*</span>
            </label> <br>
            <br> <input type="checkbox" id="accept_partenaires" name="checkpartenaires"> <label for="accept_partenaires">J'accepte
                de recevoir par email des informations sur l'évolution des
                services de Cairn.info ainsi que sur l'activité éditoriale de ses
                partenaires.</label>

            <br>
            <?php
            if($from != ''){
                echo '<input type="hidden" value="'.$from.'" />';
            }
            ?>
            <input type="submit" value="Créer mon compte" class="button right">

        </form>
        <br>
    </div>
</div>