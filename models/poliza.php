<?php
  
/**
 * User Model
 *
 * @version $Id:  
 * @author Andres Quintero
 * @package Joomla
 * @subpackage zschool
 * @license GNU/GPL
 *
 * Allows to manage user data
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

//require_once( JPATH_COMPONENT . DS .'models' . DS . 'zteam.php' );

/**
 * Mensaje
 *
 * @author      aquintero
 * @package		Joomla
 * @since 1.6
 */
		
class ModelPoliza extends JModel{

	//const TAM_MSG       = 160;
	
    /**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
	}
	
	/**
	* Lista de polizas
	*/
	function listarPolizas($filtro, $inicio, $registros){	
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$tbPolizas = $db->nameQuote('#__zpolizas');		
		
		$whereUsuario = ($user->id > 0) ?  " usuario = {$user->id} " : "";
		
		$query = "SELECT 
						*
				  FROM 
						$tbPolizas as polizas
				  WHERE 
						activo = 1 AND
						$whereUsuario
				  ORDER BY
						fecha_expedicion
						";
						
		$query = sprintf( $query, "%" . $filtro . "%" );
		$db->setQuery($query, $inicio, $registros);
	    $result = $db->loadObjectList();
		
		//Trae datos relacionados de la poliza
		foreach( $result as $data){
			$data->ramo = Entidades::getById("ramos" , $data->ramo); 
			$data->tomador = Entidades::getById("clientes" , $data->tomador); 
		}
		return $result;
	}
	
	/**
	* Contar polizas
	*/
	function contarPolizas($filtro){
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$tbPolizas = $db->nameQuote('#__zpolizas');		
		
		$whereUsuario = ($user->id > 0) ?  "  usuario = {$user->id} " : "";
		
		$query = "SELECT 
						count(*)
				  FROM 
						$tbPolizas as polizas
				  WHERE 
						activo = 1 AND
						$whereUsuario
						";
						
		$query = sprintf( $query, "%" . $filtro . "%" );
		$db->setQuery($query);
	    $result = $db->loadResult();
		return $result;
	}


    function listarPolizasAutos($polizaId){
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $tbAutos = $db->nameQuote('#__zpolizas_autos');


        $query = "SELECT
						id as idAuto
				  FROM
						$tbAutos as autos
				  WHERE
						poliza = %s
						";

        $query = sprintf( $query, "%" . $polizaId . "%" );
        $db->setQuery($query);
        $result = $db->loadObjectList();
        return $result[0];
    }


    function listarPolizasHogares($polizaId){
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $tbHogares = $db->nameQuote('#__zpolizas_hogar');


        $query = "SELECT
						id as idHogar
				  FROM
						$tbHogares as autos
				  WHERE
						poliza = %s
						";

        $query = sprintf( $query, "%" . $polizaId . "%" );
        $db->setQuery($query);
        $result = $db->loadObjectList();
        return $result[0];
    }




    /**
	* Obtiene la poliza a traves del id
	*/
	function getPoliza($id){
		JTable::addIncludePath(JPATH_COMPONENT .DS. 'tables');
		$row = &JTable::getInstance('Polizas', 'Table');
		$row->id = $id;
		$row->load();
        $relacionado = [];
        switch ($row->ramo){
            case 1:
                $relacionado = $this->listarPolizasAutos($row->id);
                break;
            case 2:
                $relacionado = $this->listarPolizasHogares($row->id);
                break;
        }
		return [$row, $relacionado];
	}
	
	/**
	* Guarda la poliza
	*/
	function guardarPoliza(){
		JTable::addIncludePath(JPATH_COMPONENT .DS. 'tables');	
		$row =& JTable::getInstance('Polizas', 'Table');
		$user = JFactory::getUser();
		if($row->bind(JRequest::get('post'))){
			$row->usuario = $user->id ;
			$row->activo = 1 ;
            $tipoRamo = JRequest::getVar('ramo');
            $conseguido= 'no';

            if($row->store()){
                switch ($tipoRamo){
                    case 1:
                        $conseguido =  $this->guardarPolizaAuto($row->id);
                        break;
                    case 2:
                        $conseguido = $this->guardarPolizaHogar($row->id);
                        break;
                }
				return JText::_('M_OK') . sprintf( JText::_('US_GUARDAR_OK') , $row->id );

			}
			else{
				return JText::_('M_ERROR'). JText::_('US_GUARDAR_ERROR');
			}
		
		}
	}

    function guardarPolizaHogar($polizaId){
        JTable::addIncludePath(JPATH_COMPONENT .DS. 'tables');
        $row =& JTable::getInstance('Hogares', 'Table');
        $user = JFactory::getUser();
        if($row->bind(JRequest::get('post'))){
            $row->id = JRequest::getVar('id_hogar');
            $row->poliza = $polizaId;
            if($row->store()){
                return $row->id;
            }
            else{
                return false;
            }
        }
    }

    function guardarPolizaAuto($polizaId){
        JTable::addIncludePath(JPATH_COMPONENT .DS. 'tables');
        $row =& JTable::getInstance('Autos', 'Table');
        $user = JFactory::getUser();
        if($row->bind(JRequest::get('post'))){
            $row->id = JRequest::getVar('id_auto');
            $row->poliza = $polizaId;
            $row->tipo = JRequest::getVar('tipo_vehiculo');
            if($row->store()){
                return $row->id;
            }
            else{
                return false;
            }
        }
    }

	
}







