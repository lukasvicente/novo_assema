<?php

/*
 * classe AcaoRecord
 * Active Record para tabela Curso
 */

class ProcessoRecord extends TRecord {

    const TABLENAME = 'processo';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

}
?>

