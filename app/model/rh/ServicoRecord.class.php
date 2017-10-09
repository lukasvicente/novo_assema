<?php
/*
 * classe TecnicoChamadoRecord
 * Active Record para tabela TecnicoChamado
 */
class ServicoRecord extends TRecord
{
    private $sla;
    
    
    /*
     * metodo get_nome_sla()
     * executado sempre que for acessada a propriedade nome_fornecedor
     */
    function get_nome_cargo()
    {
        //instancia FornecedorRecord
        //carrega na memoria a empresa de codigo $this->fornecedor_id
        if (empty ($this->cargo)){
           $this->cargo = new SlaRecord($this->cargo_id);
        }
        //retorna o objeto instanciado
        return $this->cargo->nome;
    }

  

}
?>

