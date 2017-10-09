<?php
/*
 * classe Acesso_servidorRecord
 * Active Record para tabela Acesso_servidor
 */
class Acesso_servidorRecord extends TRecord
{
	
	const TABLENAME = 'acesso_servidor';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}
	
}
?>

