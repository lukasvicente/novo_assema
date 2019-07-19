<?php

/*
 * classe ConvenioMesSocioList
 * Cadastro de PerfilPagina: Contem a listagem e o formulario de busca
 */
//include_once 'app.library/funcdate.php';

class ProcessoDocumentoDetalhe extends TPage {

    private $form;
    private $datagrid;

 
    public function __construct() {
        parent::__construct();

    $this->form = new BootstrapFormBuilder('form_documento_processo');
    $this->form->setFormTitle('Documento do Processo');

    $codigo = new THidden('id');
    $processo_id = new THidden('processo_id');
    $processo_id->setValue($_GET['fk']);
    $situacao = new TCombo('situacao');


    $situacao->addItems( ['PUBLICADO' => 'PUBLICADO',
        'DESPUBLICADO' => 'DESPUBLICADO' ] );

    $situacao->setvalue('PUBLICADO');

    $nome = new TEntry('nome');
    $arquivo = new TFile('arquivo');

    TTransaction::open('pg_ceres');
 
        $cadastro = new ProcessoRecord(filter_input( INPUT_GET, 'fk' ));

        if ($cadastro) {
            $nome_processo =  ($cadastro->nome);
            $numero_processo =  ($cadastro->numero);
        }
 
    TTransaction::close();

    $arquivo->setCompleteAction(new TAction(array($this, 'onComplete')));

    $nome->setProperty('placeholder', 'Nome do Documento');
    $nome->setSize('40%');
    $arquivo->setSize('50%');


    $titulo = new TLabel('<div style="position:floatval; width: 200px;"> <b>* Campo obrigatorio</b></div>');
    $titulo->setFontFace('Arial');
    $titulo->setFontColor('red');
    $titulo->setFontSize(10);

    
    $this->form->addFields( [ new TLabel('<b>Nome do Processo</b>') ],[ $nome_processo ] );
    $this->form->addFields( [ new TLabel('<b>Número do Processo</b>') ],[ $numero_processo ] );
    $this->form->addFields( [ new TLabel('Nome <font color=red><b>*</b></font>') ],[ $nome ] );
    $this->form->addFields( [ new TLabel('Arquivo <font color=red><b>*</b></font>') ],[ $arquivo ] );
    $this->form->addFields( [ new TLabel('Situação <font color=red><b>*</b></font>') ],[ $situacao ] );

    $this->form->addFields( [ new TLabel('') ],[ $codigo,$titulo,$processo_id ] );


    $action1 = new TAction(array($this, 'onSave'));
    $action1->setParameter('fk', '' . filter_input(INPUT_GET, 'fk') . '');

    $this->form->addAction( 'Salvar',$action1, 'ico_save.png' );

    $action2 = new TAction(array("ProcessoForm", 'onEdit'));
    $action2->setParameter('key', filter_input(INPUT_GET, 'key'));

    $this->form->addAction('Voltar', $action2, 'back_blue_arrow.png');

    $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
    $this->datagrid->style = 'width: 100%';
    $this->datagrid->setHeight(320);
    $this->datagrid->disableDefaultClick(false);

    $column_nome = new TDataGridColumn('nome', ('Nome'), 'left');
    $column_arquivo = new TDataGridColumn('linkarquivo', ('Arquivo'), 'left');
    $column_situacao = new TDataGridColumn('situacao', ('Situação'), 'left');
 
    $this->datagrid->addColumn($column_nome);
    $this->datagrid->addColumn($column_situacao);
    $this->datagrid->addColumn($column_arquivo);


    $action_edit = new TDataGridAction(array('ProcessoDocumentoDetalhe', 'onEdit'));
    $action_edit->setButtonClass('btn btn-default');
    $action_edit->setLabel(_t('Edit'));
    $action_edit->setImage('fa:pencil-square-o blue fa-lg');
    $action_edit->setField('id');
    $action_edit->setFk('processo_id');
    $this->datagrid->addAction($action_edit);

    $action_del = new TDataGridAction(array($this, 'onDelete'));
    $action_del->setButtonClass('btn btn-default');
    $action_del->setLabel(_t('Delete'));
    $action_del->setImage('fa:trash-o red fa-lg');
    $action_del->setField('id');
    //$action_del->setDid('perfil_id');
    $action_del->setFk('processo_id');
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

    public static function onComplete($param)
    {
        new TMessage('info', 'Upload Completo: '.$param['arquivo']);

    }
    function onSave()
    {

        TTransaction::open('pg_ceres');

        $cadastro = $this->form->getData( 'ProcessoDocumentoRecord' );

        $cadastro->usuarioalteracao = $_SESSION['usuario'];
        $cadastro->dataalteracao = date("d/m/Y H:i:s");

        $dados = $cadastro->toArray();

        $msg = '';
        $icone = 'info';

        if( empty( $dados['nome'] ) )
        {

            $msg .= 'O Nome deve ser informado.<br>';

        }
        if( empty( $dados['arquivo'] ) )
        {

            $msg .= 'O Arquivo deve ser informado.<br>';

        }
        if( empty( $dados['situacao'] ) )
        {

            $msg .= 'A Situação deve ser informada.';

        }


        try
        {

            if( $msg == '' )
            {
                if ( !empty( $dados['arquivo'] ) ){


                    $source_file   = 'tmp/'.$cadastro->arquivo;
                    //$finfo         = new finfo(FILEINFO_MIME_TYPE);
                    //$ext = $finfo->file($source_file);

                    $cadastro->arquivo = 'documents_' . filter_input(INPUT_GET, 'fk') . '_' .$cadastro->id.".pdf";
                    $cadastro->arquivo = 'temp';

                    $cadastro->store();

                    $arquivo = 'documents_' . filter_input(INPUT_GET, 'fk') . '_' . $cadastro->id . '.pdf';

                    $cadastro->arquivo = $arquivo;

                    $caminho = 'app/documents/site/processo/' . strtolower($cadastro->arquivo);
                    rename($source_file, $caminho);

                }

                $cadastro->store();

                $msg = 'Dados armazenados com sucesso';


                TTransaction::close();

            }else
            {

                $icone = 'error';

            }

            if ($icone == 'error')
            {


                new TMessage($icone, $msg);

                $this->form->setData( $cadastro );   // fill the form with the active record data

            }else
            {


                new TMessage( "info", $msg );

                $param = array();
                $param['key'] = $cadastro->id;
                $param['fk'] = $cadastro->processo_id;
                TApplication::gotoPage( 'ProcessoDocumentoDetalhe', 'onEdit',$param );

            }

        }catch (Exception $e)
        {

            new TMessage('error', $e->getMessage() );

            TTransaction::rollback();

            $this->form->setData($cadastro);

        }

    }
    function onReload()
    {
        TTransaction::open('pg_ceres');
        $repository = new TRepository('ProcessoDocumentoRecord');

        $criteria = new TCriteria;
        $criteria->add(new TFilter('processo_id', '=', filter_input ( INPUT_GET, 'fk' )));
        $criteria->setProperty('order', 'nome');
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
        $key = $param['key']; 

        $action1 = new TAction(array($this, 'Delete'));
        $action2 = new TAction(array($this, 'onReload'));

        $action1->setParameter('key', $key);
        $action2->setParameter('key', $key);

        $action1->setParameter('fk', $_GET['fk']);
        $action2->setParameter('fk', $_GET['fk']);
        new TQuestion('Deseja realmente excluir o registro ?', $action1, $action2);
    }

    function Delete($param)
    {

        $key = $param['key'];
        TTransaction::open('pg_ceres');
        $cadastro = new ProcessoDocumentoRecord($key);

        try {
            unlink("app/documents/site/processo/".$cadastro->arquivo);
            $cadastro->delete();
            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
        $this->onReload();
        new TMessage('info', "Registro Excluido com sucesso");
    }

    function onEdit($param)
    {
        
        try {
            if (isset($param['key'])) {


                $key = $param['key'];

                TTransaction::open('pg_ceres');

                $object = new ProcessoDocumentoRecord($key);

                $this->form->setData($object);

                TTransaction::close();
            } else {

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