<?php
/*
 * classe Vw_validacaoRecord
 * Active Record para a view Vw_validacaoRecord
 */
class vw_aniversariantes_do_mesRecord extends TRecord
{
    //put your code here
    const TABLENAME = 'vw_aniversariantes_do_mes';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

}
?>