<?php

/*
 * view Acesso_servidorRecord
 * Active Record para view acesso_servidor
 */
namespace App\Model\Acesso_Servidor;

use Adianti\Database\TRecord;

class Acesso_ServidorRecord extends TRecord {

    const TABLENAME = 'acesso_servidor';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}


}
?>