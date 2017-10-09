<?php

/*
 * classe ModuloList
 * Cadastro de Modulo: Contem a listagem e o formulario de busca
 */
  
//ini_set('display_errors', 1);
//ini_set('display_startup_erros', 1);
//error_reporting(E_ALL);

use Adianti\Database\TFilter1;
//use Adianti\Widget\Datagrid\TDatagridTables;

class PerfilList extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;


    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_search_Perfil');
        $this->form->setFormTitle(('Listagem de Perfil'));

        $opcao = new TCombo('opcao');
        $nome = new TEntry('nome');

        $this->form->addFields( [new TLabel('Op&ccedil;&atilde;o')], [$opcao] );
        $this->form->addFields( [new TLabel('Busca')], [$nome] );

        $items = array();
        $items['nome'] = 'Nome';
 
        $opcao->addItems($items);
 
        $opcao->setValue('nome');
        $opcao->setSize('30%');
        $nome->setSize('30%');
        $nome->setProperty('placeholder', 'Informe o valor da busca');
        
        $this->form->addAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addAction(_t('New'),  new TAction(array('PerfilForm', 'onEdit')), 'bs:plus-sign green');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        
        // creates the datagrid columns
        //$column_id = new TDataGridColumn('id', 'Id', 'center', 50);
        $column_nome = new TDataGridColumn('nome', ('Nome'), 'left');
        $column_modulo = new TDataGridColumn('nome_modulo', ('Modulo'), 'left');
        


        // add the columns to the DataGrid
        //$this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_modulo);



        // creates the datagrid column actions
        //$order_id = new TAction(array($this, 'onReload'));
        //$order_id->setParameter('order', 'id');
        //$column_id->setAction($order_id);
        
        $order_name = new TAction(array($this, 'onReload'));
        $order_name->setParameter('order', 'name');
        $column_nome->setAction($order_name);
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('PerfilForm', 'onEdit'));
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:pencil-square-o blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        $action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('fa:trash-o red fa-lg');
        $action_del->setField('id');
        $this->datagrid->addAction($action_del);

        $action3 = new TDataGridAction(array('PerfilPaginaDetalhe', 'onReload'));
        $action3->setLabel('Pagina');
        $action3->setImage('fa:users green fa-lg');
        $action3->setField('id');
        $this->datagrid->addAction($action3);
        
        $action3->setFk('id');
        //$action3->setParameter('fk','id');
        //$action3->setParameter('fk', filter_input(INPUT_GET, 'fk'));
        // create the datagrid model

        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $container = new TVBox;
        $container->style = 'width: 90%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid));
        $container->add($this->pageNavigation);
        
        parent::add($container);
    }

    function onReload($param = NULL)
    {
        
        TTransaction::open('pg_ceres');

        $repository = new TRepository('PerfilRecord');
        $limit = 10;

        $criteria = new TCriteria;
        //$criteria->setProperty('order', 'nome');
        if (empty($param['order']))
            {
                $param['order'] = 'nome';
                $param['direction'] = 'asc';
            }
        $criteria->setProperties($param);
        $criteria->setProperty('limit', $limit);
            
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
 
    function onSearch()
    {
        // inicia transacao com o banco 'pg_ceres'
        TTransaction::open('pg_ceres');
        $repository = new TRepository('PerfilRecord');

        $campo = $this->form->getFieldData('opcao');
        $dados = $this->form->getFieldData('nome');

        $criteria = new TCriteria;
        $criteria->setProperty('order', 'nome');

        if ($dados) {
             if (is_numeric($dados)) {
                $criteria->add(new TFilter($campo, '=', $dados));
            } else {
                $criteria->add(new TFilter1('special_like(' . $campo . ",'" . $dados . "')"));
            }
        }
        $cadastros = $repository->load($criteria);

        $this->datagrid->clear();
        if ($cadastros)
        {
            foreach ($cadastros as $cadastro)
            {
                $this->datagrid->addItem($cadastro);
            }
        }
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
        $action1->setParameter('msg', 'delete');
        
        // exibe um dialogo ao usuario
        new TQuestion('Deseja realmente excluir o registro ?', $action1 );
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
        $cadastro = new PerfilRecord($key);

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
        $this->onReload();
 
    }

    function show()
    {
         
        $this->onReload();
        parent::show();

    }
}
?>