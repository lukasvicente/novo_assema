<?php

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);
/**
 * LoginForm Registration
 * @author  <your name here>
 */
class LoginForm extends TPage {

    protected $form; // form

    /**
     * Class constructor
     * Creates the page and the registration form
     */

    function __construct() {
        parent::__construct();

        $table = new TTable;
        $table->width = '50%';
        // creates the form
        $this->form = new TForm('form_User');
//        $this->form->class = 'tform';
        $this->form->class = 'login-page-container';
         $this->form->style = 'width: 375px;margin:auto; margin-top:120px;';
        // add the notebook inside the form
        $this->form->add($table);

        // create the form fields
        $login = new TEntry('login');
        $login->class = "form-control";
//        $login = new TEntry('usuario');
        $password = new TPassword('password');
        $password->class = "form-control";

        // define the sizes
        $login->setSize(200, 40);
 //       $login->setSize(60);
        $password->setSize(200, 40);
  //      $password->setSize(60);

        $login->style = 'height:35px; font-size:14px; float:left; border-bottom-left-radius: 0;border-top-left-radius: 0;';
        $password->style = 'height:35px; margin-bottom: 15px;font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;';

//        $row = $table->addRow();
//        $row->addCell(new TLabel('Login'))->colspan = 2;
//        $row->class = 'tformtitle';

        $login->placeholder = _t('User');
        $password->placeholder = _t('Password');

        function array_random($arr, $num = 1) {
            shuffle($arr);

            $r = array();
            for ($i = 0; $i < $num; $i++) {
                $r[] = $arr[$i];
            }
            return $num == 1 ? $r[0] : $r;
        }

        $a = array("medico2");

        $user = '<span style="float:left;width:35px;margin-left:35px;height:35px; border-bottom-left-radius: 5px;border-top-left-radius: 5px" class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>';
        $locker = '<span style="float:left;width:35px;margin-left:35px;height:35px; border-bottom-left-radius: 5px;" class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>';

        $script = "<style type='text/css'>
                        body.login-page {
                            background:url('app/templates/theme2/images/bg/" . array_random($a) . ".jpg') top left no-repeat transparent;
                            background-size:cover;
                        }
                        /*
                        .msg-pagina {
                            position: absolute;
                            top: 120px;
                            left: 0px;
                            width: 100%;
                            height: 80%;
                            z-index: 9999997;
                            /* transparência compatível com os navegadores comuns.*/
                            opacity:0.65;
                            -moz-opacity: 0.65;
                            filter: alpha(opacity=65);
                            background: black;
                            text-align: center;
                         } */
                   </style>";

        $title = "
                <div  class='msg-pagina'></div>
                <div class='login-title text-center'> 
                    <img src='app/images/entrada.png' />
                 </div>";

        // create an action button (save)
        $save_button = new TButton('save');
        // define the button action
//        $save_button->setAction(new TAction(array($this, 'onLogin')), _t('Log in'));
        $objsenha = new SenhaForm();
        //$save_button->setAction(new TAction(array($this, 'Verificar')), 'Clique para logar');
        $save_button->setAction(new TAction(array($objsenha, 'Verificar')), 'Clique para logar');
        //  $save_button->class = 'btn btn-success btn-defualt';
        $save_button->class = 'btn btn-lg btn-success';
        $save_button->style = 'margin-left:32px;width:265px;height:40px;border-radius:6px;font-size:18px';

        $container1 = new TElement('div');
        $container1->class = "boxed animated flipInY input-group";

        $container2 = new TElement('div');
        $container2->class = "inner";
        $container2->add($title);
        $container2->add($script);

        //add div 1 na div2
        //  $container1->add($container2);

        $container3 = new TElement('div');
        $container3->class = "input-group";
        $container3->add($user);
        $container3->add($login);
        $container3->add($locker);
        $container3->add($password);
        $container3->add($save_button);

        $container6 = new TElement('div');
        $footer = "<p class='footer'>   </p>";
        $container6->add($footer);

        $container2->add($container3);
        $container2->add($container6);

        $container1->add($container2);



//        $container2->add($locker);
        // $container2->add($password);
//        $row = $table->addRow();
//        $row->addCell($containertitle)->colspan = 2;




        $row = $table->addRow();
        $row->addCell($container1)->colspan = 2;

        // add a row for the field password
//        $row = $table->addRow();
//        $row->addCell($container2)->colspan = 2;
//        $row = $table->addRow();
//        $row->addCell($container6)->colspan = 2;
//        $row = $table->addRow();
//        $row->class = 'inner';
//        $row->class = 'tformaction';
        // $cell = $row->addCell($save_button);
//        $cell->colspan = 2;


        $this->form->setFields(array($login, $password, $save_button));

        // add the form to the page
        parent::add($this->form);
    }

    /**
     * Autenticates the User
     */
    function onLogin() {

        try {
            TTransaction::open('permission');
            $data = $this->form->getData('StdClass');
            $this->form->validate();
            $user = SystemUser::autenticate($data->login, $data->password);
            if ($user) {
                $programs = $user->getPrograms();
                $programs['LoginForm'] = TRUE;

                TSession::setValue('logged', TRUE);
                TSession::setValue('login', $data->login);
                TSession::setValue('username', $user->name);
                TSession::setValue('frontpage', '');
                TSession::setValue('programs', $programs);

                $frontpage = $user->frontpage;

                if ($frontpage instanceof SystemProgram AND $frontpage->controller) {
                    TApplication::gotoPage($frontpage->controller); // reload
                    TSession::setValue('frontpage', $frontpage->controller);
                } else {
                    TApplication::gotoPage('EmptyPage'); // reload
                    TSession::setValue('frontpage', 'EmptyPage');
                }
            }
            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TSession::setValue('logged', FALSE);
            TTransaction::rollback();
        }
    }

    /**
     * Logout
     */
    function onLogout() {
        TSession::freeSession();
        TScript::create("__adianti_goto_page('index.php?class=LoginForm');");
        //TApplication::gotoPage('LoginForm', '');
    }

    /**
     * Login Ceres
     */
    function Verificar() {

        //echo 'passo 1';
        // inicia transacao com o banco 'pg_ceres'
        TTransaction::open('pg_ceres');
        //echo 'passo 2';
        // instancia um repositorio da Classe
        $repository = new TRepository('UsuarioRecord');
        //echo 'passo 3';
        //obtem os dados do formulario de login
        $login = $this->form->getFieldData('login');
        $senha = $this->form->getFieldData('password');

        //echo 'passo 4';
        // cria um criterio de selecao
        $criteria = new TCriteria;
        //filtra pelo campo login
        $criteria->add(new TFilter('login', '=', strtoupper($login)));
        $criteria->add(new TFilter('senha', '=', strtoupper($senha)));
        //echo 'passo 5';
        // carrega os objetos de acordo com o criterio
        $usuarios = $repository->load($criteria);

        //echo 'passo 6';
        if ($usuarios) {
            // percorre os objetos retornados
            foreach ($usuarios as $usuario) {
                // adiciona os dados do usuario na session
                session_start();
                //echo 'passo 7';

                TSession::setValue('login', $login);
                // TSession::setValue('usuario', $usuario->login);

                $_SESSION["usuario"] = $usuario->login;
                TSession::setValue('usuario_id', $usuario->id);
                $_SESSION["usuario_id"] = $usuario->id;
                TSession::setValue('usuario', $usuario->login);
                $_SESSION["validacao"] = "1";
                TSession::setValue('validacao', "1");
                $_SESSION["servidor_id"] = $usuario->servidor_id;
                TSession::setValue('servidor_id', $usuario->servidor_id);
                $_SESSION["municipio_id"] = $usuario->municipio_id;
                TSession::setValue('municipio_id', $usuario->municipio_id);
                $_SESSION["unidadeoperativa_id"] = $usuario->unidadeoperativa_id;
                TSession::setValue('unidadeoperativa_id', $usuario->unidadeoperativa_id);
                $_SESSION["nome"] = $usuario->login;
                TSession::setValue('nome', $usuario->login);
                $_SESSION["tipousuario"] = $usuario->tipo;
                TSession::setValue('tipousuario', $usuario->tipo);
                $_SESSION["matricula"] = $usuario->matricula;
                TSession::setValue('matricula', $usuario->matricula);
                TSession::setValue('usuario_id', $usuario->usuario_id);
                TSession::setValue('laticinio_id', $usuario->laticinio_id);
                $_SESSION["cpfta"] = $usuario->cpf;
                TSession::setValue('cpfta', $usuario->cpf);
                $_SESSION["matriculata"] = $usuario->matricula;
                TSession::setValue('matriculata', $usuario->matricula);
                $_SESSION["regional_id"] = $usuario->regional_id;
                TSession::setValue('regional_id', $usuario->regional_id);
                $_SESSION['modulo'] = 'EMATER';
                TSession::setValue('modulo', 'EMATER');
                $_SESSION["empresa_id"] = $usuario->empresa_id;
                TSession::setValue('empresa_id', $usuario->empresa_id);

                TSession::setValue('logged', TRUE);
                // TApplication::gotoPage('Inicio'); // reload
                TScript::create("__adianti_goto_page('index.php');");
                TSession::setValue('frontpage', 'Inicio');
            }
        } else {
            session_start();
            $_SESSION["validacao"] = "0";
            $_SESSION["usuario"] = "";
            $_SESSION["usuario_id"] = "";
            TSession::freeSession();
            //echo 'passo 7';
            ?>
            <script language=javascript>
                //   location.href = 'index.php?class=LoginForm?msg=erro';
                $(document).ready(function () {
                    $('.msg-pagina').fadeOut('fast');
                });
            </script>
            <?php

            // echo "<div align=\"center\"><font color=red><b>Login e/ou senha incorreto(s)!</b></font><br /></div>";
        }
        // finaliza a transacao
        TTransaction::close();
    }

}
?>