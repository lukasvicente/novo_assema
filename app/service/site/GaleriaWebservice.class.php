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
class GaleriaWebservice
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
			
			$repository = new TRepository('GaleriaSiteRecord');
			
			$criteria = new TCriteria;
			$criteria->add( new TFilter( 'situacao', '=', 'ATIVO' ) );
			$criteria->setProperty('order', 'datapublicacao');
			//$criteria->setProperty('limit', '5');

			$collection = $repository->load( $criteria );
			
			if( $collection )
			{
				
				$response[$successTag] = 1;
				
				$i = 0;
				
				foreach( $collection as $object )
				{
				
					$tempGaleria = array();
					
					$tempGaleria["id"] = $object->id;
					$tempGaleria["titulo"] = $object->titulo;
                    $tempGaleria["descricao"] = $object->descricao;
                    $tempGaleria["datapublicacao"] = $object->datapublicacao;
                    $tempGaleria["ano"] = substr($object->datapublicacao,0,4);
                    $tempGaleria["mes"] = retornaMes(substr($object->datapublicacao,5,2));
                    $tempGaleria["dia"] = substr($object->datapublicacao,8,2);
                    $tempGaleria["hora"] = formatar_time($object->datapublicacao);
                    $tempGaleria["arquivo"] = $object->arquivo;

					$response[$dadosTag][$i++] = $tempGaleria;
				
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

GaleriaWebservice::getDados();

?>