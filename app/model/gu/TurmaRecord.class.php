<?php
/*
 * classe TurmaRecord
 * Active Record para tabela Turma
 */
class TurmaRecord extends TRecord
{
    
    const TABLENAME = 'turma';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}
    
    private $cursoescola;

    function get_nome_curso(){
        //instancia cursoescolaRecord e
        //carrega na memoria a acao do codigo $this->acaoatividade_id
        if (empty ($this->cursoescola)){
           $this->cursoescola = new CursoEscolaRecord($this->cursoescola_id);
        }
        //retorna o objeto instanciado
        return $this->cursoescola->nome_curso;
    }

    function get_nome_escola(){
        //instancia PlanejamentoRecord e
        //carrega na memoria a acao do codigo $this->acaoatividade_id
        if (empty ($this->cursoescola)){
           $this->cursoescola = new CursoEscolaRecord($this->cursoescola_id);
        }
        //retorna o objeto instanciado
        return $this->cursoescola->nome_escola;
    }
    
    
    function get_escola_id(){
        //instancia PlanejamentoRecord e
        //carrega na memoria a acao do codigo $this->acaoatividade_id
        if (empty ($this->cursoescola)){
           $this->cursoescola = new CursoEscolaRecord($this->cursoescola_id);
        }
        //retorna o objeto instanciado
        return $this->cursoescola->escolaid;
    }

    

}
?>

