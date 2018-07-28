<?php

/*
 * classe SocioForm
 * Cadastro de Socio: Contem o formularo
 */
 
//ini_set('display_errors', 1);
//ini_set('display_startup_erros', 1);
//error_reporting(E_ALL);
include_once 'app/lib/funcdate.php';
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
        $fimpublicacao = new TDateTime('fimpublicacao');
        $tipo      = new TRadioGroup('tipo');
        $imagem    = new TFile('nomearquivo');
        $site_categoria_id = new TCombo('site_categoria_id');

        TTransaction::open('pg_ceres');

        // load items from repository
        $collection = CategoriaSiteRecord::all();

        // add the combo items
        $items = array();
        foreach ($collection as $object)
        {
            $items[$object->id] = $object->nome;
        }
        TTransaction::close();

        $site_categoria_id->addItems($items);

        $imagem->setCompleteAction(new TAction(array($this, 'onComplete')));
        //$imagem->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg'] );

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
        $this->form->addFields( [ new TLabel('Titulo <font color=red><b>*</b></font>') ],[ $nome_titulo ] );
        $this->form->addFields( [ new TLabel('Tipo da Publicação <font color=red><b>*</b></font>') ],[ $tipo ] );
        $this->form->addFields( [ new TLabel('Categoria') ],[ $site_categoria_id ] );
        $this->form->addFields( [ new TLabel('Descrição') ],[ $descricao ] );
        $this->form->addFields( [ new TLabel('Situação <font color=red><b>*</b></font>') ],[ $situacao ] );
        $this->form->appendPage('Opções');
        $this->form->addFields( [ new TLabel('Data da Publicação <font color=red><b>*</b></font>') ],[ $datapublicacao ] );
        $this->form->addFields( [ new TLabel('Fim da Publicação') ],[ $fimpublicacao ] );
        $this->form->addFields( [ new TLabel('Autor') ],[ $autor ] );
        $this->form->appendPage('Imagem');
        $this->form->addFields( [ new TLabel('Imagem') ],[ $imagem ] );

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
        $cadastro->apelido = strtolower(tiraAcento($cadastro->titulo));

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
        if( empty( $dados['datapublicacao'] ) )
        {

            $msg .= 'A data da publicação ser informada.';

        }

        try
        {

            if( $msg == '' )
            {
                if ( !empty( $dados['nomearquivo'] ) and $dados['nomearquivo'] != "semimagem.jpg"){

                $source_file   = 'tmp/'.$cadastro->nomearquivo;

                $cadastro->nomearquivo = 'noticia_' .$cadastro->id.".jpg";
                $cadastro->nomearquivo = 'temp';

                $cadastro->store();

                $nomearquivo = 'noticia_' . $cadastro->id . '.jpg';

                $cadastro->nomearquivo = $nomearquivo;

				$caminho = 'app/images/site/' . strtolower($cadastro->nomearquivo);
                rename($source_file, $caminho);

                }else{

                $cadastro->nomearquivo = "semimagem.jpg";

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

                TScript::create("$('#photo_frame').html('')");
                TScript::create("$('#photo_frame').append(\"<img style='width:100%' src='app/images/site/{$object->nomearquivo}'>\");");

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