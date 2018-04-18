<?php

/*
 * classe AcaoRecord
 * Active Record para tabela Curso
 */

class DiretorRecord extends TRecord {

    const TABLENAME = 'diretor';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

    private $socio;
    private $tipodiretor;


    function get_nome_socio() {


        if (empty($this->socio)) {
            $this->socio = new SocioRecord($this->socio_id);
        }
        //retorna o objeto instanciado
        return $this->socio->nome;
    }

    function get_nome_tipodiretor() {

        if (empty($this->tipodiretor)) {
            $this->tipodiretor = new TipoDiretorRecord($this->tipodiretor_id);
        }
        //retorna o objeto instanciado
        return $this->tipodiretor->nome;
    }

}
?>

