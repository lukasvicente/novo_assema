<?php

/*
 * classe AcaoRecord
 * Active Record para tabela Curso
 */

class TipoDocumentoSiteRecord extends TRecord {

    const TABLENAME = 'site_tipodocumento';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

}
?>

