<?php

/*
 * classe AcaoRecord
 * Active Record para tabela Curso
 */

class ArquivoSiteRecord extends TRecord {

    const TABLENAME = 'site_arquivo';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}


    function get_linkarquivo()
    {
    
        $arquivo = '<a href="app/documents/site/arquivo/'.$this->arquivo.'" target=_blank>Link</a>';
    
        return $arquivo;
    }


}
?>

