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

class PerfilPaginaDetalhe extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;


    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_detalhe_paginaperfil');
        $this->form->setFormTitle('Detalhe das Paginas do Perfil');

        $codigo     = new THidden('id');

        $perfil_id = new THidden('perfil_id');
        $perfil_id->setValue($_GET['fk']);
            
        $pagina_id  = new TDBCombo('pagina_id', 'pg_ceres', 'PaginaRecord', 'id', 'nome');

        TTransaction::open('pg_ceres');
 
        $cadastro = new PerfilRecord(filter_input( INPUT_GET, 'fk' ));

        if ($cadastro) {
            $nome_perfil = ($cadastro->nome);
            $modulo = ($cadastro->nome_modulo);

        }
        // finaliza a transacao
        TTransaction::close();


        $this->form->addFields( [new TLabel('Perfil')], [$nome_perfil] );
        $this->form->addFields( [new TLabel('Modulo')], [$modulo] );
        $this->form->addFields( [new TLabel('Pagina')], [$pagina_id] );
        $this->form->addFields( [new TLabel('')], [$codigo] );
        $this->form->addFields( [new TLabel('')], [$perfil_id] );

        
        $this->form->addAction('Salvar', new TAction(array($this, 'onSave')), 'ico_save.png');
        $this->form->addAction('Voltar',  new TAction(array('PerfilList', 'onReload')), 'back_blue_arrow.png');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        
        // creates the datagrid columns
        //$column_id = new TDataGridColumn('id', 'Id', 'center', 50);
        $column_pagina = new TDataGridColumn('nome_pagina', ('Pagina'), 'left');
        $column_arquivo = new TDataGridColumn('nome_arquivo', ('Arquivo'), 'left');
        $column_grupomenu = new TDataGridColumn('nome_grupomenu', ('Grupo Menu'), 'left');



        // add the columns to the DataGrid
        //$this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_pagina);
        $this->datagrid->addColumn($column_arquivo);
        $this->datagrid->addColumn($column_grupomenu);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        $action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('fa:trash-o red fa-lg');
        $action_del->setField('id');
        $action_del->setDid('perfil_id');
        $action_del->setFk('perfil_id');
        $this->datagrid->addAction($action_del);

        
        
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

    function onSave()
    {

        TTransaction::open('pg_ceres');
        $cadastro = $this->form->getData('PerfilPaginaRecord');

        $cadastro->usuarioalteracao = $_SESSION['usuario'];
        $cadastro->dataalteracao = date("d/m/Y H:i:s");
        $dados = $cadastro->toArray();

        $msg = '';
        $icone = 'info';

        if (empty ($dados['perfil_id'])){
           $msg .= 'O Perfil deve ser informado.';
        }

        /*
        if (empty ($dados['pagina_id'])){
           $msg .= 'A Pagina deve ser informada.';
        }
        */

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
                $param['fk'] = $dados['perfil_id'];
                new TMessage("info", "Registro salvo com sucesso!");
                TApplication::gotoPage('PerfilPaginaDetalhe','onReload', $param); // reload
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

        $repository = new TRepository('PerfilPaginaRecord');
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
        $criteria->add(new TFilter('perfil_id', '=', filter_input ( INPUT_GET, 'fk' )));   
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
        $key=$param['key'];

        $action1 = new TAction(array($this, 'Delete'));

        $action1->setParameter('key', $key);
        $action1->setParameter('fk', $_GET['fk']);
        new TQuestion('Deseja realmente excluir o registro ?', $action1, $action2);
        
    }

    function Delete($param)
    {
        $key=$param['key'];
        TTransaction::open('pg_ceres');
        $cadastro = new PerfilPaginaRecord($key);

        try{
            $cadastro->delete();

            new TMessage("info", "Registro deletado com sucesso!");
            TTransaction::close();
            

        }
        catch (Exception $e) 
        {

            new TMessage('error', $e->getMessage());
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