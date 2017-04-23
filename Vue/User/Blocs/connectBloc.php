<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="free_text">
    <h1 class="main-title">S'identifier</h1>
    <p>Si vous disposez d'un compte Cairn.info, identifiez-vous&nbsp;:</p>
    <form id="connectBlocForm" method="post"
    action="
        javascript:goToPaiementAfterIdentification(&quot;javascript:ajax.connexion<?= !$connectFrom ? ucfirst($connectFrom) : '' ?>(<?= $connectFrom == 'routeur' ? "&apos;".$fromString."&apos;" : '' ?>)&quot;)
    ">
        <div class="blue_milk w40">
            <label for="email">Votre adresse email <span>*</span></label> <input type="email" value="<?= isset($email)?$email:'' ?>" id="email_connexion" name="email_connexion" class="prenom" required="required">
        </div>&nbsp;&nbsp;
        <div class="blue_milk w40">
            <label for="mdp">Votre mot de passe <span>*</span></label> <input type="password" id="password_connexion" value="" class="prenom" name="password_connexion" required="required" <?= isset($email)?'autofocus':'' ?>>
        </div>&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="submit" value=" Valider " id="valider" name="valider" class="button">
        <br><br>
        <a href="mdp_oublie.php" class="link_custom">Mot de passe oublié</a>
        <?php if(isset($totalPrice)){
           echo '<input type="hidden" id="totalPrice" value="'.$totalPrice.'"/>';
        }?>
    </form>
    <br>
    <p>Si vous ne disposez pas de compte Cairn.info, créez-en un&nbsp;: <a class="acceder link_custom_generic" href="./creer_compte.php<?= $connectFrom==''?'':('?from='.$connectFrom)?>">Créer mon compte</a></p>
</div>
