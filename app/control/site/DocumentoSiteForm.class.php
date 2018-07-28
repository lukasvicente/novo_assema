<?php

/*
 * classe SocioForm
 * Cadastro de Socio: Contem o formularo
 */
 
//ini_set('display_errors', 1);
//ini_set('display_startup_erros', 1);
//error_reporting(E_ALL);
include_once 'app/lib/funcdate.php';

class DocumentoSiteForm extends TPage
{

    private $form;     // formulario de cadastro


    public function __construct() {
		
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_documento_site');
        $this->form->setFormTitle('Formulário de Conteúdo Documento');

        $codigo = new THidden('id');
        $nome = new TEntry('nome');
        $tipodocumento_id  = new TDBCombo('site_tipodocumento_id', 'pg_ceres', 'TipoDocumentoSiteRecord', 'id', 'nome');
        $descricao = new TText('descricao');
        $situacao = new TCombo('situacao');
        $mes = new TCombo('mes');
        $ano = new TEntry('ano');
        $arquivo = new TFile('arquivo');
        $arquivo->setService('DocumentoSiteUploaderService');

        $id = (filter_input(INPUT_GET, 'key'));
        $link = new THyperLink('Arquivo', 'app/documents/site/documents_'.$id.'.pdf', 'green', 12, 'biu');

        $situacao->addItems( ['publicado' => 'Publicado',
                                'despublicado' => 'Despublicado' ] );

        $mes->addItems( ['1' => 'Janeiro',
                         '2' => 'Fevereiro',
                         '3' => 'Março',
                         '4' => 'Abril',
                         '5' => 'Maio',
                         '6' => 'Junho',
                         '7' => 'Julho',
                         '8' => 'Agosto',
                         '9' => 'Setembro',
                         '10' => 'Outubro',
                         '11' => 'Novembro',
                         '12' => 'Dezembro',
                          ] );

        $situacao->setValue('publicado');

        $arquivo->setCompleteAction(new TAction(array($this, 'onComplete')));

        $nome->setProperty('placeholder', 'Nome do Documento');
        $ano->setProperty('placeholder', 'Ano');
        $ano->setMaxLength(4);

        $titulo = new TLabel('<div style="position:floatval; width: 200px;"> <b>* Campo obrigatorio</b></div>');
        $titulo->setFontFace('Arial');
        $titulo->setFontColor('red');
        $titulo->setFontSize(10);


        $this->form->addFields( [ new TLabel('Nome <font color=red><b>*</b></font>') ],[ $nome ] );
        $this->form->addFields( [ new TLabel('Tipo Documento <font color=red><b>*</b></font>') ],[ $tipodocumento_id ] );
        $this->form->addFields( [ new TLabel('Documento <font color=red><b>*</b></font>') ],[ $arquivo, new TLabel('<b>Obs.: </b>Formato em PDF')  ] );

        if ((filter_input(INPUT_GET, 'key'))) 
        { 
        $this->form->addFields( [ new TLabel('Link') ],[ $link ] );
        }

        $this->form->addFields( [ new TLabel('Descrição') ],[ $descricao ] );
        $this->form->addFields( [ new TLabel('Data <font color=red><b>*</b></font>') ],[ $mes,$ano ] );
        $this->form->addFields( [ new TLabel('Situação <font color=red><b>*</b></font>') ],[ $situacao ] );

        $this->form->addFields( [''], [ $codigo ] );

        $nome->setSize('50%');
        $arquivo->setSize('50%');
        $mes->setSize('18%');
        $ano->setSize('10%');
        $descricao->setSize( '70%', 100);

        $this->form->addAction( 'Salvar', new TAction( array( $this, 'onSave' ) ), 'ico_save.png' );
        $this->form->addAction('Voltar', new TAction(array('DocumentoSiteList', 'onReload')), 'back_blue_arrow.png');

        $vbox = new TVBox;
        $vbox->add($this->form);
        parent::add($this->form);

    }

    public static function onComplete($param)
    {
        new TMessage('info', 'Upload Completo: '.$param['arquivo']);

    }

    function onSave()
    {

        TTransaction::open('pg_ceres');

        $cadastro = $this->form->getData( 'DocumentoSiteRecord' );

        $cadastro->usuarioalteracao = $_SESSION['usuario'];
        $cadastro->dataalteracao = date("d/m/Y H:i:s");

        $dados = $cadastro->toArray();

        $msg = '';
        $icone = 'info';

        if( empty( $dados['nome'] ) )
        {

            $msg .= 'O Nome deve ser informado.<br>';

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

                $cadastro->arquivo = 'documents_' .$cadastro->id.".pdf";
                $cadastro->arquivo = 'temp';

                $cadastro->store();

                $arquivo = 'documents_' . $cadastro->id . '.pdf';

                $cadastro->arquivo = $arquivo;

                $caminho = 'app/documents/site/' . strtolower($cadastro->arquivo);
                rename($source_file, $caminho);

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
                $param['key'] = $cadastro->id;
                $param['fk'] = $cadastro->id;
                TApplication::gotoPage( 'DocumentoSiteForm', 'onEdit',$param );

            }

        }catch (Exception $e)
        {

            new TMessage('error', $e->getMessage() );

            TTransaction::rollback();

            $this->form->setData($cadastro);   // fill the form with the active record data

        }

    }
    function onEdit( $param )
    {

        try
        {

            if( isset( $param['key'] ) )
            {

                $key = $param['key'];

                TTransaction::open('pg_ceres');   // open a transaction with database 'samples'

                $object = new DocumentoSiteRecord( $key );  // instantiates object City

                $this->form->setData( $object );   // fill the form with the active record data

                TTransaction::close();           // close the transaction

            }

        } catch (Exception $e)
        {

            new TMessage('error', '<b>Error</b> ' . $e->getMessage());

            TTransaction::rollback();

        }

    }
}
?>