<?php

/*
 * classe PaginaDependenciaRecord
 * Active Record para tabela PaginaDependencia
 */

class PaginaDependenciaRecord extends TRecord {

    const TABLENAME = 'paginadependencia';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

    private $perfil;
    private $pagina;
    private $grupomenu;

    /*
     * metodo get_nome_perfil()
     * executado sempre que for acessada a propriedade nome_perfil
     */

    function get_nome_perfil() {
        //instancia PerfilRecord
        //carrega na memoria o perfil de codigo $this->perfil_id
        if (empty($this->perfil)) {
            $this->perfil = new PerfilRecord($this->perfil_id);
        }
        //retorna o objeto instanciado
        return $this->perfil->nome;
    }

    /*
     * metodo get_nome_arquivo()
     * executado sempre que for acessada a propriedade nome_arquivo
     */

    function get_nome_arquivo() {
        //instancia PaginaRecord
        //carrega na memoria a pagina de codigo $this->pagina_id
        if (empty($this->pagina)) {
            $this->pagina = new PaginaRecord($this->paginadependente_id);
        }
        //retorna o objeto instanciado
        return $this->pagina->arquivo;
    }

    function get_nome_grupomenu() {
        if (empty($this->grupomenu)) {
            $this->grupomenu = new PaginaRecord($this->grupomenu_id);
        }
        return $this->pagina->nome_grupomenu;
    }

}
?>

