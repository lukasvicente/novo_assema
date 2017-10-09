<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_erros',1);
//error_reporting(E_ALL);
//teste

require_once "lib/adianti/database/TRecord.php";
require_once "lib/adianti/database/TTransaction.php";
require_once "lib/adianti/database/TConnection.php";
require_once "lib/adianti/database/TSqlInstruction.php";
require_once "lib/adianti/database/TSqlSelect.php";
require_once "lib/adianti/database/TRepository.php";
require_once "lib/adianti/database/TExpression.php";
require_once "lib/adianti/database/TCriteria.php";
require_once "lib/adianti/database/TFilter.php";
require_once "app/model/Vw_usuario.class.php";

//namespace login;
use Adianti\Database\TConnection;
use Adianti\Log\TLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use PDO;

class TVerifica {

   
    
    static public function verificar() {
        
//echo 'passo 1';
        // inicia transacao com o banco 'pg_ceres'
        TTransaction::open('pg_ceres');
//echo 'passo 2';
        // instancia um repositorio da Classe
        $repository = new TRepository('vw_usuario');
//echo 'passo 3';
        //obtem os dados do formulario de login
        $login = $_POST["usuario"];
        $senha = $_POST["password"];
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

                $_SESSION["usuario"] = $usuario->login;
                $_SESSION["validacao"] = "1";
                $servidor_id = $_SESSION["servidor_id"] = $usuario->servidor_id;
                $_SESSION["municipio_id"] = $usuario->municipio_id;
                $_SESSION["unidadeoperativa_id"] = $usuario->unidadeoperativa_id;
                $_SESSION["nome"] = $usuario->login;
                $_SESSION["tipousuario"] = $usuario->tipo;
                $_SESSION["matricula"] = $usuario->tipo;
                $_SESSION["usuario_id"] = $usuario->usuario_id;
                $_SESSION["laticinio_id"] = $laticinio->laticinio_id;
                $nome = $_SESSION["nometa"] = $usuario->nome;
                $_SESSION["cpfta"] = $usuario->cpf;
                $_SESSION["matriculata"] = $usuario->matricula;
                $_SESSION["regional_id"] = $usuario->regional_id;
                $_SESSION['modulo'] = 'EMATER';

                // Connecting, selecting database
                $dbconn = pg_connect("host=localhost dbname=db_teste user=postgres password=123456") or die('Could not connect: ' . pg_last_error());

//perform the insert using pg_query
                $result = pg_query($dbconn, "INSERT INTO acessos(nome, servidor_id, data_acesso) 
                  VALUES('$nome', '$servidor_id', NOW());");

// Closing connection
                pg_close($dbconn);

                //header ("Location: index.php");
                ?>
                <script language=javascript>
                    location.href = 'index.php';
                </script>";
                <?php

            }
        } else {
            session_start();
            $_SESSION["validacao"] = "0";
            $_SESSION["usuario"] = "";
            $_SESSION["usuario_id"] = "";

            //echo 'passo 7';
            ?>
            <script language=javascript>
                location.href = 'logar.php?msg=erro';
            </script>";
            <?php

        }

        // finaliza a transacao
        TTransaction::close();
    }

}

TVerifica::verificar();
?>
