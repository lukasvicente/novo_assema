<?php

/*
 * classe AcaoRecord
 * Active Record para tabela Curso
 */

class GaleriaImagemSiteRecord extends TRecord {

    const TABLENAME = 'site_galeria_imagem';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

}
?>

