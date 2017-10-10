<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_erros', 1);
//error_reporting(E_ALL);

require_once 'init.php';
//require_once 'lib\adianti\core\AdiantiCoreTranslator.php';

//use Adianti\Core;

$uri = 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
$template = 'theme3';

new TSession ();

$menu_string = '';
$cargo = '';
$socio = '';
$title_bar = '';
$pagina = '';
$boxed = '';
$validar = '';
$avaliacao = '';
$mural = '';
$breadcrumb = '';
$fotoservidor = '';
$usuario = '';
$lotacao = 0;
$municipio = '';
$municipiologado = '';
$menuModulo = '';
$menulateral = '';
$msg = '';
$sigla_empresa = '';

if( TSession::getValue('logged') ) {
    $content = file_get_contents("app/templates/{$template}/layout.html");

    ob_start();
    // $callback = array('SystemPermission', 'checkPermission');
    // $xml = new SimpleXMLElement(file_get_contents('menu.xml'));
    // $menu = new TMenu($xml, $callback, 1, 'nav collapse', '');
    // $menu->class = 'nav';
    // $menu->id = 'side-menu';
    // $menu->show();
    $menu_string = ob_get_clean();


    // recebe modulo e armazena na sessao
    if (isset($_GET ['modulo'])) {
        $_SESSION ['modulo'] = $_GET ['modulo'];
    }
    // carregar menu e modulos
    // pega os modulos do usuario
    $menuModulo = TMenu::montaModulo($_SESSION ['usuario_id']);
    $menulateral = TMenu::montaMenu($_SESSION ['modulo'], $_SESSION ['usuario_id']);



    // montaMural dashboard
   //if (($_SERVER ['REQUEST_URI'] == "/templatemodelo/index.php?modulo=" . filter_input(INPUT_GET, 'modulo')) || ($_SERVER ['REQUEST_URI'] == "/templatemodelo/?modulo=" . filter_input(INPUT_GET, 'modulo'))) {
     if (($_SERVER ['REQUEST_URI'] == "/novo_assema/index.php?modulo=" . filter_input(INPUT_GET, 'modulo')) || ($_SERVER ['REQUEST_URI'] == "/novo_assema/index.php" )) {
        $mural = Adianti\Widget\Menu\TMenu::montaMural();
    }

    TTransaction::open('pg_ceres');

    if ($_SESSION ['empresa_id']) {
        // instancia um record da classe municipio
        $repository = new TRepository('vw_selecaoempresaRecord');
        $criteria = new TCriteria ();
        $criteria->setProperty('order', 'sigla');
        $criteria->add(new TFilter('login', '=', $_SESSION ["usuario"]));
        // carrega os objetos de acordo com o criterio
        $cadastros = $repository->load($criteria);

        // armazena o municipio do usuario
        if ($cadastros) {
            // $municipio = $municipiorecord->municipio;
            foreach ($cadastros as $object) {
                $sigla_empresa .= '<option ';
                if ($_SESSION ['empresa_id'] == $object->empresa_id)
                    $sigla_empresa .= ' selected ';
                $sigla_empresa .= 'value="?empresa=' . $object->empresa_id . '" >' . $object->sigla . '</option>';
            }
        }
    }

    $lotacao = filter_input(INPUT_GET, 'lotacao');
    if ($lotacao) {
        $_SESSION ['municipio_id'] = $lotacao;
        echo '<script language="javascript">window.location = "index.php"</script>';
    }

    $usuariorecord = new UsuarioRecord($_SESSION ['usuario_id']);
    if ($usuariorecord->nome_cargonovo) {
        $cargo = ' (' . $usuariorecord->nome_cargonovo . ')';
    }

    $socio = $usuariorecord->nome_socio . $cargo;
    // finaliza a transacao
    TTransaction::close();

    if (!$socio) {
        $socio = 'ASSEMA/RN';
    }

    if (empty($_SESSION ["municipio_id"])) {
        $usuario = '&raquo;' . $socio . ' / Usu&aacute;rio: ' . $_SESSION ["usuario"] . '/' . $_SESSION ['usuario_id'];
    } else {
        $usuario = '&raquo;' . $socio . ' / Usu&aacute;rio: ' . $_SESSION ["usuario"] . '/' . $_SESSION ['usuario_id'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MunicÃ­pio: <a href="?class=SelecionaCidadeList">' . $municipio . "</a>";
        // $usuario = '&raquo;' . $servidor . ' / Usu&aacute;rio: ' . $_SESSION["usuario"];
    }

    // ######### carregar foto servidor ##########
    //
    if (file_exists('app/images/servidor/servidor_' . $_SESSION ['servidor_id'] . '.jpg')) {
        $fotoservidor = '<img src="app/images/servidor/servidor_' . $_SESSION ['servidor_id'] . '.jpg" alt="" />';
    } else {
        $fotoservidor = '<img src="app/images/profile-60x60.png" alt="" />';
    }

    //
    // ######### fim carregar foto servidor ##########
    //
	// ######### SE ESTIVER EXIBINDO ALGUMA CLASSE EXIBE O TITULO DA PAGINA ##########
    //
	
	if (filter_input(INPUT_GET, 'class')) {
        /*
         * TTransaction::open('pg_ceres');
         * $repository = new TRepository('vw_usuario_paginaspermitidasRecord');
         * $criteria = new TCriteria;
         * $criteria->add(new TFilter('arquivo', '=', filter_input(INPUT_GET, 'class')));
         * // carrega os objetos de acordo com o criterio
         * $cadastros = $repository->load($criteria);
         *
         * if ($cadastros) {
         * // percorre os objetos retornados
         * foreach ($cadastros as $object) {
         * $pagina = $object->nome;
         * }
         * }
         * // finaliza a transacao
         * TTransaction::close();
         *
         * if ($cadastros) {
         * // $title_bar = "<div class='title-bar'>" . $pagina . "</div>";
         *
         * }
         */
        //	var_dump ( $_SESSION ['title-bar'] );
       // $title_bar = $_SESSION ['title-bar'];
    }
    //
    // ######### Breadcrumbs ##########
    //
	
	if (filter_input(INPUT_GET, 'class')) {
        $breadcrumb = "<!-- Breadcrumbs Start -->
				<div class=\"row breadcrumbs\">
				  <div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12\">
					<ul class=\"breadcrumbs\">
					  <li><a href=\"index.php\"><i class=\"fa fa-home\"></i></a></li>
					  <li><a href=\"?modulo=" . $_SESSION ['modulo'] . "\">" . $_SESSION ['modulo'] . "</a></li>
					  <li><a href=\"#\">" . $pagina . "</a></li>
					</ul>
				  </div>
				</div>
		       <!-- Breadcrumbs End -->";
    }
    //
    // ######### fim Breadcrumbs ##########
    // ######### SE ESTIVER EXIBINDO ALGUMA CLASSE CARREGA A DIV BOXED ##########
    //
	
	if (filter_input(INPUT_GET, 'class')) {
        $boxed = 'boxed';
    }
    // ######### fim Breadcrumbs ##########
    //
	// ########## MONTAR MURAL ##########
    //
	
	if ($_SERVER ['REQUEST_URI'] == "/novo_assema/index.php") {

        // pega a funcao montaMural
        $montaMural = TMenu::montaMural();
        // pega a funcao montaError
        $montaError = TMenu::montaError();
        // pega a funï¿½ï¿½o montavalidaï¿½ï¿½o
        //$montaValidacao = TMenu::montaValidacao();
        // pega a funcao montaAvaliacao
        //$montaAvaliacao = TMenu::montaAvaliacao();

        if (isset($_GET)) {
            $class = filter_input(INPUT_GET, 'class');

            if (class_exists($class)) {
                // verifica se existe permissao para ver a classe
                $pagina = new $class ();
                ob_start();
                $pagina->show();
                $content = ob_get_contents();
                ob_end_clean();
                // }
            } else if (function_exists($method)) {
                call_user_func($method, $_GET);
            } else {
                $mural = $montaMural;
                $validar = $montaValidacao;
                $avaliacao = $montaAvaliacao;
            }
            // if (TMenu::validaClasse($class) == FALSE) {
            // $_SESSION["validacao"] = "";
            // }
        } else {
            $mural = $montaMural;
            $validar = $montaValidacao;
            $avaliacao = $montaAvaliacao;
        }
    }

    //
    // ######### FIM MONTAR MURAL ##########
    //
	// ########## CARREGA A DIV MSG ##########
    //
	/*
      if (isset ( $_GET ['msg'] )) {
      if ($_GET ['msg'] == 'sucess') {
      $msg = '<div id="content-table-inner">
      <!--  start message-green -->
      <div id="message-green">
      <table border="0" width="100%" cellpadding="0" cellspacing="0">
      <tr>
      <td class="green-left">Salvo com sucesso!</td>
      <td class="green-right"><a class="close-green"><img src="app/images/table/icon_close_green.gif"   alt="" /></a></td>
      </tr>
      </table>
      </div>
      <!--  end message-green -->
      </div>';
      }

      if ($_GET ['msg'] == 'delete') {
      $msg = '<div id="content-table-inner">
      <!--  start message-yellow -->
      <div id="message-yellow">
      <table border="0" width="100%" cellpadding="0" cellspacing="0">
      <tr>
      <td class="yellow-left">Removido com sucesso!</td>
      <td class="yellow-right"><a class="close-yellow"><img src="app/images/table/icon_close_yellow.gif"   alt="" /></a></td>
      </tr>
      </table>
      </div>
      <!--  end message-yellow -->
      </div>';
      }
      if ($_GET ['msg'] == 'negado') {
      $msg = '<div id="content-table-inner">
      <!--  start message-yellow -->
      <div id="message-yellow">
      <table border="0" width="100%" cellpadding="0" cellspacing="0">
      <tr>
      <td class="yellow-left">Acesso negado!</td>
      <td class="yellow-right"><a class="close-yellow"><img src="app/images/table/icon_close_yellow.gif"   alt="" /></a></td>
      </tr>
      </table>
      </div>
      <!--  end message-yellow -->
      </div>';
      }

      }
     */

    //
    // ######### fim msg ##########
} else {
    $content = file_get_contents("app/templates/{$template}/login.php");
    // $content = file_get_contents("app/templates/{$template}/login.html");

}

//$content = TApplicationTranslator::translateTemplate($content);
$content = str_replace('{LIBRARIES}', file_get_contents("app/templates/{$template}/libraries.html"), $content);
$content = str_replace('{URI}', $uri, $content);
$content = str_replace('{class}', isset($_REQUEST ['class']) ? $_REQUEST ['class'] : '', $content);
$content = str_replace('{template}', $template, $content);
// #################### NovoCeres #####################

$content = str_replace('{SOCIO}', $socio, $content);
$content = str_replace('{FOTOSERVIDOR}', $fotoservidor, $content);
$content = str_replace('{USUARIO}', $usuario, $content);
$content = str_replace('{BREADCRUMB}', $breadcrumb, $content);
$content = str_replace('{BOXED}', $boxed, $content);
//$content = str_replace ( '{TITLE_BAR}', $title_bar, $content );
$content = str_replace('{NOMEMUNICIPIO}', $municipiologado, $content);
$content = str_replace('{MODULO}', $menuModulo, $content);
$content = str_replace('{MENU}', $menulateral, $content);
$content = str_replace('{MURAL}', $mural, $content);
$content = str_replace('{MSG}', $msg, $content);
$content = str_replace('{VALIDACAO}', $validar, $content);
$content = str_replace('{AVALIACAO}', $avaliacao, $content);
$content = str_replace('{EMPRESA}', $sigla_empresa, $content);

// #################### fim NovoCeres #####################
// $content = str_replace('{MENU}', $menu_string, $content);
//$content = str_replace ( '{username}', TSession::getValue ( 'username' ), $content );
//$content = str_replace ( '{frontpage}', TSession::getValue ( 'frontpage' ), $content );
$css = TPage::getLoadedCSS();
$js = TPage::getLoadedJS();
$content = str_replace('{HEAD}', $css . $js, $content);

if (isset($_REQUEST ['class']) and TSession::getValue('logged')) {
    $url = http_build_query($_REQUEST);
    $content = str_replace('//#javascript_placeholder#', "__adianti_load_page('engine.php?{$url}');", $content);
}
echo $content;

if( $_SESSION["validacao"] === '0' && $_SESSION["usuario_id"] === '' && !TSession::getValue('logged') )
{

  new TMessage( 'error', '<font color=red><b>Usu&aacute;rio ou Senha inv&aacute;lidos.</b></font><b> Tente novamente, se o erro persisti entre em contato com a equipe de Tecnologia da Informa&ccedil;&atilde;o (TI).</b>');
  $_SESSION["validacao"] = '';

}
