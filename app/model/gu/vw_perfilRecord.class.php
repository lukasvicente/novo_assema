<?php

/*
 * classe vw_perfilRecord
 * Active Record para view vw_perfil
 */

class vw_perfilRecord extends TRecord {

    const TABLENAME = 'vw_perfil';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

}
?>

