<?php

/*
 * classe SocioForm
 * Cadastro de Socio: Contem o formularo
 */
 
//ini_set('display_errors', 1);
//ini_set('display_startup_erros', 1);
//error_reporting(E_ALL);

class SobreSiteForm extends TPage
{

    private $form;     // formulario de cadastro
    private $form1; 

    public function __construct() {
		
        parent::__construct();
        

        $this->form = new BootstrapFormBuilder('form_sobre_site');
        $this->form->setFormTitle('Formulário de Conteúdo Sobre');

        $codigo = new THidden('id');
        $nome = new TEntry('nome');
        $nome_titulo = new TEntry('titulo');
        $descricao = new THtmlEditor('descricao');
        $descricao->setSize( '100%', 200);
        $situacao = new TCombo('situacao');

        $situacao->addItems( ['ATIVO' => 'ATIVO',
                                'INATIVO' => 'INATIVO' ] );

        $situacao->setValue('ATIVO');

        $nome->setProperty('placeholder', 'Nome do Conteudo');


        $titulo = new TLabel('<div style="position:floatval; width: 200px;"> <b>* Campo obrigatorio</b></div>');
        $titulo->setFontFace('Arial');
        $titulo->setFontColor('red');
        $titulo->setFontSize(10);


        $this->form->addFields( [ new TLabel('Nome <font color=red><b>*</b></font>') ],[ $nome ] );
        $this->form->addFields( [ new TLabel('Titulo <font color=red><b>*</b></font>') ],[ $nome_titulo ] );
        $this->form->addFields( [ new TLabel('Descrição') ],[ $descricao ] );
        $this->form->addFields( [ new TLabel('Situação <font color=red><b>*</b></font>') ],[ $situacao ] );
        $this->form->addFields( [''], [ $codigo ] );

        $nome->setSize('50%');

        $this->form->addAction( 'Salvar', new TAction( array( $this, 'onSave' ) ), 'ico_save.png' );
        $this->form->addAction('Voltar', new TAction(array('SobreSiteList', 'onReload')), 'back_blue_arrow.png');

        $vbox = new TVBox;
        $vbox->add($this->form);
        parent::add($this->form);

    }

    function onSave()
    {

        TTransaction::open('pg_ceres');

        $cadastro = $this->form->getData( 'SobreSiteRecord' );

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

                TApplication::gotoPage( 'SobreSiteList', 'onReload' ); // reload

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

                $object = new SobreSiteRecord( $key );  // instantiates object City

                $this->form->setData( $object );   // fill the form with the active record data

                TTransaction::close();           // close the transaction

            }else
            {

                $this->form->clear();

            }

        } catch (Exception $e)
        {

            new TMessage('error', '<b>Error</b> ' . $e->getMessage());

            TTransaction::rollback();

        }

    }
}
?>