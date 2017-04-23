<?php 
$this->titre = "Sign up";
?>
<div id="breadcrump">
    <a href="/">Home</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="#">Sign up</a>
</div>
<div id="body-content">
    <div id="free_text">
        <h1 class="main-title"></h1>
        
        <p class="h_center">
            Already registered? <a class="yellow italic" href="connexion.php">Login here</a>
        </p>

        <hr class="w50">
        
        <h1 class="main-title"><p class="yellow center" style="margin: 0;">Not registered yet?</p>Create your Account now</h1>
        <p>
            If you do not already have a Cairn.info account, please enter the following information
            <br>
            <em>(the fields marked with an asterisk <span class="red">*</span> are required).</em>
        </p>

        <form id="creer_compte" action="#" method="POST" name="creecompte">
            <h2><strong>Contact details</strong></h2><br>
            <div class="wrapper">
                <div class="blue_milk left w45">
                    <label for="email">E-mail address (will be your login)
                        <span class="red">*</span>
                    </label> <span class="flash "></span>
                    <input type="email" required="required" value="<?= $email ?>" id="email" name="email">
                </div>
            </div><br>

            <div class="wrapper">
                <div class="blue_milk left w45">
                    <label for="nom">Your last name <span class="red">*</span></label> <span class="flash"></span> <input type="text" required="required" value="<?= $nom ?>" id="nom" name="nom">
                </div>
                <div class="blue_milk right w45">
                    <label for="prenom">Your first name <span class="red">*</span></label>
                    <span class="flash"></span> <input type="text" required="required" value="<?= $prenom ?>" id="prenom" name="prenom">
                </div>
            </div><br>

            <div class="wrapper">
                <div class="blue_milk left w45">
                    <label for="mdp">Enter your password <span class="red">*</span></label>
                    <span class="flash"></span> <input type="password" required="required" value="" id="mdp" name="mdp">
                </div>
                <div class="blue_milk right w45">
                    <label for="cmdp">Confirm your password <span class="red">*</span></label>
                    <span class="flash "></span>
                    <input type="password" required="required" value="" id="cmdp" name="cmdp">
                </div>
            </div><br>

            <h2><strong>Profile</strong></h2><br>
            <label class="left " for="select">Activity</label> 
            <select class="right w50" id="activity" name="activity">
                    <option value="0">Choose…</option>
                    <option value="1" >undergraduate</option>
                    <option value="2" >graduate</option>
                    <option value="3" >postgraduate</option>
                    <option value="4" >teacher and/or researcher</option>
                    <option value="5" >archivist/librarian</option>
                    <option value="6" >employee of the public service</option>
                    <option value="7" >employee in the voluntary sector</option>
                    <option value="8" >employee in the private sector</option>
                    <option value="9" >profession</option>
                    <option value="10">unemployed</option>
                    <option value="11">retired</option>
                    <option value="12">other</option>                
            </select> <br>
            <br>

            <div class="wrapper">
                <label class="left w50" for="pos_disc">Subject of interest</label>
                <select class="right w50" id="pos_disc" name="pos_disc">
                    <option value="0">Choose…</option> 
                    <?php                    
                    foreach($disciplines as $discipline){
                        echo '<option value="'.$discipline["POS_DISC"].'">'.$discipline["DISCIPLINE_EN"].'</option>';
                    }
                    ?>
                </select>
            </div>

            <h2><strong>Promotional code</strong></h2><br>
            <div class="blue_milk left w45">
                <label for="codepromo">Enter the code that was sent to you</label>
                <input type="text" onchange="ajax.promotion('code=' + this.value + '&amp;user=' + document.getElementById('email').value)" value="" id="codepromo" name="codepromo">
            </div><br><br><br>

            <h2><strong>Terms of use</strong></h2><br>
            <input type="checkbox" required="required" id="accept_conditions" name="checkconditions">
            <label for="accept_conditions">I accept the <a class="" target="_blank" href="./conditions.php"><span style="text-decoration: underline;">terms of use</span></a> of
                Cairn.info. <span class="red">*</span>
            </label> <br>
            <br> 
            <input type="checkbox" id="accept_partenaires" name="checkpartenaires"> 
            <label for="accept_partenaires">I agree to receive email information on the evolution of services Cairn.info and on the editorial activity of its partners.</label>
            <br>
            <?php
            if($from != ''){
                echo '<input type="hidden" value="'.$from.'" />';
            }
            ?>
            <input type="submit" value="Create my account" class="button right">

        </form>
        <br>
    </div>
</div>