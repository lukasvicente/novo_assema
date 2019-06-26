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
class ProcessoDocumentoWebservice
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
			
			$repository = new TRepository('ProcessoDocumentoRecord');
			
			$criteria = new TCriteria;
			$criteria->add( new TFilter( 'situacao', '=', 'PUBLICADO' ) );
			//$criteria->setProperty('order', 'datapublicacao DESC');
			//$criteria->setProperty('limit', '10');


            $key = $_REQUEST['key'];


            if($key)
            {
                $criteria->add( new TFilter( 'processo_id', '=', $key  ));
            }


            $collection = $repository->load( $criteria );
			
			if( $collection )
			{
				
				$response[$successTag] = 1;
				
				$i = 0;
				
				foreach( $collection as $object )
				{
				
					$tempDocumento = array();
					
					$tempDocumento["id"] = $object->id;
					$tempDocumento["processo_id"] = $object->processo_id;
                    $tempDocumento["nome"] = $object->nome;
                    $tempDocumento["arquivo"] = $object->arquivo;

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

ProcessoDocumentoWebservice::getDados();

?>