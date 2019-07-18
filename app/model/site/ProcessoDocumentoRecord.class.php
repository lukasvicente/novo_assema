<?php

/*
 * classe AcaoRecord
 * Active Record para tabela Curso
 */

class ProcessoDocumentoRecord extends TRecord {

    const TABLENAME = 'processo_documento';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

    function get_linkarquivo()
    {

        $arquivo = '<a href="app/documents/site/processo/'.$this->arquivo.'" target=_blank>Donwload</a>';

        return $arquivo;
    }


}
?>

