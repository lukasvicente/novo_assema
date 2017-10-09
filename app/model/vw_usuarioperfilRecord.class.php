<?php
/*
 * classe Vw_usuarioperfilRecord
 * Active Record para a view vw_usuarioperfil
 */
class vw_usuarioperfilRecord extends TRecord
{
    //put your code here
    const TABLENAME = 'vw_usuarioperfil';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'max'; // {max, serial}
}
?>
