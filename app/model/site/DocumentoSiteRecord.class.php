<?php

/*
 * classe AcaoRecord
 * Active Record para tabela Curso
 */

class DocumentoSiteRecord extends TRecord {

    const TABLENAME = 'site_documento';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

    private $tipodocumento;

    function get_nome_tipodocumento() 
    {
         
        if (empty($this->tipodocumento)) 
        {
            $this->tipodocumento = new TipoDocumentoSiteRecord($this->site_tipodocumento_id);
        }
         
        return $this->tipodocumento->nome;
    }

    function get_linkarquivo()
    {
    
        $arquivo = '<a href="app/documents/site/documents_'.$this->id.'.pdf'.'" target=_blank>Donwload</a>';
    
        return $arquivo;
    }


}
?>

