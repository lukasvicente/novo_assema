<?php

/*
 * classe SocioForm
 * Cadastro de Socio: Contem o formularo
 */
 
//ini_set('display_errors', 1);
//ini_set('display_startup_erros', 1);
//error_reporting(E_ALL);
include_once 'app/lib/funcdate.php';

class ArquivoSiteForm extends TPage
{

    private $form;     // formulario de cadastro


    public function __construct() {
		
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_arquivo_site');
        $this->form->setFormTitle('Formulário de Arquivo');

        $codigo = new THidden('id');
        $nome = new TEntry('nome');

        $situacao = new TCombo('situacao');

        $arquivo = new TFile('arquivo');
        $arquivo->setService('ArquivoSiteUploaderService');

        $situacao->addItems( ['publicado' => 'Publicado',
                                'despublicado' => 'Despublicado' ] );


        $situacao->setValue('publicado');

        $arquivo->setCompleteAction(new TAction(array($this, 'onComplete')));

        $nome->setProperty('placeholder', 'Nome do Documento');


        $titulo = new TLabel('<div style="position:floatval; width: 200px;"> <b>* Campo obrigatorio</b></div>');
        $titulo->setFontFace('Arial');
        $titulo->setFontColor('red');
        $titulo->setFontSize(10);


        $this->form->addFields( [ new TLabel('Nome <font color=red><b>*</b></font>') ],[ $nome ] );

        $this->form->addFields( [ new TLabel('Arquivo <font color=red><b>*</b></font>') ],[ $arquivo] );
/*
        if ((filter_input(INPUT_GET, 'key'))) 
        { 
        $this->form->addFields( [ new TLabel('Link') ],[ $link ] );
        }
*/
        //$this->form->addFields( [ new TLabel('Situação <font color=red><b>*</b></font>') ],[ $situacao ] );

        $this->form->addFields( [''], [ $codigo ] );

        $nome->setSize('50%');
        $arquivo->setSize('50%');

        $this->form->addAction( 'Salvar', new TAction( array( $this, 'onSave' ) ), 'ico_save.png' );
        $this->form->addAction('Voltar', new TAction(array('ArquivoSiteList', 'onReload')), 'back_blue_arrow.png');

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

        $cadastro = $this->form->getData( 'ArquivoSiteRecord' );

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

        try
        {

            if( $msg == '' )
            {
                if ( !empty( $dados['arquivo'] ) ){


                $source_file   = 'tmp/'.$cadastro->arquivo;
                $finfo         = new finfo(FILEINFO_MIME_TYPE);
                $ext = $finfo->file($source_file);
                $extensao = explode("/",$ext);
//var_dump($ext);
//exit();
                $cadastro->arquivo = tiraAcento($cadastro->nome).'.'.$extensao[1];
                $cadastro->arquivo = 'temp';

                $cadastro->store();

                $arquivo = tiraAcento($cadastro->nome).'.'.$extensao[1];

                $cadastro->arquivo = strtolower($arquivo);

                $caminho = 'app/documents/site/arquivo/' . strtolower($cadastro->arquivo);
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

                TApplication::gotoPage( 'ArquivoSiteList', 'onReload' );

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

                $object = new ArquivoSiteRecord( $key );  // instantiates object City

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