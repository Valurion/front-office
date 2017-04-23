<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="free_text">
    <h1 class="main-title">Login</h1>
    <p>If you have a Cairn.info account, please login:</p>
    <form id="connectBlocForm" method="post"
    action="
        javascript:goToPaiementAfterIdentification(&quot;javascript:ajax.connexion<?= !$connectFrom ? ucfirst($connectFrom) : '' ?>(<?= $connectFrom == 'routeur' ? "&apos;".$fromString."&apos;" : '' ?>)&quot;)
    ">
        <div class="blue_milk w40">
            <label for="email">Your email address <span>*</span></label> <input type="email" value="<?= isset($email)?$email:'' ?>" id="email_connexion" name="email_connexion" class="prenom" required="required">
        </div>&nbsp;&nbsp;
        <div class="blue_milk w40">
            <label for="mdp">Your password <span>*</span></label> <input type="password" id="password_connexion" value="" class="prenom" name="password_connexion" required="required">
        </div>&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="submit" value=" Continue " id="valider" name="valider" class="button">
        <br><br>
        <a href="password_forgotten.php" class="link_custom">Forgot password</a>
        <?php if(isset($totalPrice)){
           echo '<input type="hidden" id="totalPrice" value="'.$totalPrice.'"/>';
        }?>
    </form>
    <br>
    <p>If you do not have Cairn.info account, create one: <a class="acceder link_custom_generic" href="./create_account.php<?= $connectFrom==''?'':('?from='.$connectFrom)?>">Sign up</a></p>
</div>
