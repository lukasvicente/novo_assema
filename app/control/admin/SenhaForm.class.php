<?php

use Adianti\Database\TTransaction;
use Adianti\Database\TRepository;

/**
 * SenhaForm 
 * @author  <your name here>
 */
class SenhaForm extends TPage {

    protected $form; // form

    /**
     * Login Ceres
     */

    public function Verificar() {
        // inicia transacao com o banco 'pg_ceres'
        TTransaction::open('pg_ceres');
        // instancia um repositorio da Classe
        $repository = new TRepository('UsuarioRecord');
        //obtem os dados do formulario de login
//        $login = $this->form->getFieldData('login');
//        $senha = $this->form->getFieldData('password');
//        $login = $_REQUEST['login'];
//        $senha = $_REQUEST['password'];
        $login = preg_replace('/[^[:alnum:]_]/', '', $_REQUEST['login']);
        $senha = preg_replace('/[^[:alnum:]_]/', '', $_REQUEST['password']);



        // cria um criterio de selecao
        $criteria = new TCriteria;
        //filtra pelo campo login
        $criteria->add(new TFilter('login', '=', strtoupper($login)));
        $criteria->add(new TFilter('senha', '=', strtoupper($senha)));

        $nurows = $repository->count($criteria);

     //  var_dump($nurows);
       // exit();
       // exit();
        if ($nurows == 1) {
            // carrega os objetos de acordo com o criterio
            $usuarios = $repository->load($criteria);
            if ($usuarios) {
                // percorre os objetos retornados
                foreach ($usuarios as $usuario) {
                   // Comentario feito por lucas p/ parar os erros na tela inicial
                    // adiciona os dados do usuario na session
                    //session_start();
                    TSession::setValue('login', $login);
                    $_SESSION["usuario_id"] = $usuario->id;
                    $_SESSION["usuario"] = $usuario->login;
                    $_SESSION["validacao"] = "1";
                    $servidor_id = $_SESSION["servidor_id"] = $usuario->servidor_id;
                    //$_SESSION["municipio_id"] = $usuario->municipio_id;
                    //$_SESSION["unidadeoperativa_id"] = $usuario->unidadeoperativa_id;
                    $_SESSION["nome"] = $usuario->login;
                    $_SESSION["tipousuario"] = $usuario->tipo;
                    $_SESSION["matricula"] = $usuario->tipo;
                    //$_SESSION["laticinio_id"] = $laticinio->laticinio_id;
                    $nome = $_SESSION["nometa"] = $usuario->nome;
                    $_SESSION["cpfta"] = $usuario->cpf;
                    $_SESSION["matriculata"] = $usuario->matricula;
                    //$_SESSION["regional_id"] = $usuario->regional_id;
                    $_SESSION['modulo'] = 'SGS';
                    $_SESSION["empresa_id"] = $usuario->empresa_id;

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
                TScript::create("__adianti_goto_page('index.php?class=LoginForm?msg=erro');");
                //echo 'passo 7'; index.php?class=LoginForm
                ?>
                <!--            <script language=javascript>
                                //   location.href = 'index.php?class=LoginForm?msg=erro';
                                $(document).ready(function() {
                                    $('.msg-pagina').fadeOut('fast');
                                });
                            </script>-->
                <?php
                // echo "<div align=\"center\"><font color=red><b>Login e/ou senha incorreto(s)!</b></font><br /></div>";
            }
        } else {
            session_start();
            $_SESSION["validacao"] = "0";
            $_SESSION["usuario"] = "";
            $_SESSION["usuario_id"] = "";
            TSession::freeSession();
            TScript::create("__adianti_goto_page('index.php?class=LoginForm?msg=erro');");
        }
        // finaliza a transacao
        TTransaction::close();
    }

}
?>