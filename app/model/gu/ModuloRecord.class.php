<?php
/*
 * classe ModuloRecord
 * Active Record para tabela Modulo
 */

class ModuloRecord extends TRecord
{
	
	const TABLENAME = 'modulo';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}
	
}
?>