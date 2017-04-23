<div class="window_modal" id="error_div_modal" style="display: none;">
    <div class="info_modal">
        <h2>Mot de passe incorrect</h2>
        <?php         
            switch($error_num){
                case '1':
                    echo '<p>Le mot de passe saisi ne correspond pas au compte Cairn.info.</p>';
                    break;
                case '2':
                    echo '<p>La confirmation du mot de passe est incorrecte.</p>';
                    break;
            }
        ?>
        <div class="buttons">
            <span onclick="cairn.close_modal()" class="blue_button ok">Fermer</span>
        </div>
        
    </div>
</div>
