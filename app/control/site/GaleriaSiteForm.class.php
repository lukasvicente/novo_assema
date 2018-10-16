<?php

/*
 * classe SocioForm
 * Cadastro de Socio: Contem o formularo
 */
 
//ini_set('display_errors', 1);
//ini_set('display_startup_erros', 1);
//error_reporting(E_ALL);
include_once 'app/lib/funcdate.php';
class GaleriaSiteForm extends TPage
{

    private $form;     // formulario de cadastro
    private $frame;

    public function __construct() {
		
        parent::__construct();


        $this->form = new BootstrapFormBuilder('form_noticia_site');
        $this->form->setFormTitle('Formulário da Galeria');

        $codigo = new THidden('id');

        $nome_titulo = new TEntry('titulo');
        $descricao = new TText('descricao');
        $descricao->setSize( '80%', 200);
        $situacao = new TCombo('situacao');

        $datapublicacao = new TDateTime('datapublicacao');
        //$fimpublicacao = new TDateTime('fimpublicacao');

        $imagem    = new TFile('arquivo');

        $imagem->setCompleteAction(new TAction(array($this, 'onComplete')));
        //$imagem->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg'] );


        $situacao->addItems( ['ATIVO' => 'ATIVO',
                                'INATIVO' => 'INATIVO' ] );

        $situacao->setvalue('ATIVO');

        $nome_titulo->setProperty('placeholder', 'Titulo da Galeria');


        $titulo = new TLabel('<div style="position:floatval; width: 200px;"> <b>* Campo obrigatorio</b></div>');
        $titulo->setFontFace('Arial');
        $titulo->setFontColor('red');
        $titulo->setFontSize(10);

        $this->form->appendPage('Artigo');
        $this->form->addFields( [ new TLabel('Titulo <font color=red><b>*</b></font>') ],[ $nome_titulo ] );

        $this->form->addFields( [ new TLabel('Descrição') ],[ $descricao ] );
        $this->form->addFields( [ new TLabel('Situação <font color=red><b>*</b></font>') ],[ $situacao ] );

        $this->form->appendPage('Opções');
        $this->form->addFields( [ new TLabel('Data da Publicação <font color=red><b>*</b></font>') ],[ $datapublicacao ] );

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
        $this->form->addAction('Voltar', new TAction(array('GaleriaSiteList', 'onReload')), 'back_blue_arrow.png');

        $vbox = new TVBox;
        $vbox->add($this->form);
        parent::add($this->form);

    }
    public static function onComplete($param)
    {
        new TMessage('info', 'Upload completed: '.$param['arquivo']);

        // refresh photo_frame
        TScript::create("$('#photo_frame').html('')");
        TScript::create("$('#photo_frame').append(\"<img style='width:100%' src='tmp/{$param['arquivo']}'>\");");
    }

    function onSave()
    {

        TTransaction::open('pg_ceres');

        $cadastro = $this->form->getData( 'GaleriaSiteRecord' );

        $cadastro->usuarioalteracao = $_SESSION['usuario'];
        $cadastro->dataalteracao = date("d/m/Y H:i:s");
        //$cadastro->apelido = strtolower(tiraAcento($cadastro->titulo));

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
                if ( !empty( $dados['arquivo'] ) and $dados['arquivo'] != "semimagem.jpg"){

                $source_file   = 'tmp/'.$cadastro->arquivo;

                $cadastro->arquivo = 'foto_' .$cadastro->id.".jpg";
                $cadastro->arquivo = 'temp';

                $cadastro->store();

                $arquivo = 'foto_' . $cadastro->id . '.jpg';

                $cadastro->arquivo = $arquivo;

				$caminho = 'app/images/site/galeria/' . strtolower($cadastro->arquivo);
                rename($source_file, $caminho);

                }else{

                $cadastro->arquivo = "semimagem.jpg";

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
                TApplication::gotoPage( 'GaleriaSiteForm', 'onEdit',$param );

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

                $object = new GaleriaSiteRecord( $key );  // instantiates object City

                TScript::create("$('#photo_frame').html('')");
                TScript::create("$('#photo_frame').append(\"<img style='width:100%' src='app/images/site/galeria/{$object->arquivo}'>\");");

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