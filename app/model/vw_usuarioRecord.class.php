<?php
/*
 * classe Vw_usuarioRecord
 * Active Record para a view vw_usuario
 */

class vw_usuarioRecord extends TRecord
{   
    
    const TABLENAME = 'vw_usuario';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}
    //
    //put your code here
    private $municipio;
    
    public function get_nome_municipio() {
        if(empty($this->municipio)){
            $this->municipio = new MunicipioRecord($this->municipio_id);
        }
        return $this->municipio->nome;
        
    }
}
?>