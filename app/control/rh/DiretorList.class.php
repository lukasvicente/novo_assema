<?php

/*
 * classe SocioList
 * Cadastro de Socio: Contem a listagem e o formulario de busca
 */
  
//ini_set('display_errors', 1);
//ini_set('display_startup_erros', 1);
//error_reporting(E_ALL);

use Adianti\Database\TFilter1;
//use Adianti\Widget\Datagrid\TDatagridTables;

class DiretorList extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;


    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_search_TipoDiretor');
        $this->form->setFormTitle(('Listagem de Diretor'));

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
        $this->form->addAction(_t('New'),  new TAction(array('DiretorForm', 'onEdit')), 'bs:plus-sign green');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        
        // creates the datagrid columns
        //$column_id = new TDataGridColumn('id', 'Id', 'center', 50);
        $column_name = new TDataGridColumn('nome_socio', ('Diretor'), 'left');
        $column_tipodiretor = new TDataGridColumn('nome_tipodiretor', ('Tipo Diretor'), 'left');
        $column_situacao = new TDataGridColumn('situacao', ('Situação'), 'left');

        // add the columns to the DataGrid
        //$this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_name);
        $this->datagrid->addColumn($column_tipodiretor);
        $this->datagrid->addColumn($column_situacao);
        
        // creates the datagrid column actions
        //$order_id = new TAction(array($this, 'onReload'));
        //$order_id->setParameter('order', 'id');
        //$column_id->setAction($order_id);
        
        $order_name = new TAction(array($this, 'onReload'));
        $order_name->setParameter('order', 'id');
        $column_name->setAction($order_name);
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('DiretorForm', 'onEdit'));
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
        //$action_del->setDid('id');
        $this->datagrid->addAction($action_del);

        $this->datagrid->createModel();

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

        $repository = new TRepository('DiretorRecord');
        $limit = 10;

        $criteria = new TCriteria;
        //$criteria->setProperty('order', 'socio_id');
        if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
        $criteria->setProperties($param);
        $criteria->setProperty('limit', $limit);
            
        $cadastros = $repository->load($criteria);

        $this->datagrid->clear();
        if ($cadastros)
        {
            
            foreach ($cadastros as $cadastro)
            {
                
                $this->datagrid->addItem($cadastro);
            }
        }
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); 
            $this->pageNavigation->setProperties($param); 
            $this->pageNavigation->setLimit($limit); 

        TTransaction::close();
        $this->loaded = true;
    }

 
    function onSearch( $param )
    {
      
        TTransaction::open('pg_ceres');
        $repository = new TRepository('DiretorRecord');
        $limit = 10;

        $campo = $this->form->getFieldData('opcao');
        $dados = $this->form->getFieldData('nome');

        $criteria = new TCriteria;

        if (empty($param['order']))
            {
                $param['order'] = 'nome';
                $param['direction'] = 'asc';
            }

        $criteria->setProperties($param);
        $criteria->setProperty('limit', $limit);
            
        if ($dados)
         {
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
        
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); 
            $this->pageNavigation->setProperties($param); 
            $this->pageNavigation->setLimit($limit); 

        TTransaction::close();
        $this->loaded = true;

    }

    function onDelete($param)
    {
        
        $key=$param['key'];

        $action1 = new TAction(array($this, 'Delete'));
        $action1->setParameter('key', $key);
        $action1->setParameter('msg', 'delete');

        new TQuestion('Deseja realmente excluir o registro ?', $action1 );
    }

    function Delete($param)
    {
        $key=$param['key'];
        TTransaction::open('pg_ceres');

        $cadastro = new DiretorRecord($key);

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
        // carrega os dados no datagrid
        $this->onReload();
        //chama o metodo show da super classe
        parent::show();

    }
}
?>