<?php
/*
 * classe DisciplinaRecord
 * Active Record para tabela Disciplina
 */
class DisciplinaRecord extends TRecord
{
    const TABLENAME = 'disciplina';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}
}
?>

