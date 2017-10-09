<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>ASSEMA - RN / ASSOCIOACAO</title>
        {LIBRARIES}
        {HEAD}


        <!--
        <link rel="stylesheet" type="text/css" href="app/templates/{template}/assets/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="app/templates/{template}/assets/font-awesome/css/font-awesome.min.css"> -->
        <!--já esta no libraries.html
        <link rel="stylesheet" type="text/css" href="app/templates/{template}/css/style.css">
        <link rel="stylesheet" type="text/css" href="app/templates/{template}/css/responsive.css">
        <link rel="stylesheet" type="text/css" href="app/templates/{template}/css/animate.css">
        -->

        <link rel="shortcut icon" href="app/templates/{template}/images/favicon.ico" type="image/png">
        <link rel="shortcut icon" type="image/png" href="app/templates/{template}/images/favicon.ico" />

        <script type="text/javascript">
            $(document).ready(function() {
                __adianti_load_page('engine.php?class=LoginForm');
            });
        </script>
        <!-- Google Fonts -->
        <!--
        <link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
        <link href='http://fonts.googleapis.com/css?family=Lato:400,300,700,700italic,900,100' rel='stylesheet' type='text/css'>
        -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->


    </head>

    <body class="login-page">
        <section class="content login-page">
            <div class="content-liquid">
                <div class="container">
                    <div class="row">
                        <!-- content adianti -->
                        <div id='adianti_div_content'>
                        </div>
                        <script type="text/javascript">
                            //#javascript_placeholder#
                        </script>
                        <div id="adianti_online_content"></div>
                        <div id="adianti_online_content2"></div>
                        <!-- and content adianti -->
                        <!--
                        <form name="formsenha" action="senha.php" method="POST">
                            <div class="login-page-container">

                                <div class="boxed animated flipInY">
                                    <div class="inner">

                                        <div class="login-title text-center">
                                            <img src="app/templates/{template}/images/framework_logo.png" width="100" />
                                            <br />
                                            <br />
                                            <h4>NovoCeres</h4>
                                            <h5></h5>
                                        </div>

                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                            <input type="text" name="usuario" class="form-control" placeholder="Usuario" />
                                        </div>

                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                            <input type="password" name="password" class="form-control" placeholder="Senha" />
                                        </div>

                                        <input type="submit" class="btn btn-lg btn-success" value="Clique para logar" name="submit" id="submit" />

                                        <p class="footer">ADIANTI FRAMEWORK</p>
                        <?php
                        //if($_REQUEST["msg"] == 'erro'){
                        //		echo "<div align=\"center\"><font color=red><b>Login e/ou senha incorreto(s)!</b></font><br /></div>";
                        //} 
                        ?>

                                    </div>
                                </div>

                            </div>
                        </form>-->

                    </div>

                </div>
            </div>

        </section>

        <!-- Javascript -->
        <!--
        <script src="app/templates/{template}/assets/jquery/jquery.min.js"></script>
        <script src="app/templates/{template}/assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="app/templates/{template}/assets/flippy/jquery.flippy.min.html"></script>
        -->

        <script type="text/javascript">
                jQuery(document).ready(function ($) {

                    var min_height = jQuery(window).height();
                    jQuery('div.login-page-container').css('min-height', min_height);
                    jQuery('div.login-page-container').css('line-height', min_height + 'px');

                    //$(".inner", ".boxed").fadeIn(500);
                });
        </script>
        -
    </body>
</html>