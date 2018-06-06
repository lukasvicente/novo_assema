<?php

/*
 * classe SocioForm
 * Cadastro de Socio: Contem o formularo
 */
 
//ini_set('display_errors', 1);
//ini_set('display_startup_erros', 1);
//error_reporting(E_ALL);

class NoticiaSiteForm extends TPage
{

    private $form;     // formulario de cadastro
    private $frame;

    public function __construct() {
		
        parent::__construct();
        

        $this->form = new BootstrapFormBuilder('form_noticia_site');
        $this->form->setFormTitle('Formulário de Conteúdo Notícia');

        $codigo = new THidden('id');

        $nome_titulo = new TEntry('titulo');
        $descricao = new THtmlEditor('descricao');
        $descricao->setSize( '100%', 200);
        $situacao = new TCombo('situacao');
        $autor = new TEntry('autor');
        $datapublicacao = new TDateTime('datapublicacao');
        $tipo      = new TRadioGroup('tipo');
        $imagem    = new TFile('nomearquivo');

        $imagem->setCompleteAction(new TAction(array($this, 'onComplete')));
        $imagem->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg'] );

        $items = array( 'SLIDE' => 'Slide', 'DESTAQUE' => 'Destaque');
        $tipo->addItems($items);
        $tipo->setLayout('horizontal');
        $tipo->setValue('SLIDE');

        $situacao->addItems( ['ATIVO' => 'ATIVO',
                                'INATIVO' => 'INATIVO' ] );

        $situacao->setvalue('ATIVO');

        $nome_titulo->setProperty('placeholder', 'Titulo do Conteudo');


        $titulo = new TLabel('<div style="position:floatval; width: 200px;"> <b>* Campo obrigatorio</b></div>');
        $titulo->setFontFace('Arial');
        $titulo->setFontColor('red');
        $titulo->setFontSize(10);

        $this->form->appendPage('Artigo');
        //$this->form->addFields( [ new TLabel('Nome <font color=red><b>*</b></font>') ],[ $nome ] );
        $this->form->addFields( [ new TLabel('Titulo <font color=red><b>*</b></font>') ],[ $nome_titulo ] );
        $this->form->addFields( [ new TLabel('Tipo Publicação <font color=red><b>*</b></font>') ],[ $tipo ] );
        $this->form->addFields( [ new TLabel('Descrição') ],[ $descricao ] );
        $this->form->addFields( [ new TLabel('Situação <font color=red><b>*</b></font>') ],[ $situacao ] );
        $this->form->appendPage('Opções');
        $this->form->addFields( [ new TLabel('Data Publicação <font color=red><b>*</b></font>') ],[ $datapublicacao ] );
        $this->form->addFields( [ new TLabel('Autor <font color=red><b>*</b></font>') ],[ $autor ] );
        $this->form->appendPage('Imagem');
        $this->form->addFields( [ new TLabel('Imagem<font color=red><b>*</b></font>') ],[ $imagem ] );

        $this->form->addFields( [''], [ $codigo ] );

        $nome_titulo->setSize('80%');
        $imagem->setSize('50%');

        $this->frame = new TElement('div');
        $this->frame->id = 'photo_frame';
        $this->frame->style = 'width:400px;height:auto;min-height:200px;border:1px solid gray;padding:4px;';
        $this->form->addContent(array($this->frame));
        //$row->addCell('');
        //$row->addCell($this->frame);

        $this->form->addAction( 'Salvar', new TAction( array( $this, 'onSave' ) ), 'ico_save.png' );
        $this->form->addAction('Voltar', new TAction(array('NoticiaSiteList', 'onReload')), 'back_blue_arrow.png');

        $vbox = new TVBox;
        $vbox->add($this->form);
        parent::add($this->form);

    }
    public static function onComplete($param)
    {
        new TMessage('info', 'Upload completed: '.$param['nomearquivo']);

        // refresh photo_frame
        TScript::create("$('#photo_frame').html('')");
        TScript::create("$('#photo_frame').append(\"<img style='width:100%' src='tmp/{$param['nomearquivo']}'>\");");
    }

    function onSave()
    {

        TTransaction::open('pg_ceres');

        $cadastro = $this->form->getData( 'NoticiaSiteRecord' );

        $cadastro->usuarioalteracao = $_SESSION['usuario'];
        $cadastro->dataalteracao = date("d/m/Y H:i:s");

        $dados = $cadastro->toArray();

        $msg = '';
        $icone = 'info';

        if( empty( $dados['titulo'] ) )
        {

            $msg .= 'O Titulo deve ser informado.<br>';

        }
        if( empty( $dados['situacao'] ) )
        {

            $msg .= 'A Situação deve ser informada.';

        }

        $source_file   = 'tmp/'.$cadastro->nomearquivo;
        $target_file   = 'app/images/site/noticia/' . $cadastro->nomearquivo;
        $finfo         = new finfo(FILEINFO_MIME_TYPE);


            // move to the target directory
           // rename($source_file, $target_file);

        try
        {

            if( $msg == '' )
            {
                if ( !empty( $dados['nomearquivo'] )){

                $cadastro->nomearquivo = 'noticia_' .$cadastro->id.".jpg";

                }else{
                    $cadastro->nomearquivo = "semimagem.jpg";
                }
                $caminho = 'app/images/site/' . strtolower($cadastro->nomearquivo);
                rename($source_file, $caminho);

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
                TApplication::gotoPage( 'NoticiaSiteForm', 'onEdit',$param );

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

                $object = new NoticiaSiteRecord( $key );  // instantiates object City

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