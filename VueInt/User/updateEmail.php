<?php
$this->titre = "Modification d'email ou de mot de passe";
include (__DIR__ . '/../CommonBlocs/tabs.php');
?>
<div id="breadcrump">
    <a href="./">Home</a> <span class="icon-breadcrump-arrow icon"></span>
    <a href="./my_account.php">My account</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="#">Update my account</a>
</div>
<div id="mainContent">
    <div id="body-content">
        <div id="free_text">
            <h1 class="main-title">Update email or password</h1>
            <form method="POST" id="modifiercompte" name="modifiercompte" action="javascript:ajax.modifEmail()">
                <fieldset>
                    <legend>My current informations</legend>
                    <div class="wrapper">
                        <div class="blue_milk left w45">
                            <label class="email" for="email">Email <span class="red">*</span></label>
                            <input type="email" required="required" value="<?= isset($authInfos["U"])?$authInfos["U"]["EMAIL"]:""?>" id="email" name="email" class="email">
                        </div>
                        <div class="blue_milk right w45">
                            <label class="mdp" for="mdp">Password <span class="red">*</span></label>
                            <input type="password" required="required" value="" id="mdp" name="mdp" class="mdp">
                        </div>
                    </div>
                </fieldset>
                <fieldset class="mt2">
                    <legend>My new informations</legend>
                    <div class="wrapper">
                        <div class="blue_milk left w45">
                            <label class="email2" for="email2">New email <span class="red">*</span></label>
                            <input type="email" required="required" value="" id="email2" name="email2" class="email2">
                        </div>
                    </div>
                    <div class="wrapper mt1">
                        <div class="blue_milk left w45">
                            <label class="mdp2" for="mdp2">New password<span class="red">*</span></label>
                            <input type="password" required="required" value="" id="mdp2" name="mdp2" class="mdp2">
                        </div>
                        <div class="blue_milk right w45">
                            <label class="mdp3" for="mdp3">Confirm your new password <span class="red">*</span></label>
                            <input type="password" required="required" value="" id="mdp3" name="mdp3" class="mdp3">
                        </div>
                    </div>
                </fieldset>
                <div class="wrapper mt1">
                    <button class="blue_button right bold">Update my account</button>
                </div>
            </form>
        </div>
    </div>
</div>

