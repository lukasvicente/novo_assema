<?php

/*
 * classe UsuarioRecord
 * Active Record para tabela Usuario
 */

class UsuarioRecord extends TRecord {

    const TABLENAME = 'usuario';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

    private $socio;
    private $colaboradorleite;
    private $agentebancario;
    private $regional;

    /*
     * metodo get_nome_servidor()
     * executado sempre que for acessada a propriedade nome_servidor
     */

    function get_nome_socio() {
        //instancia ServidorRecord
        //carrega na memoria o servidor
        if (empty($this->socio)) {
            $this->socio = new SocioRecord($this->socio_id);
        }
        //retorna o objeto instanciado
        return $this->socio->nome;
    }

    /*
     * metodo get_nome_agentebancario()
     * executado sempre que for acessada a propriedade nome_agentebancario
     */

    function get_nome_agentebancario() {
        //instancia AgenteBancarioRecord
        //carrega na memoria o agentebancario
        if (empty($this->agentebancario)) {
            $this->agentebancario = new ServidorRecord($this->agentebancario_id);
        }
        //retorna o objeto instanciado
        return $this->agentebancario->nome;
    }

    function get_nome_colaboradorleite() {
        //instancia AgenteBancarioRecord
        //carrega na memoria o agentebancario
        if (empty($this->colaboradorleite)) {
            $this->colaboradorleite = new ColaboradorLeiteRecord($this->colaboradorleite_id);
        }
        //retorna o objeto instanciado
        return $this->colaboradorleite->nome;
    }

    /*
     * metodo get_nome_regional()
     * executado sempre que for acessada a propriedade nome_regional
     */

    function get_nome_regional() {
        //instancia RegionalRecord
        //carrega na memoria o regional
        if (empty($this->regional)) {
            $this->regional = new RegionalRecord($this->regional_id);
        }
        //retorna o objeto instanciado
        return $this->regional->nome;
    }

}

?>