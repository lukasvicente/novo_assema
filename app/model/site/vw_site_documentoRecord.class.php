<?php
/*
 * classe Vw_usuarioperfilRecord
 * Active Record para a view vw_usuarioperfil
 */
class vw_site_documentoRecord extends TRecord
{
    //put your code here
    const TABLENAME = 'vw_site_documento';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'max'; // {max, serial}
}
?>
