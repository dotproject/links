<?php /* FILES $Id: index.php,v 1.29 2004/07/25 18:15:35 gregorerhardt Exp $ */
$AppUI->savePlace();

// retrieve any state parameters
if (isset( $_REQUEST['project_id'] )) {
	$AppUI->setState( 'LinkIdxProject', $_REQUEST['project_id'] );
}

$project_id = $AppUI->getState( 'LinkIdxProject' ) !== NULL ? $AppUI->getState( 'LinkIdxProject' ) : 0;

if (dPgetParam($_GET, 'tab', -1) != -1 ) {
        $AppUI->setState( 'LinkIdxTab', dPgetParam($_GET, 'tab'));
}
$tab = $AppUI->getState( 'LinkIdxTab' ) !== NULL ? $AppUI->getState( 'LinkIdxTab' ) : 0;
$active = intval( !$AppUI->getState( 'LinkIdxTab' ) );

require_once( $AppUI->getModuleClass( 'projects' ) );

// get the list of visible companies
$extra = array(
	'from' => 'links',
	'where' => 'AND project_id = link_project'
);

$project = new CProject();
$projects = $project->getAllowedRecords( $AppUI->user_id, 'project_id,project_name', 'project_name', null, $extra );
$projects = arrayMerge( array( '0'=>$AppUI->_('All') ), $projects );

// setup the title block
$titleBlock = new CTitleBlock( 'Files', 'folder5.png', $m, "$m.$a" );
$titleBlock->addCell( $AppUI->_('Filter') . ':' );
$titleBlock->addCell(
	arraySelect( $projects, 'project_id', 'onChange="document.pickProject.submit()" size="1" class="text"', $project_id ), '',
	'<form name="pickProject" action="?m=links" method="post">', '</form>'
);
if ($canEdit) {
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new link').'">', '',
		'<form action="?m=links&a=addedit" method="post">', '</form>'
	);
}
$titleBlock->show();

$link_types = dPgetSysVal("FileType");
if ( $tab != -1 ) {
        array_unshift($link_types, "All Links");
}

$tabBox = new CTabBox( "?m=links", "{$dPconfig['root_dir']}/modules/links/", $tab );

$i = 0;

foreach($link_types as $link_type)
{
        $tabBox->add("index_table", $link_type);
        ++$i;
}
                                                                                
$tabBox->show();

?>
