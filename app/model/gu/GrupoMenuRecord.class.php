<?php
/*
 * classe GrupoMenuRecord
 * Active Record para tabela GrupoMenu
 */
class GrupoMenuRecord extends TRecord
{
    
    const TABLENAME = 'grupomenu';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}
}
?>