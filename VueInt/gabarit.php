<!doctype html>
<html lang="fr">
    <head>
        <meta charset="UTF-8" />
        <title><?= strip_tags($titre) ?> - <?= Configuration::get('siteName', 'Cairn International') ?></title>
        <meta name="viewport" content="initial-scale=1">
        <link rel="icon" type="image/png" href="favicon.ico" />
        <!-- cairn-build :: [test] -->

        <?php
            // Inclusion des feuilles de style css
            require_once('CommonBlocs/headerCss.php');
        ?>

        <?= $this->getHeaders('html', "\n        ") ?>

    </head>
    <body>
        <div id="header">

            <ul id="nav1">
                <li>
                    <?php
                    if (isset($authInfos['U']) || isset ($authInfos['I'])) {
                        include(__DIR__ . '/User/logas.php');
                    } else {
                        include(__DIR__ . '/User/login.php');
                    }
                    ?>
                </li>
                <li><a href="#" onclick="cairn.show_menu(this, '#menu_mycairn');" id="user_mycairn_menu">My Cairn.info</a></li>
                <!--li><a href="./off_campus.php"><span class="icon-remote-access icon" id="icon_remote-acess"></span>Off-Campus</a></li-->

                <li><a href="./contact.php" class="grey">Contact</a></li>
                <li><a href="./help.php" class="grey" target="blank">Help</a></li>
                <li><a href="./about.php" class="grey">About</a></li>
                <li><a href="http://<?= $corsURL ?>" class="grey">French Edition</a></li>
                <?php
                if(!empty($authInfos['U']['ADMIN']))
                {
                    echo '<li><a id="isAdminLink" href="./administration.php"><span id="isAdmin">Administration</span></a></li>';
                }
                ?>
            </ul>
            <input type="hidden" id="corsURL" value="<?= $corsURL ?>"/>
            <div id="menu_login" class="menu menu_login" style="display: none; height: 34px; margin-bottom: 0px;">
                <div id="login_links" class="inbl right">
                    <b>Not registered yet?</b>
                    <a href="./create_account.php">Sign Up!</a><br>
                    <a href="./password_forgotten.php">Password forgotten?</a>
                </div>
                <div id="login_inputs" class="inbl right">
                    <form id="login_form" name="login_form" action="javascript:ajax.login()" method="GET">
                    <label for="email_input">Login</label>
                    <input id="email_input" name="LOG" required="required" type="text">
                    <label for="password_input">Password</label>
                    <input id="password_input" name="PWD" required="required" type="password">
                    <input id="login_button" value="" class="inbl icon-submit-arrow" type="submit">
                    </form>
                </div>
            </div>
            <!--div id="menu_login_dev" class="menu menu_login" style="display:none;">
                <input value="CONNECT" name="OPERATE" type="hidden">
                <div id="login_inputs" class="inbl right">
                    <label for="email_input">Veuillez choisir un compte de test:</label>
                    <input value="Anonyme" class="inbl" type="button" onclick="ajax.logintest('1');">
                    <input value="Paris VII (vue grisée)" class="inbl" type="button" onclick="ajax.logintest('2');">
                    <input value="Paris VII (vue filtrée)" class="inbl" type="button" onclick="ajax.logintest('3');">
                </div>
            </div-->
            <style>
                #wrapper_menu_mycairn { position : relative; }
                #wrapper_menu_mycairn .close_menu {
                    height : 26px;
                    width : 26px;
                    position :absolute;
                    right : -52px;
                    cursor : pointer;
                }
            </style>

            <?php include(__DIR__ . '/User/mycairn.php'); ?>

            <div id="wrapper_search">
                <a href="./" class="inbl" style="margin-right:136px;">
                    <img src="./static/images/logo-cairn-int.png" alt="CAIRN.INFO : International Edition">
                </a>
                <div class="inbl" style="width:464px; margin-top: 28px;">
                    <form id="main_search_form2" action="./resultats_recherche.php" method="GET">
                    <div class="border_grey w100" >
                        <button type="submit" class="right black_button" id="send_search_field" style="padding-bottom:0.1em;">
                            <span class="icon-magnifing-glass left mr6"></span>Search
                        </button>
                        <div id="wrapper_search_input">
                            <input autocomplete="off" id="compute_search_field" placeholder="Your keywords" class="w98 no_border ui-autocomplete-input" name="searchTerm" style="width:325px" type="text" title="rechercher sur Cairn-int.info"><span class="ui-helper-hidden-accessible" aria-live="polite" role="status"></span>
                        </div>
                    </div>
                    <span id="link_search_advanced" style="top: 0px;">
                        <?php if((isset($authInfos['U']) && isset($authInfos['U']['HISTO_JSON']->searchMode) && $authInfos['U']['HISTO_JSON']->searchMode[0] == 'english')
                            || (!isset($authInfos['U']) && isset($authInfos['G']) && isset($authInfos['G']['HISTO_JSON']->searchMode) && $authInfos['G']['HISTO_JSON']->searchMode[0] == 'english')){ ?>
                            <input type="radio" name="type_search" value="all" >All articles
                            <input type="radio" name="type_search" value="english" checked="1">English full-text articles
                        <?php }else{ ?>
                            <input type="radio" name="type_search" value="all" checked="1">All articles
                            <input type="radio" name="type_search" value="english">English full-text articles
                        <?php } ?>
                    </span>
                    </form>
                </div>
            </div>

        </div>

        <div id="contenu">
            <?= $contenu ?>
        </div> <!-- #contenu -->
        <!-- #global -->

        <div id="wrapper_footer">
            <div id="footer">
                <div id="footer_shortcuts"><br/>
                    <a href="./" id="logo_cairn_footer">
                        <img src="./static/images/logo-int-footer.png" alt="CAIRN.INFO : Chercher, repérer, avancer.">
                    </a>
                    <ul>
                        <li><a href="./about.php">About</a></li>
                        <li><a href="./conditions.php">Terms of use</a></li>
                        <li><a href="http://www.cairn.info">French edition</a></li>
                    </ul>
                </div>
                <div id="footer_disciplines"><br/>
                    <h1>Research areas</h1>
                    <ul>
                        <li><a href="./disc-communication.htm" class="btn">Communication</a></li>
                        <li><a href="./disc-economics.htm" class="btn">Economics &amp; Management</a></li>
                        <li><a href="./disc-education.htm" class="btn">Education</a></li>
                        <li><a href="./disc-general-interest.htm" class="btn">General Interest</a></li>
                        <li><a href="./disc-geography.htm" class="btn">Geography</a></li>
                        <li><a href="./disc-history.htm" class="btn">History</a></li>
                        <li><a href="./disc-literature.htm" class="btn">Literature &amp; Linguistics</a></li>
                        <li><a href="./disc-philosophy.htm" class="btn">Philosophy</a></li>
                        <li><a href="./disc-political-science-and-law.htm" class="btn">Political Science &amp; Law</a></li>
                        <li><a href="./disc-psychology.htm" class="btn">Psychology</a></li>
                        <li><a href="./disc-sociology.htm" class="btn">Sociology &nbsp;&amp;&nbsp; Culture&nbsp;</a></li>
                    </ul>
                </div>
                <div id="footer_tools"><br/>
                    <h1>Tools</h1>
                    <ul>
                        <li><a href="./help.php">Help</a></li>
                        <li><a href="./rss_feeds.php">RSS feeds</a></li>
                        <li><a href="./contact.php">Contact</a></li>
                    </ul>
                </div>
                <div id="footer_menu_user"><br/>
                    <h1>My Cairn.info</h1>
                    <ul>
                        <li><a href="./my_account.php">My account</a></li>
                        <li><a href="./my_cart.php">My cart</a></li>
                        <li><a href="./my_purchases.php">My purchases</a></li>
                        <li><a href="./biblio.php">My list of articles</a></li>
                        <li><a href="./my_alerts.php">My email alerts</a></li>
                        <li id="footer-logos-partner" style="width: 350px; margin-top: 2em;">
                            <a href="http://www.centrenationaldulivre.fr/" id="logo_cnl">
                                <img src="./static/images/logo-cnl.png" alt="logo CNL" id="footer_logo_cnl" style="float: left; margin-right: 0.5em;">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="post_footer">
            <span>&copy; 2010-2014 Cairn.info</span>
            <!--    <a href="#">Privacy Policy</a>
            <a href="#">Terms of use</a> -->
        </div>
        <div onclick="cairn.close_modal()" id="blackground"></div>
        <div id="error_div">
            <?php
                if(isset($contenu_erreur)){
                    echo $contenu_erreur;
                    $this->javascripts[] = 'cairn.show_modal(\'#error_div_modal\');';
                }
            ?>
        </div>
        <a id="jump-top" href="#top" style="display: block;"><img alt="back to top" src="./static/images/jump-top.png"></a>
        <div id="feedback">
            <a href="#" onclick="cairn.show_modal('#modal_feedback');" class="feedback-trigger">Feedback</a>
        </div>
        <div style="display: none;" class="window_modal" id="modal_feedback">
            <form method="POST" onsubmit="return ajax.sendFeedback()" id="send_feedback">
                <input type="hidden" name="f_from" value="cairnint">
                <h1>Your Feedback about Cairn International</h1>
                <span onclick="cairn.close_modal();" class="close_modal"></span>
                <div id="feedback_radios">
                    <input type="radio" name="f_category" value="idea" id="radio_idea">
                    <label for="radio_idea" class="active">Idea</label>
                    <input type="radio" name="f_category" value="question" checked="" id="radio_question">
                    <label for="radio_question">Question</label>
                    <input type="radio" name="f_category" value="bug" id="radio_bug">
                    <label for="radio_bug">Bug</label>
                </div>
                <div id="feedback_fields">
                    <div class="feedback_field">
                        <label for="f_email">
                            <strong>E-mail Address</strong>
                        </label>
                        <input type="email" name="f_email" value="" required="" id="f_email">
                    </div>
                    <div class="feedback_field">
                        <label for="f_message">
                            <strong>Message</strong>
                        </label>
                        <textarea name="f_message" required="" id="f_message"></textarea>
                    </div>
                    <button type="submit">Send</button>
                </div>
            </form>
        </div>
        <div id="modal-success-feedback" class="window_modal" style="display: none;">
            <div class="info_modal">
                <h2>Your feedback message has been sent</h2>
                <div class="buttons">
                    <span class="blue_button ok" onclick="cairn.close_modal()">Close</span>
                </div>
            </div>
        </div>

        <div id="modal_why-not-article" class="window_modal" style="display: none;">
        <div class="basic_modal">
            <span onclick="cairn.close_modal();" class="close_modal"></span>
            <h1>Why is this article not available in English?</h1>
            <!--h2 id="article-title_translation">Asia-Pacific: China’s Foreign Policy Priority</h2-->
            <div class="w100">
                <p class="w45 inbl mr3">
                    Cairn International Edition is a service dedicated to helping a <span style="white-space:nowrap;">non&ndash;French&ndash;speaking</span> readership to browse, read, and discover work published in French journals. You will find English <span style="white-space:nowrap;">full&ndash;text</span> translations, in addition to French version already available on Cairn regular edition. Full text translations only exist for a selection of articles.
                </p>
                <div class="w45 inbl">
                    <p>
                        If you are interested in having this article translated into English, please enter your email address and you will receive an email alert when this article has been translated.
                    </p>
                    <form id="alert_on_translation" method="POST" action="./static/includes/feedback/alert_translation_form.php">
                        <input type="hidden" id="id_article_translation" name="id_article_translation" value="E_PE_143_0011">
                        <div class="case_blue-milk w100 inbl mt3">
                            <label for="email_translation">Your email address</label>
                            <input type="email" required="" value="" name="email_translation" id="email_translation">
                        </div>
                        <input type="submit" style="margin-top:0.5em;" class="button-blue right" value="Send">
                    </form>
                </div>
            </div>
        </div>
        </div>

        <script type="text/javascript">
        function showEjectModal(){
            cairn.show_modal('#modal_logouteject');
        }
        </script>
        <div id="modal_logouteject" class="window_modal" style="display:none;">
            <div class="info_modal">
                <h2>Connection closed</h2>
                <p>Your account is in use from another device. Your connection has been closed</p>
                <div class="buttons">
                    <span class="blue_button ok" onclick="cairn.close_modal()">Close</span>
                </div>
            </div>
        </div>

        <div id="modal_empty_input" class="window_modal" style="display:none;">
            <div class="info_modal">
                <h2>Alert</h2>
                <p>Some required fields are empty.</p>
                <div class="buttons">
                    <span class="blue_button ok" onclick="cairn.close_modal()">Close</span>
                </div>
            </div>
        </div>
        <div id="modal_confirm-why-not-article" class="window_modal">
            <div class="basic_modal">
                <h1>Message sent</h1>
                <p>
                    Your email address has been saved.<br>
                    We will notify you when this article becomes available in English.
                </p>
                <br>
                <br>
                <button onclick="cairn.close_modal();" class="button-blue">Close</button>
            </div>
        </div>

    <!-- JS starts here -->
    <?php
        // Inclusion des scripts js
        require_once('CommonBlocs/footerJavascript.php');
    ?>

    <?php
        if (Configuration::get('webtrends_datasource', null)) {
            include(__DIR__ . '/../Vue/CommonBlocs/webtrends.php');
        }
    ?>

    <?php if (Configuration::get('allow_backoffice', false)): ?>
        <script>
            <?php if (isset($_SERVER['REDIRECT_QUERY_STRING'])): ?>
                // Uniquement à destination des développeurs, pour faciliter la recherche de controlleur et actions
                console.debug("URL-DEBUG :: <?= $_SERVER['REDIRECT_QUERY_STRING'] ?>");
            <?php endif; ?>
            window.DEBUG = true;
        </script>
    <?php endif; ?>

    <?= $this->getJavascripts(); ?>
    <!-- JS ends here -->

    </body>
</html>
