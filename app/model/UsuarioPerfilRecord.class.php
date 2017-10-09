<?php
/*
 * classe UsuarioPerfilRecord
 * Active Record para tabela Usuarioperfil
 */
class UsuarioPerfilRecord extends TRecord
{
    private $usuario;
    private $perfil;

    	const TABLENAME = 'usuarioperfil';
	const PRIMARYKEY = 'id';
	const IDPOLICY = 'serial'; // {max, serial}
    /*
     * metodo get_nome_usuario()
     * executado sempre que for acessada a propriedade nome_servidor
     */
    function get_nome_usuario()
    {
        //instancia UsuarioRecord
        //carrega na memoria o usuario
        if (empty ($this->usuario)){
           $this->usuario = new UsuarioRecord($this->usuario_id);
        }
        //retorna o objeto instanciado
        return $this->usuario->nome_servidor;
    }

    /*
     * metodo get_nome_perfil()
     * executado sempre que for acessada a propriedade nome_perfil
     */
    function get_nome_perfil()
    {
        //instancia PerfilRecord
        //carrega na memoria o perfil
        if (empty ($this->perfil)){
           $this->perfil = new PerfilRecord($this->perfil_id);
        }
        //retorna o objeto instanciado
        return $this->perfil->nome;
    }
}
?>

