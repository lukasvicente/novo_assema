<?php

/*
 * classe ConvenioMesSocioList
 * Cadastro de PerfilPagina: Contem a listagem e o formulario de busca
 */
//include_once 'app.library/funcdate.php';

//ini_set('display_errors', 1);
//ini_set('display_startup_erros', 1);
//error_reporting(E_ALL);
class GaleriaImagemDetalhe extends TPage {

    private $form;
    private $datagrid;

 
    public function __construct() {
        parent::__construct();

    $this->form = new BootstrapFormBuilder('form_GaleriaImagem');
    $this->form->setFormTitle('Galeria de Imagem');

    $codigo = new THidden('id');
    $galeria_id = new THidden('site_galeria_id');
    $galeria_id->setValue($_GET['fk']);

    $descricao = new TEntry('descricao');
    $arquivo = new TMultiFile('arquivo');

    //$arquivo->setAllowedExtensions( ['png', 'jpg'] );

    TTransaction::open('pg_ceres');
 
        $cadastro = new GaleriaSiteRecord(filter_input( INPUT_GET, 'fk' ));

        if ($cadastro) {
            $nome_titulo =  ($cadastro->titulo);

        }
 
    TTransaction::close();

    $descricao->setSize( '50%');

    $titulo = new TLabel('<div style="position:floatval; width: 200px;"> <b>* Campo obrigatorio</b></div>');
    $titulo->setFontFace('Arial');
    $titulo->setFontColor('red');
    $titulo->setFontSize(10);

    
    $this->form->addFields( [ new TLabel('Titulo da Galeria') ],[ $nome_titulo ] );
    $this->form->addFields( [ new TLabel('Descrição <font color=red><b>*</b></font>') ],[ $descricao ] );
    $this->form->addFields( [ new TLabel('Imagem <font color=red><b>*</b></font>') ],[ $arquivo ] );

    $this->form->addFields( [ new TLabel('') ],[ $codigo ,$titulo,$galeria_id ] );

    TTransaction::open('pg_ceres');
        $action_voltar = new \Adianti\Control\TAction(array('GaleriaSiteList', 'onSearch'));
        $action_voltar->setParameter('nome', (new GaleriaSiteRecord(filter_input(INPUT_GET, 'fk')))->nome);
    TTransaction::close(); 
    //var_dump($action_voltar);exit();

    $action1 = new TAction(array($this, 'onSave'));
    $action1->setParameter('fk', '' . filter_input(INPUT_GET, 'fk') . '');

    $this->form->addAction( 'Salvar',$action1, 'ico_save.png' );
    //$this->form->addAction( 'Voltar',$action_voltar, 'back_blue_arrow.png');
    
    $action2 = new TAction(array("GaleriaSiteForm", 'onEdit'));
    $action2->setParameter('fk', filter_input(INPUT_GET, 'fk'));

    $this->form->addAction('Voltar', $action2, 'back_blue_arrow.png');
    //$this->form->addAction('Voltar', new TAction(array('SocioList', 'onReload')), 'back_blue_arrow.png');

     //------### DATAGRID ###----//

    $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
    $this->datagrid->enablePopover('Image', "<img style='max-height: 300px' src='app/images/site/galeria/{arquivo}'>");

        $this->datagrid->style = 'width: 100%';
    $this->datagrid->setHeight(320);

    $column_descricao = new TDataGridColumn('descricao', ('Descricao'), 'left');
    $column_arquivo = new TDataGridColumn('arquivo', ('Arquivo'), 'left');

    $this->datagrid->addColumn($column_descricao);
    $this->datagrid->addColumn($column_arquivo);

    $action_edit = new TDataGridAction(array('GaleriaImagemDetalhe', 'onEdit'));
    $action_edit->setButtonClass('btn btn-default');
    $action_edit->setLabel(_t('Edit'));
    $action_edit->setImage('fa:pencil-square-o blue fa-lg');
    $action_edit->setField('id');
    $action_edit->setFk('site_galeria_id');
    $this->datagrid->addAction($action_edit);

    $action_del = new TDataGridAction(array($this, 'onDelete'));
    $action_del->setButtonClass('btn btn-default');
    $action_del->setLabel(_t('Delete'));
    $action_del->setImage('fa:trash-o red fa-lg');
    $action_del->setField('id');
    //$action_del->setDid('perfil_id');
    $action_del->setFk('site_galeria_id');
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

        $cadastro = $this->form->getData( 'GaleriaImagemSiteRecord' );

        $cadastro->usuarioalteracao = $_SESSION['usuario'];
        $cadastro->dataalteracao = date("d/m/Y H:i:s");
        //$cadastro->apelido = strtolower(tiraAcento($cadastro->titulo));

        $dados = $cadastro->toArray();

        $msg = '';
        $icone = 'info';



        try
        {

            if( $msg == '' )
            {

                foreach ($cadastro->arquivo as $itemnomearquivo) {

                    $source_file = 'tmp/' . $itemnomearquivo;
                    $cadastro->store();
                    $itemnomearquivo = 'foto_' . filter_input(INPUT_GET, 'fk') . '_' . $cadastro->id . ".jpg";


                    $target_file = 'app/images/site/teste/' . strtolower($itemnomearquivo);
                    if (file_exists($source_file))
                    {
                        rename($source_file,$target_file);
                        $cadastro->store();
                    }
                }

                $cadastro->store();

                $msg = 'Dados armazenados com sucesso';

                // finaliza a transacao
                TTransaction::close();

            }else
            {

                $icone = 'error';

            }

            if ($icone == 'error')
            {

                // exibe mensagem de erro
                new TMessage($icone, $msg);

                $this->form->setData( $cadastro );   // fill the form with the active record data

            }else
            {

                // exibe um dialogo ao usuario
                new TMessage( "info", $msg );

                $param = array();
                $param['key'] = $cadastro->site_galeria_id;
                $param['fk'] = $cadastro->site_galeria_id;
                TApplication::gotoPage( 'GaleriaImagemDetalhe', 'onEdit',$param );

            }

        }catch (Exception $e)
        {

            new TMessage('error', $e->getMessage() );

            TTransaction::rollback();

            $this->form->setData($cadastro);   // fill the form with the active record data

        }

    }
    function onReload() {
        TTransaction::open('pg_ceres'); // inicia transacao com o banco
        $repository = new TRepository('GaleriaImagemSiteRecord'); // instancia um repositorio da Classe
        $criteria = new TCriteria; // cria um criterio de selecao
        $criteria->add(new TFilter('site_galeria_id', '=', filter_input ( INPUT_GET, 'key' )));
        $criteria->setProperty('order', 'descricao');
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
        $cadastro = new GaleriaImagemSiteRecord($key); // instanicia objeto Record

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

                $object = new GaleriaImagemSiteRecord($key);        // instantiates object City
                //$object->dtnascimento = TDate::date2br($object->dtnascimento);
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