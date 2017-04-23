<?php 
$this->titre = 'My account';
include (__DIR__ . '/../CommonBlocs/tabs.php');
?>
<div id="cnt_1">
    <div id="body-content">
        <div id="free_text">
            <h1 class="main-title">My Cairn.info account</h1>

            <form id="accountNameForm" method="post" name="modifiercompte" action="my_account.php">

                <div class="wrapper mt1">
                    <a class="blue_button right bold" href="update_email.php">
                        Change email address or password
                    </a>
                </div>
                <fieldset>
                    <legend>Contact details</legend>
                    <div class="">
                        <div class="blue_milk left w45">
                            <label class="prenom" for="prenom">
                                First name <span class="red">*</span>
                            </label>
                            <input type="text" required="required" value="<?= $authInfos["U"]["PRENOM"]?>" id="prenom" name="prenom" class="prenom">
                        </div>
                        <div class="blue_milk right w45">
                            <label class="prenom" for="nom">
                                Last name <span class="red">*</span></label>
                            <input type="text" required="required" value="<?= $authInfos["U"]["NOM"]?>" id="nom" name="nom" class="prenom">
                        </div>
                    </div>                    
                </fieldset>

                <fieldset class="mt2">
                    <legend>Profile</legend>
                    <div class="">
                        <label class="left" for="activity">Activity</label>
                        <select class="right w50" id="activity" name="activity">
                            <option value="0">Choose…</option>
                            <option value="1" <?= $authInfos["U"]["PROFESSION"]==1?"selected":""?>>undergraduate</option>
                            <option value="2" <?= $authInfos["U"]["PROFESSION"]==2?"selected":""?>>graduate</option>
                            <option value="3" <?= $authInfos["U"]["PROFESSION"]==3?"selected":""?>>postgraduate</option>
                            <option value="4" <?= $authInfos["U"]["PROFESSION"]==4?"selected":""?>>teacher and/or researcher</option>
                            <option value="5" <?= $authInfos["U"]["PROFESSION"]==5?"selected":""?>>archivist/librarian</option>
                            <option value="6" <?= $authInfos["U"]["PROFESSION"]==6?"selected":""?>>employee of the public service</option>
                            <option value="7" <?= $authInfos["U"]["PROFESSION"]==7?"selected":""?>>employee in the voluntary sector</option>
                            <option value="8" <?= $authInfos["U"]["PROFESSION"]==8?"selected":""?>>employee in the private sector</option>
                            <option value="9" <?= $authInfos["U"]["PROFESSION"]==9?"selected":""?>>profession</option>
                            <option value="10" <?= $authInfos["U"]["PROFESSION"]==10?"selected":""?>>unemployed</option>
                            <option value="11" <?= $authInfos["U"]["PROFESSION"]==11?"selected":""?>>retired</option>
                            <option value="12" <?= $authInfos["U"]["PROFESSION"]==12?"selected":""?>>other</option>
                        </select>
                    </div>
                    <br/><br/>
                    <div class="">
                        <label class="left" for="pos_disc">Subject of interest</label>
                        <select class="right w50" id="pos_disc" name="pos_disc">
                            <option value="0">Choose…</option> 
                            <?php                    
                            foreach($disciplines as $discipline){
                                echo '<option value="'.$discipline["POS_DISC"].'" '.($discipline["POS_DISC"]==$authInfos["U"]["POS_DISCU"]?'selected':'').'>'.$discipline["DISCIPLINE_EN"].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </fieldset>

                <fieldset class="mt2">
                    <legend>Promotional code</legend>
                    <div class="blue_milk block">
                        <label for="codepromo">If you received a promotional code, enter it</label>
                        <input type="text" onchange="ajax.promotion('code=' + this.value + '&amp;user=<?=$authInfos['U']['EMAIL']?>')" value="" id="codepromo" name="codepromo" class="nom">
                    </div>
                </fieldset>

                <fieldset class="mt2">
                    <legend>Terms of use</legend>
                    <input type="checkbox" id="checkshowall" name="checkshowall" <?= $authInfos["U"]["SHOWALL"]==1?'checked':''?>>
                    <label for="checkshowall">
                        I want full access to the base Cairn.info whatever restrictions the institution from which I connect.
                    </label>
                    <br/>
                    <input type="checkbox" id="checkpartenaires" name="checkpartenaires" <?= !empty($alerte)?"checked":"" ?>> 
                    <label for="checkpartenaires">I agree to receive email information on the evolution of service Cairn.info and on the editorial activity of its partners.
                    </label>
                </fieldset>
                <br/>

                <div class="wrapper mt1">
                    <button class="blue_button right bold">Change my account</button>                    
                </div>
            </form>
        </div>
    </div>
</div>
