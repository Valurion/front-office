<?php 
$this->titre = 'Mon compte';
include (__DIR__ . '/../CommonBlocs/tabs.php');
?>
<div id="cnt_1">
    <div id="body-content">
        <div id="free_text">
            <h1 class="main-title">Mon compte Cairn.info</h1>

            <form id="accountNameForm" method="post" name="modifiercompte" action="mon_compte.php">

                <div class="wrapper mt1">
                    <a class="blue_button right bold" href="modif_email.php">
                        Modifier mon adresse email ou mon mot de passe
                    </a>
                </div>
                <fieldset>
                    <legend>Coordonnées</legend>
                    <div class="wrapper">
                        <div class="blue_milk left w45">
                            <label class="prenom" for="prenom">
                                Prénom <span class="red">*</span>
                            </label>
                            <input type="text" required="required" value="<?= $authInfos["U"]["PRENOM"]?>" id="prenom" name="prenom" class="prenom">
                        </div>
                        <div class="blue_milk right w45">
                            <label class="prenom" for="nom">Nom <span class="red">*</span></label>
                            <input type="text" required="required" value="<?= $authInfos["U"]["NOM"]?>" id="nom" name="nom" class="prenom">
                        </div>
                    </div>                    
                </fieldset>

                <fieldset class="mt2">
                    <legend>Profil</legend>
                    <div class="wrapper">
                        <label class="left" for="activity">Activité</label>
                        <select class="right w50" id="activity" name="activity">
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
                        </select>
                    </div>
                    <br/>
                    <div class="wrapper">
                        <label class="left" for="pos_disc">Discipline de prédilection</label>
                        <select class="right w50" id="pos_disc" name="pos_disc">
                            <option value="0">Choisissez…</option> 
                            <?php                    
                            foreach($disciplines as $discipline){
                                echo '<option value="'.$discipline["POS_DISC"].'" '.($discipline["POS_DISC"]==$authInfos["U"]["POS_DISCU"]?'selected':'').'>'.$discipline["DISCIPLINE"].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </fieldset>

                <fieldset class="mt2">
                    <legend>Code promotionnel</legend>
                    <div class="blue_milk block">
                        <label for="codepromo">Si vous avez reçu un code promotionnel, indiquez-le</label>
                        <input type="text" onchange="ajax.promotion('code=' + this.value + '&amp;user=<?=$authInfos['U']['EMAIL']?>')" value="" id="codepromo" name="codepromo" class="nom">
                    </div>
                </fieldset>

                <fieldset class="mt2">
                    <legend>Conditions d'utilisation</legend>
                    <input type="checkbox" id="checkshowall" name="checkshowall" <?= $authInfos["U"]["SHOWALL"]==1?'checked':''?>>
                    <label for="checkshowall">
                        Je veux un accès complet à la base Cairn.info, quelles que soient les restrictions de l'institution à partir de laquelle je me connecte.
                    </label>
                    <br/>
                    <input type="checkbox" id="checkpartenaires" name="checkpartenaires" <?= !empty($alerte)?"checked":"" ?>> 
                    <label for="checkpartenaires">J'accepte de recevoir par email des informations sur l'évolution des services de Cairn.info ainsi que sur l'activité éditoriale de ses partenaires.
                    </label>
                </fieldset>

                <div class="wrapper mt1">
                    <button class="blue_button right bold">Modifier mon compte</button>                    
                </div>
            </form>
        </div>
    </div>
</div>
