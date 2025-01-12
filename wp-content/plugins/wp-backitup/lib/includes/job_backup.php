<?php if (!defined ('ABSPATH')) die('No direct access allowed');

// Checking safe mode is on/off and set time limit
if( ini_get('safe_mode') ){
   @ini_set('max_execution_time', WPBACKITUP__SCRIPT_TIMEOUT_SECONDS);
}else{
   @set_time_limit(WPBACKITUP__SCRIPT_TIMEOUT_SECONDS);
}

/**
 * WP BackItUp  - Backup Job
 *
 * @package WP BackItUp
 * @author  Chris Simmons <chris.simmons@wpbackitup.com>
 * @link    http://www.wpbackitup.com
 *
 */

/*** Includes ***/

if( !class_exists( 'WPBackItUp_Utility' ) ) {
 	include_once 'class-utility.php';
}


if( !class_exists( 'WPBackItUp_SQL' ) ) {
	include_once 'class-sql.php';
}

if( !class_exists( 'WPBackItUp_Backup' ) ) {
	include_once 'class-backup.php';
}

 if( !class_exists( 'WPBackItUp_Zip' ) ) {
 	include_once 'class-zip.php';
 }


if( !class_exists( 'WPBackItUp_Filesystem' ) ) {
	include_once 'class-filesystem.php';
}

if( !class_exists( 'WPBackItUp_DataAccess' ) ) {
	include_once 'class-database.php';
}

/*** Globals ***/
global $WPBackitup;

global $status_array,$inactive,$active,$complete,$failure,$warning,$success;
$inactive=0;
$active=1;
$complete=2;
$failure=-1;
$warning=-2;
$success=99;

//setup the status array
global $status_array;
$status_array = array(
	'preparing' =>$inactive,
	'create_inventory' =>$inactive,
	'backupdb' =>$inactive ,
	'infofile'=>$inactive,
	'backup_themes'=>$inactive,
	'backup_plugins'=>$inactive,
	'backup_uploads'=>$inactive,
	'backup_other'=>$inactive,
	'validate_backup'=>$inactive,
	'finalize_backup'=>$inactive,
 );

//*****************//
//*** MAIN CODE ***//
//*****************//

if (! is_object ($current_job)){
	WPBackItUp_LoggerV2::log_error($events_logname,$process_id,'Current job not object:');
	WPBackItUp_LoggerV2::log_error($events_logname,$process_id,var_export($current_job));
	return false;
}

if (! is_object ($current_task)){
	WPBackItUp_LoggerV2::log_error($events_logname,$process_id,'Current task not object');
	WPBackItUp_LoggerV2::log_error($events_logname,$process_id,var_export($current_task));
	return false;
}

WPBackItUp_LoggerV2::log_info($events_logname,$process_id ,'Run task:' .$current_task->getTaskName());

//*************************//
//*** MAIN BACKUP CODE  ***//
//*************************//
global $backup_logname;
//Get the backup ID
$backup_name =  $current_job->getJobName();
$backup_logname =  sprintf('JobLog_%s',$current_job->getJobName());

$log_function='job_backup::'.$current_task->getTaskName();

global $wp_backup;
$wp_backup = new WPBackItUp_Backup($backup_logname,$backup_name,$WPBackitup->backup_type);


//*************************//
//***   BACKUP TASKS    ***//
//*************************//

WPBackItUp_LoggerV2::log_info($backup_logname,$log_function ,'Run task:' .$current_task->getTaskName());

//An error has occurred on the previous tasks
if (WPBackItUp_Job::ERROR==$current_task->getStatus()){
	WPBackItUp_LoggerV2::log_error($backup_logname,$log_function,'Fatal error on previous task:'. $current_task->getTaskName());

	//Fetch last wordpress error(might not be related to timeout)
	//error type constants: http://php.net/manual/en/errorfunc.constants.php
	WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'Last Error: ' .var_export(error_get_last(),true));

	//Check for error type
	switch ($current_task->getTaskName()) {
		case "task_preparing":
			set_status('preparing',$active,true);
			write_fatal_error_status('2101');
			end_backup(2101, false);
			break;

		case "task_inventory_database":
		case "task_inventory_plugins":
		case "task_inventory_themes":
		case "task_inventory_uploads":
		case "task_inventory_others":
			set_status('create_inventory',$active,true);
			write_fatal_error_status('2127');
			end_backup(2127, false);
			break;

		case "task_backup_db":
			set_status( 'backupdb', $active, true );
			write_fatal_error_status( '2104' );
			end_backup( 2104, false );
			break;

		case "task_backup_siteinfo":
			set_status( 'infofile', $active, true );
			write_fatal_error_status( '2105' );
			end_backup( 2105, false );
			break;

		case "task_backup_themes":
			set_status( 'backup_themes', $active, true );
			write_fatal_error_status( '2120' );
			end_backup( 2120, false );
			break;

		case "task_backup_plugins":
			set_status( 'backup_plugins', $active, true );
			write_fatal_error_status( '2121' );
			end_backup( 2121, false );
			break;

		case "task_backup_uploads":
			set_status( 'backup_uploads', $active, true );
			write_fatal_error_status( '2122' );
			end_backup( 2122, false );
			break;

		case "task_backup_other":
			set_status( 'backup_other', $active, true );
			write_fatal_error_status( '2123' );
			end_backup( 2123, false );
			break;

		case "task_validate_backup":
			set_status( 'validate_backup', $active, true );
			write_fatal_error_status( '2126' );
			end_backup( 2126, false );
			break;

		case "task_finalize_backup":
			set_status( 'finalize_backup', $active, true );
			write_fatal_error_status( '2109' );
			end_backup( 2109, false );
			break;

		default:
			write_warning_status( '2999' );
			end_backup( 2999, false );
			break;
	}

	return false;

}

if ('task_preparing'==$current_task->getTaskName()) {

	//Init
	WPBackItUp_LoggerV2::log($backup_logname,'***BEGIN BACKUP***');
	WPBackItUp_LoggerV2::log_sysinfo($backup_logname);
	WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'BACKUP TYPE:' .$wp_backup->backup_type);
	WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'BACKUP ID:' .$current_job->getJobId());

	$WPBackitup->increment_backup_count();
	//End Init

	WPBackItUp_LoggerV2::log($backup_logname,'**BEGIN CLEANUP**');

	//Cleanup & Validate the backup folded is ready
	write_response_processing("preparing for backup");
	set_status('preparing',$active,true);

	write_response_processing("Cleanup before backup");

	//*** Check Dependencies ***
	if ( ! WPBackItUp_Zip::zip_utility_exists()) {
		WPBackItUp_LoggerV2::log_error($backup_logname,$log_function,'Zip Util does not exist.' );
		$current_task->setStatus(WPBackItUp_Job_Task::ERROR,125);
		write_fatal_error_status( '125' );
		end_backup( 125, false );
		return false;
	}

	//*** END Check Dependencies ***


	//Make sure wpbackitup_backups exists
	if (! $wp_backup->backup_root_folder_exists() ){
		$current_task->setStatus(WPBackItUp_Job_Task::ERROR,101);

	    write_fatal_error_status('101');
	    end_backup(101, false);
		return false;
	}

	//Create the root folder for the current backup
	if (! $wp_backup->create_current_backup_folder()){
		$current_task->setStatus(WPBackItUp_Job_Task::ERROR,101);

	    write_fatal_error_status('101');
	    end_backup(101, false);
		return false;
	}

	//Check to see if the directory exists and is writeable
	if (! $wp_backup->backup_folder_exists()){
		$current_task->setStatus(WPBackItUp_Job_Task::ERROR,102);

	    write_fatal_error_status('102');
	    end_backup(102,false);
		return false;
	}

	set_status('preparing',$complete,false);
	$current_task->setStatus(WPBackItUp_Job_Task::COMPLETE);

	WPBackItUp_LoggerV2::log($backup_logname,'**END CLEANUP**');
	return;
}

if ('task_inventory_database'==$current_task->getTaskName()) {
	WPBackItUp_LoggerV2::log( $backup_logname, '**INVENTORY DATABASE**' );

	write_response_processing( "Create inventory" );
	set_status( 'create_inventory', $active, true );

	// Exclude wp backitup job tables from backup
	$wpb_job_tables = WPBackItUp_DataAccess::get_excluded_jobs_tables();

	//get excludes from settings
	$backup_dbtables_filter_list = explode(', ', WPBackitup_Admin::backup_dbtables_filter_list());
	$tables_exclude = array_merge(
		$backup_dbtables_filter_list,
		$wpb_job_tables
	);

	//get batch size from user settings
	$batch_size = $WPBackitup->backup_dbtables_batch_size();

	if ( ! $wp_backup->save_database_inventory($current_job->getJobId(), WPBackItUp_Job_Item::DATABASE, $batch_size, $tables_exclude ) ) {
		WPBackItUp_LoggerV2::log_error( $backup_logname, $log_function, 'Database Inventory Error.' );
		$current_task->setStatus(WPBackItUp_Job_Task::ERROR,127);

		write_fatal_error_status( '127' );
		end_backup( 127, false );

		return false;
	};


	WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'Database Inventory complete.');
	$current_task->setStatus(WPBackItUp_Job_Task::COMPLETE);

	WPBackItUp_LoggerV2::log($backup_logname,'**INVENTORY DATABASE**');
	return;

}


if ('task_inventory_plugins'==$current_task->getTaskName()) {
	WPBackItUp_LoggerV2::log( $backup_logname, '**INVENTORY PLUGINS**' );

	write_response_processing( "Create inventory" );
	set_status( 'create_inventory', $active, true );

	$global_exclude = explode(',', WPBACKITUP__BACKUP_GLOBAL_IGNORE_LIST);
	// Getting user filter
	$backup_plugins_filter = explode(',', WPBackitup_Admin::backup_plugins_filter());

	// WARNING
	// Remember these are wildcard searches(FOLDERS ONLY) so any folders containing these values will be excluded from inventory
	// ie. backup and backups will be excluded because "backup" was contained in the array
	// This is a deep filter as well so ANY occurrence of these will be excluded no matter how deep in the tree
	$plugin_exclude = array_merge(
		$global_exclude,
		$backup_plugins_filter,
		array(
			"wp-backitup",
		)
	);

	if ( ! $wp_backup->save_folder_inventory( WPBACKITUP__SQL_BULK_INSERT_SIZE, $current_job->getJobId(), WPBackItUp_Job_Item::PLUGINS, WPBACKITUP__PLUGINS_ROOT_PATH, $plugin_exclude ) ) {
		WPBackItUp_LoggerV2::log_error( $backup_logname, $log_function, 'Plugins Inventory Error.' );
		$current_task->setStatus(WPBackItUp_Job_Task::ERROR,127);

		write_fatal_error_status( '127' );
		end_backup( 127, false );

		return false;
	};


	WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'Plugin Inventory complete.');

	//set_status('inventory',$complete,false);
	$current_task->setStatus(WPBackItUp_Job_Task::COMPLETE);

	WPBackItUp_LoggerV2::log($backup_logname,'**INVENTORY PLUGINS**');
	return;

}



if ('task_inventory_themes'==$current_task->getTaskName()) {
	WPBackItUp_LoggerV2::log( $backup_logname, '**INVENTORY THEMES**' );

	$global_exclude = explode(',', WPBACKITUP__BACKUP_GLOBAL_IGNORE_LIST);

	// Getting user filter
	$backup_themes_filter = explode(',', WPBackitup_Admin::backup_themes_filter());

	// WARNING
	// Remember these are wildcard searches(FOLDERS ONLY) so any folders containing these values will be excluded from inventory
	// ie. backup and backups will be excluded because "backup" was contained in the array
	// This is a deep filter as well so ANY occurrence of these will be excluded no matter how deep in the tree
	$theme_exclude = array_merge(
		$global_exclude,
		$backup_themes_filter
	);

	if (! $wp_backup->save_folder_inventory(WPBACKITUP__SQL_BULK_INSERT_SIZE,$current_job->getJobId(),WPBackItUp_Job_Item::THEMES,WPBACKITUP__THEMES_ROOT_PATH,$theme_exclude)){
		WPBackItUp_LoggerV2::log_error($backup_logname,$log_function,'Themes Inventory Error.');
		$current_task->setStatus(WPBackItUp_Job_Task::ERROR,127);

		write_fatal_error_status('127');
		end_backup(127,false);
		return false;
	};

	WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'Theme Inventory complete.');

	$current_task->setStatus(WPBackItUp_Job_Task::COMPLETE);

	WPBackItUp_LoggerV2::log($backup_logname,'**INVENTORY THEMES**');
	return;

}

if ('task_inventory_uploads'==$current_task->getTaskName()) {
	WPBackItUp_LoggerV2::log( $backup_logname, '**INVENTORY UPLOADS**' );

	$global_exclude = explode(',', WPBACKITUP__BACKUP_GLOBAL_IGNORE_LIST);
	// Getting user filter
	$backup_uploads_filter = explode(',', WPBackitup_Admin::backup_uploads_filter());

	// WARNING
	// Remember these are wildcard searches(FOLDERS ONLY) so any folders containing these values will be excluded from inventory
	// ie. backup and backups will be excluded because "backup" was contained in the array
	// This is a deep filter as well so ANY occurrence of these will be excluded no matter how deep in the tree

	$upload_exclude = array_merge (
		$global_exclude,
		$backup_uploads_filter,
		array(
			"backup",
			"backwpup",
			"updraft",
			"wp-clone",
			"backwpup",
			"backupwordpress",
			"cache",
			"backupcreator",
			"backupbuddy"
		));

	$upload_array = wp_upload_dir();
	$uploads_root_path = $upload_array['basedir'];
	if (! $wp_backup->save_folder_inventory(WPBACKITUP__SQL_BULK_INSERT_SIZE,$current_job->getJobId(),WPBackItUp_Job_Item::UPLOADS,$uploads_root_path,$upload_exclude)){
		WPBackItUp_LoggerV2::log_error($backup_logname,$log_function,'Uploads Inventory Error.');
		$current_task->setStatus(WPBackItUp_Job_Task::ERROR,127);

		write_fatal_error_status('127');
		end_backup(127,false);
		return false;
	};

	WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'Uploads Inventory complete.');
	$current_task->setStatus(WPBackItUp_Job_Task::COMPLETE);

	WPBackItUp_LoggerV2::log($backup_logname,'**INVENTORY UPLOADS**');
	return;

}

if ('task_inventory_others'==$current_task->getTaskName()) {
	WPBackItUp_LoggerV2::log( $backup_logname, '**INVENTORY OTHERS**' );

	$global_exclude = explode(',', WPBACKITUP__BACKUP_GLOBAL_IGNORE_LIST);
	// Getting user filter
	$backup_others_filter = explode(',', WPBackitup_Admin::backup_others_filter());

	// WARNING
	// Remember these are wildcard searches(FOLDERS ONLY) so any folders containing these values will be excluded from inventory
	// ie. backup and backups will be excluded because "backup" was contained in the array
	// This is a deep filter as well so ANY occurrence of these will be excluded no matter how deep in the tree

	$other_exclude = array_merge (
		$global_exclude,
		$backup_others_filter,
		array(
			"debug.log",
			"backup",
			"plugins",
			"themes",
			"uploads",
			"wpbackitup_backups",
			"wpbackitup_restore",
			"backup",
			"w3tc-config",
			"updraft",
			"wp-clone",
			"backwpup",
			"backupwordpress",
			"cache",
			"backupcreator",
			"backupbuddy",
			"wptouch-data",
			"ai1wm-backups",
			"sedlex",
		));

	if (! $wp_backup->save_folder_inventory(WPBACKITUP__SQL_BULK_INSERT_SIZE,$current_job->getJobId(),WPBackItUp_Job_Item::OTHERS,WPBACKITUP__CONTENT_PATH,$other_exclude)){
		WPBackItUp_LoggerV2::log_error($backup_logname,$log_function,'Other Inventory Error.');
		$current_task->setStatus(WPBackItUp_Job_Task::ERROR,127);

		write_fatal_error_status('127');
		end_backup(127,false);
		return false;
	};

	WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'Others Inventory complete.');

	//when others is done then update the task as completed
	set_status('create_inventory',$complete,false);
	$current_task->setStatus(WPBackItUp_Job_Task::COMPLETE);

	WPBackItUp_LoggerV2::log($backup_logname,'**INVENTORY OTHERS**');
	return;

}


//Backup the database
if ('task_backup_db'==$current_task->getTaskName()) {
	WPBackItUp_LoggerV2::log($backup_logname,'**BEGIN DATABASE EXPORT**');
	write_response_processing( "Create database export" );
	set_status( 'backupdb', $active, true );

	$default_db_export_method = $WPBackitup->backup_db_export_method();//default to user setting
	$db_backup_method = $current_task->getTaskMetaValue('db_backup_method',$default_db_export_method);
	$db_mysqldump_path = $current_task->getTaskMetaValue('db_mysqldump_path',false);

	//get batch size from user settings
	$batch_size = $WPBackitup->backup_dbtables_batch_size();

	if ('mysqldump'==$db_backup_method) {

		$tables_remaining_count = $wp_backup->export_database_mysqldump( $current_job, WPBackItUp_Job_Item::DATABASE,$batch_size,$db_mysqldump_path);

		//if first attempt failed AND dunp type false then try using true
		if (false===$tables_remaining_count && false===$db_mysqldump_path){
			$db_mysqldump_path=true;
			$tables_remaining_count = $wp_backup->export_database_mysqldump( $current_job, WPBackItUp_Job_Item::DATABASE,$batch_size,$db_mysqldump_path );
		}

		//try manual if mysqldump failed
		if (false===$tables_remaining_count){
			$db_backup_method='wpbackitup';
		}
	}

	//use wpbackitup export if MYSQL fails
	if ('wpbackitup'==$db_backup_method) {
		$tables_remaining_count = $wp_backup->export_database_wpbackitup($current_job,WPBackItUp_Job_Item::DATABASE,$batch_size);
	}

	WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'Database Export Items remaining:' .var_export($tables_remaining_count,true));
	if (false===$tables_remaining_count) {
		$current_task->setStatus(WPBackItUp_Job_Task::ERROR,104);
		write_fatal_error_status( '104' );
		end_backup( 104, false );
		return false;
	}else{
		$current_task->setTaskMetaValue('db_backup_method',$db_backup_method);
		$current_task->setTaskMetaValue('db_mysqldump_path',$db_mysqldump_path);
		if ($tables_remaining_count>0){
			//CONTINUE
			WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'Continue backing up tables.');
			$current_task->setStatus(WPBackItUp_Job_Task::QUEUED);
		}else{
			//COMPLETE
			WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'Complete - All tables backed up.');

			set_status( 'backupdb', $complete, false );
			$current_task->setStatus(WPBackItUp_Job_Task::COMPLETE);
			WPBackItUp_LoggerV2::log($backup_logname,'**END DATABASE EXPORT**');
		}
	}

	return;

}

//Extract the site info
if ('task_backup_siteinfo'==$current_task->getTaskName()) {
	WPBackItUp_LoggerV2::log($backup_logname,'**SITE INFO**' );
	write_response_processing( "Retrieve Site Info" );
	set_status( 'infofile', $active, true );

	if ( $wp_backup->create_siteinfo_file()  ) {

		//Add site Info and SQL data to main zip
		$suffix='main';
		$source_site_data_root = $wp_backup->backup_project_path;
		$target_site_data_root = 'site-data';

		$file_system = new WPBackItUp_FileSystem($backup_logname);

		// adding wp-config.php to main zip
		$wpconfig_path =  ABSPATH.'wp-config.php';
		copy($wpconfig_path, $source_site_data_root.'wp-config.txt');
		
		$site_data_files = $file_system->get_fileonly_list($wp_backup->backup_project_path, 'txt|sql');
		WPBackItUp_LoggerV2::log_info($backup_logname,__METHOD__, 'Main Files Found:' . var_export($site_data_files,true));
		$site_data_complete = $wp_backup->backup_file_list( $source_site_data_root, $target_site_data_root, $suffix, $site_data_files, WPBACKITUP__OTHERS_BATCH_SIZE );
		if ( $site_data_complete == 'error' ) {
			$current_task->setStatus(WPBackItUp_Job_Task::ERROR,105);

			write_fatal_error_status( '105' );
			end_backup( 105, false );
			return false;
		}
	} else {
		//Site data could be extracted
		$current_task->setStatus(WPBackItUp_Job_Task::ERROR,105);

		write_fatal_error_status( '105' );
		end_backup( 105, false );
		return false;
	}

	//get rid of the SQL and sitedata file - will check again at end in cleanup
	$wp_backup->cleanup_current_backup();

	set_status( 'infofile', $complete, false );
	$current_task->setStatus(WPBackItUp_Job_Task::COMPLETE);

	WPBackItUp_LoggerV2::log($backup_logname,'**END SITE INFO**' );
	return;

}


//Backup the themes
if ('task_backup_themes'==$current_task->getTaskName()) {
	WPBackItUp_LoggerV2::log($backup_logname,'**BACKUP THEMES TASK**' );
	write_response_processing( "Backup themes " );
	set_status( 'backup_themes', $active, true );

	$themes_remaining_files_count = $wp_backup->backup_files($current_job->getJobId(),WPBACKITUP__THEMES_ROOT_PATH,WPBackItUp_Job_Item::THEMES);
	WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'Themes remaining:' .$themes_remaining_files_count);
	if ($themes_remaining_files_count===false) {
		//ERROR
		WPBackItUp_LoggerV2::log_error($backup_logname,$log_function,'Error backing up themes.');
		$current_task->setStatus(WPBackItUp_Job_Task::ERROR,120);
		write_fatal_error_status( '120' );
		end_backup( 120, false );
		return false;
	}else{
		if ($themes_remaining_files_count>0){
			//CONTINUE
			WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'Continue backing up themes.');
			$current_task->setStatus(WPBackItUp_Job_Task::QUEUED);
		}else{
			//COMPLETE
			WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'Complete - All themes backed up.');

			set_status( 'backup_themes', $complete, false );
			$current_task->setStatus(WPBackItUp_Job_Task::COMPLETE);
			WPBackItUp_LoggerV2::log($backup_logname,'**END BACKUP THEMES TASK**');
		}
	}

	return;
}


//Backup the plugins
if ('task_backup_plugins'==$current_task->getTaskName()) {
	WPBackItUp_LoggerV2::log($backup_logname,'**BACKUP PLUGINS TASK**' );
	write_response_processing( "Backup plugins " );
	set_status( 'backup_plugins', $active, true );

	$plugins_remaining_files_count = $wp_backup->backup_files($current_job->getJobId(),WPBACKITUP__PLUGINS_ROOT_PATH,WPBackItUp_Job_Item::PLUGINS);
	WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'Plugins remaining:' .$plugins_remaining_files_count);
	if ($plugins_remaining_files_count===false) {
		//ERROR
		WPBackItUp_LoggerV2::log_error($backup_logname,$log_function,'Error backing up plugins.');

		$current_task->setStatus(WPBackItUp_Job_Task::ERROR,121);
		write_fatal_error_status( '121' );
		end_backup( 121, false );
		return false;
	} else {
		if ($plugins_remaining_files_count>0){
			//CONTINUE
			WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'Continue backing up plugins.');
			$current_task->setStatus(WPBackItUp_Job_Task::QUEUED);
		} else{
			//COMPLETE
			WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'Complete - All plugins backed up.');
			set_status( 'backup_plugins', $complete, false );
			$current_task->setStatus(WPBackItUp_Job_Task::COMPLETE);
			WPBackItUp_LoggerV2::log($backup_logname,'**END BACKUP PLUGINS TASK**');
		}
	}

	return;
}

//Backup the uploads
if ('task_backup_uploads'==$current_task->getTaskName()) {
	WPBackItUp_LoggerV2::log($backup_logname,'**BACKUP UPLOADS TASK**' );
	write_response_processing( "Backup uploads " );
	set_status( 'backup_uploads', $active, true );

	$upload_array        = wp_upload_dir();
	$source_uploads_root = $upload_array['basedir'];

	//exclude zip files from backup
	$uploads_remaining_files_count = $wp_backup->backup_files($current_job->getJobId(),$source_uploads_root,WPBackItUp_Job_Item::UPLOADS);
	WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'Uploads remaining:' .$uploads_remaining_files_count);
	if ( $uploads_remaining_files_count ===false) {
		//ERROR
		WPBackItUp_LoggerV2::log_error($backup_logname,$log_function,'Error backing up uploads.' );
		$current_task->setStatus(WPBackItUp_Job_Task::ERROR,122);

		write_fatal_error_status( '122' );
		end_backup( 122, false );
		return false;
	} else {
		if ( $uploads_remaining_files_count > 0 ) {
			//CONTINUE
			WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'Continue backing up uploads.' );
			$current_task->setStatus(WPBackItUp_Job_Task::QUEUED);

		} else {
			//COMPLETE
			WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'All uploads backed up.' );
			set_status( 'backup_uploads', $complete, false );
			$current_task->setStatus(WPBackItUp_Job_Task::COMPLETE);
			WPBackItUp_LoggerV2::log($backup_logname,'**END BACKUP UPLOADS TASK**' );
		}
	}

	return;
}

//Backup all the other content in the wp-content root
if ('task_backup_other'==$current_task->getTaskName()) {
	WPBackItUp_LoggerV2::log($backup_logname,'**BACKUP OTHER TASK**' );
	write_response_processing( "Backup other files " );
	set_status( 'backup_other', $active, true );

	$others_remaining_files_count = $wp_backup->backup_files($current_job->getJobId(),WPBACKITUP__CONTENT_PATH,WPBackItUp_Job_Item::OTHERS);
	WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'Others remaining:' .$others_remaining_files_count);
	if ( $others_remaining_files_count ===false) {
		//ERROR
		WPBackItUp_LoggerV2::log_error($backup_logname,$log_function,'Error backing up others.' );
		$current_task->setStatus(WPBackItUp_Job_Task::ERROR,123);

		write_fatal_error_status( '123' );
		end_backup( 123, false );
		return false;
	} else {
		if ( $others_remaining_files_count > 0 ) {
			//CONTINUE
			WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'Continue backing up others.' );
			$current_task->setStatus(WPBackItUp_Job_Task::QUEUED);
		} else {
			//COMPLETE
			WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'All others backed up.' );

			set_status( 'backup_other', $complete, false );
			$current_task->setStatus(WPBackItUp_Job_Task::COMPLETE);
			WPBackItUp_LoggerV2::log($backup_logname,'**END BACKUP OTHER TASK**' );
		}

	}

	return;
}



//ENCRYPT CONTENT TASK
//wp-config.php
//db backup

//Validate the backup IF logging is turned on - reporting only
if ('task_validate_backup'==$current_task->getTaskName()) {
	//Validate the content if logging is on
	WPBackItUp_LoggerV2::log($backup_logname,'**VALIDATE CONTENT**');

	write_response_processing( "Validating Backup " );
	set_status( 'validate_backup', $active, true );

	$set_validate_backup_error = false;
	$set_validate_backup_job_queue = true;
	$db = new WPBackItUp_DataAccess();

	$plugin_validation_meta = $current_task->getTaskMetaValue('task_multistep_validate_plugins');
	$theme_validation_meta = $current_task->getTaskMetaValue('task_multistep_validate_themes');
	$upload_validation_meta = $current_task->getTaskMetaValue('task_multistep_validate_uploads');
	$other_validation_meta = $current_task->getTaskMetaValue('task_multistep_validate_others');

	$validation_meta=false;
	$validation_task=false;
	if( $plugin_validation_meta != WPBackItUp_Job_Task::COMPLETE ) {
		$validation_task=WPBackItUp_Job_Item::PLUGINS;
		$validation_meta=$plugin_validation_meta;
	} elseif( $theme_validation_meta != WPBackItUp_Job_Task::COMPLETE ) {
		$validation_task=WPBackItUp_Job_Item::THEMES;
		$validation_meta=$theme_validation_meta;
	} elseif( $upload_validation_meta != WPBackItUp_Job_Task::COMPLETE )  {
		$validation_task=WPBackItUp_Job_Item::UPLOADS;
		$validation_meta=$upload_validation_meta;
	} elseif( $other_validation_meta != WPBackItUp_Job_Task::COMPLETE )  {
		$validation_task=WPBackItUp_Job_Item::OTHERS;
		$validation_meta=$other_validation_meta;
	} else {
		$set_validate_backup_job_queue = false;
	}

	if( $validation_meta !==false ) {
		$meta_task = sprintf( 'task_multistep_validate_%s', $validation_task );
		$batch_ids = $db->get_item_batch_ids( $current_job->getJobId(), $validation_task );

		if(!empty($batch_ids)){
			WPBackItUp_LoggerV2::log_info( $backup_logname, $log_function, sprintf('%s Batch Ids: %s',$validation_task,var_export( $batch_ids, true )));
			//$plugin_validation_batch_ids will never be empty

			$array_index = 0;
			if ( is_numeric( $validation_meta ) ) {
				$array_index = intval( $validation_meta );
				$array_index ++;
			}


			if ( array_key_exists( $array_index, $batch_ids ) ) {
				$batch_id        = $batch_ids[ $array_index ];//get batch ID
				$validate_result = $wp_backup->validate_backup_files_by_batch_id( $current_job->getJobId(), $validation_task, $batch_id );
				if ( $validate_result === false ) {
					$set_validate_backup_error = true;
				} else {
					$current_task->setTaskMetaValue( $meta_task, $array_index );
					WPBackItUp_LoggerV2::log_info( $backup_logname, $log_function, sprintf('%s Content, Batch ID: %s Validated Successfully!',$validation_task,$batch_id ));
				}
			} else {
				//task is done
				$current_task->setTaskMetaValue( $meta_task, WPBackItUp_Job_Task::COMPLETE );
				WPBackItUp_LoggerV2::log_info( $backup_logname, $log_function, sprintf('%s Content Validated Successfully!',$validation_task));
			}
		} else{
			//task is done
			WPBackItUp_LoggerV2::log_info( $backup_logname, $log_function, sprintf('Task Done! Folder is empty'));
			$current_task->setTaskMetaValue( $meta_task, WPBackItUp_Job_Task::COMPLETE );
			WPBackItUp_LoggerV2::log_info( $backup_logname, $log_function, sprintf('%s Content Validated Successfully!',$validation_task));
		}
	}

	//if error set error message
	if($set_validate_backup_error) {
		//ERROR
		WPBackItUp_LoggerV2::log_error($backup_logname,$log_function,'Content Validation ERROR.' );
		$current_task->setStatus(WPBackItUp_Job_Task::ERROR,126);

		write_fatal_error_status( '126' );
		end_backup( 123, false );
		return false;
	} elseif($set_validate_backup_job_queue === false) {

		//set backup status to success
		set_status( 'validate_backup', $complete, false );
		$current_task->setStatus(WPBackItUp_Job_Task::COMPLETE);

		WPBackItUp_LoggerV2::log_info($backup_logname,$log_function,'All Content Validated Successfully!' );
		WPBackItUp_LoggerV2::log($backup_logname,'**END VALIDATE CONTENT**' );

	} elseif ($set_validate_backup_job_queue === true) {
		$current_task->setStatus(WPBackItUp_Job_Task::QUEUED);
	}

	return;
}


//Zip up the backup folder
if ('task_finalize_backup'==$current_task->getTaskName()) {
	WPBackItUp_LoggerV2::log($backup_logname,'**FINALIZE BACKUP**' );
	write_response_processing( "Compress Backup " );
	set_status( 'finalize_backup', $active, true );

	//Generate manifest
	if ( ! $wp_backup->create_backup_manifest()) {
		$current_task->setStatus(WPBackItUp_Job_Task::ERROR,109);

		write_fatal_error_status( '109' );
		end_backup( 109, false );
		return false;
	}

	//cleanup the manifest and sql files from root
	if ( ! $wp_backup->cleanup_current_backup()  ) {
		//Warning - no need to error job
		write_warning_status( '106' );
	}

	//Rename backup folder
	if ( ! $wp_backup->rename_backup_folder()) {
		$current_task->setStatus(WPBackItUp_Job_Task::ERROR,109);

		write_fatal_error_status( '109' );
		end_backup( 109, false );
		return false;
	}

	//Take an inventory of the zip files created

	$file_system = new WPBackItUp_FileSystem($backup_logname);
	$zip_files = $file_system->get_fileonly_list_with_filesize($wp_backup->backup_project_path, 'zip');

	$wp_backup->save_file_list_inventory(WPBACKITUP__SQL_BULK_INSERT_SIZE,$current_job->getJobId(),WPBackItUp_Job_Item::BACKUPS,$wp_backup->backup_project_path,$zip_files);

	$current_job->setJobMetaValue('backup_zip_files' , $zip_files ); //list of zip files

	set_status( 'finalize_backup', $complete, false );
	$current_task->setStatus(WPBackItUp_Job_Task::COMPLETE);

	WPBackItUp_LoggerV2::log($backup_logname,'**END FINALIZE BACKUP**' );

	//If we get this far we have a finalized backup so change the path

	$WPBackitup->increment_successful_backup_count();

	//If complete then no post backup tasks
	if ($current_job->is_job_complete()) {
		//SUCCESS- End Job!
		//write response file first to make sure it is there
		write_response_file_success();
		set_status_success();

		end_backup( null, true );
		return true;
	}else{
		return;
	}
}

//POST BACKUP TASKS

$wp_backup->set_final_backup_path();

//RUN POST BACKUP TASKS
//error_log('TASK:'.$current_task->getTaskName());
//If not complete then we must have some post backup tasks
$post_backup_task = sprintf("%s_%s",WPBACKITUP__NAMESPACE,$current_task->getTaskName());
//error_log('Run Task:'.$post_backup_task);

//task will be updated in call
do_action($post_backup_task,$current_job);


if ($current_job->is_job_complete()) {
	//SUCCESS- End Job!

	//write response file first to make sure it is there
	write_response_file_success();
	set_status_success();

	end_backup( null, true );
	return true;
}else{
	//If not complete then post backup tasks available
	return;
}


exit();


/******************/
/*** Functions ***/
/******************/

function end_backup($err=null, $success=null){
    global $WPBackitup,$wp_backup,$backup_logname,$current_job;
	WPBackItUp_LoggerV2::log_info($backup_logname,__METHOD__,'Begin');

	$wp_backup->set_final_backup_path();

	WPBackItUp_LoggerV2::log_info($backup_logname,__METHOD__,'Zip up all the logs.');
	//Zip up all the logs in the log folder
	$logs_path = WPBACKITUP__PLUGIN_PATH .'logs';
	$zip_file_path = $wp_backup->backup_project_path .'logs_' .$current_job->getJobId() . '.zip';

	//copy WP debug file
	$wpdebug_file_path = WPBACKITUP__CONTENT_PATH . '/debug.log';
	WPBackItUp_LoggerV2::log_info($backup_logname,__METHOD__,'Save WP Debug: ' .$wpdebug_file_path);
	if (file_exists($wpdebug_file_path)) {
		$debug_log = sprintf('%s/wpdebug_%s.log',$logs_path,$current_job->getJobId());
		copy( $wpdebug_file_path, $debug_log );
		WPBackItUp_LoggerV2::log_info($backup_logname,__METHOD__,'WP Debug file saved: ' .$debug_log);
	}else{
		WPBackItUp_LoggerV2::log_info($backup_logname,__METHOD__,'NO WP Debug file: ' .$wpdebug_file_path);
	}

	$zip = new WPBackItUp_Zip($backup_logname,$zip_file_path);
	$zip->zip_files_in_folder($logs_path,$current_job->getJobId(),'*.log');
	$zip->close();

	WPBackItUp_Backup::end(); //Release the lock
	$current_datetime = current_time( 'timestamp' );
	$WPBackitup->set_backup_lastrun_date($current_datetime);


    $util = new WPBackItUp_Utility($backup_logname);
    $seconds = $util->timestamp_diff_seconds($current_job->getJobStartTimeTimeStamp(),$current_job->getJobEndTimeTimeStamp());

    $processing_minutes = round($seconds / 60);
    $processing_seconds = $seconds % 60;

	WPBackItUp_LoggerV2::log_info($backup_logname,__METHOD__,'Script Processing Time:' .$processing_minutes .' Minutes ' .$processing_seconds .' Seconds');

    if (true===$success) WPBackItUp_LoggerV2::log_info($backup_logname,__METHOD__,'Backup completed: SUCCESS');
	if (false===$success) WPBackItUp_LoggerV2::log_info($backup_logname,__METHOD__,'Backup completed: ERROR');

	WPBackItUp_LoggerV2::log($backup_logname,'*** END BACKUP ***');

	//Send Notification email
	WPBackItUp_LoggerV2::log_info($backup_logname,__METHOD__,'Send Email notification');
	$logs_attachment = array( $zip_file_path  );
	send_backup_notification_email($err, $success,$logs_attachment);

    $logFileName = WPBackItUp_LoggerV2::getLogFileName($backup_logname);
    $logFilePath = WPBackItUp_LoggerV2::getLogFilePath($backup_logname);

    //COPY the log if it exists
    $newlogFilePath = $wp_backup->backup_project_path .$logFileName;
    if (null!=$success && file_exists($logFilePath)){
	    copy($logFilePath,$newlogFilePath);
    }

	WPBackItUp_LoggerV2::close($backup_logname);
    echo('Backup has completed');
}

function send_backup_notification_email($err, $success,$logs=array()) {
	global $WPBackitup, $wp_backup, $backup_logname,$status_array,$current_job;
	WPBackItUp_LoggerV2::log_info($backup_logname,__METHOD__,'Begin');

	$start_timestamp = $current_job->getJobStartTimeTimeStamp();
	$end_timestamp = $current_job->getJobEndTimeTimeStamp();

    $utility = new WPBackItUp_Utility($backup_logname);
    $seconds = $utility->timestamp_diff_seconds($start_timestamp,$end_timestamp);

    $processing_minutes = round($seconds / 60);
    $processing_seconds = $seconds % 60;

	$message="";
	if($success)
	{
		//Don't send logs on success unless debug is on.
		if (WPBACKITUP__DEBUG!==true){
			$logs=array();
		}

		//If this is a scheduled backup and the license is expired
		if ($WPBackitup->premium_license() && 'expired'===$WPBackitup->license_status()){
			//send an email to customer letting them know their license
			$message .= sprintf( __('<strong>IMPORTANT NOTICE:</strong> <span style="color: #ff0000;"> Your license has expired.</span><br/><br/>Scheduled backups will only continue to function for <strong>30</strong> days after your license has expired.Please <strong><a href="%s" target="blank">renew</a></strong> for another year of product updates and access to our world class support team.<br/><br/>','wp-backitup'),
			esc_url(sprintf('%s/checkout?edd_license_key=%s&download_id=679&nocache=true&utm_medium=plugin&utm_source=wp-backitup&utm_campaign=premium&utm_content=license&utm_term=scheduled+backups', WPBACKITUP__SECURESITE_URL,$WPBackitup->license_key())));
		}


        $subject = sprintf(__('%s - Backup completed successfully.', 'wp-backitup'), get_bloginfo());
        $message .= '<b>' . __('Your backup completed successfully.', 'wp-backitup') . '</b><br/><br/>';

    } else  {
        $subject = sprintf(__('%s - Backup did not complete successfully.', 'wp-backitup'), get_bloginfo());
        $message .= '<b>' . __('Your backup did not complete successfully.', 'wp-backitup') . '</b><br/><br/>';
    }


	$local_start_datetime = get_date_from_gmt(date( 'Y-m-d H:i:s',$start_timestamp));
	$local_end_datetime = get_date_from_gmt(date( 'Y-m-d H:i:s',$end_timestamp));

	$message .= sprintf(__('WordPress Site: <a href="%s" target="_blank"> %s </a><br/>', 'wp-backitup'), home_url(), home_url());
    $message .= __('Backup date:', 'wp-backitup') . ' ' . $local_start_datetime . '<br/>';
	$message .= __('Number of backups completed with WP BackItUp:', 'wp-backitup') . ' ' . $WPBackitup->backup_count() . '<br/>';

	$message .= __('Completion Code:', 'wp-backitup') . ' ' . $current_job->getJobId() .'-'. $processing_minutes .'-' .$processing_seconds .'<br/>';
	$message .= __('WP BackItUp Version:', 'wp-backitup') . ' '  . WPBACKITUP__VERSION . '<br/>';
    $message .= '<br/>';

	//Add the completed steps on success
	if(! $success) {
		//Error occurred
        $message .= '<br/>';
        $message .= 'Errors:<br/>' . get_error_message($err);
	}

    $term='success';
    if(!$success)$term='error';
      $message .='<br/><br/>' . sprintf(__('Checkout %s for info about WP BackItUp and our other products.', 'wp-backitup'), $WPBackitup->get_anchor_with_utm('www.wpbackitup.com', '', 'notification+email', $term) ) . '<br/>';


	$notification_email = $WPBackitup->get_option('notification_email');
	if($notification_email)
		$utility->send_email($notification_email,$subject,$message,$logs);

	WPBackItUp_LoggerV2::log_info($backup_logname,__METHOD__,'End');
}

function write_fatal_error_status($status_code) {
	global $status_array,$active,$failure;
	
	//Find the active status and set to failure
	foreach ($status_array as $key => $value) {
		if ($value==$active){
			$status_array[$key]=$failure;	
		}
	}

	write_status();
	write_response_file_error($status_code);
}

function write_warning_status($status_code) {
	global $status_array,$active,$warning;

	//Find the active status and set to failure
	foreach ($status_array as $key => $value) {
		if ($value==$active){
			$status_array[$key]=$warning;
		}
	}

	write_status();
}

//function write_warning_status($status_code) {
//	global $status_array,$warning;
//
//	//Add warning to array
//	$status_array[$status_code]=$warning;
//	write_status();
//}

function write_status() {
	global $status_array;
	$fh=getStatusLog();

	foreach ($status_array as $key => $value) {
		fwrite($fh, '<div class="' . $key . '">' . $value .'</div>');		
	}

	fclose($fh);
}

function set_status($process,$status,$flush){
	global $status_array,$complete;

	$status_array[$process]=$status;

	//Mark all the others complete and flush
	foreach ($status_array as $key => $value) {
		if ($process==$key) {
			break;
		}else{
			$status_array[$key]=$complete;
		}
	}

	if ($flush) write_status(); 
}

function set_status_success(){
	global $status_array,$complete,$success;

	//Mark all the others complete and flush
	foreach ($status_array as $key => $value) {
		$status_array[$key]=$complete;
	}

	$status_array['finalinfo']=$success;
	write_status();
}

//Get Status Log
function getStatusLog(){
	global $backup_logname;

	$status_file_path = WPBACKITUP__PLUGIN_PATH .'/logs/backup_status.log';
	$filesystem = new WPBackItUp_FileSystem($backup_logname);
	return $filesystem->get_file_handle($status_file_path);

}

//write Response Log
function write_response_processing($message) {

    $jsonResponse = new stdClass();
	$jsonResponse->backupStatus = 'processing';
    $jsonResponse->backupMessage = $message;

	write_response_file($jsonResponse);
}


//write Response Log
function write_response_file_error($error) {

	$jsonResponse = new stdClass();
	$jsonResponse->backupStatus = 'error';
	$jsonResponse->backupMessage = get_error_message($error);

	write_response_file($jsonResponse);
}

//write Response Log
function write_response_file_success() {
    global $WPBackitup,$wp_backup,$backup_logname;

    $jsonResponse = new stdClass();
	$jsonResponse->backupStatus = 'success';
    $jsonResponse->backupMessage = 'success';
    $jsonResponse->backupName = $wp_backup->backup_name;
    $jsonResponse->backupLicense = $WPBackitup->license_active();
    $jsonResponse->backupRetained = $wp_backup->backup_retained_number;

	$jsonResponse->logFileExists = file_exists(WPBackItUp_LoggerV2::getLogFilePath($backup_logname));

	write_response_file($jsonResponse);
}

//write Response Log
function write_response_file($JSON_Response) {
	global $backup_logname;

	$json_response = json_encode($JSON_Response);
	WPBackItUp_LoggerV2::log_info($backup_logname,__METHOD__,'Write response file:' . $json_response);

	$fh=get_response_file();
	fwrite($fh, $json_response);
	fclose($fh);
}

//Get Response Log
function get_response_file() {
    global $backup_logname;

    $response_file_path = WPBACKITUP__PLUGIN_PATH .'logs/backup_response.log';
    $filesytem = new WPBackItUp_FileSystem($backup_logname);
    return $filesytem->get_file_handle($response_file_path,false);
}


/**
 * Get error message
 *
 * @param $error_code
 *
 * @return string
 */
function get_error_message($error_code){

	$error_message_array = array(
		'101' => __('(101) Unable to create a new directory for backup. Please check your CHMOD settings of your wp-backitup backup directory', 'wp-backitup'),
		'102'=> __('(102) Cannot create backup directory. Please check the CHMOD settings of your wp-backitup plugin directory', 'wp-backitup'),
		'103'=> __('(103) Unable to backup your files. Please try again', 'wp-backitup'),
		'104'=> __('(104) Unable to export your database. Please try again', 'wp-backitup'),
		'105'=> __('(105) Unable to export site information file. Please try again', 'wp-backitup'),
		'106'=> __('(106) Unable to cleanup your backup directory', 'wp-backitup'),
		'107'=> __('(107) Unable to compress(zip) your backup. Please try again', 'wp-backitup'),
		'108'=> __('(108) Unable to backup your site data files. Please try again', 'wp-backitup'),
		'109'=> __('(109) Unable to finalize backup. Please try again', 'wp-backitup'),
		'114'=> __('(114) Your database was accessible but an export could not be created. Please contact support by clicking the get support link on the right. Please let us know who your host is when you submit the request', 'wp-backitup'),
		'120'=> __('(120) Unable to backup your themes. Please try again', 'wp-backitup'),
		'121'=> __('(121) Unable to backup your plugins. Please try again', 'wp-backitup'),
		'122'=> __('(122) Unable to backup your uploads. Please try again', 'wp-backitup'),
		'123'=> __('(123) Unable to backup your miscellaneous files. Please try again', 'wp-backitup'),
		'125'=> __('(125) Unable to compress your backup because there is no zip utility available.  Please contact support', 'wp-backitup'),
		'126'=> __('(126) Unable to validate your backup. Please try again', 'wp-backitup'),
		'127'=> __('(127) Unable to create inventory of files to backup. Please try again', 'wp-backitup'),
		'128'=> __('(128) Unable to create job control record. Please try again', 'wp-backitup'),

		'2101' => __('(2101) Unable to create a new directory for backup. Please check your CHMOD settings of your wp-backitup backup directory', 'wp-backitup'),
		'2102'=> __('(2102) Cannot create backup directory. Please check the CHMOD settings of your wp-backitup plugin directory', 'wp-backitup'),
		'2103'=> __('(2103) Unable to backup your files. Please try again', 'wp-backitup'),
		'2104'=> __('(2104) Unable to export your database. Please try again', 'wp-backitup'),
		'2105'=> __('(2105) Unable to export site information file. Please try again', 'wp-backitup'),
		'2106'=> __('(2106) Unable to cleanup your backup directory', 'wp-backitup'),
		'2107'=> __('(2107) Unable to compress(zip) your backup. Please try again', 'wp-backitup'),
		'2108'=> __('(2108) Unable to backup your site data files. Please try again', 'wp-backitup'),
		'2109'=> __('(2109) Unable to finalize backup. Please try again', 'wp-backitup'),
		'2114'=> __('(2114) Your database was accessible but an export could not be created. Please contact support by clicking the get support link on the right. Please let us know who your host is when you submit the request', 'wp-backitup'),
		'2120'=> __('(2120) Unable to backup your themes. Please try again', 'wp-backitup'),
		'2121'=> __('(2121) Unable to backup your plugins. Please try again', 'wp-backitup'),
		'2122'=> __('(2122) Unable to backup your uploads. Please try again', 'wp-backitup'),
		'2123'=> __('(2123) Unable to backup your miscellaneous files. Please try again', 'wp-backitup'),
		'2125'=> __('(2125) Unable to compress your backup because there is no zip utility available.  Please contact support', 'wp-backitup'),
		'2126'=> __('(2126) Unable to validate your backup. Please try again', 'wp-backitup'),
		'2127'=> __('(2127) Unable to create inventory of files to backup. Please try again', 'wp-backitup'),
		'2128'=> __('(2128) Unable to create job control record. Please try again', 'wp-backitup'),
	);

	$error_message = __('(999) Unexpected error', 'wp-backitup');
	if (array_key_exists($error_code,$error_message_array)) {
		$error_message = $error_message_array[ $error_code ];
	}

	return $error_message;
}
