<?php
/*
 * classe CursoEscolaRecord
 * Active Record para tabela CursoEscola
 */
class CursoEscolaRecord extends TRecord
{

    private $curso;
    private $escola;
    
    const TABLENAME = 'cursoescola';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

    function get_nome_curso(){
        if(empty ($this->curso)){
            $this->curso = new CursoRecord($this->curso_id);
        }
        return $this->curso->nome;
    }

    function get_nome_escola(){
        if(empty($this->escola)){
            $this->escola = new EscolaRecord($this->escola_id);
        }
        return $this->escola->nome;
    }
    
    function get_escolaid(){
        if(empty($this->escola)){
            $this->escola = new EscolaRecord($this->escola_id);
        }
        return $this->escola->id;
    }
    
    function get_municipio_id(){
        if(empty ($this->escola)){
            $this->escola = new EscolaRecord($this->escola_id);
        }
        return $this->municipio_id;
    }
    
}
?>

