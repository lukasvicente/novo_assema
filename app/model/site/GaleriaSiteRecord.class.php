<?php

/*
 * classe AcaoRecord
 * Active Record para tabela Curso
 */

class GaleriaSiteRecord extends TRecord {

    const TABLENAME = 'site_galeria';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

}
?>

