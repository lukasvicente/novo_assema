<?php

/*
 * classe AcaoRecord
 * Active Record para tabela Curso
 */

class AcaoRecord extends TRecord {

    const TABLENAME = 'acao';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

}
?>

