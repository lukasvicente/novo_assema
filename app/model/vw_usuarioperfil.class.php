<?php

/*
 * classe Vw_usuarioperfilRecord
 * Active Record para a view vw_usuarioperfil
 */

//namespace App\Model;

class vw_usuarioperfil extends TRecord {

    //put your code here
    const TABLENAME = 'vw_usuarioperfil';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

}
