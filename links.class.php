<?php /* FILES $Id: files.class.php,v 1.10 2004/07/25 18:15:35 gregorerhardt Exp $ */
require_once( $AppUI->getSystemClass( 'dp' ) );
require_once( $AppUI->getModuleClass( 'tasks' ) );
require_once( $AppUI->getModuleClass( 'projects' ) );
/**
* Link Class
*/
class CLink extends CDpObject {

	var $link_id = NULL;
	var $link_project = NULL;
	var $link_url = NULL;
	var $link_task = NULL;
	var $link_name = NULL;
	var $link_parent = NULL;
	var $link_description = NULL;
	var $link_type = NULL;
	var $link_owner = NULL;
	var $link_date = NULL;
        var $link_category = NULL;

	
	function CLink() {
		$this->CDpObject( 'links', 'link_id' );
	}

	function check() {
	// ensure the integrity of some variables
		$this->link_id = intval( $this->link_id );
		$this->link_parent = intval( $this->link_parent );
                $this->link_category = intval( $this->link_category );
		$this->link_task = intval( $this->link_task );
		$this->link_project = intval( $this->link_project );

		return NULL; // object is ok
	}

	function delete() {
		global $dPconfig;
		$this->_message = "deleted";

	// delete the main table reference
		$sql = "DELETE FROM links WHERE link_id = $this->link_id";
		if (!db_exec( $sql )) {
			return db_error();
		}
		return NULL;
	}
}
?>
