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
class SobreWebservice
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
			
			$repository = new TRepository('SobreSiteRecord');
			
			$criteria = new TCriteria;
			$criteria->add( new TFilter( 'situacao', '=', 'ATIVO' ) );
			$criteria->setProperty('order', 'id');
			$criteria->setProperty('limit', '5');
			
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
					$tempDocumento["nome"] = $object->nome;
                    $tempDocumento["nome_low"] = strtolower($object->nome);
                    $tempDocumento["titulo"] = $object->titulo;
					$tempDocumento["descricao"] = $object->descricao;
					$tempDocumento["situacao"] = $object->situacao;


					
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

SobreWebservice::getDados();

?>