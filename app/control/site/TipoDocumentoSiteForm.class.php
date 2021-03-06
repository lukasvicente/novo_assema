<?php

/*
 * classe SocioForm
 * Cadastro de Socio: Contem o formularo
 */
 
//ini_set('display_errors', 1);
//ini_set('display_startup_erros', 1);
//error_reporting(E_ALL);

class TipoDocumentoSiteForm extends TPage
{

    private $form;     // formulario de cadastro

    public function __construct() {
		
        parent::__construct();
        

        $this->form = new BootstrapFormBuilder('form_tipodocumento_site');
        $this->form->setFormTitle('Formulário de Tipo Documento');

        $codigo = new THidden('id');
        $nome = new TEntry('nome');

        $nome->setProperty('placeholder', 'Nome do Tipo Documento');


        $titulo = new TLabel('<div style="position:floatval; width: 200px;"> <b>* Campo obrigatorio</b></div>');
        $titulo->setFontFace('Arial');
        $titulo->setFontColor('red');
        $titulo->setFontSize(10);


        $this->form->addFields( [ new TLabel('Nome <font color=red><b>*</b></font>') ],[ $nome ] );

        $this->form->addFields( [''], [ $codigo ] );

        $nome->setSize('50%');

        $this->form->addAction( 'Salvar', new TAction( array( $this, 'onSave' ) ), 'ico_save.png' );
        $this->form->addAction('Voltar', new TAction(array('TipoDocumentoSiteList', 'onReload')), 'back_blue_arrow.png');

        $vbox = new TVBox;
        $vbox->add($this->form);
        parent::add($this->form);

    }

    function onSave()
    {

        TTransaction::open('pg_ceres');

        $cadastro = $this->form->getData( 'SiteTipoDocumentoRecord' );

        $cadastro->usuarioalteracao = $_SESSION['usuario'];
        $cadastro->dataalteracao = date("d/m/Y H:i:s");

        $dados = $cadastro->toArray();

        $msg = '';
        $icone = 'info';

        if( empty( $dados['nome'] ) )
        {

            $msg .= 'O Nome deve ser informado.<br>';

        }

        try
        {

            if( $msg == '' )
            {

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

                TApplication::gotoPage( 'TipoDocumentoSiteList', 'onReload' ); // reload

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

                $object = new SiteTipoDocumentoRecord( $key );  // instantiates object City

                $this->form->setData( $object );   // fill the form with the active record data

                TTransaction::close();           // close the transaction

            }else
            {

               // $this->form->clear();

            }

        } catch (Exception $e)
        {

            new TMessage('error', '<b>Error</b> ' . $e->getMessage());

            TTransaction::rollback();

        }

    }
}
?>