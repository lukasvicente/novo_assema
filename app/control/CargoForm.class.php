<?php

/*
 * classe CargoForm
 * Cadastro de Cargo: Contem o formularo
 */
 
//ini_set('display_errors', 1);
//ini_set('display_startup_erros', 1);
//error_reporting(E_ALL);

class CargoForm extends TPage 
{

    private $form;     // formulario de cadastro

    public function __construct() {
		
        parent::__construct();

        $this->form = new BootstrapFormWrapper( new TQuickForm );
        $panel = new TPanelGroup('Formulario de Cargo');

        // cria os campos do formulario
        $codigo = new THidden('id');
        $nome = new TEntry('nome');
        $perfil = new TText('perfil');
        $usuarioalteracao = new THidden('usuarioalteracao');
        $dataalteracao = new THidden('dataalteracao');

		// cria um rotulo para o titulo
        $titulo = new TLabel('<div style="position:floatval; width: 200px;"> <b>* Campo obrigatorio</b></div>');
        $titulo->setFontFace('Arial');
        $titulo->setFontColor('red');
        $titulo->setFontSize(10);

		// define os campos
        $this->form->addQuickField( null, $codigo, 10 );
		$this->form->addQuickField(null, $usuarioalteracao, 10);
        $this->form->addQuickField(null, $dataalteracao, 10);
		$this->form->addQuickField("Nome <font color=red><b>*</b></font>", $nome, '50%');
		//$this->form->addQuickField("Perfil", $perfil, 50 );
		$this->form->addQuickField(null, $titulo, 50);

        //Define o auto-sugerir
        $nome->setProperty('placeholder', 'Informe o Cargo');

		// cria um botao de acao
        $this->form->addQuickAction( 'Salvar', new TAction( array( $this, 'onSave' ) ), 'ico_save.png' );
        $this->form->addQuickAction('Voltar', new TAction(array('CargoList', 'onReload')), 'back_blue_arrow.png');

		
        $panel->add($this->form);
        $panel->style = 'width: 250%';
        $vbox = new TVBox;
        //$vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->alertBox);
        $vbox->add($panel);

        parent::add($vbox);
        // adiciona a tabela a pagina
        //parent::add($this->form);
		
    }

    /*
     * metodo onSave()
     * Executada quando o usuario clicar no botao salvar do formulario
     */
    function onSave() 
	{
		
        // inicia transacao com o banco 'pg_ceres'
        TTransaction::open('pg_ceres');
		
        // obtem os dados no formulario em um objeto CarroRecord
        $cadastro = $this->form->getData( 'CargoRecord' );

        $dados = $cadastro->toArray();
        $msg = '';
        $icone = 'info';

        if( empty( $dados['nome'] ) )
		{
			
            $msg .= 'O Nome deve ser informado.';
			
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
				
				TApplication::gotoPage( 'CargoList', 'onReload' ); // reload
				
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

                $object = new CargoRecord( $key );  // instantiates object City

                $this->form->setData( $object );   // fill the form with the active record data

                TTransaction::close();           // close the transaction
				
            }else 
			{
				
                $this->form->clear();
				
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