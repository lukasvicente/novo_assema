<?php
/*
 * classe ConselhoRecord
 * Active Record para tabela Conselho
 */

class ConselhoRecord extends TRecord
{
	const TABLENAME = 'conselho';
	const PRIMARYKEY = 'id';
	const IDPOLICY = 'serial'; // {max, serial}
}
?>