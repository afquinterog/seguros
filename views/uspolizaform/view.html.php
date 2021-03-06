<?php
/**
 * @version		$Id: view.html.php 21023 2011-03-28 10:55:01Z infograf768 $
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Form login
 *
 * @package		
 * @subpackage	
 * @since		
 */
class ViewUsPolizaForm extends JView
{
	protected $form;
	protected $params;
	protected $state;
	protected $user;

	/**
	 * Method to display the view.
	 *
	 * @param	string	The template file to include
	 * @since	1.5
	 */
	public function display($tpl = null)
	{
		$id    = JRequest::getVar("id");
		$aseguradoraActual   = JRequest::getVar("aseguradora");
		
		$model = $this->getModel('Poliza');
		if( $id > 0 ){
            $tablaPoliza  = $model->getPoliza($id);
            print_r("lolque".$tablaPoliza[0]);
			$data      = $tablaPoliza[0];
            $relacionado = $tablaPoliza[1];
		}
		
		// Obtiene las listas de datos
		$aseguradoras = Listas::getAseguradoras();
		$clientes     = Listas::getClientes();
		$ramos     	  = Listas::getRamos();
		$vendedores   = Listas::getVendedores();
		
		$this->assignRef('id', $id );
		$this->assignRef('data',$data );
		$this->assignRef('relacionado',$relacionado );
		// Asigna las listas a la gui
		$this->assignRef('aseguradoras', $aseguradoras );
		$this->assignRef('clientes', $clientes );
		$this->assignRef('ramos', $ramos );
		$this->assignRef('vendedores', $vendedores );
		
		parent::display($tpl);
	}

	
}
