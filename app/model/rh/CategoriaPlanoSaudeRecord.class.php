<?php

/*
 * classe SocioPlanoSaudeRecord
 * Active Record para tabela Pagina
 */

class CategoriaPlanoSaudeRecord extends TRecord {
    
    const TABLENAME = 'categoriaplanosaude';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';

    private $socio;
    private $categoriaplanosaude;
    private $planosaude;

 

    function get_nome_socio() {
         
        if (empty($this->socio)) {
            $this->socio = new SocioRecord($this->socio_id);
        }
         
        return $this->socio->nome;
    }

    function get_nome_categoriaplanosaude() {
         
        if (empty($this->categoriaplanosaude)) {
            $this->categoriaplanosaude = new CategoriaPlanoSaudeRecord($this->categoriaplanosaude_id);
        }
        
        return $this->categoriaplanosaude->nome;
    }
    
    function get_nome_planosaude() {
        
        if (empty($this->planosaude)) {
            $this->planosaude = new CategoriaPlanoSaudeRecord($this->categoriaplanosaude_id);
        }
         
        return $this->categoriaplanosaude->nome_planosaude;
    }

}
?>