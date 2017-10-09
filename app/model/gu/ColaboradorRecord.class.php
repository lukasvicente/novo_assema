<?php
/*
 * classe ColaboradorRecord
 * Active Record para tabela Colaborador
 */
class ColaboradorRecord extends TRecord{
	
    const TABLENAME = 'colaborador';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}
    
    private $escola;
    private $curso;


    function get_nome_municipio(){

        //instancia municipioRecord
        //carrega na memoria o turma de codigo $this->municipio_id
        if (empty ($this->escola)){
           $this->escola = new EscolaRecord($this->escola_id);
        }
        //retorna o objeto instanciado
        return $this->escola->nome_municipio;
    }

   function get_nome_escola(){

        //instancia municipioRecord
        //carrega na memoria o turma de codigo $this->municipio_id
        if (empty ($this->escola)){
           $this->escola = new EscolaRecord($this->escola_id);
        }
        //retorna o objeto instanciado
        return $this->escola->nome;
    }


   function get_nome_curso(){

        //instancia municipioRecord
        //carrega na memoria o turma de codigo $this->municipio_id
        if (empty ($this->curso)){
           $this->curso = new CursoRecord($this->curso_id);
        }
        //retorna o objeto instanciado
        return $this->curso->nome;
    }
}
?>

