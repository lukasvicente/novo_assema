<?php

/*
 * classe ModuloForm
 * Cadastro de Modulo: Contem o formularo
 */
 
//ini_set('display_errors', 1);
//ini_set('display_startup_erros', 1);
//error_reporting(E_ALL);

class PaginaForm extends TPage 
{

    private $form;     // formulario de cadastro

    public function __construct() {
        
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_modulo');
        $this->form->setFormTitle('Formulário de Pagina');

        $titulo = new TTextDisplay('* Campo obrigat&oacute;rio', 'red', 12, 'bi');
        $id = new THidden('id');
        $nome = new TEntry('nome');
        $arquivo = new TEntry('arquivo');
        $modulo_id  = new TDBCombo('modulo_id', 'pg_ceres', 'ModuloRecord', 'id', 'nome');
        $grupomenu_id  = new TDBCombo('grupomenu_id', 'pg_ceres', 'GrupoMenuRecord', 'id', 'nome');
        $situacao = new TCombo('situacao');
        $novajanela = new TCombo('novajanela');

        $nome->setProperty('placeholder', 'Ex.: Socio');
        $arquivo->setProperty('placeholder', 'Ex.: SocioList');

        $items = array();
        $items['ATIVO'] = 'ATIVO';
        $items['INATIVO'] = 'INATIVO';
        
        $situacao->addItems($items);
        $situacao->setValue('ATIVO');

        $items1 = array();
        $items1['SIM'] = 'SIM';
        $items1['NAO'] = 'NAO';
        
        $novajanela->addItems($items1);
        $novajanela->setValue('NAO');
        
        
        $this->form->addFields( [new TLabel('Nome <font color=red><b>*</b></font>')], [$nome] );
        $this->form->addFields( [new TLabel('Arquivo <font color=red><b>*</b></font>')], [$arquivo] );
        $this->form->addFields( [new TLabel('Modulo <font color=red><b>*</b></font>')], [$modulo_id] );
        $this->form->addFields( [new TLabel('Grupo Menu <font color=red><b>*</b></font>')], [$grupomenu_id] );
        $this->form->addFields( [new TLabel('Situa&ccedil;&atilde;o <font color=red><b>*</b></font>')], [$situacao] );
        $this->form->addFields( [new TLabel('Nova Janela <font color=red><b>*</b></font>')], [$novajanela] );
        $this->form->addFields( [new TLabel('')], [$id] );
        $this->form->addFields( [new TLabel('')], [$titulo] );

        $this->form->addAction('Salvar', new TAction(array($this, 'onSave')), 'ico_save.png');
        $this->form->addAction('Voltar',  new TAction(array('PaginaList', 'onReload')), 'back_blue_arrow.png');


        $container = new TVBox;

        $container->style = 'width: 90%';
        //$container->add(new TXMLBreadCrumb('menu.xml', 'SystemUnitList'));
        $container->add($this->form);
        
        parent::add($container);
    
        
    }


    function onSave() 
    {

        TTransaction::open('pg_ceres');

        $cadastro = $this->form->getData( 'PaginaRecord' );

        $cadastro->usuarioalteracao = $_SESSION['usuario'];
        $cadastro->dataalteracao = date("d/m/Y H:i:s");

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
                
                TApplication::gotoPage( 'PaginaList', 'onReload' ); // reload
                
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

                $object = new PaginaRecord( $key );  // instantiates object City

                $this->form->setData( $object );   // fill the form with the active record data

                TTransaction::close();           // close the transaction
                
            }else 
            {
                
                //$this->form->clear();
                
            }
            
        } catch (Exception $e) 
        {

            new TMessage('error', '<b>Error</b> ' . $e->getMessage());

            TTransaction::rollback();
            
        }
      
    }

}
?>