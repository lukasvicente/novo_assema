<?php
/*
 * classe CargoRecord
 * Active Record para tabela Cargo
 */
class CargoRecord extends TRecord
{
    const TABLENAME = 'cargo';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
}
?>