<?php
/*
 * classe Vw_servidores_por_cargosRecord
 * Active Record para a view vw_servidores_por_cargos
 */
class MunicipioCoordenadasRecord extends TRecord
{
    const TABLENAME = 'municipiocoordenadas';
	const PRIMARYKEY = 'id';
	const IDPOLICY = 'serial'; // {max, serial}
}
?>
