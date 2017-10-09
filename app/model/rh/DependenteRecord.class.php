<?php
/*
 * classe DependenteRecord
 * Active Record para tabela Dependente
 */
class DependenteRecord extends TRecord
{
    const TABLENAME = 'dependente';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';

    private $socio;

    /*
     * metodo get_nome_modulo()
     * executado sempre que for acessada a propriedade nome_modulo
     */
    function get_nome_socio()
    {
        //instancia ModuloRecord
        //carrega na memoria o modulo de codigo $this->modulo_id
        if (empty ($this->socio)){
           $this->socio = new SocioRecord($this->socio_id);
        }
        //retorna o objeto instanciado
        return $this->socio->nome;
    }
    
    function get_calc_idade() {
            return (date('Y-m-d') - ($this->dtnascimento));
    }
    
}
?>

