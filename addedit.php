<?php /* FILES $Id: addedit.php,v 1.6 2005/02/14 05:20:52 cyberhorse Exp $ */
$link_id = intval( dPgetParam( $_GET, 'link_id', 0 ) );
 
// check permissions for this record
$perms =& $AppUI->acl();
$canEdit = $perms->checkModuleItem( $m, 'edit', $link_id );
if (!$canEdit) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

// load the companies class to retrieved denied companies
require_once( $AppUI->getModuleClass( 'projects' ) );

$link_task = intval( dPgetParam( $_GET, 'link_task', 0 ) );
$link_parent = intval( dPgetParam( $_GET, 'link_parent', 0 ) );
$link_project = intval( dPgetParam( $_GET, 'project_id', 0 ) );

$sql = "
SELECT links.*,
	user_username,
	contact_first_name,
	contact_last_name,
	project_id,
	task_id, task_name
FROM links
LEFT JOIN users ON link_owner = user_id
LEFT JOIN contacts ON user_contact = contact_id
LEFT JOIN projects ON project_id = link_project
LEFT JOIN tasks ON task_id = link_task
WHERE link_id = $link_id
";

// check if this record has dependancies to prevent deletion
$msg = '';
$obj = new CLink();
$canDelete = $obj->canDelete( $msg, $link_id );

// load the record data
$obj = null;
if (!db_loadObject( $sql, $obj ) && $link_id > 0) {
	$AppUI->setMsg( 'Link' );
	$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect();
}

// setup the title block
$ttl = $link_id ? "Edit Link" : "Add Link";
$titleBlock = new CTitleBlock( $ttl, 'folder5.png', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=$m", "links list" );
$canDelete = $perms->checkModuleItem( $m, 'delete', $link_id );
if ($canDelete && $link_id > 0) {
	$titleBlock->addCrumbDelete( 'delete link', $canDelete, $msg );
}
$titleBlock->show();

if ($obj->link_project) {
	$link_project = $obj->link_project;
}
if ($obj->link_task) {
	$link_task = $obj->link_task;
	$task_name = @$obj->task_name;
} else if ($link_task) {
	$sql = "SELECT task_name FROM tasks WHERE task_id=$link_task";
	$task_name = db_loadResult( $sql );
} else {
	$task_name = '';
}

$extra = array(
	'where'=>'project_active <> 0'
);
$project = new CProject();
$projects = $project->getAllowedRecords( $AppUI->user_id, 'project_id,project_name', 'project_name', null, $extra );
$projects = arrayMerge( array( '0'=>$AppUI->_('All', UI_OUTPUT_JS) ), $projects );

//$sql = "SELECT project_id, project_name  FROM projects ORDER BY project_name";
//$projects = arrayMerge( array( '0'=>'- ALL PROJECTS -'), db_loadHashList( $sql ) );
?>
<script language="javascript">
function submitIt() {
	var f = document.uploadFrm;
	f.submit();
}
function delIt() {
	if (confirm( "<?php echo $AppUI->_('linksDelete', UI_OUTPUT_JS);?>" )) {
		var f = document.uploadFrm;
		f.del.value='1';
		f.submit();
	}
}
function popTask() {
    var f = document.uploadFrm;
    if (f.link_project.selectedIndex == 0) {
        alert( "<?php echo $AppUI->_('Please select a project first!', UI_OUTPUT_JS);?>" );
    } else {
        window.open('./index.php?m=public&a=selector&dialog=1&callback=setTask&table=tasks&task_project='
            + f.link_project.options[f.link_project.selectedIndex].value, 'task','left=50,top=50,height=250,width=400,resizable')
    }
}

// Callback function for the generic selector
function setTask( key, val ) {
    var f = document.uploadFrm;
    if (val != '') {
        f.link_task.value = key;
        f.task_name.value = val;
    } else {
        f.link_task.value = '0';
        f.task_name.value = '';
    }
}
</script>

<table width="100%" border="0" cellpadding="3" cellspacing="3" class="std">

<form name="uploadFrm" action="?m=links" method="post">
	<input type="hidden" name="dosql" value="do_link_aed" />
	<input type="hidden" name="del" value="0" />
	<input type="hidden" name="link_id" value="<?php echo $link_id;?>" />

<tr>
	<td width="100%" valign="top" align="center">
		<table cellspacing="1" cellpadding="2" width="60%">
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Link Name' );?>:</td>
			<td align="left"><input type="text" class="text" name="link_name" value="<?echo $obj->link_name;?>"></td>
	<?php if ($link_id) { ?>
			<td>
				<a href="<?php echo $obj->link_url;?>"><?php echo $AppUI->_( 'go' );?></a>
			</td>
		</tr>
		<tr valign="top">
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Type' );?>:</td>
			<td align="left" class="hilite"><?php echo $obj->link_type;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Uploaded By' );?>:</td>
			<td align="left" class="hilite"><?php echo $obj->user_first_name . ' '. $obj->user_last_name;?></td>
	<?php } ?>
		</tr>
                <tr>
                        <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Category');?>:</td>
                        <td align="left">
                                <?php echo arraySelect(dPgetSysVal("LinkType"), 'link_category', '', $obj->link_category); ?>
                        <td>

		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Project' );?>:</td>
			<td align="left">
			<?php
				echo arraySelect( $projects, 'link_project', 'size="1" class="text" style="width:270px"', $link_project );
			?>
			</td>
		</tr>

		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Task' );?>:</td>
			<td align="left" colspan="2" valign="top">
				<input type="hidden" name="link_task" value="<?php echo $link_task;?>" />
				<input type="text" class="text" name="task_name" value="<?php echo $task_name;?>" size="40" disabled />
				<input type="button" class="button" value="<?php echo $AppUI->_('select task');?>..." onclick="popTask()" />
			</td>
		</tr>

		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Description' );?>:</td>
			<td align="left">
				<textarea name="link_description" class="textarea" rows="4" style="width:270px"><?php echo $obj->link_description;?></textarea>
			</td>
		</tr>

		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Link URL' );?>:</td>
			<td align="left"><input type="field" name="link_url" style="width:270px" value="<?= $obj->link_url ?>"></td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td>
		<input class="button" type="button" name="cancel" value="<?php echo $AppUI->_('cancel');?>" onClick="javascript:if(confirm('<?php echo $AppUI->_('Are you sure you want to cancel?', UI_OUTPUT_JS); ?>')){location.href = './index.php?m=links';}" />
	</td>
	<td align="right">
		<input type="button" class="button" value="<?php echo $AppUI->_( 'submit' );?>" onclick="submitIt()" />
	</td>
</tr>
</form>
</table>
