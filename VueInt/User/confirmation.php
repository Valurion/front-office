<?php 
$this->titre = "Confirmation of account creation";
include (__DIR__ . '/../CommonBlocs/tabs.php');
?>
<div id="breadcrump">
    <a href="/">Home</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="#">Confirmation</a>
</div>
<div id="body-content">
    <div id="free_text">
        <h1 class="main-title">Confirmation of account creation</h1>
        <p>Your account was created successfully.
            <br>You will receive in the next few minutes a confirmation email.  
            <br/>
            <?php switch($from){
                case 'demandeBiblio':
                    echo '<a class="acceder" href="javascript:ajax.demandeBiblio()">Continue</a>';
                    break;
                case 'panierAchat':
                    echo '<a class="acceder" href="./my_cart.php">Continue</a>';
                    break;
                default:
                    echo '<a class="acceder" href="/">Continue</a>';
            }?>
            
        </p>
    </div>
</div>
