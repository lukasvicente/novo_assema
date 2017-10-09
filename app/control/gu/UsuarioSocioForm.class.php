<?php

/*
 * classe UsuarioSocioForm
 * Cadastro de UsuarioSocio: Contem o formularo
 */
 
//ini_set('display_errors', 1);
//ini_set('display_startup_erros', 1);
//error_reporting(E_ALL);

class UsuarioSocioForm extends TPage 
{

    private $form;     // formulario de cadastro

    public function __construct() {
        
        parent::__construct();

        $this->form = new BootstrapFormWrapper( new TQuickForm('form_usuario') );

        $panel = new TPanelGroup('Formulario de Usuario Socio');

        // cria os campos do formulario
        $codigo = new THidden('id');
        $socio_id = new TCombo('socio_id');
        //$socio_id = new TMultiSearch('socio_id');
        $login = new TEntry('login');
        $senha = new TPassword('senha');
        $expira = new TCombo('expira');
        $dataexpira = new TDate('dataexpira');
        $usuarioalteracao = new THidden('usuarioalteracao');
        $dataalteracao = new THidden('dataalteracao');

        TTransaction::open('pg_ceres');

        $collection = SocioRecord::all();
        
        // add the combo items
        $items = array();
        foreach ($collection as $object)
        {
            $items[$object->id] = $object->nome." / ".$object->matricula;
        }
        TTransaction::close();

        $socio_id->addItems($items);

        //$socio_id->setMinLength(1);
        //$socio_id->setMaxSize(1);

        $expira->setChangeAction(new TAction(array($this, 'onChangeExpira')));
        $combo_items = array();
        $combo_items['n'] ='NAO';
        $combo_items['s'] ='SIM';
        $expira->addItems($combo_items);
 
        $expira->setValue('n');
        self::onChangeExpira( ['expira' => 'n'] );

        // cria um rotulo para o titulo
        $titulo = new TLabel('<div style="position:floatval; width: 200px;"> <b>* Campo obrigatorio</b></div>');
        $titulo->setFontFace('Arial');
        $titulo->setFontColor('red');
        $titulo->setFontSize(10);

        // define os campos
        $this->form->addQuickField( null, $codigo, 10 );
        $this->form->addQuickField(null, $usuarioalteracao, 10);
        $this->form->addQuickField(null, $dataalteracao, 10);
        $this->form->addQuickField("Socio <font color=red><b>*</b></font>", $socio_id );
        $this->form->addQuickField("Login <font color=red><b>*</b></font>", $login );
        $this->form->addQuickField("Senha <font color=red><b>*</b></font>", $senha );
        $this->form->addQuickField("Expira ", $expira,'20%' );
        $this->form->addQuickField("Data Expira ", $dataexpira,'30%' );
        $this->form->addQuickField(null, $titulo, 50);

        //Define o auto-sugerir
        //$socio_id->setProperty('placeholder', 'Buscar Nome ou Matricula');
        $login->setProperty('placeholder', 'Matricula');
        $senha->setProperty('placeholder', 'CPF');
        //$socio_id->setSize(300,30);
        // cria um botao de acao
        $this->form->addQuickAction( 'Salvar', new TAction( array( $this, 'onSave' ) ), 'ico_save.png' );
        $this->form->addQuickAction('Voltar', new TAction(array('UsuarioSocioList', 'onLoad')), 'back_blue_arrow.png');

        
        $panel->add($this->form);
        $panel->style = 'width: 180%';
        $vbox = new TVBox;
        //$vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->alertBox);
        $vbox->add($panel);

        parent::add($vbox);
        // adiciona a tabela a pagina
        //parent::add($this->form);
        
    }
    public static function onChangeExpira($param)
    {
        if ($param['expira'] == 'n')
        {
            
            TQuickForm::hideField('form_usuario', 'dataexpira');
        }
        if ($param['expira'] == 's') 
        
        {
            
            TQuickForm::showField('form_usuario', 'dataexpira');
        }else{
            
            TQuickForm::hideField('form_usuario', 'dataexpira');
        }
    }

    function onSave() 
    {
        
        // inicia transacao com o banco 'pg_ceres'
        TTransaction::open('pg_ceres');
        
        // obtem os dados no formulario em um objeto CarroRecord
        $cadastro = $this->form->getData( 'UsuarioRecord' );
                //lanca o default
        $cadastro->usuarioalteracao = $_SESSION['usuario'];
        $cadastro->dataalteracao = date("d/m/Y H:i:s");

        $dados = $cadastro->toArray();
        $msg = '';
        $icone = 'info';

        if( empty( $dados['socio_id'] ) )
        {
            
            $msg .= 'O Socio deve ser informado.</br>';
            
        }
        if( empty( $dados['login'] ) )
        {
            
            $msg .= 'O Login deve ser informado.</br>';
            
        }
        if( empty( $dados['senha'] ) )
        {
            
            $msg .= 'A Senha deve ser informado.';
            
        }
        try 
        {

            if( $msg == '' ) 
            {
                
                // armazena o objeto no banco
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
                
                TApplication::gotoPage( 'UsuarioSocioList', 'onload' ); // reload
                
            }
            
        }catch (Exception $e) 
        {

            // em caso de exc
            // exibe a mensagem gerada pela exce��o
            new TMessage('error', $e->getMessage() );
            
            // desfaz todas altera��es no banco de dados
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

                // get the parameter $key
                $key = $param['key'];

                TTransaction::open('pg_ceres');   // open a transaction with database 'samples'

                $object = new UsuarioRecord( $key );  // instantiates object City

                $this->form->setData( $object );   // fill the form with the active record data

                TTransaction::close();           // close the transaction
                
            }
        } catch (Exception $e) 
        {

            // in case of exception
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
            
        }
      
    }

}
?>