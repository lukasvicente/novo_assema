<?php
/*
 * classe Vw_validacaoRecord
 * Active Record para a view Vw_validacaoRecord
 */
class vw_validacaoRecord extends TRecord
{
    //put your code here
    const TABLENAME = 'vw_validacao';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

}
?>