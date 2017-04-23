<?php ?>
<div id="free_text"> 

    <a name="top"></a>
    <h1 class="main-title">Formulaire de demande à mon bibliothécaire</h1>

    <div class="clear">&nbsp;</div>
    <form action="javascript:ajax.envoiDemandeBiblio()" id="form_envoi"> 
    <div class="blue_milk left w40">
        <label for="PRENOM" class="prenom">Votre prénom <span class="red">*</span></label><br> 
        <input type="text" value="<?= $authInfos['U']['PRENOM']?>" name="PRENOM" id="PRENOM" class="prenom" required="required"> 
    </div>

    <div class="blue_milk right w40">
        <label for="NOM" class="prenom">Votre nom <span class="red">*</span></label><br> 
        <input type="text" value="<?= $authInfos['U']['NOM']?>" name="NOM" id="NOM" class="prenom" required="required"> 
    </div>
    <br>
    <br>
    <br>
    <div class="blue_milk left w40">
        <label for="Fonction">Votre fonction <span class="red">*</span></label><br> 
        <input type="text" value="" name="FONCTION" id="FONCTION" required="required"> 
        <!--select class="right w50" id="activity" name="activity">
            <option value="0">Choisissez…</option>
            <option value="1" <?= $authInfos["U"]["PROFESSION"]==1?"selected":""?>>étudiant en premier cycle (licence - y. c. classes préparatoires)</option>
            <option value="2" <?= $authInfos["U"]["PROFESSION"]==2?"selected":""?>>étudiant en second cycle (maîtrise)</option>
            <option value="3" <?= $authInfos["U"]["PROFESSION"]==3?"selected":""?>>étudiant en troisième cycle (doctorat)</option>
            <option value="4" <?= $authInfos["U"]["PROFESSION"]==4?"selected":""?>>enseignant et/ou chercheur</option>
            <option value="5" <?= $authInfos["U"]["PROFESSION"]==5?"selected":""?>>documentaliste/bibliothécaire</option>
            <option value="6" <?= $authInfos["U"]["PROFESSION"]==6?"selected":""?>>autre salarié de la fonction publique</option>
            <option value="7" <?= $authInfos["U"]["PROFESSION"]==7?"selected":""?>>autre salarié dans le secteur associatif</option>
            <option value="8" <?= $authInfos["U"]["PROFESSION"]==8?"selected":""?>>autre salarié dans le secteur privé</option>
            <option value="9" <?= $authInfos["U"]["PROFESSION"]==9?"selected":""?>>profession libérale</option>
            <option value="10" <?= $authInfos["U"]["PROFESSION"]==10?"selected":""?>>sans emploi</option>
            <option value="11" <?= $authInfos["U"]["PROFESSION"]==11?"selected":""?>>retraité</option>
            <option value="12" <?= $authInfos["U"]["PROFESSION"]==12?"selected":""?>>autre</option>
        </select-->
    </div>
    <br>
    <br>
    <br>
    <div class="blue_milk center w80">
        <label for="MOTIVATION">Motivation de votre demande </label> 
        <textarea name="MOTIVATION" id="MOTIVATION" cols="85" rows="10" class="custom_textarea_bm"></textarea>
    </div>
    <br>
    <br>
    <br>
    <button class="continuer checkout-button">Confirmation</button>
    </form>
    <br/>
</div>

