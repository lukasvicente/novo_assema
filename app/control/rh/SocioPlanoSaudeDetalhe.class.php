<?php

/*
 * classe ConvenioMesSocioList
 * Cadastro de PerfilPagina: Contem a listagem e o formulario de busca
 */
//include_once 'app.library/funcdate.php';

class SocioPlanoSaudeDetalhe extends TPage {

    private $form;
    private $datagrid;

 
    public function __construct() {
        parent::__construct();

    $this->form = new BootstrapFormBuilder('form_planosaudedetalhe');
    $this->form->setFormTitle('Formulario Sócio no Plano Saúde');

    $codigo = new THidden('id');
    $socio_id = new THidden('socio_id');
    $socio_id->setValue($_GET['fk']);

    $categoriaplanosaude_id  = new TDBCombo('categoriaplanosaude_id', 'pg_ceres', 'CategoriaPlanoSaudeRecord', 'id', 'nome');
    $datainicio = new TDate('inicio');
    $datafim = new TDate('fim');
    $numero = new TEntry('numero');

    TTransaction::open('pg_ceres');
 
        $cadastro = new socioRecord(filter_input( INPUT_GET, 'fk' ));

        if ($cadastro) {
            $nome_socio =  ($cadastro->nome);
            $cpf_socio =  ($cadastro->cpf);

        }
 
    TTransaction::close();

    $datainicio->setSize('17%');
    $datafim->setSize('17%');
    $datainicio->setProperty('placeholder', 'DD/MM/AAAA');
    $datafim->setProperty('placeholder', 'DD/MM/AAAA');
    $numero->setProperty('placeholder', 'Número do Beneficiário');

    $titulo = new TLabel('<div style="position:floatval; width: 200px;"> <b>* Campo obrigatorio</b></div>');
    $titulo->setFontFace('Arial');
    $titulo->setFontColor('red');
    $titulo->setFontSize(10);

    
    $this->form->addFields( [ new TLabel('Nome ') ],[ $nome_socio ] );
    $this->form->addFields( [ new TLabel('CPF ') ],[ $cpf_socio ] );

    $this->form->addFields( [ new TLabel('Categoria <font color=red><b>*</b></font>') ],[ $categoriaplanosaude_id ] );
    $this->form->addFields( [ new TLabel('Data Inicio <font color=red><b>*</b></font>') ],[ $datainicio ] );
    $this->form->addFields( [ new TLabel('Data Fim ') ],[ $datafim ] );
    $this->form->addFields( [ new TLabel('Número <font color=red><b>*</b></font>') ],[ $numero ] );
 
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
    $this->form->addAction('Voltar', new TAction(array('SocioList', 'onReload')), 'back_blue_arrow.png');

    $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
    $this->datagrid->style = 'width: 100%';
    $this->datagrid->setHeight(320);

    $column_nome = new TDataGridColumn('nome', ('Nome'), 'left');
    $column_categoriaplano = new TDataGridColumn('nome_categoriaplanosaude', ('Categoria Plano Saúde'), 'left');
    $column_datainicio = new TDataGridColumn('inicio', ('Data Inicio'), 'left');
    $column_datafim = new TDataGridColumn('fim', ('Data Fim'), 'left');
    $column_numero = new TDataGridColumn('numero', ('Número'), 'left');
 
 
    $this->datagrid->addColumn($column_categoriaplano);
    $this->datagrid->addColumn($column_datainicio);
    $this->datagrid->addColumn($column_datafim);
    $this->datagrid->addColumn($column_numero);

    $action_edit = new TDataGridAction(array('SocioPlanoSaudeDetalhe', 'onEdit'));
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
        $cadastro = $this->form->getData('SocioPlanoSaudeRecord');

        $cadastro->usuarioalteracao = $_SESSION['usuario'];
        $cadastro->dataalteracao = date("d/m/Y H:i:s");
        $dados = $cadastro->toArray();

        $msg = '';
        $icone = 'info';

 
        if (empty ($dados['socio_id'])){
           $msg .= 'O Socio deve ser informado.</br>';
        }
        if (empty ($dados['categoriaplanosaude_id'])){
           $msg .= 'A Categoria deve ser informada.</br>';
        }
        if (empty ($dados['inicio'])){
           $msg .= 'A Data Inicio deve ser informada.</br>';
        }

        if (empty ($dados['numero'])){
           $msg .= 'O Numero deve ser informado.</br>';
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
                TApplication::gotoPage('SocioPlanoSaudeDetalhe','onReload', $param); // reload
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
        $repository = new TRepository('SocioPlanoSaudeRecord'); // instancia um repositorio da Classe
        $criteria = new TCriteria; // cria um criterio de selecao
        $criteria->add(new TFilter('socio_id', '=', filter_input ( INPUT_GET, 'fk' )));
        $criteria->setProperty('order', 'id');
        $cadastros = $repository->load($criteria); // carrega os objetos de acordo com o criterio
        $this->datagrid->clear(); //limpa o grid
        if ($cadastros) {
            foreach ($cadastros as $cadastro) {// percorre os objetos retornados
                
                $cadastro->inicio = TDate::date2br($cadastro->inicio);
                $cadastro->fim = TDate::date2br($cadastro->fim);
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
        $cadastro = new SocioPlanoSaudeRecord($key); // instanicia objeto Record

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

                $object = new SocioPlanoSaudeRecord($key);        // instantiates object City
                $object->inicio = TDate::date2br($object->inicio);
                $object->fim = TDate::date2br($object->fim);
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