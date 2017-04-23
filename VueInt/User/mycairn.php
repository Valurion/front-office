<?php ?>

<div id="menu_mycairn" class="menu" style="display : none;">
    <div id="wrapper_menu_mycairn" class="grid-g">
        <span class="close_menu" onclick="cairn.close_menu();"></span>
        <div id="recently_searched" class="grid-u-1-3">
            <div class="mycairn_title_wrapper">
                <span class="title-like">Recently searched</span>
                <a href="./my_searches.php">My Searches</a>
            </div>
            <ul>
                <?php
                $recherches = array();
                if(isset($authInfos['U']) && isset($authInfos['U']['HISTO_JSON']->recherches)){
                    $recherches = $authInfos['U']['HISTO_JSON']->recherches;
                }else if(isset($authInfos['G']) && isset($authInfos['G']['HISTO_JSON']->recherches)){
                    $recherches = $authInfos['G']['HISTO_JSON']->recherches;
                }
                for($ind = 0 ; $ind < count($recherches) && $ind < 3 ; $ind++){
                    echo '<li><a href="resultats_recherche.php?searchTerm='.$recherches[$ind][0].'">'.$recherches[$ind][0].'</a></li>';
                }
                ?>
            </ul>
        </div>

        <div id="recently_viewed" class="grid-u-1-2">
            <div class="mycairn_title_wrapper">
                <span class="title-like">Recently viewed</span>
                <a href="./my_history.php">My history</a>
            </div>
            <ul>
                <?php
                $articles = array();
                if(isset($authInfos['U']) && isset($authInfos['U']['HISTO_JSON_ARTICLES'])){
                    $articles = $authInfos['U']['HISTO_JSON_ARTICLES'];
                }else if(isset($authInfos['G']) && isset($authInfos['G']['HISTO_JSON_ARTICLES'])){
                    $articles = $authInfos['G']['HISTO_JSON_ARTICLES'];
                }
                for($ind = 0 ; $ind < count($articles) && $ind < 3 ; $ind++){
                    echo '<li><a href="article.php?ID_ARTICLE='.$articles[$ind][0].'">'.$articles[$ind][1].'</a></li>';
                }
                ?>
            </ul>
        </div>

        <div id="mycairn_pannel" class="grid-u-1-6">
            <div class="mycairn_title_wrapper">
                <span class="title-like"><i>Create an account to save:</i></span>
            </div>
            <ul>
                <?php if(isset($authInfos["U"])){
                   echo '<li><a href="./my_account.php">My account</a></li>';
                }?>
                <?php if($modeAchat == 1){?>
                    <li><a href="./my_cart.php">My cart</a></li>
                <?php }else if($modeAchat == 2){ ?>
                    <li><a href="./my_requests.php">My requests</a></li>
                <?php } ?>
                <?php if(isset($authInfos["U"])){
                   echo '<li><a href="./my_purchases.php">My purchases</a></li>';
                }?>
                <li><a href="./biblio.php">My list of articles</a></li>
                <li><a href="./my_alerts.php">My email alerts</a></li>
            </ul>
            <?php if(!isset($authInfos["U"])){
                echo '<a href="./create_account.php" id="button_SignIn">Sign up</a>';
            }?>
        </div>
    </div>
</div>

