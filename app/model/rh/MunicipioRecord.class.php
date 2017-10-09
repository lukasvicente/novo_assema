<?php

/*
 * classe MunicipioRecord
 * Active Record para tabela Municipio
 */

class MunicipioRecord extends TRecord {

    const TABLENAME = 'municipio';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

    private $territorio;

    /*
     * metodo get_nome_territorio()
     * executado sempre que for acessada a propriedade nome_territorio
     */

    function get_nome_territorio() {
        //instancia TerritorioRecord
        //carrega na memoria a empresa de codigo $this->empresa_id
        if (empty($this->territorio)) {
            $this->territorio = new TerritorioRecord($this->territorio_id);
        }
        //retorna o objeto instanciado
        return $this->territorio->nome;
    }

    function get_link() {

        $arquivo = '<a href="app.maps/municipio.php?id=' . $this->id . '" target=_blank>(ver mapa)</a>';

        return $arquivo;
    }

}

?>