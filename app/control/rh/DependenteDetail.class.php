<?php
class DependenteDetail extends TWindow
{
    private $form;
    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Dependente" );
        parent::setSize( 0.600, 0.800 );
        $redstar = '<font color="red"><b>*</b></font>';
        $this->form = new BootstrapFormBuilder( "detail_prescricao_medicao" );
        $this->form->setFormTitle( "({$redstar}) campos obrigatórios" );
        $this->form->class = "tform";
        $id                 = new THidden("id");
        $bau_id             = new THidden("bau_id");
        $paciente_id        = new THidden( "paciente_id" );
        $medicamento_id     = new THidden( "medicamento_id" );
        $bauatendimento_id  = new THidden( "bauatendimento_id" );
        $paciente_nome      = new TEntry( "paciente_nome" );
        $medico_id          = new THidden("medico_id");
        $data_prescricao    = new TDateTime("data_prescricao");
        $dosagem            = new TEntry("dosagem");
        $posologia          = new TCombo("posologia");
        $observacao         = new TText("observacao");
        $criteria3 = new TCriteria;
        $principioativo_id = new TDBMultiSearch('principioativo_id', 'database', 'VwPrincipioAtivoMedicamentoRecord', 'medicamento_id', 'principiomedicamento', 'principiomedicamento', $criteria3);
        $principioativo_id->style = "text-transform: uppercase;";
        $principioativo_id->setProperty('placeholder', 'DIGITE O NOME OU PRINCIPIO ATIVO');
        $principioativo_id->setMinLength(1);
        $principioativo_id->setMaxSize(1);
        $id2 = filter_input( INPUT_GET, "key" );
        $fk = filter_input( INPUT_GET, "fk" );
        $did = filter_input( INPUT_GET, "did" );
        $bauatendimento_id->setValue ($id2);
        $bau_id->setValue ($fk);
        $paciente_id->setValue ($did);
        
            TTransaction::open( "pg_ceres" );
            //$bau = new BauRecord( $fk );
            $socio = new SocioRecord( $did );
            if(   ( $socio ) ) {
                $paciente_id->setValue( $paciente->id );
                $paciente_nome->setValue( $socio->nome );
            }
            TTransaction::close();
          
        $dosagem->placeholder = 'Ex: 40 Gotas ou 1 Comp...';
        $principioativo_id      ->setSize( "70%" );
        $paciente_nome          ->setSize( "70%" );
        $data_prescricao         ->setSize( "20%" );
        $observacao               ->setSize( "70%" );
        $posologia->addItems( [
            "4" => "6x ao Dia",
            "6" => "4x ao Dia",
            "8" => "3x ao Dia",
            "12" => "2x ao Dia",
            "24" => "1x ao Dia",
            "25" => "Após da Refeição",
            "26" => "Antes da Refeição" ] );
       
        $this->form->addFields( [ new TLabel( "Paciente:" ) ], [ $paciente_nome, $data_prescricao ] );
        $this->form->addFields( [ new TLabel( "Medicamento:{$redstar}" ) ], [ $principioativo_id ] );
        $this->form->addFields( [ new TLabel( "Dosagem:{$redstar}" ) ], [ $dosagem ] );
        $this->form->addFields( [ new TLabel( "Posologia:{$redstar}" ) ], [ $posologia ] );
        $this->form->addFields( [ new TLabel( "Observação" ) ], [ $observacao ] );
        $this->form->addFields( [ $id, $paciente_id, $medico_id, $bauatendimento_id, $bau_id, $medicamento_id ] );
        $onSave   = new TAction( [ $this, "onSave" ] );
        $onSave->setParameter( "fk", $fk );
        $onSave->setParameter( "did", $did );
        //$onReload = new TAction( [ "AtendimentoDetail", "onReload" ] );
       // $onReload->setParameter( "fk", $fk );
        //$onReload->setParameter( "did", $did );
        
        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->setAction( new TAction( [ $this, "onReload" ] ) );
       // $this->pageNavigation->setWidth( $this->datagrid->getWidth() );
        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );
        $container->add( $this->pageNavigation );
        parent::add( $container );
    }
    public function onSave( $param = null )
    {
        try {
            $this->form->validate();
            $object = $this->form->getData( "BauPrescricaoRecord" );
            $object->medicamento_id = key($object->principioativo_id);
            $object->medico_id = TSession::getValue('profissionalid');
            TTransaction::open( "database" );
            unset($object->paciente_nome);
            unset($object->principioativo_id);
            $object->store();
            TTransaction::close();
            $action = new TAction( [ $this, "onReload" ] );
            $action->setParameters( $param );
            new TMessage( "info", "Medicação Prescrita com sucesso!", $action );
        } catch ( Exception $ex ) {
            TTransaction::rollback();
            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );
        }
    }
    public function onDelete( $param = null )
    {
        if( isset( $param[ "key" ] ) ) {
            $param = [
            "key" => $param[ "key" ],
            "fk"  => $param[ "fk" ],
            "did"  => $param[ "did" ]
            ];
            $action1 = new TAction( [ $this, "Delete" ] );
            $action2 = new TAction( [ $this, "onReload" ] );
            $action1->setParameters( $param );
            $action2->setParameters( $param );
            new TQuestion( "Deseja realmente apagar o registro?", $action1, $action2 );
        }
    }
    public function Delete( $param = null )
    {
        try {
            TTransaction::open( "database" );
            $object = new BauPrescricaoRecord( $param[ "key" ] );
            $object->delete();
            TTransaction::close();
            $this->onReload( $param );
            new TMessage( "info", "O Registro foi apagado com sucesso!" );
        } catch ( Exception $ex ) {
            TTransaction::rollback();
            new TMessage( "error", $ex->getMessage() );
        }
    }
    public function onReload( $param )
    {
        try {
            TTransaction::open( "pg_ceres" );
            $repository = new TRepository( "DependenteRecord" );
            //$properties = [ "order" => "data_prescricao", "direction" => "desc" ];
            $limit = 10;
            $criteria = new TCriteria();
            $criteria->setProperties( $properties );
            $criteria->setProperty( "limit", $limit );
            $criteria->add( new TFilter( "socio_id", "=", $param[ "fk" ] ) );
            $objects = $repository->load( $criteria, FALSE );
            if ( isset( $objects ) ) {
                //$this->datagrid->clear();
                foreach ( $objects as $object ) {
                    $object->data_prescricao = TDate::date2br($object->data_prescricao);
                    $this->datagrid->addItem( $object );
                }
            }
            $criteria->resetProperties();
            $count = $repository->count( $criteria );
            $this->pageNavigation->setCount( $count );
            $this->pageNavigation->setProperties( $properties );
            $this->pageNavigation->setLimit( $limit );
            TTransaction::close();
            $this->loaded = true;
        } catch ( Exception $ex ) {
            TTransaction::rollback();
            new TMessage( "error", $ex->getMessage() );
        }
    }
}