<?php

/*
 * classe AcaoRecord
 * Active Record para tabela Curso
 */

class SubMenuSiteRecord extends TRecord {

    const TABLENAME = 'site_submenu';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

}
?>

