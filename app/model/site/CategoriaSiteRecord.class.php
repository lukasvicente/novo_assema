<?php

/*
 * classe AcaoRecord
 * Active Record para tabela Curso
 */

class CategoriaSiteRecord extends TRecord {

    const TABLENAME = 'site_categoria';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

}
?>

