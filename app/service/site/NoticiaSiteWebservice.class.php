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
class NoticiaSiteWebservice
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
			
			$repository = new TRepository('NoticiaSiteRecord');
			
			$criteria = new TCriteria;
			//$criteria->add( new TFilter( 'situacao', '=', 'ATIVO' ) );
			$criteria->setProperty('order', 'datapublicacao DESC');
			//$criteria->setProperty('limit', '5');

			$collection = $repository->load( $criteria );
			
			if( $collection )
			{
				
				$response[$successTag] = 1;
				
				$i = 0;
				
				foreach( $collection as $object )
				{
				
					$tempDocumento = array();
					
					$tempDocumento["id"] = $object->id;
					$tempDocumento["titulo"] = $object->titulo;
					$tempDocumento["descricao"] = $object->descricao;
					$tempDocumento["nomearquivo"] = $object->nomearquivo;
					$tempDocumento["datapublicacao"] = $object->datapublicacao;
					$tempDocumento["ano"] = substr($object->datapublicacao,0,4);
					$tempDocumento["mes"] = retornaMes(substr($object->datapublicacao,5,2));
					$tempDocumento["dia"] = substr($object->datapublicacao,8,2);
					$tempDocumento["situacao"] = $object->situacao;
					$tempDocumento["situacao"] = $object->situacao;
					$tempDocumento["autor"] = $object->autor;

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

NoticiaSiteWebservice::getDados();

?>