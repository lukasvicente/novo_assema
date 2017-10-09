<?php

/*
 * classe PaginaRecord
 * Active Record para tabela Pagina
 */

class PaginaRecord extends TRecord {

    const TABLENAME = 'pagina';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

    private $modulo;
    private $grupomenu;

    /*
     * metodo get_nome_modulo()
     * executado sempre que for acessada a propriedade nome_modulo
     */

    function get_nome_modulo() {
        //instancia ModuloRecord
        //carrega na memoria o modulo de codigo $this->modulo_id
        if (empty($this->modulo)) {
            $this->modulo = new ModuloRecord($this->modulo_id);
        }
        //retorna o objeto instanciado
        return $this->modulo->nome;
    }

    function get_nome_grupomenu() {
        //instancia ModuloRecord
        //carrega na memoria o modulo de codigo $this->modulo_id
        if (empty($this->grupomenu)) {
            $this->grupomenu = new GrupoMenuRecord($this->grupomenu_id);
        }
        //retorna o objeto instanciado
        return $this->grupomenu->nome;
    }

}
?>

