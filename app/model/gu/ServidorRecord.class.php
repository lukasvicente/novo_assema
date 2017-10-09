<?php

/*
 * classe ServidorRecord
 * Active Record para tabela Servidor
 */

class ServidorRecord extends TRecord {

    const TABLENAME = 'servidor';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

    private $cargo;
    private $cargonovo;
    private $nivel;

    /**
     * metodo para retornar o cargo
     * @return <type>
     */
    
      function get_nome_cargo() {
      //instancia cargoRecord
      //carrega na memoria a empresa de codigo $this->empresa_id
      if (empty($this->cargo)) {
      $this->cargo = new CargoRecord($this->cargo_id);
      }
      //retorna o objeto instanciado
      return $this->cargo->nome;
      }
/*
      function get_nome_cargonovo() {
      //instancia cargoRecord
      //carrega na memoria a empresa de codigo $this->empresa_id
      if (empty($this->cargonovo)) {
      $this->cargonovo = new CargoRecord($this->cargonovo_id);
      }
      //retorna o objeto instanciado
      return $this->cargonovo->nome;
      }
     * 
     */

    /**
     * metodo para retornar o nivel salarial
     * @return <type>
     */
    /*
      function get_nome_nivel() {
      //instancia nivelRecord
      //carrega na memoria a empresa de codigo $this->empresa_id
      if (empty($this->nivel)) {
      $this->nivel = new NivelSalarialRecord($this->nivelsalarial_id);
      }
      //retorna o objeto instanciado
      return $this->nivel->nome;
      }
     * 
     */
}

?>