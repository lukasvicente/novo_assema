<?php

/*
 * classe SetorRecord
 * Active Record para tabela Setor
 */

class SetorRecord extends TRecord {
	
	const TABLENAME = 'setor';
	const PRIMARYKEY = 'id';
	const IDPOLICY = 'serial'; // {max, serial}

    private $servidor;
    private $setorgrupo;

    public function get_nome_servidor() {
        if (empty($this->servidor)) {
            $this->servidor = new ServidorRecord($this->servidor_id);
        }
        return $this->servidor->nome;
    }

    public function get_nome_setorgrupo() {
        if (empty($this->setorgrupo)) {
            $this->setorgrupo = new SetorGrupoRecord($this->setorgrupo_id);
        }
        return $this->setorgrupo->nome;
    }

}
?>

