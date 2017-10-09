<?php
/*
 * classe RegionalRecord
 * Active Record para tabela Regional
 */
class RegionalRecord extends TRecord
{
    
    const TABLENAME = 'regional';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}
    
    private $empresa;
    private $sigla;
    
    /*
     * metodo get_nome_empresa()
     * executado sempre que for acessada a propriedade nome_empresa
     */
    function get_nome_empresa()
    {
        //instancia EmpresaRecord
        //carrega na memoria a empresa de codigo $this->empresa_id
        if (empty ($this->empresa)){
           $this->empresa = new EmpresaRecord($this->empresa_id);
        }
        //retorna o objeto instanciado
        return $this->empresa->nome;
    }

    /*
     * metodo get_sigla_empresa()
     * executado sempre que for acessada a propriedade nome_empresa
     */
    function get_sigla_empresa()
    {
        //instancia EmpresaRecord
        //carrega na memoria a empresa de codigo $this->empresa_id
        if (empty ($this->sigla)){
           $this->sigla = new EmpresaRecord($this->empresa_id);
        }
        //retorna o objeto instanciado
        return $this->sigla->sigla;
    }
    
    function get_cor()
    {
        
        $arquivo = '<div style="backgroung-color:red;"><font color='.$this->cormapa.'><strong>||||||||||||||||||||||||||||||||</strong></font></div>';            
        
        return $arquivo;
    }

}
?>