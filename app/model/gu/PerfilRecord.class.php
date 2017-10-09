<?php
/*
 * classe PerfilRecord
 * Active Record para tabela Perfil
 */
class PerfilRecord extends TRecord
{
    
    const TABLENAME = 'perfil';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}
    
    private $modulo;

    /*
     * metodo get_nome_modulo()
     * executado sempre que for acessada a propriedade nome_modulo
     */
    function get_nome_modulo()
    {
        //instancia ModuloRecord
        //carrega na memoria o modulo de codigo $this->modulo_id
        if (empty ($this->modulo)){
           $this->modulo = new ModuloRecord($this->modulo_id);
        }
        //retorna o objeto instanciado
        return $this->modulo->nome;
    }

}
?>

