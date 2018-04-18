<?php

/*
 * classe AcaoRecord
 * Active Record para tabela Curso
 */

class TipoDiretorRecord extends TRecord {

    const TABLENAME = 'tipodiretor';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

}
?>

