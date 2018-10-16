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
class GaleriaImagemWebservice
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
			
			$repository = new TRepository('vw_site_GaleriaImagemRecord');
			
			$criteria = new TCriteria;
			//$criteria->add( new TFilter( 'situacao', '=', 'publicado' ) );
			//$criteria->setProperty('order', 'datapublicacao DESC');
			//$criteria->setProperty('limit', '10');


			$galeria_id = $_REQUEST['galeria_id'];
			//$tipo_documento = $_REQUEST['tipo_documento'];

			if($galeria_id)
			{
                $criteria->add( new TFilter( 'galeria_id', '=', $galeria_id  ));
            }



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
				
					$tempGaleriaImagem = array();
					
					$tempGaleriaImagem["galeria_id"] = $object->galeria_id;
					$tempGaleriaImagem["titulo"] = $object->titulo;
                    $tempGaleriaImagem["foto_descricao"] = $object->foto_descricao;
                    $tempGaleriaImagem["foto_arquivo"] = $object->foto_arquivo;

					$response[$dadosTag][$i++] = $tempGaleriaImagem;
				
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

GaleriaImagemWebservice::getDados();

?>