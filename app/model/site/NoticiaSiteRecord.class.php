<?php

/*
 * classe AcaoRecord
 * Active Record para tabela Curso
 */

class NoticiaSiteRecord extends TRecord {

    const TABLENAME = 'site_noticia';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

}
?>

