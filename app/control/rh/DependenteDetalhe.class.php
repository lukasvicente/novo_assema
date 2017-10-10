<?php

/*
 * classe ConvenioMesSocioList
 * Cadastro de PerfilPagina: Contem a listagem e o formulario de busca
 */
//include_once 'app.library/funcdate.php';

class DependenteDetalhe extends TPage {

    private $form;
    private $datagrid;

 
    public function __construct() {
        parent::__construct();

    $this->form = new BootstrapFormBuilder('form_dependente');
    $this->form->setFormTitle('Dependente do Socio');

    $codigo = new THidden('id');
    $socio_id = new THidden('socio_id');
    $socio_id->setValue($_GET['fk']);

    $nome = new TEntry('nome');
    $cpf = new TEntry('cpf');
    $dtnascimento = new TDate('dtnascimento');
    $grauparentesco = new TCombo('grauparentesco');
    $situacao = new TCombo('situacao');

    TTransaction::open('pg_ceres');
 
        $cadastro = new socioRecord(filter_input( INPUT_GET, 'fk' ));

        if ($cadastro) {
            $nome_socio =  ($cadastro->nome);

        }
 
    TTransaction::close();

    $situacao->addItems( [ 'ATIVO' => 'ATIVO', 'INATIVO' => 'INATIVO' ] );
    $situacao->setValue('ATIVO');
    $grauparentesco->addItems( [ 'FILHO(A)' => 'FILHO(A)', 'ESPOSA' => 'ESPOSA' ] );

    $nome->forceUpperCase();
    $nome->setProperty('placeholder', 'Nome do Dependente');
    $nome->setSize('40%');
    $dtnascimento->setSize('17%');
    $dtnascimento->setProperty('placeholder', 'DD/MM/AAAA');
    $cpf->setSize('17%');
    $cpf->setProperty('placeholder', 'Ex.: 00011122233');

    $titulo = new TLabel('<div style="position:floatval; width: 200px;"> <b>* Campo obrigatorio</b></div>');
    $titulo->setFontFace('Arial');
    $titulo->setFontColor('red');
    $titulo->setFontSize(10);

    
    $this->form->addFields( [ new TLabel('Nome do Socio') ],[ $nome_socio ] );
    $this->form->addFields( [ new TLabel('Nome <font color=red><b>*</b></font>') ],[ $nome ] );
    $this->form->addFields( [ new TLabel('CPF <font color=red><b>*</b></font>') ],[ $cpf ] );
    $this->form->addFields( [ new TLabel('Data de Nascimento <font color=red><b>*</b></font>') ],[ $dtnascimento ] );
    $this->form->addFields( [ new TLabel('Grau Parentesto <font color=red><b>*</b></font>') ],[ $grauparentesco ] );
    $this->form->addFields( [ new TLabel('Situação <font color=red><b>*</b></font>') ],[ $situacao ] );
    $this->form->addFields( [ new TLabel('') ],[ $codigo,$titulo,$socio_id ] );

    TTransaction::open('pg_ceres');
        $action_voltar = new \Adianti\Control\TAction(array('SocioList', 'onSearch'));
        $action_voltar->setParameter('nome', (new SocioRecord(filter_input(INPUT_GET, 'fk')))->nome);
    TTransaction::close(); 
    //var_dump($action_voltar);exit();

    $action1 = new TAction(array($this, 'onSave'));
    $action1->setParameter('fk', '' . filter_input(INPUT_GET, 'fk') . '');

    $this->form->addAction( 'Salvar',$action1, 'ico_save.png' );
    //$this->form->addAction( 'Voltar',$action_voltar, 'back_blue_arrow.png');
    
    $action2 = new TAction(array("SocioForm", 'onEdit'));
    $action2->setParameter('fk', filter_input(INPUT_GET, 'fk'));

    $this->form->addAction('Voltar', $action2, 'back_blue_arrow.png');
    //$this->form->addAction('Voltar', new TAction(array('SocioList', 'onReload')), 'back_blue_arrow.png');

    $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
    $this->datagrid->style = 'width: 100%';
    $this->datagrid->setHeight(320);

    $column_nome = new TDataGridColumn('nome', ('Nome'), 'left');
    $column_cpf = new TDataGridColumn('cpf', ('CPF'), 'left');
    $column_grauparentesco = new TDataGridColumn('grauparentesco', ('Grau Parentesco'), 'left');
    $column_situacao = new TDataGridColumn('situacao', ('Situação'), 'left');
 
    $this->datagrid->addColumn($column_nome);
    $this->datagrid->addColumn($column_cpf);
    $this->datagrid->addColumn($column_grauparentesco);
    $this->datagrid->addColumn($column_situacao);

    $action_edit = new TDataGridAction(array('DependenteDetalhe', 'onEdit'));
    $action_edit->setButtonClass('btn btn-default');
    $action_edit->setLabel(_t('Edit'));
    $action_edit->setImage('fa:pencil-square-o blue fa-lg');
    $action_edit->setField('id');
    $action_edit->setFk('socio_id');
    $this->datagrid->addAction($action_edit);

    $action_del = new TDataGridAction(array($this, 'onDelete'));
    $action_del->setButtonClass('btn btn-default');
    $action_del->setLabel(_t('Delete'));
    $action_del->setImage('fa:trash-o red fa-lg');
    $action_del->setField('id');
    //$action_del->setDid('perfil_id');
    $action_del->setFk('socio_id');
    $this->datagrid->addAction($action_del);    

    $this->datagrid->createModel();
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
        $cadastro = $this->form->getData('DependenteRecord');

        $cadastro->usuarioalteracao = $_SESSION['usuario'];
        $cadastro->dataalteracao = date("d/m/Y H:i:s");
        $dados = $cadastro->toArray();

        $msg = '';
        $icone = 'info';

 
        if (empty ($dados['nome'])){
           $msg .= 'O Nome do Dependente deve ser informado.</br>';
        }
        if (empty ($dados['cpf'])){
           $msg .= 'O CPF do Dependente deve ser informado.</br>';
        }
        if (empty ($dados['grauparentesco'])){
           $msg .= 'O Grau de Parentesco deve ser informado.</br>';
        }
        if (empty ($dados['situacao'])){
           $msg .= 'O Grau de Parentesco deve ser informado.</br>';
        }

        try{

            if ($msg == ''){
              $cadastro->store();
              $msg = 'Dados armazenados com sucesso';
              TTransaction::close();

            }else{

                $icone = 'error';
                $this->form->setData($cadastro);

            }

            if ($icone == 'error'){
 
            new TMessage($icone, $msg);

            $this->form->setData($cadastro);
 
            }else{
                
                $param = array();
                $param['fk'] = $dados['socio_id'];
                //$param['key'] = $dados['socio_id'];
                new TMessage("info", "Registro salvo com sucesso!");
                TApplication::gotoPage('DependenteDetalhe','onReload', $param); // reload
                $this->form->setData($cadastro);   // fill the form with the active record data
                
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
 
            $this->form->setData($cadastro);   // fill the form with the active record data
            $this->onReload();
            
        }

    }
    function onReload() {
        TTransaction::open('pg_ceres'); // inicia transacao com o banco
        $repository = new TRepository('DependenteRecord'); // instancia um repositorio da Classe
        $criteria = new TCriteria; // cria um criterio de selecao
        $criteria->add(new TFilter('socio_id', '=', filter_input ( INPUT_GET, 'fk' )));
        $criteria->setProperty('order', 'nome');
        $cadastros = $repository->load($criteria); // carrega os objetos de acordo com o criterio
        $this->datagrid->clear(); //limpa o grid
        if ($cadastros) {
            foreach ($cadastros as $cadastro) {// percorre os objetos retornados
                $this->datagrid->addItem($cadastro); // adiciona o objeto na DataGrid
            }
        }
        TTransaction::close(); // finaliza a transacao
        $this->loaded = true;
    }

 
    function onDelete($param) {
        $key = $param['key']; 

        $action1 = new TAction(array($this, 'Delete'));
        $action2 = new TAction(array($this, 'onReload'));

        $action1->setParameter('key', $key);
        $action2->setParameter('key', $key);

        $action1->setParameter('fk', $_GET['fk']);
        $action2->setParameter('fk', $_GET['fk']);
        new TQuestion('Deseja realmente excluir o registro ?', $action1, $action2);
    }

    function Delete($param) {
        $key = $param['key']; // obtem o parametro $key
        TTransaction::open('pg_ceres'); // inicia transacao com o banco
        $cadastro = new DependenteRecord($key); // instanicia objeto Record

        try {

            $cadastro->delete(); // deleta objeto do banco de dados
            TTransaction::close(); // finaliza a transacao
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage()); // exibe a mensagem gerada pela excecao
            TTransaction::rollback(); // desfaz todas alteracoes no banco de dados
        }
        $this->onReload(); // re-carrega a datagrid
        new TMessage('info', "Registro Excluido com sucesso"); // exibe mensagem de sucesso
    }

    function onEdit($param)
    {
        
        try {
            if (isset($param['key'])) {

                // get the parameter $key
                $key = $param['key'];

                TTransaction::open('pg_ceres');   // open a transaction with database 'samples'

                $object = new DependenteRecord($key);        // instantiates object City
                $object->dtnascimento = TDate::date2br($object->dtnascimento);
                $this->form->setData($object);   // fill the form with the active record data

                TTransaction::close();           // close the transaction
            } else {
                //$this->form->clear();
            }
        } catch (Exception $e) {

            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            TTransaction::rollback();
        }
        
    }
    function show() {
        $this->onReload();  
        parent::show();  
    }
}

?>