<?php if (!defined ('ABSPATH')) die('No direct access allowed');

/**
 * Class for Database access
 *
 * @package     WPBackItUp Database Class
 * @copyright   Copyright (c) 2015, Chris Simmons
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 *
 */

class WPBackItUp_DataAccess {

	private $log_name;

	function __construct() {
		$this->log_name='debug_database';

		try {

			self::get_jobs_tables();

		} catch(Exception $e) {
			error_log($e);
			WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'Constructor Exception: ' .$e);
		}
	}

	/**
	 * get all the jobs tables that should be excluded
	 *
	 * @return array
	 */
	static public function get_excluded_jobs_tables(){
		global $table_prefix;

		$old_job_table= $table_prefix . 'wpbackitup_job';
		$exclude_tables = self::get_jobs_tables();
		$exclude_tables[]=$old_job_table;

		return  $exclude_tables;
	}

	/**
	 * Adds tables to wpdb class AND returns array of tables
	 *
	 * @return array
	 */
	static public function get_jobs_tables(){
		global $wpdb,$table_prefix;

		$wpdb->wpbackitup_job_control = $table_prefix . 'wpbackitup_job_control';
		$wpdb->wpbackitup_job_task = $table_prefix . 'wpbackitup_job_tasks';
		$wpdb->wpbackitup_job_item = $table_prefix . 'wpbackitup_job_items';

		return  array($wpdb->wpbackitup_job_control,$wpdb->wpbackitup_job_task,$wpdb->wpbackitup_job_item);
	}

	/**
	 * Save Batch of SQL values to inventory table
	 * @param $sql_values
	 *
	 * @return bool
	 */
	public function insert_job_items($sql_values) {
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');
		global $wpdb;

		$sql_insert = "INSERT INTO $wpdb->wpbackitup_job_item
        (job_id, group_id, item, size_kb, create_date)
         VALUES " ;

		//Get rid of last comma and replace with  semicolon
		$sql = $sql_insert . substr_replace($sql_values, ";",-1);

		//If inserts return false
		$sql_rtn = $this->query($sql);
		if (false=== $sql_rtn ||  $sql_rtn==0 ) return false;
		else return true;

	}

	/**
	 * Save Batch of SQL values to inventory table with offset
	 * @param $sql_values
	 *
	 * @return bool
	 */
	public function insert_job_items_with_offset($sql_values) {
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');
		global $wpdb;

		$sql_insert = "INSERT INTO $wpdb->wpbackitup_job_item
        (job_id, group_id, item, size_kb, offset, create_date)
         VALUES " ;

		//Get rid of last comma and replace with  semicolon
		$sql = $sql_insert . substr_replace($sql_values, ";",-1);

		//If inserts return false
		$sql_rtn = $this->query($sql);
		if (false=== $sql_rtn ||  $sql_rtn==0 ) return false;
		else return true;

	}


	/**
	 * Get count of active and queued jobs for all types passed into job_type array
	 *
	 * @param $job_types Job Types to count
	 *
	 * @return int
	 */
	public function get_queued_active_job_count( $job_types ) {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$job_types_list = self::get_delimited_list($job_types);

		$sql = $wpdb->prepare(
			"SELECT count(*) as job_count
				FROM  $wpdb->wpbackitup_job_control
				WHERE
				  job_type  IN ( {$job_types_list})
				  AND job_status IN (%s, %s)
		    ",WPBackItUp_Job::ACTIVE,WPBackItUp_Job::QUEUED);

		$result = $this->get_row($sql);

		return (int) $result->job_count;

	}


	/**
	 * Get jobs by status
	 *
	 * @param $job_type
	 * @param $job_status_array
	 * @param int $limit
	 *
	 * @return mixed
	 */
	public function get_jobs_by_status( $job_type,$job_status_array, $limit=100 ) {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$job_status_list = self::get_delimited_list($job_status_array);

		$sql = $wpdb->prepare(
			"SELECT *
				FROM  $wpdb->wpbackitup_job_control
				WHERE
				  job_type = %s
				  AND job_status IN ( {$job_status_list})
				ORDER BY job_id DESC
				LIMIT %d
		    ",$job_type, $limit);

		$query_result=$this->get_rows($sql);
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Results:'.var_export($query_result,true));

		return $query_result;
	}

	/**
	 * Get job by job name
	 *
	 * @param $job_type
	 * @param $job_name
	 * @param $job_status_array
	 *
	 * @return mixed
	 */
	public function get_jobs_by_name( $job_type, $job_name,$job_status_array ) {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$job_status_list = self::get_delimited_list($job_status_array);

		$sql = $wpdb->prepare(
			"SELECT *
				FROM  $wpdb->wpbackitup_job_control
				WHERE
				  job_type = %s
				  AND job_name = %s
				  AND job_status IN ( {$job_status_list})
				ORDER BY job_id DESC
		    ",$job_type,$job_name);

		$query_result=$this->get_rows($sql);
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Results:'.var_export($query_result,true));

		return $query_result;
	}

	/**
	 * Create job
	 *
	 * @param $job_id Job Id
	 * @param $job_type Job Type
	 * @param $job_run_type Job Run Type
	 * @param $job_name Job Name
	 * @param $job_status Job Status
	 *
	 * @return bool true on success/ false on error
	 */
	public function create_job($job_id,$job_type,$job_run_type,$job_name,$job_status) {
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');
		global $wpdb;

		$sql = $wpdb->prepare(
			"INSERT  $wpdb->wpbackitup_job_control
				(job_id, job_type,job_run_type,job_name,job_status,create_date)
		        VALUES(%d,%s,%s,%s,%s,%s)"
			,$job_id,$job_type,$job_run_type,$job_name,$job_status,current_time('mysql'));

		$sql_rtn = $this->query($sql);
		if (false=== $sql_rtn ||  $sql_rtn==0 ) return false;
		else return true;
	}


	/**
	 * Fetch job by id
	 *
	 * @param $job_id
	 *
	 * @return mixed
	 */
	public function get_job_by_id( $job_id ) {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql = $wpdb->prepare(
			"SELECT *
			 FROM $wpdb->wpbackitup_job_control
		     WHERE
				  job_id=%d
		    ",$job_id);

		$query_result=$this->get_row($sql);
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Results:'.var_export($query_result,true));

		return $query_result;
	}


	/**
	 * Update Job status
	 *
	 * @param $job_id
	 * @param $job_status
	 *
	 * @return bool
	 */
	public function update_job_status( $job_id, $job_status ) {
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');
		global $wpdb;

		$sql = $wpdb->prepare(
			"UPDATE  $wpdb->wpbackitup_job_control
				SET job_status=%s,
					update_date = %s
			 WHERE  job_id=%d"
			,$job_status,current_time('mysql'),$job_id);


		$sql_rtn = $this->query($sql);
		if (false=== $sql_rtn) return false;
		else return true;

	}

	/**
	 * Update job run type
	 *
	 * @param $job_id
	 * @param $job_run_type
	 *
	 * @return bool
	 */
	public function update_job_run_type( $job_id, $job_run_type ) {
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');
		global $wpdb;

		$sql = $wpdb->prepare(
			"UPDATE  $wpdb->wpbackitup_job_control
				SET job_run_type=%s,
					update_date = %s
			 WHERE  job_id=%d"
			,$job_run_type,current_time('mysql'),$job_id);

		$sql_rtn = $this->query($sql);
		if (false=== $sql_rtn) return false;
		else return true;

	}

	/**
	 * Update the job_meta value
	 *
	 * @param $job_id
	 * @param $job_meta
	 *
	 * @return bool
	 */
	public function update_job_meta( $job_id, $job_meta ) {
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');
		global $wpdb;

		$sql = $wpdb->prepare(
			"UPDATE  $wpdb->wpbackitup_job_control
				SET job_meta=%s,
					update_date = %s
			 WHERE  job_id=%d
			 ",
				maybe_serialize($job_meta),
				current_time('mysql'),
				$job_id);

		$sql_rtn = $this->query($sql);
		if (false=== $sql_rtn) return false;
		else return true;

	}

	/**
	 *  Create Job Task
	 *
	 * @param $job_id  Job Id
	 * @param $task_name Name of task
	 *
	 * @return bool|int ID on success/ false on error
	 */
	public function create_task( $job_id, $task_name) {
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');
		global $wpdb;

		$sql = $wpdb->prepare(
			"INSERT  $wpdb->wpbackitup_job_task
				(job_id,task_name,task_status, create_date)
		        VALUES(%d,%s,%s,%s)"
			,$job_id,$task_name,WPBackItUp_Job::QUEUED,current_time('mysql'));

		$sql_rtn = $this->query($sql);
		if (false=== $sql_rtn ||  $sql_rtn==0 ) return false;
		else return  $wpdb->insert_id;

	}

	/**
	 * Get all tasks for job by status
	 *
	 * @param $job_id Job Id
	 * @param $status_array Array of statuses to be included
	 *
	 * @return mixed
	 */
	public function get_job_tasks( $job_id, $status_array ) {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$status_list = self::get_delimited_list($status_array);

		$sql = $wpdb->prepare(
			"SELECT *
				FROM  $wpdb->wpbackitup_job_task
				WHERE
				  job_id=%d
				  AND task_status IN ( {$status_list})
				ORDER BY task_id
		    ",$job_id);

		$query_result=$this->get_rows($sql);
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Results:'.var_export($query_result,true));

		return $query_result;


	}


	/**
	 *  Allocate task
	 *
	 * @param $task_id
	 *
	 * @return bool true on allocated, false on not allocated,
	 */
	public function allocate_task( $task_id ) {
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');
		global $wpdb;

		$allocation_id = current_time('timestamp');

		$sql = $wpdb->prepare(
			"UPDATE  $wpdb->wpbackitup_job_task
				SET allocation_id=%d,
					update_date = %s
			 WHERE  task_id=%d"
			,$allocation_id,current_time('mysql'),$task_id);

		$sql_rtn = $this->query($sql);
		if (false=== $sql_rtn) return false;

		//make sure it was allocated
		$sql = $wpdb->prepare(
			"SELECT *
				FROM  $wpdb->wpbackitup_job_task
				WHERE
				  task_id=%d
				  AND allocation_id=%d
		    ",$task_id, $allocation_id);

		$query_result=$this->get_row($sql);
		if (empty($query_result)) return false;
		else return true;

	}

	/**
	 *  Get task by task id
	 * @param $task_id
	 *
	 * @return mixed
	 */
	public function get_task_by_id( $task_id ) {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql = $wpdb->prepare(
			"SELECT *
				FROM  $wpdb->wpbackitup_job_task
				WHERE
				  task_id=%d
		    ",$task_id);

		$query_result=$this->get_row($sql);
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Results:'.var_export($query_result,true));

		return $query_result;
	}


	/**
	 * Update task with property values
	 *
	 * @param $task
	 *
	 * @return bool true on success, false on failure
	 */
	public function update_task( $task ) {
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');
		global $wpdb;

		$sql = $wpdb->prepare(
			"UPDATE  $wpdb->wpbackitup_job_task
				SET
					task_meta=%s,
					retry_count=%d,
					task_start = %s,
					task_end = %s,
					update_date =%s,
					task_status=%s,
					error=%d
			 WHERE  task_id=%d
			",
			maybe_serialize($task->getTaskMeta()),
			$task->getRetryCount(),
			$task->getTaskStart(),
			$task->getTaskEnd(),
			current_time('mysql'),
			$task->getStatus(),
			$task->getError(),
			$task->getTaskId()
		);

		$sql_rtn = $this->query($sql);
		if (false=== $sql_rtn) return false;
		else return true;
	}


	/**
	 * Fetch a distinct list of job_ids from the job_item table
	 *
	 * @return mixed
	 */
	public function get_job_item_job_list(){
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');
		$sql_select = "SELECT DISTINCT job_id FROM $wpdb->wpbackitup_job_item";
		return $this->get_rows($sql_select);
	}

	/**
	 *
	 * Get all open items (status 0 or -1) and mark them with batch id
	 *  -  order by ITEM ID is extremely important because we will update partial batches by LIMIT
	 *
	 * @param $batch_id
	 * @param $batch_size
	 * @param $job_id
	 * @param $group_id
	 *
	 * @return mixed
	 */
	function get_batch_open_items($batch_id,$batch_size,$job_id,$group_id){
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$item_status_list = self::get_delimited_list(array(WPBackItUp_Job_Item::OPEN,WPBackItUp_Job_Item::QUEUED,WPBackItUp_Job_Item::ERROR));

		$sql_update = $wpdb->prepare(
			"UPDATE  $wpdb->wpbackitup_job_item
				set batch_id=%d
				 ,item_status=%s
				 ,retry_count=retry_count + 1
		         ,update_date=%s
		     WHERE
		     	  record_type=%s
				  && job_id=%d
				  && group_id=%s
				  && retry_count <= 3
				  && item_status IN ( {$item_status_list})
			  ORDER BY item_id
			  LIMIT %d
		    ",$batch_id,WPBackItUp_Job_Item::QUEUED,current_time('mysql'),WPBackItUp_Job_Item::JOB_ITEM_RECORD,$job_id,$group_id,$batch_size);

		//If no updates return false else # updated
		$sql_rtn = $this->query($sql_update);
		if (false=== $sql_rtn) return $sql_rtn;

		$sql_select = $wpdb->prepare(
			"SELECT * FROM $wpdb->wpbackitup_job_item
			          WHERE
						  record_type=%s
			              && batch_id=%d
					  ORDER BY item_id
					  ",WPBackItUp_Job_Item::JOB_ITEM_RECORD,$batch_id);

		return $this->get_rows($sql_select);
	}


	/**
	 * Get all completed items by job, group and batch ID
	 *
	 * @param $job_id
	 * @param $group_id
	 * @param $batch_id
	 *
	 * @return mixed
	 */
	function get_completed_items_by_batch_id($job_id,$group_id,$batch_id){
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql_select = $wpdb->prepare(
			"SELECT * FROM $wpdb->wpbackitup_job_item
			          WHERE
		              	record_type=%s
			          	&& job_id=%d
			          	&& group_id=%s
						&& item_status=%s
						&& batch_id=%d
					  ORDER BY item_id
					  ",WPBackItUp_Job_Item::JOB_ITEM_RECORD,$job_id,$group_id,WPBackItUp_Job_Item::COMPLETE,$batch_id);

		return $this->get_rows($sql_select);
	}

	/**
	 * Get array of batch ids for a job and group
	 * Group example; Plugin, Theme, Upload
	 *
	 * @param $job_id
	 * @param $group_id
	 *
	 * @return mixed
	 */
	function get_item_batch_ids($job_id,$group_id) {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql_select = $wpdb->prepare(
			"SELECT DISTINCT batch_id FROM $wpdb->wpbackitup_job_item
			          WHERE
		              	record_type=%s
			          	&& job_id=%d
			          	&& group_id=%s
						&& item_status=%s
						&& batch_id != ''
					  ORDER BY batch_id
					  ",WPBackItUp_Job_Item::JOB_ITEM_RECORD,$job_id,$group_id,WPBackItUp_Job_Item::COMPLETE);

		// get_col Returns an empty array if no result is found.
		return $this->get_col($sql_select);

	}

	/**
	 * Fetch item by id
	 *
	 * @param $item_id
	 *
	 * @return mixed
	 */
	public function get_item_by_id( $item_id ) {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql = $wpdb->prepare(
			"SELECT *
			 FROM $wpdb->wpbackitup_job_item
		     WHERE
				  item_id=%d
		    ",$item_id);

		$query_result=$this->get_row($sql);
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Results:'.var_export($query_result,true));

		return $query_result;
	}


	/**
	 * Delete job by job id
	 *
	 * @param $job_id
	 *
	 * @return bool|mixed
	 */
	public function delete_job_by_id( $job_id ) {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql_update = $wpdb->prepare(
			"DELETE FROM $wpdb->wpbackitup_job_control
		     WHERE
				job_id=%d
		    ",$job_id);

		//If no deletes return false else # updated
		$sql_rtn = $this->query($sql_update);
		if (false=== $sql_rtn ||  $sql_rtn==0 ) return false;
		else return $sql_rtn;


	}


	/**
	 * Delete all tasks for a job
	 *
	 * @param $job_id
	 *
	 * @param int $limit limit the purge to this number
	 *
	 * @return bool|mixed
	 */
	public function delete_job_tasks( $job_id,$limit=9999999 ) {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql = $wpdb->prepare(
			"DELETE FROM $wpdb->wpbackitup_job_task
		     WHERE
				job_id=%d
			LIMIT %d
		    ",$job_id,$limit);

		//If no deletes return false else # updated
		$sql_rtn = $this->query($sql);
		if (false=== $sql_rtn ||  $sql_rtn==0 ) return false;
		else return $sql_rtn;

	}

	/**
	 *
	 * delete all job records by job id
	 *
	 * @param $job_id
	 *
	 * @param $limit limit the purge to this number
	 *
	 * @return mixed - return false on none or error, count on success
	 */
	function delete_job_items($job_id, $limit=9999999){
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql = $wpdb->prepare(
			"DELETE FROM $wpdb->wpbackitup_job_item
		     WHERE
				job_id=%d
			LIMIT %d
		    ",$job_id,$limit);

		//If no deletes return false else # updated
		$sql_rtn = $this->query($sql);
		if (false=== $sql_rtn ||  $sql_rtn==0 ) return false;
		else return $sql_rtn;

	}

	/**
	 * Get count of open items with retry < 3
	 *
	 * @param $job_id
	 * @param $group_id
	 *
	 * @return mixed
	 */
	function get_open_item_count($job_id,$group_id){
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$item_status_list = self::get_delimited_list(array(WPBackItUp_Job_Item::OPEN,WPBackItUp_Job_Item::QUEUED,WPBackItUp_Job_Item::ERROR));

		$sql = $wpdb->prepare(
			"SELECT count(*) as item_count FROM $wpdb->wpbackitup_job_item
		     WHERE
		     	  record_type=%s
				  && job_id=%d
				  && group_id=%s
				  && retry_count <= 3
				  && item_status IN ( {$item_status_list})
		    ",WPBackItUp_Job_Item::JOB_ITEM_RECORD,$job_id,$group_id);

		$row=$this->get_row($sql);
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Results:'.var_export($row,true));

		return $row->item_count;
	}

	/**
	 * Get count of items not marked completed
	 * - these are assumed in error
	 *
	 * @param $job_id
	 * @param $group_id
	 *
	 * @return mixed
	 */
	function get_error_item_count($job_id,$group_id){
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql = $wpdb->prepare(
			"SELECT count(*) as item_count FROM $wpdb->wpbackitup_job_item
		     WHERE
		     	  record_type=%s
				  && job_id=%d
				  && group_id=%s
				  && (item_status!=%s)
		    ",WPBackItUp_Job_Item::JOB_ITEM_RECORD,$job_id,$group_id,WPBackItUp_Job_Item::COMPLETE);

		$row=$this->get_row($sql);
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Results:'.var_export($row,true));

		return $row->item_count;
	}

	/**
	 * Set Job batch to success
	 *
	 * @param $job_id
	 * @param $batch_id
	 * @param $file_count - Number of files added
	 *
	 * @return bool
	 */
	function update_item_batch_complete($job_id,$batch_id,$file_count){
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql = $wpdb->prepare(
			"UPDATE  $wpdb->wpbackitup_job_item
                set item_status=%s
                ,update_date=%s
                where
                job_id=%d
                && batch_id=%d
            ORDER BY item_id
            LIMIT %d
		    ",WPBackItUp_Job_Item::COMPLETE,current_time('mysql'),$job_id,$batch_id, $file_count);

		$sql_rtn = $this->query($sql);
		if (false=== $sql_rtn) return false;
		else return true;
	}

	/**
	 * Update item status
	 *
	 * @param $item_id
	 * @param $item_status
	 *
	 * @return bool
	 */
	public function update_item_status( $item_id, $item_status ) {
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');
		global $wpdb;

		$sql = $wpdb->prepare(
			"UPDATE  $wpdb->wpbackitup_job_item
				SET item_status=%s,
					update_date = %s
			 WHERE  item_id=%d"
			,$item_status,current_time('mysql'),$item_id);

		$sql_rtn = $this->query($sql);
		if (false=== $sql_rtn) return false;
		else return true;

	}


	/**
	 *  Update job start time
	 *
	 * @param $job_id
	 * @param $job_start_time
	 *
	 * @return bool
	 */
	public function update_job_start_time( $job_id, $job_start_time ) {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql = $wpdb->prepare(
			"UPDATE  $wpdb->wpbackitup_job_control
                set job_start=%s
                ,update_date=%s
                where
                job_id=%d
		    ",$job_start_time,current_time('mysql'),$job_id);

		$sql_rtn = $this->query($sql);
		if (false=== $sql_rtn) return false;
		else return true;

	}

	/**
	 * Update job end time
	 * @param $job_id
	 * @param $job_end_time
	 *
	 * @return bool
	 */
	public function update_job_end_time( $job_id, $job_end_time ) {
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');
		global $wpdb;

		$sql = $wpdb->prepare(
			"UPDATE  $wpdb->wpbackitup_job_control
				SET job_end=%s,
					update_date = %s
			 WHERE  job_id=%d"
			,$job_end_time,current_time('mysql'),$job_id);

		$sql_rtn = $this->query($sql);
		if (false=== $sql_rtn) return false;
		else return true;

	}


	/**
	 * Fetch user by login
	 *
	 * @param $user_login
	 *
	 * @return mixed
	 */
	public function get_user_by_login( $user_login ) {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql = $wpdb->prepare(
			"SELECT *
			 FROM $wpdb->users
		     WHERE
				  user_login=%s
		    ",$user_login);

		$query_result=$this->get_row($sql);
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Results:'.var_export($query_result,true));

		return $query_result;
	}


	/**
	 * Find duplicate profiles
	 *
	 * @param $user_id
	 * @param $user_login
	 *
	 * @return mixed
	 */
	public function get_duplicate_users( $user_id, $user_login ) {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql = $wpdb->prepare(
			"SELECT *
			 FROM $wpdb->users
		     WHERE
		     	  ID != %d AND
				  user_login=%s
		    ",$user_id,$user_login);

		$query_result=$this->get_rows($sql);
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Results:'.var_export($query_result,true));

		return $query_result;
	}

	/**
	 * Create or Update user by login
	 *  -  Lookup the user by their login and then either update or create
	 *
	 * @param $db_user
	 *
	 * @return bool
	 */
	public function update_create_user_by_login( $db_user ) {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		//fetch user
		$user = $this->get_user_by_login($db_user->user_login );

		//If NOT found then create a new user
		if ( empty($user) ) {
			$sql = $wpdb->prepare(
				"INSERT INTO $wpdb->users
				(
					 user_login,
					 user_pass,
					 user_nicename,
					 user_email,
					 user_url,
					 user_registered,
					 user_activation_key,
					 user_status,
					 display_name
		 		)
		 		VALUES
		 		(%s,%s,%s,%s,%s,%s,%s,%d,%s)"
				,
				$db_user->user_login,
				$db_user->user_pass,
				$db_user->user_nicename,
				$db_user->user_email,
				$db_user->user_url,
				$db_user->user_registered,
				$db_user->user_activation_key,
				$db_user->user_status,
				$db_user->display_name
			);

		} else {

			$sql = $wpdb->prepare(
				"UPDATE $wpdb->users
				SET
					 user_pass = %s,
					 user_nicename = %s,
					 user_email = %s,
					 user_url = %s,
					 user_registered = %s,
					 user_activation_key = %s,
					 user_status = %d,
					 display_name = %s
			 WHERE
			 	user_login=%s"
				,
				$db_user->user_pass,
				$db_user->user_nicename,
				$db_user->user_email,
				$db_user->user_url,
				$db_user->user_registered,
				$db_user->user_activation_key,
				$db_user->user_status,
				$db_user->display_name,
				$db_user->user_login
			);


		}

		$sql_rtn = $this->query($sql);
		if (false=== $sql_rtn ) return false;
		else return true;

	}

	/**
	 * Create  user
	 * @param $db_user
	 *
	 * @return bool
	 */
	public function create_user( $db_user ) {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql = $wpdb->prepare(
			"INSERT INTO $wpdb->users
				(
					 user_login,
					 user_pass,
					 user_nicename,
					 user_email,
					 user_url,
					 user_registered,
					 user_activation_key,
					 user_status,
					 display_name
		 		)
		 		VALUES
		 		(%s,%s,%s,%s,%s,%s,%s,%d,%s)"
			,
			$db_user->user_login,
			$db_user->user_pass,
			$db_user->user_nicename,
			$db_user->user_email,
			$db_user->user_url,
			$db_user->user_registered,
			$db_user->user_activation_key,
			$db_user->user_status,
			$db_user->display_name
		);

		$sql_rtn = $this->query($sql);
		if (false=== $sql_rtn ||  $sql_rtn==0 ) return false;
		else return true;

	}

	/**
	 * Delete all users except user id passed
	 *
	 * @param $id
	 * @param int $limit
	 *
	 * @return mixed false on error, count of records deleted
	 */
	public function delete_users_except( $id, $limit=5000 ) {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql = $wpdb->prepare(
			"DELETE FROM $wpdb->users
		     WHERE
				ID != %d
			 LIMIT %d
		    ",$id,$limit);

		//If no deletes return false else # updated
		$sql_rtn = $this->query($sql);
		return $sql_rtn;

	}


	/**
	 * Get count of users in user table
	 *
	 * @return int
	 */
	public function get_user_count() {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql = "SELECT count(*) as user_count FROM  $wpdb->users";
		$result = $this->get_row($sql);

		return (int) $result->user_count;
	}

	/**
	 * Get count of users in usermeta table
	 *
	 * @return int
	 */
	public function get_usermeta_count() {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql = "SELECT count(distinct user_id) as user_count FROM  $wpdb->usermeta";
		$result = $this->get_row($sql);

		return (int) $result->user_count;
	}


	/**
	 * Delete all user meta except id
	 * @param $id
	 * @param int $limit
	 *
	 * @return mixed
	 */
	public function delete_usermeta_except( $id, $limit=5000 ) {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql = $wpdb->prepare(
			"DELETE FROM $wpdb->usermeta
		     WHERE
				user_id != %d
			 LIMIT %d
		    ",$id,$limit);

		//If error return false else # updated 0 for none to update
		$sql_rtn = $this->query($sql);
		return $sql_rtn;
	}


	public function get_options_wpbackitup() {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql ="SELECT * FROM $wpdb->options WHERE option_name LIKE 'wp-backitup%' ";

		$query_result=$this->get_rows($sql);
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Results:'.var_export($query_result,true));

		return $query_result;

	}


	public function update_create_option($option_name,$option_value,$autoload) {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		//does option already exist
		$option = $this->get_option_by_name($option_name);

		if (empty($option)){
			//Create Meta
			$sql = $wpdb->prepare(
				"INSERT INTO $wpdb->options
				(
					 option_name,
					 option_value,
					 autoload
		        )
		        VALUES
		        (%s,%s,%s)
			    ",
				$option_name,
				$option_value,
				$autoload
			);

		} else {
			//Update Meta
			$sql = $wpdb->prepare(
				"UPDATE $wpdb->options
					SET
					 	 option_value=%s,
					     autoload = %s
				 WHERE
				        option_name=%s
				 "
				,
				$option_value,
				$autoload,
				$option_name
			);

		}

		$sql_rtn = $this->query($sql);
		if (false=== $sql_rtn ) return false;
		else return true;

	}

	public function get_option_by_name( $option_name ) {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql = $wpdb->prepare(
			"SELECT *
			 FROM $wpdb->options
		     WHERE
				  option_name=%s
		    ",$option_name);

		$query_result=$this->get_rows($sql);
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Results:'.var_export($query_result,true));

		return $query_result;
	}


	/**
	 * Fetch user meta by id
	 *
	 * @param $user_id
	 *
	 * @return mixed
	 */
	public function get_usermeta_by_id( $user_id ) {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql = $wpdb->prepare(
			"SELECT *
			 FROM $wpdb->usermeta
		     WHERE
				  user_id=%d
		    ",$user_id);

		$query_result=$this->get_rows($sql);
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Results:'.var_export($query_result,true));

		return $query_result;
	}

	public function get_usermeta_by_id_metakey( $user_id,$meta_key ) {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql = $wpdb->prepare(
			"SELECT *
			 FROM $wpdb->usermeta
		     WHERE
				  user_id=%d AND
				  meta_key=%s
		    ",$user_id,$meta_key);

		$query_result=$this->get_row($sql);
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Results:'.var_export($query_result,true));

		return $query_result;
	}

	/**
	 * Update or create meta for this user
	 *
	 * @param $user_id
	 * @param $meta_key
	 * @param $meta_value
	 *
	 * @return bool
	 */
	public function update_create_usermeta($user_id,$meta_key,$meta_value) {
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		//does usermeta already exist
		$user_meta = $this->get_usermeta_by_id_metakey($user_id,$meta_key);

		if (empty($user_meta)){
			//Create Meta
			$sql = $wpdb->prepare(
				"INSERT INTO $wpdb->usermeta
				(
					 user_id,
					 meta_key,
					 meta_value
		        )
		        VALUES
		        (%d,%s,%s)
			    ",
				$user_id,
				$meta_key,
				$meta_value
			);

		} else {
			//Update Meta
			$sql = $wpdb->prepare(
				"UPDATE $wpdb->usermeta
					SET
						 meta_value = %s
				 WHERE
				        user_id=%d AND
				        meta_key=%s
				 "
				,
					$meta_value,
					$user_id,
					$meta_key
			);

		}

		$sql_rtn = $this->query($sql);
		if (false=== $sql_rtn ) return false;
		else return true;

	}
	/**
	 * Get array of all tables
	 * @return array
	 */
	function get_tables(){
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$sql = "SHOW TABLES;";
        $table_list = $wpdb->get_results($sql, ARRAY_N);
        $all_tables_name = array();
        foreach ($table_list as $key => $value) {
            $all_tables_name[$key] = $value[0];
        }

        return $all_tables_name;
	}

	/**
	 * Get array of wordpress tables
	 * @return array
	 */
	function get_wp_tables(){
		global $wpdb;
        return array_values($wpdb->tables());
	}

	public function drop_table($table_name) {
		global $wpdb;
		return $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . $table_name );
	}

	/**
	 *
	 *   PRIVATES
	 *
	 */


	/**
	 * Query (Update/Insert Sql statements)
	 *
	 * @param $sql
	 * @return mixed
	 *
	 */
	private function query($sql){
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,$sql);
		$wpdb_result = $wpdb->query($sql);
		//$last_query = $wpdb->last_query;
		$last_error = $wpdb->last_error;

		//WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Last Query:' .var_export( $last_query,true ) );
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Query Result: ' .($wpdb_result=== FALSE?'Query Error': $wpdb_result));

		if ($wpdb_result === FALSE && !empty($last_error)) {
			WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'Last Error:' .var_export( $last_error,true ) );
		}

		return $wpdb_result;
	}

	/**
	 * Get single row
	 *
	 * @param $sql
	 * @return mixed object|null returned on query
	 */
	private function get_row($sql){
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,$sql);
		$wpdb_result = $wpdb->get_row($sql);
		$last_query = $wpdb->last_query;
		$last_error = $wpdb->last_error;

		//WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Last Query:' .var_export( $last_query,true ));
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Query Result: ' .($wpdb_result==null?'NULL': $wpdb->num_rows));

		if (null == $wpdb_result && !empty($last_error)) {
			WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'Last Error:' .var_export( $last_query,true ));
		}

		return $wpdb_result;

	}

	/**
 * Get multiple rows
 *
 * @param $sql
 * @return mixed
 */
	private function get_rows($sql,$output=OBJECT){
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,$sql);
		$wpdb_result = $wpdb->get_results($sql,$output);
		//$last_query = $wpdb->last_query;
		$last_error = $wpdb->last_error;

		//WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Last Query:' .var_export( $last_query,true ));
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Query Result: ' .($wpdb_result==null?'NULL': $wpdb->num_rows));

		if (null == $wpdb_result && ! empty($last_error)) {
			WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'Last Error:' .var_export( $last_error,true ));
		}

		return $wpdb_result;

	}

	/**
	 * Retrieve rows for one column from the database.
	 *
	 * @param $sql
	 * @return mixed
	 */
	private function get_col($sql,$column_index=0){
		global $wpdb;
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,$sql);
		$wpdb_result = $wpdb->get_col($sql,$column_index);
		$last_error = $wpdb->last_error;

		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Query Result: ' .($wpdb_result==null?'NULL': $wpdb->num_rows));

		if (null == $wpdb_result && ! empty($last_error)) {
			WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'Last Error:' .var_export( $last_error,true ));
		}

		return $wpdb_result;

	}

	/**
	 * Convert an array to a delimited list with quotes around each values
	 * @param $array_list
	 *
	 * @return string
	 */
	private function get_delimited_list($array_list){

		if (! is_array($array_list))
			$array_list = array($array_list);

		$delimted_list = "'" .implode("', '", $array_list) . "'";
		return $delimted_list;
	}


}