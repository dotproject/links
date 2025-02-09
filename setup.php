<?php
/**
 *  Name: Links
 *  Directory: links
 *  Version 1.0
 *  Type: user
 *  UI Name: Links
 *  UI Icon: ?
 */

$config = array();
$config['mod_name'] = 'Links';               // name the module
$config['mod_version'] = '1.0';               // add a version number
$config['mod_directory'] = 'links';          // tell dotProject where to find this module
$config['mod_setup_class'] = 'CSetupLinks';  // the name of the PHP setup class (used below)
$config['mod_type'] = 'user';                   // 'core' for modules distributed with dP by standard, 'user' for additional modules from dotmods
$config['mod_ui_name'] = 'Links';            // the name that is shown in the main menu of the User Interface
$config['mod_ui_icon'] = 'communicate.gif';     // name of a related icon
$config['mod_description'] = 'Links related to tasks';     // some description of the module
$config['mod_config'] = false;                   // show 'configure' link in viewmods


if (@$a == 'setup') {
        echo dPshowModuleConfig( $config );
}

// TODO: To be completed later as needed.
class CSetupLinks {

  function configure() { return true; }

  function remove() { 
        db_exec('drop table links');
        db_exec("DELETE FROM `sysvals` WHERE sysval_title='LinkType'");
 }
  
  function upgrade($old_version) { return true; }

  function install() {
        $sql = "
CREATE TABLE `links` (
`link_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
`link_url` varchar( 255 ) NOT NULL default '',
`link_project` int( 11 ) NOT NULL default '0',
`link_task` int( 11 ) NOT NULL default '0',
`link_name` varchar( 255 ) NOT NULL default '',
`link_parent` int( 11 ) default '0',
`link_description` text,
`link_type` varchar( 100 ) default NULL ,
`link_owner` int( 11 ) default '0',
`link_date` datetime default NULL ,
`link_icon` varchar( 20 ) default 'obj/',
`link_category` int( 11 ) NOT NULL default '0',
PRIMARY KEY ( `link_id` ) ,
KEY `idx_link_task` ( `link_task` ) ,
KEY `idx_link_project` ( `link_project` ) ,
KEY `idx_link_parent` ( `link_parent` ) 
) TYPE = MYISAM ";

        db_exec($sql);
        db_exec("INSERT INTO `sysvals` VALUES (null, 1, 'LinkType', '0|Unknown\n1|Document\n2|Application')");
 }

}
?>
