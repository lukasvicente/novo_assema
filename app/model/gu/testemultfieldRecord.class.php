<?php

/*
 * classe testemultfieldRecord
 * Active Record para tabela Acaoppa
 */

class testemultfieldRecord extends TRecord {

    const TABLENAME = 'testemultfield';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

    public function __construct($id = NULL) {
        parent::__construct($id);
        parent::addAttribute('name');
        parent::addAttribute('phone');
        parent::addAttribute('type_id');
        parent::addAttribute('type_value');
    }

}

?>