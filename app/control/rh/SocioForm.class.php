<?php

/*
 * classe SocioForm
 * Cadastro de Socio: Contem o formularo
 */
 
//ini_set('display_errors', 1);
//ini_set('display_startup_erros', 1);
//error_reporting(E_ALL);

class SocioForm extends TPage 
{

    private $form;     // formulario de cadastro
    private $form1; 

    public function __construct() {
		
        parent::__construct();
        $script = new TElement('script'); 
$script->type = 'text/javascript';
        
$script->add(" 
    function limpa_formulário_cep() {
            //Limpa valores do formulário de cep.
            document.getElementById('rua').value=('');
            document.getElementById('bairro').value=('');
            document.getElementById('cidade').value=('');
            document.getElementById('uf').value=('');
            
    }
        function meu_callback(conteudo) {
        if (!('erro' in conteudo)) {
            //Atualiza os campos com os valores.
            document.getElementById('rua').value=(conteudo.logradouro);
            document.getElementById('bairro').value=(conteudo.bairro);
            document.getElementById('cidade').value=(conteudo.localidade);
            document.getElementById('uf').value=(conteudo.uf);
            
        } //end if.
        else {
            //CEP não Encontrado.
            limpa_formulário_cep();
            alert('CEP não encontrado.');
        }
    }

    function pesquisacep(valor) {

        
        var cep = valor.replace(/\D/g, '');

        //Verifica se campo cep possui valor informado.
        if (cep != '') {

            //Expressão regular para validar o CEP.
            var validacep = /^[0-9]{8}$/;

            //Valida o formato do CEP.
            if(validacep.test(cep)) {

                
                document.getElementById('rua').value='...';
                document.getElementById('bairro').value='...';
                document.getElementById('cidade').value='...';
                document.getElementById('uf').value='...';
                //document.getElementById('ibge').value='...';

                //Cria um elemento javascript.
                var script = document.createElement('script');

                //Sincroniza com o callback.
                script.src = 'https://viacep.com.br/ws/'+ cep + '/json/?callback=meu_callback';

                //Insere script no documento e carrega o conteúdo.
                document.body.appendChild(script);

            } //end if.
            else {
                //cep é inválido.
                limpa_formulário_cep();
                alert('Formato de CEP inválido.');
            }
        } //end if.
        else {
            //cep sem valor, limpa formulário.
            limpa_formulário_cep();
        }
    };");
        
        parent::add($script); 

        $this->form = new BootstrapFormBuilder('form_socio');
        $this->form->setFormTitle('Formulario de Sócio');
        
        ########### DADOS PESSOAIS ################## 
        $codigo = new THidden('id');
        $nome = new TEntry('nome');
        $matricula = new TEntry('matricula');
        $cpf = new TEntry('cpf');
        $dtnascimento = new TDate('dtnascimento');
        $sexo = new TRadioGroup('sexo');
        $telefonefixo = new TEntry('telefonefixo');
        $telefonecelular = new TEntry('telefonecelular');
        $email = new TEntry('email');
        $estadocivil = new TCombo('estadocivil');
        ########### ENDEREÇO ##################
        $endereco = new TEntry('endereco');
        $complemento = new TEntry('complemento');
        $bairro = new TEntry('bairro');
        $cidade = new TEntry('cidade');
        $uf = new TCombo('uf');
        $cep = new TEntry('cep');
        ########### DOCUMENTOS ##################
        $rg = new TEntry('identidade');
        $orgaorg = new TEntry('orgaoidentidade');
        $dtrgexpedicao = new TDate('dtrgexpedicao');
        $banco = new TCombo('banco');
        $agencia = new TEntry('agencia');
        $contacorrente = new TEntry('contacorrente');
        $tiposocio = new TCombo('tiposocio');
        $fundador = new TCheckGroup('fundador');
        $situacaoemater = new TCombo('situacaoemater');
        $secretaria = new TEntry('secretaria');

       
        $cep->setProperty('onblur', 'pesquisacep(this.value);');
        $cep->setProperty('ID', 'cep');
        $cidade->setProperty('ID', 'cidade');
        $endereco->setProperty('ID', 'rua');
        $bairro->setProperty('ID', 'bairro');
        $uf->setProperty('ID', 'uf');
 
        $observacao = new TText('observacao');
        $consultar_cep = new THyperLink('Consultar CEP', 'http://www.buscacep.correios.com.br/sistemas/buscacep/BuscaCepEndereco.cfm', 'blue', 11, 'biu');

        $tiposocio->setChangeAction(new TAction(array($this, 'onChangeTipoSocio')));
        $combo_items = array();
        $combo_items['EFETIVO'] ='EFETIVO';
        $combo_items['CONTRIBUINTE'] ='CONTRIBUINTE';
        $tiposocio->addItems($combo_items);
 
        $tiposocio->setValue('EFETIVO');
        self::onChangeTipoSocio( ['tiposocio' => 'EFETIVO'] );
        
        $sexo->addItems( [ 'M' => 'Masculino', 'F' => 'Feminino' ] );
        $sexo->setLayout('horizontal');
        $estadocivil->addItems( [ 'SOLTEIRO(A)' => 'SOLTEIRO(A)', 'CASADO(A)' => 'CASADO(A)', 'VIUVO(A)' => 'VIUVO(A)'
                                , 'DIVORCIADO(A)' => 'DIVORCIADO(A)' ] );
        $uf->addItems( [ 'RN' => 'RN', 'PB' => 'PB' ] );
        $banco->addItems( [ 'BANCO DO BRASIL' => 'BANCO DO BRASIL', 'CAIXA' => 'CAIXA','BRADESCO' => 'BRADESCO','ITAU' => 'ITAU' ] );

        $fundador->addItems( [ 'S' => '  Fundador' ] );

        $situacaoemater->addItems( ['APOSENTADO(A)' => 'APOSENTADO(A)', 
                                    'EM ATIVIDADE' => 'EM ATIVIDADE',
                                    'FALECIDO(A)' => 'FALECIDO(A)',
                                    'CEDIDO(A)' => 'CEDIDO(A)',
                                    'EM VACANCIA' => 'EM VACANCIA' ,
                                    'EXONERADO(A)' => 'EXONERADO(A)' ,
                                    'LICENCIADO(A)' => 'LICENCIADO(A)' ] );

        $situacaoemater->setValue('EM ATIVIDADE');

        $nome->setProperty('placeholder', 'Nome do Socio');
        $matricula->setProperty('placeholder', 'Somente Numeros');
        $cpf->setProperty('placeholder', 'Ex.: 00011122233');
        $dtnascimento->setProperty('placeholder', 'DD/MM/AAAA');
        $email->setProperty('placeholder', 'exemplo@exemplo.com');
        $endereco->setProperty('placeholder', 'Ex.: Rua, 99');
        $complemento->setProperty('placeholder', 'Ex.: Condomínio, Bloco, Apartamento e ETC. ');
        $cep->setProperty('placeholder', '59000-000');

        $titulo = new TLabel('<div style="position:floatval; width: 200px;"> <b>* Campo obrigatorio</b></div>');
        $titulo->setFontFace('Arial');
        $titulo->setFontColor('red');
        $titulo->setFontSize(10);

        
        $this->form->appendPage('Dados Pessoais');

        $this->form->addFields( [ new TLabel('Nome <font color=red><b>*</b></font>') ],[ $nome ] );

        $this->form->addFields( [ new TLabel('Matricula <font color=red><b>*</b></font>') ],[ $matricula ] );
        $this->form->addFields( [ new TLabel('CPF <font color=red><b>*</b></font>') ],[ $cpf ],
                                [ new TLabel('Estado Civil') ],[ $estadocivil ]  );
        $this->form->addFields( [ new TLabel('Data de Nascimento') ],[ $dtnascimento  ],
                                [ new TLabel('Sexo') ],[ $sexo  ] );
        $this->form->addFields( [ new TLabel('Telefone Fixo') ],[ $telefonefixo  ],
                                [ new TLabel('Telefone Celular') ],[ $telefonecelular ] );
        $this->form->addFields( [ new TLabel('E-Mail') ],[ $email ]);
        $this->form->addFields( [ new TLabel('') ],[ $codigo,$titulo ] );



        $nome->setSize('50%');
        $nome->forceUpperCase();
        $matricula->setSize('20%');
        $cpf->setSize('53%'); 
        $dtnascimento->setSize('40%');


        $this->form->appendPage('Endereço');
        $this->form->addFields( [ new TLabel('CEP <font color=red><b>*</b></font>') ],[ $cep,$consultar_cep ] );
        $this->form->addFields( [ new TLabel('Endereco <font color=red><b>*</b></font>') ],[ $endereco ] );
        $this->form->addFields( [ new TLabel('complemento <font color=red><b>*</b></font>') ],[ $complemento ] );
        $this->form->addFields( [ new TLabel('Bairro <font color=red><b>*</b></font>') ],[ $bairro ] );
        $this->form->addFields( [ new TLabel('Cidade <font color=red><b>*</b></font>') ],[ $cidade ] );
        $this->form->addFields( [ new TLabel('UF <font color=red><b>*</b></font>') ],[ $uf ] );

        $endereco->setSize('40%');
        $complemento->setSize('40%');
        $bairro->setSize('25%');
        $cidade->setSize('25%');
        $uf->setSize('15%');
        $cep->setSize('15%');

        $this->form->appendPage('Documentos');

        $this->form->addFields( [ new TLabel('RG <font color=red><b>*</b></font>') ],[ $rg ],
                                [ new TLabel('Orgão RG <font color=red><b>*</b></font>') ],[ $orgaorg ] );
        $this->form->addFields( [ new TLabel('Banco') ],[ $banco ] );
        $this->form->addFields( [ new TLabel('Agencia') ],[ $agencia ],
                                [ new TLabel('Conta Corrente') ],[ $contacorrente ]  );
        $this->form->addFields( [ new TLabel('Tipo Socio <font color=red><b>*</b></font>') ],[ $tiposocio,$fundador  ] );
        $this->form->addFields( [ new TLabel('Secretaria ') ],[ $secretaria ] );
        $this->form->addFields( [ new TLabel('Situação <font color=red><b>*</b></font>') ],[ $situacaoemater ] );
        
        $orgaorg->setSize('30%');
        $tiposocio->setSize('27.5%');
        $this->form->appendPage('Adicional');
        

        $this->form->addFields( [new TLabel('Observação')], [$observacao ]);
        $observacao->setSize('100%', 100);
        


        $this->form->addAction( 'Salvar', new TAction( array( $this, 'onSave' ) ), 'ico_save.png' );
        $this->form->addAction('Voltar', new TAction(array('SocioList', 'onLoad')), 'back_blue_arrow.png');
       
        if (!empty($_GET['fk'])) {   
            
        $this->form1 = new BootstrapFormBuilder('form_socio1');
        $this->form1->setFormTitle('Ações para o Sócio');

        $buttondependente = new TAction( ['DependenteDetalhe', 'onEdit' ] );
        $buttondependente->setParameter('fk', '' . filter_input(INPUT_GET, 'fk') . '');

        $buttonplanosaude = new TAction( ['SocioPlanoSaudeDetalhe', 'onEdit' ] );
        $buttonplanosaude->setParameter('fk', '' . filter_input(INPUT_GET, 'fk') . '');

        $add_button2 = new TActionLink('Dependente', $buttondependente, 'white', 11, '', 'fa:plus white');
        $add_button2->class='btn btn-success';

        $add_button3 = new TActionLink('Plano Saude', $buttonplanosaude, 'white', 11, '', 'fa:plus white');
        $add_button3->class='btn btn-success';

        $this->form1->addFields(  [ $add_button2,$add_button3 ]  );
        
        }

        $vbox = new TVBox;
        $vbox->add($this->form);
        parent::add($this->form);

        $vbox1 = new TVBox;
        $vbox1->add($this->form1);
        parent::add($this->form1);
        

    }

    public static function onChangeTipoSocio($param)
    {
        if ($param['tiposocio'] == 'EFETIVO')
        {
            
            TQuickForm::hideField('form_socio', 'secretaria');
        }
        if ($param['tiposocio'] == 'CONTRIBUINTE') 
        
        {
            
            TQuickForm::showField('form_socio', 'secretaria');
            
        }else{
            
            TQuickForm::hideField('form_socio', 'secretaria');
        }
    }


    function onSave() 
	{
        TTransaction::open('pg_ceres');
        $cadastro = $this->form->getData( 'SocioRecord' );

        $dados = $cadastro->toArray();
        $msg = '';
        $icone = 'info';

        if( empty( $dados['nome'] ) )
		{
			
            $msg .= 'O Nome deve ser informado.</br>';
			
        }
        if( empty( $dados['cpf'] ) )
		{
			
            $msg .= 'O CPF deve ser informado.</br>';
			
        }
        if( empty( $dados['matricula'] ) )
		{
			
            $msg .= 'A Matricula deve ser informada.</br>';
			
        }
        if( empty( $dados['dtnascimento'] ) )
		{
			
            $msg .= 'A Data de Nascimento deve ser informada.</br>';
			
        }
        if( empty( $dados['sexo'] ) )
		{
			
            $msg .= 'O Sexo deve ser informado.</br>';
			
        }
        if( empty( $dados['endereco'] ) )
		{
			
            $msg .= 'O Endereço deve ser informado.</br>';
			
        }
        if( empty( $dados['bairro'] ) )
		{
			
            $msg .= 'O Bairro deve ser informado.</br>';
			
        }
        if( empty( $dados['cep'] ) )
		{
			
            $msg .= 'O CEP deve ser informado.</br>';
			
        }
        if( empty( $dados['cidade'] ) )
		{
			
            $msg .= 'A Cidade deve ser informada.</br>';
			
        }
        if( empty( $dados['uf'] ) )
		{
			
            $msg .= 'A UF deve ser informada.</br>';
			
        }
        if( empty( $dados['identidade'] ) )
		{
			
            $msg .= 'O RG deve ser informado.</br>';
			
        }
        if( empty( $dados['orgaoidentidade'] ) )
		{
			
            $msg .= 'O Orgão do RG deve ser informado.</br>';
			
        }
        if( empty( $dados['situacaoemater'] ) )
		{
			
            $msg .= 'A Situação deve ser informada.</br>';
			
        }
        if( empty( $dados['tiposocio'] ) )
		{
			
            $msg .= 'O Tipo do Socio deve ser informado.</br>';
			
        }

        try 
		{

            if( $msg == '' ) 
			{
                $cadastro->store();
				
                $msg = 'Dados armazenados com sucesso';
                TTransaction::close();
				
            }else 
			{
				
                $icone = 'error';
				
            }

            if ($icone == 'error') 
			{

                new TMessage($icone, $msg);
				
				$this->form->setData( $cadastro );   // fill the form with the active record data
				
            }else 
			{

            new TMessage( "info", $msg );
			$param = array();
        	$param['key'] = $cadastro->id;
        	$param['fk'] = $cadastro->id;
			TApplication::gotoPage( 'SocioForm', 'onEdit',$param ); 
				
            }
			
        }catch (Exception $e) 
		{
 
            new TMessage('error', $e->getMessage() );
 
            TTransaction::rollback();
			
			$this->form->setData($cadastro); 
			
        }
		
    }
    function onEdit( $param ) 
	{
		
		try 
		{
			
            if( isset( $param['fk'] ) ) 
			{
 
                $key = $param['fk'];

                TTransaction::open('pg_ceres');  

                $object = new SocioRecord( $key );  
                $object->dtnascimento = TDate::date2br($object->dtnascimento);
                $this->form->setData( $object );   

                TTransaction::close(); 
				
            } 
			
        } catch (Exception $e) 
		{
 
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
 
            TTransaction::rollback();
			
        }
      
    }

}
?>