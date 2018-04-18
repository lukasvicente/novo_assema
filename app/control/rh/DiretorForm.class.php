<?php

/*
 * classe SocioForm
 * Cadastro de Socio: Contem o formularo
 */
 
//ini_set('display_errors', 1);
//ini_set('display_startup_erros', 1);
//error_reporting(E_ALL);

class DiretorForm extends TPage
{

    private $form;     // formulario de cadastro
    private $form1; 

    public function __construct() {
		
        parent::__construct();
        

        $this->form = new BootstrapFormBuilder('form_Diretor');
        $this->form->setFormTitle('Formulário de Diretor');

        $codigo = new THidden('id');

        $socio_id = new TDBMultiSearch('socio_id', 'pg_ceres', 'SocioRecord', 'id', 'nome');
        $tipodiretor_id = new TDBCombo('tipodiretor_id', 'pg_ceres', 'TipoDiretorRecord', 'id', 'nome');
        $datainicio = new TDate('datainicio');
        $datafim = new TDate('datafim');
        $situacao = new TCombo('situacao');
        $descricao = new THtmlEditor('descricao');
        $discurso = new TText('discurso');

        $descricao->setSize( '80%', 200);
        $datainicio->setSize(80);
        $datafim->setSize(80);
        $discurso->setSize('80%',50);

        $socio_id->setProperty('placeholder', 'Nome do Diretor');
        $socio_id->setMinLength(1);
        $socio_id->setMaxSize(1);

        //$tipodiretor_id->enableSearch();

        $situacao->addItems( ['ATIVO' => 'ATIVO',
            'INATIVO' => 'INATIVO' ] );
        $situacao->setValue('ATIVO');


        $titulo = new TLabel('<div style="position:floatval; width: 200px;"> <b>* Campo obrigatorio</b></div>');
        $titulo->setFontFace('Arial');
        $titulo->setFontColor('red');
        $titulo->setFontSize(10);

        $this->form->appendPage('Dados Pessoais');
        $this->form->addFields( [ new TLabel('Nome <font color=red><b>*</b></font>') ],[ $socio_id ] );
        $this->form->addFields( [ new TLabel('Tipo Diretor <font color=red><b>*</b></font>') ],[ $tipodiretor_id ] );
        $this->form->addFields( [ new TLabel('Data Inicio <font color=red><b>*</b></font>') ],[ $datainicio ] );
        $this->form->addFields( [ new TLabel('Data Fim <font color=red><b>*</b></font>') ],[ $datafim ] );
        $this->form->addFields( [ new TLabel('Situação <font color=red><b>*</b></font>') ],[ $situacao ] );
        $this->form->appendPage('Adicional');

        $this->form->addFields( [ new TLabel('Discurso <font color=red><b>*</b></font>') ],[ $discurso ] );
        $this->form->addFields( [ new TLabel('Descrição <font color=red><b>*</b></font>') ],[ $descricao ] );


        $this->form->addFields( [''], [ $codigo ] );


        $this->form->addAction( 'Salvar', new TAction( array( $this, 'onSave' ) ), 'ico_save.png' );
        $this->form->addAction('Voltar', new TAction(array('DiretorList', 'onReload')), 'back_blue_arrow.png');

        $vbox = new TVBox;
        $vbox->add($this->form);
        parent::add($this->form);

    }

    function onSave()
    {

        TTransaction::open('pg_ceres');

        $cadastro = $this->form->getData( 'DiretorRecord' );

        $cadastro->usuarioalteracao = $_SESSION['usuario'];
        $cadastro->dataalteracao = date("d/m/Y H:i:s");
        $cadastro->socio_id = key($cadastro->socio_id);

        $dados = $cadastro->toArray();

        $msg = '';
        $icone = 'info';

        if( empty( $dados['socio_id'] ) )
        {

            $msg .= 'O Nome do Diretor deve ser informado.<br>';

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

                TApplication::gotoPage( 'DiretorList', 'onReload' ); // reload

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

                $object = new DiretorRecord( $key );  // instantiates object City

                $objSocio = new SocioRecord($object->socio_id);
                $object->socio_id = [$objSocio->id => $objSocio->nome];

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