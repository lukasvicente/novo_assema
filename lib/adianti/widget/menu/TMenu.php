<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_erros', 1);
//error_reporting(E_ALL);

Namespace Adianti\Widget\Menu;

use Adianti\Widget\Menu\TMenuItem;
use Adianti\Widget\Base\TElement;
use SimpleXMLElement;
use Adianti\Database\TTransaction;
use Adianti\Database\TRepository;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use App\Model\Acesso_Servidor\Acesso_ServidorRecord;

//use App\Model\vw_usuario;
//use App\Model\vw_usuarioperfil;
//use App\Model\Acesso_ServidorRecord;

/**
 * Menu Widget
 *
 * @version    2.0
 * @package    widget
 * @subpackage menu
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TMenu extends TElement {

    private $items;
    private $menu_class;
    private $item_class;
    private $menu_level;

    /**
     * Class Constructor
     * @param $xml SimpleXMLElement parsed from XML Menu
     */
    public function __construct($xml, $permission_callback = NULL, $menu_level = 1, $menu_class = 'dropdown-menu', $item_class = '') {
        parent::__construct('ul');
        $this->items = array();

        $this->{'class'} = $menu_class . " level-{$menu_level}";
        $this->menu_class = $menu_class;
        $this->menu_level = $menu_level;
        $this->item_class = $item_class;

        if ($xml instanceof SimpleXMLElement) {
            $this->parse($xml, $permission_callback);
        }
    }

    /**
     * Add a MenuItem
     * @param $menuitem A TMenuItem Object
     */
    public function addMenuItem(TMenuItem $menuitem) {
        $this->items[] = $menuitem;
    }

    /**
     * Return the menu items
     */
    public function getMenuItems() {
        return $this->items;
    }

    /**
     * Parse a XMLElement reading menu entries
     * @param $xml A SimpleXMLElement Object
     * @param $permission_callback check permission callback
     */
    public function parse($xml, $permission_callback = NULL) {
        $i = 0;
        foreach ($xml as $xmlElement) {
            $atts = $xmlElement->attributes();
            $label = (string) $atts['label'];
            $action = (string) $xmlElement->action;
            $icon = (string) $xmlElement->icon;
            $menu = NULL;
            $menuItem = new TMenuItem($label, $action, $icon);

            if ($xmlElement->menu) {
                $menu = new TMenu($xmlElement->menu->menuitem, $permission_callback, $this->menu_level + 1, $this->menu_class, $this->item_class);
                $menuItem->setMenu($menu);
            }

            // just child nodes have actions
            if ($action) {
                if (!empty($action) AND $permission_callback) {
                    // check permission
                    $parts = explode('#', $action);
                    $className = $parts[0];
                    if (call_user_func($permission_callback, $className)) {
                        $this->addMenuItem($menuItem);
                    }
                } else {
                    // menus without permission check
                    $this->addMenuItem($menuItem);
                }
            }
            // parent nodes are shown just when they have valid children (with permission)
            else if (isset($menu) AND count($menu->getMenuItems()) > 0) {
                $this->addMenuItem($menuItem);
            }

            $i ++;
        }
    }

    static public function validaClasse($classe) {
        // inicia transacao com o banco 'pg_ceres'
        TTransaction::open('pg_ceres');
        // instancia um repositorio da Classe
        $repository = new TRepository('Vw_usuariopaginagrupo');

        // cria um criterio de selecao
        $criteria = new TCriteria;
        //filtra pelo campo arquivo
        $criteria->add(new TFilter('arquivo', '=', $classe));
        // carrega os objetos de acordo com o criterio
        $usuarios = $repository->load($criteria);
        if ($usuarios) {
            // percorre os objetos retornados
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static public function usuarioAtivo($usuario_id) {
        // inicia transacao com o banco 'pg_ceres'
        TTransaction::open('pg_ceres');
        // instancia um repositorio da Classe
        $acesso = new Acesso_servidorRecord($usuario_id);
        $acesso->dataacesso = date('d/m/Y');
        $acesso->store();
        // finaliza a transacao
        TTransaction::close();

        return TRUE;
    }

    static public function montaModulo($usuario_id) {

        // inicia transacao com o banco 'pg_ceres'
        TTransaction::open('pg_ceres');

        // instancia um repositorio da Classe
        $repository = new TRepository('vw_usuarioperfil');

        // cria um criterio de selecao
        $criteria = new TCriteria;
        //filtra pelo campo usuario_id
        $criteria->add(new TFilter('usuario_id', '=', $usuario_id));
        $criteria->setProperty('order', 'modulo');

        // carrega os objetos de acordo com o criterio
        $usuarios = $repository->load($criteria);
        // var_dump($criteria->dump());
        // exit();

        $menu = "";
        $menu .= "<option>SELECIONE O MODULO</option>";

        $socio_id = 0;

        if ($usuarios) {
            // percorre os objetos retornados
            foreach ($usuarios as $usuario) {
                // adiciona os dados do perfil do usuario no menu
                $menu .= "<option ";
                if ($_SESSION['modulo'] == $usuario->modulo)
                    $menu .= ' selected ';
                $menu .= "value='?modulo=" . $usuario->modulo . "' data-image=\"app/images/modulos/icon_" . $usuario->modulo . ".png\" data-title=" . $usuario->modulo . ">" . $usuario->modulo . "</option>";

                $socio_id = $usuario->socio_id;
                
            }
        }

        // finaliza a transacao
        TTransaction::close();

        /*
          //usuarioAtivo($usuario_id);
          // inicia transacao com o banco 'pg_ceres'
          TTransaction::open('pg_ceres');
          // instancia um repositorio da Classe
          $acesso = new Acesso_ServidorRecord($usuario_id);
          $acesso->id = $usuario_id;
          $acesso->servidor_id = $servidor_id;
          $acesso->dataacesso = date('d/m/Y');
          $acesso->store();
          // finaliza a transacao
          TTransaction::close();
         * 
         */

        return $menu;
    }

    /*
      static public function montaMenu($modulo, $usuario_id) {

      // inicia transacao com o banco 'pg_ceres'
      TTransaction::open('pg_ceres');

      // instancia um repositorio da Classe
      $repository = new TRepository('vw_usuariopagina');

      // cria um criterio de selecao
      $criteria = new TCriteria;
      //filtra pelo campo usuario_id
      $criteria->add(new TFilter('usuario_id', '=', $usuario_id));
      //filtra pelo campo modulo
      $criteria->add(new TFilter('modulo', '=', $modulo));

      // carrega os objetos de acordo com o criterio
      $usuarios = $repository->load($criteria);
      $menu='';
      if ($usuarios) {
      $menu = '<ul id="nav">';
      // percorre os objetos retornados
      foreach ($usuarios as $usuario) {
      // adiciona os dados do perfil do usuario no menu
      $menu .= "<li><a href=\"#\" OnClick=\"document.location='?class=".$usuario->arquivo."'\">".$usuario->pagina."</a></li>";
      }
      $menu .= '</ul>';
      }
      // finaliza a transacao
      TTransaction::close();

      return $menu;

      }
     */

    static public function montaMenu($modulo, $usuario_id) {

        // inicia transacao com o banco 'pg_ceres'
        TTransaction::open('pg_ceres');

        $repositorygrupo = new TRepository('vw_grupomenuusuario');

        $criteria1 = new TCriteria;

        //filtra pelo campo usuario_id
        $criteria1->add(new TFilter('usuario_id', '=', $usuario_id));
        //filtra pelo campo modulo
        $criteria1->add(new TFilter('modulo', '=', $modulo));

        $criteria1->setProperty('order', 'grupo');

        $grupos = $repositorygrupo->load($criteria1);

        $menu = '';
        if ($grupos) {
            $id = '';
            $menu .= '<ul class="sidebar-menu">';
            foreach ($grupos as $grupo) {
                $id = $grupo->id;
                // adiciona os dados do perfil do usuario no menu
                $menu .= "<li class=\"parent green\" ><a href=\"index.html\"><span class=\"menu-icon\"><i class='" . $grupo->icone . "'></i></span> <span class=\"menu-text\">" . $grupo->grupo . "</span></a>";

                // instancia um repositorio da Classe
                $repository = new TRepository('vw_usuariopaginagrupo');

                // cria um criterio de selecao
                $criteria = new TCriteria;
                //filtra pelo campo usuario_id
                $criteria->add(new TFilter('usuario_id', '=', $usuario_id));
                //filtra pelo campo modulo
                $criteria->add(new TFilter('modulo', '=', $modulo));
                //filtra pelo campo
                $criteria->setProperty('order', 'grupo');
                $criteria->setProperty('order', 'pagina');

                // carrega os objetos de acordo com o criterio
                $usuarios = $repository->load($criteria);
                if ($usuarios) {
                    // percorre os objetos retornados
                    $menu .= '<ul class="treeview-menu level-2">';
                    foreach ($usuarios as $usuario) {
                        if ($usuario->grupo_id == $id) {
                            // adiciona os dados do perfil do usuario no menu
                            if ($usuario->arquivo == 'AtualizaForm') {
                                $menu .= "<li><a href=\"#\" OnClick=\"document.location='?class=" . $usuario->arquivo . "&method=onEdit&key=" . $_SESSION['servidor_id'] . "&fk=" . $_SESSION['servidor_id'] . "'\">" . $usuario->pagina . "</a></li>";
                            } else {
                                if ($usuario->arquivoleitura == 'SIM' && $usuario->novajanela == 'SIM') {
                                    $menu .= "<li><a target=\"_blank\" href=\"http://servicos.emater.rn.gov.br/novoceres/" . $usuario->arquivo . "\">" . $usuario->pagina . "</a></li>";
                                } else {
                                    if ($usuario->novajanela == 'NAO') {
                                        $menu .= "<li><a href=\"#\" OnClick=\"document.location='?class=" . $usuario->arquivo . "'\">" . $usuario->pagina . "</a></li>";
                                    } else {
                                        $menu .= "<li><a href=\"#\" OnClick=\"javascript:window.open('?class=" . $usuario->arquivo . "');\">" . $usuario->pagina . "</a></li>";
                                    }
                                }
                            }
                        }
                    }

                    $menu .= '</ul>';
                }

                $menu .= "</li>";
            }

            $menu .= '</ul>';
        }
        // finaliza a transacao
        TTransaction::close();

        return $menu;
    }

    static public function montaError() {

        return 'teste';
    }

    static public function montaMural() {
        $mural = "";

        /*
if ($_GET["modulo"] == 'DIRECAO') {
            TTransaction::open('pg_ceres');

            // instancia um repositorio para aniversariantes
            $repository = new TRepository('vw_dash_monta_listagem_dashboardRecord');

            // cria um criterio de selecao, ordenado pelo id
            $criteria = new TCriteria;

            //filtra pelo campo avaliador_id
            $criteria->add(new TFilter('login', '=', $_SESSION['usuario']));

            $results = $repository->load($criteria);

            if ($results) {
                $contador = 0;
                $mural .= "<br><h2 align='center'>DASHBOARDS</h2><br><table cellpadding='0' cellspacing='0' border='0' class='display' id='example' style='margin-left:10px;'>";
                // percorre os objetos retornados
                foreach ($results as $result) {
                    if (($contador == 0) || (($contador % 4) == 0)) {
                        $mural .= "<tr id='tritensdashboard'>";
                    }
                    // $mural .= "";
                    $mural .= "
                        <div class='col-xs-12 col-sm-12 col-md-4 col-lg-4'>
                        <div class='box social-stats' style='align: center'>
                            <div class='title-bar'>
                                <i class='fa fa-users'></i>{$result->modulo}
                                <div class='close-box'>
                                    <a href='#'><i class='fa fa-times-circle-o'></i></a>
                                </div>
                            </div>
                            <ul>
                                <li>
                                <a style='text-decoration:none;color:#000; font-weight:bold; font-size:13px; width: 170px; height: 165px;' href='index.php?class={$result->arquivo}&dash={$result->grafico_id}'>
                                        <div id='dash'>
                                          <center>
                                            <img src='app/images/dash_tipo_{$result->tipo}.png' width='120' height='110' title='{$result->nome}' alt='{$result->nome}'/>
                                            <img src='app/images/icon_{$result->modulo}.png' width='50' height='50'/>
                                            <p>{$result->nome}</p>
                                          </center>
                                        </div>
                                   </a>
                                </li>
                            </ul>
                        </div>
                        </div>";

                    $mural .= "
                            
                         <div class='col-xs-12 col-sm-12 col-md-4 col-lg-4'>
                        <div class='boxed no-padding'>
                        <!-- Title Bart Start -->
                        <div class='title-bar white'>
                            <h4>{$result->modulo}</h4>
                                <ul class='actions'>
                                    <li><a href='#' class='close-box'><i class='fa fa-chevron-up'></i></a></li>
                                    <li><a href='#' class='remove-box'><i class='fa fa-times-circle-o'></i></a></li>
				</ul>
			</div>
                        <!-- Title Bart End -->
									
                        <div class='inner'>
                            <!-- Google Maps Example End -->
                            <div class='dashboard-container'>
                            <ul>
                                <li>
                                <a style='text-decoration:none;color:#000; font-weight:bold; font-size:13px; width: 170px; height: 165px;' href='index.php?class={$result->arquivo}&dash={$result->grafico_id}'>
                                        <div id='dash'>
                                          <center>
                                            <img src='app/images/dash_tipo_{$result->tipo}.png' width='120' height='110' title='{$result->nome}' alt='{$result->nome}'/>
                                            <img src='app/images/icon_{$result->modulo}.png' width='50' height='50'/>
                                            <p>{$result->nome}</p>
                                          </center>      
                                        </div>
                                   </a>
                                </li>
                            </ul>
                            </div>
                            <!-- Google Maps Example End -->
                        </div>
                    </div></div>";
                    $contador++;
                }
                $mural .= '</table>';
            }

            

              $mural .= "<br><br><table cellpadding='0' cellspacing='0' border='0' class='display' id='example' style='margin-left:10px;'>
              <tr id='tritensdashboard'>
              <td id='tditemdashboard' align='center' valign='middle'>
              <a href='index.php?class=DashBeneficiarioGraficoList'><img src='app/images/bar_chart.png' width='250' height='200' alt='dashboard_sales'/>
              <p>Dashboard Teste 01</p></a>
              </td>
              <td id='tditemdashboard' align='center' valign='middle'>
              <img onclick='alert('Dashboard 02')' src='app/images/bar_chart.png' width='250' height='200' alt='dashboard_sales'/>
              <p>Dashboard Teste 02</p>
              </td>
              <td id='tditemdashboard' align='center' valign='middle'>
              <img onclick='alert('Dashboard 03')' src='app/images/bar_chart.png' width='250' height='200' alt='dashboard_3'/>
              <p>Dashboard Teste 03</p>
              </td>
              </tr>
              <tr id='tritensdashboard'>
              <td id='tditemdashboard' align='center' valign='middle'>
              <img onclick='alert('Dashboard 04')' src='app/images/bar_chart.png' width='250' height='200' alt='dashboard_sales'/>
              <p>Dashboard Teste 04</p>
              </td>
              <td id='tditemdashboard' align='center' valign='middle'>
              <img onclick='alert('Dashboard 05')' src='app/images/bar_chart.png' width='250' height='200' alt='dashboard_sales'/>
              <p>Dashboard Teste 05</p>
              </td>
              <td id='tditemdashboard' align='center' valign='middle'>
              <img onclick='alert('Dashboard 06')' src='app/images/bar_chart.png' width='250' height='200' alt='dashboard_3'/>
              <p>Dashboard Teste 06</p>
              </td>
              </tr>
              </table>";

              $modulo = file_get_contents('dashboard.html');
              echo str_replace('#dashboards#', null, $modulo); 
        } else if ($_SESSION["tipousuario"] == 'LATICINIO') {
            $mural = '<br><br><table cellpadding="0" cellspacing="0" border="0" class="display" id="example">';
            $mural .= "<thead>";
            $mural .= "<center><b><h3>AVISOS</h3></b></center>";
            $mural .= '<tr><th>Data</th><th>Aviso</th><th>Link</th></tr>';
            $mural .= "</thead>";
            $mural .= "<tr class=\"gradeA\"><td><font color='ff0000'>08/10/10</font></td><td>Video da NF</td><td><font color='ff0000'><a href='video/nflaticinio/'>NF Laticinio</a></font></td></tr>";
            $mural .= "<tr class=\"gradeA\"><td><font color='ff0000'>23/11/10</font></td><td><font color='ff0000'><b>SISTEMA LIBERADO PARA ALIMENTAÇÃO DAS NOTAS</b></font></td><td><font color='ff0000'><a href=''></a></font></td></tr>";
            $mural .= '</table><br><br>';
            $mural .= "<b>Telefones Suporte:</b>";
            $mural .= "<br><b>LEITE: 3232-1126/1127</br>GIN: 3232-2198</b>";
            $mural .= "<br><b>EMAIL: novoprogramadoleite@rn.gov.br</b>";
        } else if ($_SESSION["tipousuario"] == 'COLABORADORLEITE') {
            $mural = '<br><br><table cellpadding="0" cellspacing="0" border="0" class="display" id="example">';
            $mural .= "<thead>";
            $mural .= "<center><b><h3>AVISOS</h3></b></center>";
            $mural .= '<tr><th>Data</th><th>Aviso</th><th>Link</th></tr>';
            $mural .= "</thead>";
            $mural .= "<tr class=\"gradeA\"><td><font color='ff0000'>07/11/13</font></td><td>Resolução 61 10/2013 MDS</td><td><font color='ff0000'><a href='app.docs/Resolucao_61_10_2013_mds.pdf' target=_blank>link</a></font></td></tr>";
            $mural .= "<tr class=\"gradeA\"><td><font color='ff0000'>07/11/13</font></td><td>Resolução 001/2013 CONSEA-RN</td><td><font color='ff0000'><a href='app.docs/resolucao_001_2013_consearn.doc' target=_blank>link</a></font></td></tr>";
            $mural .= "<tr class=\"gradeA\"><td><font color='ff0000'>07/11/13</font></td><td>Checklist Programa do Leite</td><td><font color='ff0000'><a href='app.docs/checkList_programadoleite.docx' target=_blank>link</a></font></td></tr>";
            //$mural .= "<tr class=\"gradeC\"><td><font color='ff0000'>08/10/10</font></td><td>Video da NF</td><td><font color='ff0000'><a href='video/nflaticinio/'>NF Laticinio</a></font></td></tr>";
            $mural .= "<tr class=\"gradeA\"><td><font color='ff0000'><b>26/08/13</b></font></td><td><font color='ff0000'><b>DADOS DO CADASTRO UNICO ATUALIZADO ATE JUNHO/2013</b></font></td><td><font color='ff0000'><a href=''></a></font></td></tr>";
            $mural .= '</table><br><br>';
            $mural .= "<b>Telefones Suporte:</b>";
            $mural .= "<br><b>LEITE: 3232-1126/1127</br>GIN: 3232-2198</b>";
            $mural .= "<br><b>EMAIL: novoprogramadoleite@rn.gov.br</b>";
        } else {

            TTransaction::open('pg_ceres');

            // instancia um repositorio para DASHBOARDS
            $repository = new TRepository('vw_dash_monta_listagem_dashboardRecord');

            // cria um criterio de selecao, ordenado pelo id
            $criteria = new TCriteria;

            //filtra pelo campo USUARIO
            $criteria->add(new TFilter('login', '=', $_SESSION['usuario']));

            //if (isset($_SESSION['modulo'])) {
            //$criteria->add(new TFilter('modulo', '=', $_SESSION['modulo']));
            $criteria->add(new TFilter('modulo', '=', filter_input(INPUT_GET, 'modulo')));

            $results = $repository->load($criteria);

            //}
            if ($results) {

                $contador = 0;
                $mural .= "<br><h2 align='center'>DASHBOARDS</h2><br><table cellpadding='0' cellspacing='0' border='0' class='display' id='example' style='margin-left:10px;'>";
                // percorre os objetos retornados
                foreach ($results as $result) {
                    if (($contador == 0) || (($contador % 4) == 0)) {
                        $mural .= "<tr id='tritensdashboard'>";
                    }
                    $mural .= "
                         <div class='col-xs-12 col-sm-12 col-md-4 col-lg-4'>
                        <div class='boxed no-padding'>
                        <!-- Title Bart Start -->
                        <div class='title-bar white'>
                            <h4>{$result->modulo}</h4>
                                <ul class='actions'>
                                    <li><a href='#' class='close-box'><i class='fa fa-chevron-up'></i></a></li>
                                    <li><a href='#' class='remove-box'><i class='fa fa-times-circle-o'></i></a></li>
				</ul>
			</div>
                        <!-- Title Bart End -->
									
                        <div class='inner'>
                            <!-- Google Maps Example End -->
                            <div class='dashboard-container'>
                            <ul>
                                <li>
                                <a style='text-decoration:none;color:#000; font-weight:bold; font-size:13px; width: 170px; height: 165px;' href='index.php?class={$result->arquivo}&dash={$result->grafico_id}'>
                                        <div id='dash'>
                                           <center>
                                            <img src='app/images/dash_tipo_{$result->tipo}.png' width='120' height='110' title='{$result->nome}' alt='{$result->nome}'/>
                                            <img src='app/images/icon_{$result->modulo}.png' width='50' height='50'/>
                                            <p>{$result->nome}</p>
                                           </center>    
                                        </div>
                                   </a>
                                </li>
                            </ul>
                            </div>
                            <!-- Google Maps Example End -->
                        </div>
                    </div></div>";
                    $contador++;
                }
                $mural .= '</table>';
            } else {

                TTransaction::open('pg_ceres');

                // instancia um repositorio para aniversariantes
                $repository = new TRepository('vw_aniversariantes_do_mesRecord');
                // $repository = new TRepository('vw_servidor_por_cidades');
                // cria um criterio de selecao, ordenado pelo id
                $criteria = new TCriteria;

                $criteria->setProperty('order', 'nome');
                //$criteria->setProperty('order', 'qtd');
                // carrega os objetos de acordo com o criterio
                $aniversariantes = $repository->load($criteria);

                $repository2 = new TRepository('vw_painel_regionalRecord');
                $criteria2 = new TCriteria();
                $criteria2->add(new TFilter('municipio_id', '=', $_SESSION['municipio_id']));

                $painel_info = $repository2->load($criteria2);
                if ($painel_info) {
                    foreach ($painel_info as $info) {

                        $painel = '
					<!-- INICIO DO PAINEL -->
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<!-- Row Start -->
					<div class="row">

						<div class="col-xs-12 col-sm-6 col-md-4 col-lg-12">
						<!-- Projetos -->
						  <div class="social-box twitter">

							<div class="icon-container">
							  <div class="inner">
								<img src="app/images/icons/siap.png" />
							  </div>
							</div>
							<div class="text-container">
							
							  <div class="inner">
								<span>' . $info->n_pj_contratados . '</span> Projetos Contratados
							  </div>
							</div>
						  </div>
						  <!-- Projetos -->
						</div>
				
				
						<div class="col-xs-12 col-sm-6 col-md-4 col-lg-12">
						  <!-- Social Box - Facebook -->
						  <div class="social-box facebook">

							<div class="icon-container">
							  <div class="inner">
								<img src="app/images/icons/produtor.png" />
							  </div>
							</div>

							<div class="text-container">
							  <div class="inner">
								<span>' . $info->n_produtores . '</span> Produtores
							  </div>
							</div>
						  </div>
						  <!-- Social Box - Facebook End -->
						</div>
						
						<div class="col-xs-12 col-sm-6 col-md-4 col-lg-12">
						  <!-- Social Box - Dribble -->
						  <div class="social-box dribbble">

							<div class="icon-container">
							  <div class="inner">
								<img src="app/images/icons/propriedade.png" />
							  </div>
							</div>

							<div class="text-container">
							  <div class="inner">
								<span>' . $info->n_propriedades . '</span> Propriedades
							  </div>
							</div>
						  </div>
						  <!-- Social Box - Dribble End -->
						</div>
						
						<div class="col-xs-12 col-sm-6 col-md-4 col-lg-12">
						  <!-- Social Box - Projects -->
						  <div class="social-box projects">

							<div class="icon-container">
							  <div class="inner">
								<img src="app/images/icons/barragem.png" />
							  </div>
							</div>

							<div class="text-container">
							  <div class="inner">
								<span>' . $info->n_barragens . '</span> Barragens
							  </div>
							</div>
						  </div>
						  <!-- Social Box - Projects End -->
						</div>
						
						<div class="col-xs-12 col-sm-6 col-md-4 col-lg-12">

						<!-- Social Box - Projects -->
							<div class="social-box tasks">
								<div class="icon-container">
									<div class="inner">
										<img src="app/images/icons/semente.png" />
									</div>
								</div>

								<div class="text-container">
									<div class="inner">
										<span>' . $info->n_bancossementes . '</span> Bancos de sementes
									</div>
								</div>
							</div>
						<!-- Social Box - Projects End -->

						</div>
						
						<div class="col-xs-12 col-sm-6 col-md-4 col-lg-12">

						  <!-- Social Box - Projects -->
						  <div class="social-box posts">

							<div class="icon-container">
							  <div class="inner">
									<img src="app/images/icons/carro.png" />
							  </div>
							</div>

							<div class="text-container">
							  <div class="inner">
								<span>' . $info->n_veiculos . '</span> Veículos
							  </div>
							</div>
						  </div>
						  <!-- Social Box - Projects End -->

						</div>
					</div>
					</div>
					
					<!-- FIM DO PAINEL -->';
                    }
                }

                $repository = new TRepository('vw_processo_mov_setorRecord');

                $criteria = new TCriteria;
                $criteria->setProperty('order', 'id DESC');
                $criteria->add(new TFilter(' (SELECT * FROM public.vw_setorservidorprocesso s WHERE s.servidor_id = ' . $_SESSION['servidor_id'] . '  AND s.setor_id = setordestino_id) ', 'EXISTS', null));

                $processo = $repository->load($criteria);

                if ($processo) {


                    // new TMessage('info', 'Teste' );
                }


//CHAT AVISO


                $repositoryAviso = new TRepository('Vw_chat_avisoRecord');
                $criteriaAviso = new TCriteria();
                $criteriaAviso->add(new TFilter('servidor_id_recipient', '=', $_SESSION['servidor_id']));

                $avisosChat = $repositoryAviso->load($criteriaAviso);

                if ($avisosChat) {

                    $s = ' <script type="text/javascript">

                        function re(){ location.reload(); }

                        window.onload=function(){';

                    foreach ($avisosChat as $avisoC) {

                        $s .= ' 
                          setInterval(
                            function ivi(){
                              if( document.getElementById(' . $avisoC->servidor_id . ').style.display == "block" ){
                                document.getElementById(' . $avisoC->servidor_id . ').style.display = "none";
                              }else{
                                document.getElementById(' . $avisoC->servidor_id . ').style.display = "block";
                              } 
                            },
                          700); ';
                    }

                    $s .= ' } </script>';

                    echo $s;
                }

//    
//CHAT AVISO
                //$repository3 = new TRepository('vw_ultimos_acessosRecord');
                $repository3 = new TRepository('vw_acesso_ServidorRecord');

                $criteria3 = new TCriteria();
                $criteria3->add(new TFilter('online', '=', '1'));
                $criteria3->setProperty('order', 'online DESC, dataacesso, horaacesso   ');
                //$online_info = $repository3->load($criteria3);

                $online_info = $repository3->load($criteria3);

                $online = ' 
				<!-- INICIO ULTIMOS ACESSOS -->
				<div style="height:300px; overflow: auto">
							<ul >';

                if ($online_info) {

                    foreach ($online_info as $onlines) {
                        //verificar se o usuario esta logado nao abrir o chat para ele mesmo
                        if ($_SESSION['servidor_id'] != $onlines->servidor_id) {

                            if ($onlines->online == 1) {
                                $statuschat = 'online';
                            } else {
                                $statuschat = 'offline';
                            }

                            if (file_exists('app/images/servidor/servidor_' . $onlines->servidor_id . '.jpg')) {
                                $fotoservidor = $onlines->servidor_id;
                            } else {
                                $fotoservidor = 'avatar';
                            }

                            $online .= '<li>
                            <div>              
                                        <div id=' . $onlines->servidor_id . ' class="notify-chat"> <i class="fa fa-envelope-o"></i> </div>
                                        <div class="user-avatar"><img width="23" src="app/images/servidor/servidor_' . $fotoservidor . '.jpg" /> </div>
                                        <span class="username" OnClick="re();"><a  href=/chatbox/index.php?username=' . $_SESSION['servidor_id'] . '&recipient=' . $onlines->servidor_id . ' target=_blank > ' . substr($onlines->nomecurto, 0, 22) . '.</a></span>
                                        <span class="status ' . $statuschat . '">&nbsp;</span>
                            </div>
                                       </li>';
                        }
                    }
                }

                $online .= '</ul>
							</div>
				<!-- FIM ULTIMOS ACESSOS -->';

                $mural = '';    

                //if ($aniversariantes) {

                $mural = '<div class="row">';
                $mural .= $painel;
                $mural .= '<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">';
                $mural .= '<div class="row" id="step4">';
                $mural .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">';
                $mural .= '					
							<!-- Google Maps Start -->
							<div class="boxed no-padding">

									<!-- Title Bart Start -->
									<div class="title-bar white">
									  <h4>Previsão do Tempo</h4>
									  <ul class="actions">
										<li><a href="#" class="close-box"><i class="fa fa-chevron-up"></i></a></li>
										<li><a href="#" class="remove-box"><i class="fa fa-times-circle-o"></i></a></li>
									  </ul>
									</div>
									<!-- Title Bart End -->
									
									
								<div class="inner">
							
									<!-- Google Maps Example End -->
									<div class="google-maps-container">
									  <div id="map-canvas"></div>
									</div>
									<!-- Google Maps Example End -->

								</div>
							</div>
							<!-- Google Maps End -->';


                $mural .= '<!-- Aniversariantes Start -->
							<div class="box social-stats">
								
								<div class="title-bar">
									<i class="fa fa-users"></i>Aniversariantes do Dia 
									  <div class="close-box">
										<a href="#"><i class="fa fa-times-circle-o"></i></a>
									  </div><div style="float: right; margin-right: 15px">' . date("d/m/Y") . '</div>
								</div>';
                $mural .= '<ul>';

                foreach ($aniversariantes as $aniversariante) {
                    if (date("d") == $aniversariante->dia) {
                        $mural .= '<li><i class="fa fa-arrow-right"></i>' . $aniversariante->nome . '<span>' . $aniversariante->cidade . '</span></li>';
                    }
                }
                $mural .= '</ul>';

                $mural .= '</div>
							<!-- Aniversariantes End -->';
                $mural .= '<!-- Daily Sales Start -->
                <div class="box tasks">
               <!-- Title Bar Start -->
                  <div class="title-bar">
                    <i class="fa fa-comments"></i>Emater na mídia
                      <div class="close-box">
                        <a href="#"><i class="fa fa-times-circle-o"></i></a>
                      </div>
                  </div>
                  <!-- Title Bar End -->
                 ';
                $mural .= "<ul>";
                                  $feed = file_get_contents('http://news.google.com.br/news?pz=1&cf=all&ned=pt-BR_br&hl=pt-BR&output=rss&q=emater+rn&oq=emater+rn');
                  $rss = new SimpleXmlElement($feed);
                  foreach ($rss->channel->item as $entrada) {
                  $mural .= " <li><a target=\"_blank\" href=\"$entrada->link\" title=\"$entrada->title\"><font color='green'>$entrada->title</font></a><span>$entrada->pubDate</span></li>";
                  }
                 * 
                 
                $mural .= "</ul>";

                $mural .= '
                  <!-- Sales List End -->

              </div>
              <!-- Daily Sales End -->
';

                $mural .= '</div>';

                $mural .= '</div>';

                $mural .= '</div>';

                $mural .= '<!-- Right Sidebar Start -->
				<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
				<div class="row">

				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				
					<!-- Open Tasks Start -->
					<div class="box projects">

					<!-- Title Bar Start -->
					<div class="title-bar">
						<i class="fa fa-twitter"></i>@EmaterRN
						<div class="close-box">
                        <a href="#"><i class="fa fa-times-circle-o"></i></a>
                      </div>
					</div>
					<!-- Title Bar End -->

					<!-- Stats List Start -->';


                $mural .= '
					<a target="_blank" class="twitter-timeline" href="https://twitter.com/EmaterRN" data-widget-id="463667206736183296">Tweets de @EmaterRN <img src="app/images/loading.gif"/></a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
					';
                $mural .= '
					<!-- Stats List End -->

					</div>
					<!-- Open Tasks End -->
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6"></div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                <!-- Whos online Start -->
                <div class="box whos-online">
					<!-- Title Bar Start -->
					<div class="title-bar"><i class="fa fa-user"></i>Últimos Acessos (' . date("d/m/Y") . ')
									  <div class="close-box">
										<a href="#"><i class="fa fa-times-circle-o"></i></a>
									  </div></div>
					<!-- Title Bar End -->

                 ';

                $mural .= $online;

                $mural .= '</div>';

                $mural .= '</div>';

                $mural .= '</div>';

                $mural .= '</div>';

                $mural .= '</div>';


                //}
                // finaliza a transacao
                TTransaction::close();
            }
        }

        $negado = '<div class="error-404 text-center">
            <i class="fa fa-frown-o"></i>
            <h1>Whooops!</h1>
            <h4>Você não tem permissão para acessar esta página</h4>
            
            <p>para continuar <a href="index.php">clique aqui</a></p>
          </div>';


        if (isset($_REQUEST['acesso']) == 'negado') {
            return $negado;
        } else {

        TTransaction::open('pg_ceres');

        $repository = new TRepository('UsuarioRecord');
        $count = $repository->count(); 
 
        TTransaction::close();

            $mural .= '
    <div class="col-lg-4 col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-male fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">'. $count .'</div>
                            <div>Total de Usuario</div>
                        </div>
                    </div>
                </div>
                <a href="index.php?class=UsuarioSocioList">
                    <div class="panel-footer">
                        <span class="pull-left">Detalhes</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>';

            return $mural;
        //}
*/

            TTransaction::open('pg_ceres');

                // instancia um repositorio para aniversariantes
                $repository = new TRepository('vw_aniversariantes_do_mesRecord');

                $criteria = new TCriteria;
                $criteria->setProperty('order', 'nome');
                $aniversariantes = $repository->load($criteria);
                TTransaction::close();
                if ($aniversariantes) {



                $mural .= '<!-- Aniversariantes Start -->
                            <div class="box social-stats">
                                
                                <div class="title-bar">
                                    <i class="fa fa-users"></i>Aniversariantes do Dia 
                                      <div class="close-box">
                                       
                                      </div><div style="float: right; margin-right: 15px">' . date("d/m/Y") . '</div>
                                </div>';
                $mural .= '<ul>';

                foreach ($aniversariantes as $aniversariante) {
                    if (date("d") == $aniversariante->dia) {
                        $mural .= '<li><i class="fa fa-arrow-right"></i>' . $aniversariante->nome ." / ". '<span>' . $aniversariante->cidade . '</span></li>';
                    }
                }
                $mural .= '</ul>';

                $mural .= '</div>
                            <!-- Aniversariantes End -->';
                }

         TTransaction::open('pg_ceres');

        $repository = new TRepository('SocioRecord');
        $socio = $repository->count(); 
 
        TTransaction::close();

            $mural .= '
    <div class="col-lg-4 col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-male fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">'. $socio .'</div>
                            <div>Total de Socio</div>
                        </div>
                    </div>
                </div>
                <a href="index.php?class=SocioList">
                    <div class="panel-footer">
                        <span class="pull-left">Detalhes</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>';

        TTransaction::open('pg_ceres');

        $repository = new TRepository('UsuarioRecord');
        $count = $repository->count(); 
 
        TTransaction::close();

            $mural .= '
    <div class="col-lg-4 col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-user fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">'. $count .'</div>
                            <div>Total de Usuario</div>
                        </div>
                    </div>
                </div>
                <a href="index.php?class=UsuarioSocioList">
                    <div class="panel-footer">
                        <span class="pull-left">Detalhes</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>';
        return $mural;

    }



    static public function montaAvaliacao() {

        TTransaction::open('pg_ceres');

        // instancia um repositorio para aniversariantes
        $repository = new TRepository('vw_avaliacao_servidoresRecord');

        // cria um criterio de selecao, ordenado pelo id
        $criteria = new TCriteria;
        //filtra pelo campo avaliador_id
        $criteria->add(new TFilter('avaliador_id', '=', $_SESSION['servidor_id']));
        // carrega os objetos de acordo com o criterio
        $results = $repository->load($criteria);

        $avaliacao = '';

        if ($results) {

            $avaliacao = '<table cellpadding="0" cellspacing="0" border="0" class="display" id="example2">';
            $avaliacao .= "<thead>";
            $avaliacao .= '<center><b><h3>AVALIAÇÕES PENDENTES</h3></b></center>';
            $avaliacao .= '<tr><th>Avaliação</th><th>Servidor Avaliado</th><th>Lotação</th><th>Data de Conclusão da Avaliação</th><th>Responder</th></tr>';
            $avaliacao .= "</thead>";
            // percorre os objetos retornados
            foreach ($results as $result) {


                $avaliacao .= "<tr class=\"gradeC\"><td>" . $result->nomeavaliacao . '</td><td>' . $result->servidoravaliado . '</td><td>' . $result->lotacaoavaliado . '</td><td>' . $result->datafim . "</td><td><font color='ff0000'><a href='index.php?class=QuestionarioAvaliacaoServidorForm&avaliacao_id=" . $result->avaliacao_id . "&avaliacaoservidor_id=" . $result->avaliacaoservidor_id . "&ordem=1'>Responder Avaliação</a></font></td></tr>";
            }
            $avaliacao .= '</table>';
        }

        //$avaliacao = '';
        // finaliza a transacao
        TTransaction::close();

        return $avaliacao;
    }

    /**
     * Shows the widget at the screen
     */
    public function show() {
        if ($this->items) {
            foreach ($this->items as $item) {
                parent::add($item);
            }
        }
        parent::show();
    }

}
