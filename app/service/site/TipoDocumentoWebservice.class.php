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
 * Classe SliderMulherWebservice
 */
class TipoDocumentoWebservice
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
			
			$repository = new TRepository('Site_TipoDocumentoRecord');
			
			$criteria = new TCriteria;
			//$criteria->add( new TFilter( 'situacao', '=', 'ATIVO' ) );
			//$criteria->setProperty('order', 'datapublicacao DESC');
			$criteria->setProperty('limit', '10');
			
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
				
					$tempTipoDocumento = array();
					
					$tempTipoDocumento["id"] = $object->id;
					$tempTipoDocumento["nome"] = $object->nome;
					
					$response[$dadosTag][$i++] = $tempTipoDocumento; 
				
				}
				
			}else
			{
				
				$response[$successTag] = 2; //Não tem nenhum dado = 2
				
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

TipoDocumentoWebservice::getDados();

?>