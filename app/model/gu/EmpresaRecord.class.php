<?php
/*
 * classe EmpresaRecord
 * Active Record para tabela Empresa
 */
class EmpresaRecord extends TRecord
{
	
	const TABLENAME = 'empresa';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}
	
}
?>

