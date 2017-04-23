<?php 
$this->titre = "Confirmation de création de compte";
include (__DIR__ . '/../CommonBlocs/tabs.php');
?>
<div id="breadcrump">
    <a href="/">Accueil</a>
    <span class="icon-breadcrump-arrow icon"></span>
    <a href="#">Confirmation</a>
</div>
<div id="body-content">
    <div id="free_text">
        <h1 class="main-title">Confirmation de création de compte</h1>
        <p>Votre compte a bien été créé.
            <br>Vous allez recevoir dans les prochaines minutes un e-mail de confirmation.  
            <br/>
            <?php switch($from){
                case 'demandeBiblio':
                    echo '<a class="acceder" href="javascript:ajax.demandeBiblio()">Continuer</a>';
                    break;
                case 'panierAchat':
                    echo '<a class="acceder" href="./mon_panier.php">Continuer</a>';
                    break;
                default:
                    echo '<a class="acceder" href="/">Continuer</a>';
            }?>
            
        </p>
    </div>
</div>
