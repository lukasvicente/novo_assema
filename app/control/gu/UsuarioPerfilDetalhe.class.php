<?php
/*
 * classe UsuarioPerfilDetalhe
 * Cadastro de UsuarioPerfil: Contem a listagem e o formulario de busca
 */
//ini_set('display_errors', 1);
//ini_set('display_startup_erros', 1);
//error_reporting(E_ALL);

use Adianti\Database\TFilter1;

class UsuarioPerfilDetalhe extends TPage
{
    private   $form;     
    protected $datagrid;  
    protected $pageNavigation;
 
    public function __construct()
    {
        parent::__construct();

		
        $this->form = new BootstrapFormWrapper( new TQuickForm('form_perfil_usuario') );

        $panel = new TPanelGroup('Formulario de Usuario Socio');

        // cria os campos do formulario
        $codigo = new THidden('id');

        $usuario_id = new THidden('usuario_id');
        $usuario_id->setValue($_GET['fk']);

        $perfil_id  = new TDBCombo('perfil_id', 'pg_ceres', 'PerfilRecord', 'id', 'nome');


        $usuarioalteracao = new THidden('usuarioalteracao');
        $dataalteracao = new THidden('dataalteracao');

        TTransaction::open('pg_ceres');
 
        $cadastro = new UsuarioRecord(filter_input( INPUT_GET, 'fk' ));

        if ($cadastro) {
            $usuario = new TLabel($cadastro->nome_socio);
            $login = new TLabel($cadastro->login);

        }
 
        TTransaction::close();


        // cria um rotulo para o titulo
        $titulo = new TLabel('<div style="position:floatval; width: 200px;"> <b>* Campo obrigatorio</b></div>');
        $titulo->setFontFace('Arial');
        $titulo->setFontColor('red');
        $titulo->setFontSize(10);

        // define os campos
        $this->form->addQuickField( null, $codigo, 10 );
        $this->form->addQuickField( null, $usuario_id, 10 );
        $this->form->addQuickField(null, $usuarioalteracao, 10);
        $this->form->addQuickField(null, $dataalteracao, 10);
        $this->form->addQuickField("Socio ", $usuario,300 );
        $this->form->addQuickField("Login ", $login,200);
        $this->form->addQuickField("Perfil <font color=red><b>*</b></font>", $perfil_id );

        $this->form->addQuickField(null, $titulo, 50);

        $this->form->addQuickAction( 'Salvar', new TAction( array( $this, 'onSave' ) ), 'ico_save.png' );
        $this->form->addQuickAction('Voltar', new TAction(array('UsuarioSocioList', 'onLoad')), 'back_blue_arrow.png');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(800);
        
        // creates the datagrid columns
        //$column_id = new TDataGridColumn('id', 'Id', 'center', 50);
        $column_perfil = new TDataGridColumn('nome_perfil', ('Perfil'), 'left');

        $this->datagrid->addColumn($column_perfil);

        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        $action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('fa:trash-o red fa-lg');
        $action_del->setField('id');
        $action_del->setDid('usuario_id');
        $action_del->setFk('usuario_id');   

        $this->datagrid->addAction($action_del);

        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        $panel->add($this->form);
        $panel->style = 'width: 180%';
        $vbox = new TVBox;
        //$vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->alertBox);
        
        $vbox->add($panel);
        $vbox->add(TPanelGroup::pack('', $this->datagrid));
        $vbox->add($this->pageNavigation);

        parent::add($vbox);
        // adiciona a tabela a pagina
        //parent::add($this->form);
        
    }

    function onSave()
    {

        TTransaction::open('pg_ceres');
        $cadastro = $this->form->getData('UsuarioPerfilRecord');

        $cadastro->usuarioalteracao = $_SESSION['usuario'];
        $cadastro->dataalteracao = date("d/m/Y H:i:s");
        $dados = $cadastro->toArray();

        $msg = '';
        $icone = 'info';

        if (empty ($dados['usuario_id'])){
           $msg .= 'O Usuario deve ser informado.</br>';
        }

        
        if (empty ($dados['perfil_id'])){
           $msg .= 'O Perfil deve ser informada.';
        }
        

        try{

            if ($msg == ''){
              $cadastro->store();
              $msg = 'Dados armazenados com sucesso';
              TTransaction::close();

            }else{
                $icone = 'error';
            }

            if ($icone == 'error'){

            }else{
                
                $param = array();
                $param['fk'] = $dados['usuario_id'];
                new TMessage("info", "Registro salvo com sucesso!");
                TApplication::gotoPage('UsuarioPerfilDetalhe','onEdit', $param); // reload
                $this->form->setData($cadastro);   // fill the form with the active record data
                
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
            $this->form->setData($cadastro);
            
        }

    }
    
    function onReload($param = NULL)
    {
        
        TTransaction::open('pg_ceres');

        $repository = new TRepository('UsuarioPerfilRecord');
        $limit = 10;

        $criteria = new TCriteria;
        //$criteria->setProperty('order', 'nome');
        if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
        $criteria->setProperties($param);
        $criteria->setProperty('limit', $limit);
        $criteria->add(new TFilter('usuario_id', '=', filter_input ( INPUT_GET, 'fk' )));   
        // carrega os objetos de acordo com o criterio
        $cadastros = $repository->load($criteria);

        $this->datagrid->clear();
        if ($cadastros)
        {
            // percorre os objetos retornados
            foreach ($cadastros as $cadastro)
            {
                // adiciona o objeto na DataGrid
                $this->datagrid->addItem($cadastro);
            }
        }
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit

        // finaliza a transacao
        TTransaction::close();
        $this->loaded = true;
    }



    function onDelete($param)
    {
        // obtem o parametro $key
        $key=$param['key'];
        
        // define duas acoes
        $action1 = new TAction(array($this, 'Delete'));
        
        // define os parametros de cada acao
        $action1->setParameter('key', $key);
       
        //encaminha a chave estrangeira
        $action1->setParameter('fk', filter_input(INPUT_GET, 'fk'));

        // exibe um dialogo ao usuario
        new TQuestion('Deseja realmente excluir o registro ?', $action1, $action2);
    }
    
    /*
     * metodo Delete()
     * Exclui um registro
     */
    function Delete($param)
    {
        // obtem o parametro $key
        $key=$param['key'];
        // inicia transacao com o banco 'pg_ceres'
        TTransaction::open('pg_ceres');

        // instanicia objeto Record
        $cadastro = new UsuarioPerfilRecord($key);

        try{
            // deleta objeto do banco de dados
            $cadastro->delete();
			
			// exibe um dialogo ao usuario
            new TMessage("info", "Registro deletado com sucesso!");

            // finaliza a transacao
            TTransaction::close();
        }
        catch (Exception $e) // em caso de exce��o
        {
            // exibe a mensagem gerada pela exce��o
            new TMessage('error', $e->getMessage());
            // desfaz todas altera��es no banco de dados
            TTransaction::rollback();
        }

        // re-carrega a datagrid
        $this->onReload();
        // exibe mensagem de sucesso
        //new TMessage('info', "Registro Excluido com sucesso");
    }
    
    /*
     * metodo show()
     * Exibe a pagina
     */
    function show()
    {
        // carrega os dados no datagrid
        $this->onReload();
        //chama o metodo show da super classe
        parent::show();

    }

    function onEdit($param)
    {
		
		try {
            if (isset($param['key'])) {

                // get the parameter $key
                $key = $param['key'];

                TTransaction::open('pg_ceres');   // open a transaction with database 'samples'

                $object = new UsuarioPerfilRecord($key);        // instantiates object City

                $this->form->setData($object);   // fill the form with the active record data

                TTransaction::close();           // close the transaction
            } else {
                $this->form->clear();
            }
        } catch (Exception $e) { // in case of exception
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
		
    }

}
?>