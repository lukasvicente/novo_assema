<?php
/*
 * classe CursoRecord
 * Active Record para tabela Curso
 */
class CursoRecord extends TRecord
{
    const TABLENAME = 'curso';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}
}
?>

