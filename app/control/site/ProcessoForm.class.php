<?php

/*
 * classe SocioForm
 * Cadastro de Socio: Contem o formularo
 */
 
//ini_set('display_errors', 1);
//ini_set('display_startup_erros', 1);
//error_reporting(E_ALL);

class ProcessoForm extends TPage
{

    private $form;     // formulario de cadastro

    public function __construct() {
		
        parent::__construct();

        $buttondependente = new TAction( ['ProcessoDocumentoDetalhe', 'onReload' ] );
        $buttondependente->setParameter('key', '' . filter_input(INPUT_GET, 'key') . '');
        $buttondependente->setParameter('fk', '' . filter_input(INPUT_GET, 'fk') . '');

        $dropdown = new TDropDown('Ação', 'fa:list');
        $dropdown->addAction( 'Documento', $buttondependente, 'onReload') ;
        //$dropdown->addAction( 'Movimentação', new TAction(array('SocioList', 'onReload') ));


        $this->form = new BootstrapFormBuilder('form_Processo');
        $this->form->setFormTitle('Formulário de Processos');

        $codigo = new THidden('id');
        $nome = new TEntry('nome');
        $numero = new TEntry('numero');
        $origem = new TEntry('origem');
        $autor = new TEntry('autor');
        $descricao = new TText('descricao');
        $situacao = new TCombo('situacao');


        $nome->setProperty('placeholder', 'Nome do Processo');
        $nome->setSize('40%');

        $numero->setSize('40%');
        $numero->setProperty('placeholder', 'Numero do Processo');

        $origem->setSize('40%');
        $origem->setProperty('placeholder', 'Origem do Processo');

        $autor->setSize('40%');
        $autor->setProperty('placeholder', 'Autor do Processo');

        $descricao->setSize( '40%', 100);
        $situacao->addItems( ['TRAMITANDO' => 'TRAMITANDO',
            'ENCERRADO' => 'ENCERRADO' ] );

        $situacao->setvalue('TRAMITANDO');


        $titulo = new TLabel('<div style="position:floatval; width: 200px;"> <b>* Campo obrigatorio</b></div>');
        $titulo->setFontFace('Arial');
        $titulo->setFontColor('red');
        $titulo->setFontSize(10);


        $this->form->addFields( [ new TLabel('Nome <font color=red><b>*</b></font>') ],[ $nome ] );
        $this->form->addFields( [ new TLabel('Numero <font color=red><b>*</b></font>') ],[ $numero ] );
        $this->form->addFields( [ new TLabel('Origem') ],[ $origem ] );
        $this->form->addFields( [ new TLabel('Autor') ],[ $autor ] );
        $this->form->addFields( [ new TLabel('Descrição') ],[ $descricao ] );
        $this->form->addFields( [ new TLabel('Situação <font color=red><b>*</b></font>') ],[ $situacao ] );

        $this->form->addFields( [''], [ $codigo ] );

        $this->form->addAction( 'Salvar', new TAction( array( $this, 'onSave' ) ), 'ico_save.png' );
        $this->form->addAction('Voltar', new TAction(array('ProcessoList', 'onReload')), 'back_blue_arrow.png');

        $vbox2 = new TVBox;

        if (filter_input(INPUT_GET, 'key'))
        {
            $vbox2->add($dropdown);
        }

        parent::add($vbox2);


        $vbox = new TVBox;
        $vbox->add($this->form);
        parent::add($this->form);

    }

    function onSave()
    {

        TTransaction::open('pg_ceres');

        $cadastro = $this->form->getData( 'ProcessoRecord' );

        $cadastro->usuarioalteracao = $_SESSION['usuario'];
        $cadastro->dataalteracao = date("d/m/Y H:i:s");

        $dados = $cadastro->toArray();

        $msg = '';
        $icone = 'info';

        if( empty( $dados['nome'] ) )
        {

            $msg .= 'O Nome deve ser informado.<br>';

        }
        if( empty( $dados['numero'] ) )
        {

            $msg .= 'O Numero deve ser informado.<br>';

        }
        if( empty( $dados['situacao'] ) )
        {

            $msg .= 'A Situação deve ser informada.<br>';

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

                $param = array();
                $param['key'] = $cadastro->id;
                $param['fk'] = $cadastro->id;
                TApplication::gotoPage( 'ProcessoForm', 'onEdit',$param );

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

                $object = new ProcessoRecord( $key );  // instantiates object City

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