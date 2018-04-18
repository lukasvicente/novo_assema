<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_erros', 1);
//error_reporting(E_ALL);

chdir('../../../');

require_once 'init.php';

include_once 'app/lib/funcdate.php';


use Adianti\Database\TTransaction;
use Adianti\Database\TRepository;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;

/*
 * Classe SobreWebservice
 */
class DiretorWebservice
{

	static public function getDados()
	{
		
		$successTag = "success";
		$errorTag = "error";
		$dadosTag = "dados";
		
		$whereTag = "id";

		$offSet = "offSet";

		try 
		{ 
			
			$response = array();

			TTransaction::open('pg_ceres');
			
			$repository = new TRepository('DiretorRecord');
			
			$criteria = new TCriteria;
			$criteria->add( new TFilter( 'situacao', '=', 'ATIVO' ) );
			$criteria->setProperty('order', 'id');
			//$criteria->setProperty('limit', '5');
			
			//if( filter_input(INPUT_GET, $whereTag)  ){ $criteria->setProperty('limit', '3'); }	
			
			/*if( filter_input(INPUT_GET, $whereTag) ){
				$criteria->add( new TFilter( 'id', '=', filter_input(INPUT_GET, $whereTag)  ));
			}*/

			$collection = $repository->load( $criteria );
			
			if( $collection )
			{
				
				$response[$successTag] = 1;
				
				$i = 0;
				
				foreach( $collection as $object )
				{
				
					$tempDocumento = array();
					
					$tempDocumento["id"] = $object->id;
					$tempDocumento["nome_diretor"] = $object->nome_socio;
                    $tempDocumento["nome_tipodiretor"] = $object->nome_tipodiretor;
                    $tempDocumento["nome_tipodiretor_low"] = strtolower($object->nome_tipodiretor);
					$tempDocumento["descricao"] = $object->descricao;
                    $tempDocumento["discurso"] = $object->discurso;
					$tempDocumento["situacao"] = $object->situacao;
                    $tempDocumento["datainicio"] = substr($object->datainicio,0,4);
                    $tempDocumento["datafim"] = substr($object->datafim,0,4);
					
					$response[$dadosTag][$i++] = $tempDocumento; 
				
				}
				
			}else
			{
				
				$response[$successTag] = 2; //N�o tem nenhum dado = 2
				
			}
			
			TTransaction::close();
			
		}catch( Exception $e ) 
		{
		
			$response[$successTag] = 0;
			$response[$errorTag] = $e->getMessage();
		
			TTransaction::rollback();
			
		}
		
        echo json_encode( $response );

	}
	
}

DiretorWebservice::getDados();

?>