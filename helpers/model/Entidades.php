<?php 

class Entidades{

	function getById($table, $id){
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$tbName = "#__z" . $table ;
		$tbData = $db->nameQuote( $tbName );			
		
		$query = "SELECT 
						*
				  FROM 
						$tbData a
				  WHERE
						a.usuario = %s  AND
						a.id = %s
						";
						
		$query = sprintf($query, $user->id, $id);
		$db->setQuery($query);
		$result = $db->loadObjectList();
		return isset($result[0]) ? $result[0] : "" ;	
	}
}