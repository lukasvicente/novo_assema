<?php

/*
 * classe AcaoRecord
 * Active Record para tabela Curso
 */

class SobreSiteRecord extends TRecord {

    const TABLENAME = 'site_sobre';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

}
?>

