<?php if (!defined ('ABSPATH')) die('No direct access allowed');

// Checking safe mode is on/off and set time limit
if( ini_get('safe_mode') ){
   @ini_set('max_execution_time', 0);
}else{
   @set_time_limit(0);
}

/**
 * WP BackItUp  - SQL Class
 *
 * @package WP BackItUp
 * @author  Chris Simmons <chris.simmons@wpbackitup.com>
 * @link    http://www.wpbackitup.com
 *
 */

class WPBackItUp_SQL {

	private $log_name;
    private $mysqli; //use getter

	function __construct($log_name=null) {
		try {
			$this->log_name = 'debug_sql';//default log name
			if (is_object($log_name)){
				//This is for the old logger
				$this->log_name = $log_name->getLogFileName();
			} else{
				if (is_string($log_name) && isset($log_name)){
					$this->log_name = $log_name;
				}
			}

			$this->mysqli = $this->get_mysqli();

		} catch(Exception $e) {
			error_log($e);
			WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'Constructor Exception: ' .$e);
		}
   }

   function __destruct() {
       // Close the connection
	   if (is_object($this->mysqli)){
        $this->mysqli->close() ;
	   }
   }

   public function mysqldump_export_OLD($sql_file_path,$item_list,$with_mysqlpath=false) {
	    WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin.');

        $db_name = DB_NAME;
        $db_user = DB_USER;
        $db_pass = DB_PASSWORD;
        $db_host = $this->get_hostonly(DB_HOST);
        $db_port = $this->get_portonly(DB_HOST);

		//This is to ensure that exec() is enabled on the server
	    if($this->exec_enabled()) {
			try {
                $mysql_path='';
                if ($with_mysqlpath)  {
                    $mysql_path = $this->get_mysql_path();
                    if ($mysql_path===false) return false;
                }

                $process = $mysql_path .'mysqldump';
	            $command = $process
	             . ' --host=' . $db_host;

				//Check for port
	             if (false!==$db_port){
	                $command .=' --port=' . $db_port;
	             }

				//get list of tables from inventory
				foreach($item_list as $item){
					$table = $item->item;
					$tables_list[] = $table;
					WPBackItUp_LoggerV2::log($this->log_name,$table);
				}

				if (is_array($tables_list)){
					$tables = implode( " ", $tables_list);
				}else{
					WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'No tables found.');
					return false;
				}

				$command .=
	               ' --user=' . $db_user
	             . ' --password=' . $db_pass
	             . ' ' . $db_name
	             . ' ' . $tables
	             . ' >> "' . $sql_file_path .'"';

                if (WPBACKITUP__DEBUG) {
					$masked_command = str_replace(array($db_user,$db_pass),'XXXXXX',$command);
	                WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Execute command:' . $masked_command);
                }

                exec($command,$output,$rtn_var);
				WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Execute output:');
				WPBackItUp_LoggerV2::log($this->log_name,$output);
				WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Return Value:' .$rtn_var);

	            //0 is success
	            if ($rtn_var>0){
		            WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'EXPORT FAILED return Value:' .$rtn_var);
	                return false;
	            }

                //Did the export work
                clearstatcache();
                if (!file_exists($sql_file_path) || filesize($sql_file_path)<=0) {
	                WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'EXPORT FAILED: Dump was empty or missing.');
                    return false;
                }
            } catch(Exception $e) {
				WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'EXPORT FAILED Exception: ' .$e);
                return false;
            }
        }
        else
        {
	        WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'EXPORT FAILED Exec() disabled.');
            return false;
        }

	    WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'SQL Dump SUCCESS.');
        return true;
	}




	/**
	 *
	 * Fetch all tables and number of rows in database
	 *
	 * @return array|bool
	 */
	public function get_table_rows() {
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin');

		$mysqli = $this->get_mysqli();
		if (false === $mysqli) {
			WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'No SQL Connection');
			return false;
		}
		$sql = "SELECT TABLE_NAME,TABLE_ROWS,
 				  round(((data_length + index_length) / 1024), 2)
				  FROM INFORMATION_SCHEMA.TABLES
                  WHERE TABLE_SCHEMA = '" . DB_NAME . "'
                  ORDER BY TABLE_ROWS DESC;";
		$result = $mysqli->query($sql);

		// Cycle through "$result" and put content into an array
		$tables = array();
		while ($row = $result->fetch_row()) {
			$tables[] = array("table_name" => $row[0], 'table_rows' => $row[1], 'table_size_kb'=>$row[2]) ;
		}

		mysqli_free_result($result);
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'End');
		return $tables;
	}

//    public function manual_export_OLD($sql_file_path,$item_list,$total_item_count) {
//	    WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Export Database');
//
//	    try{
//			$mysqli = $this->get_mysqli();
//			if (false===$mysqli) {
//				WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'No SQL Connection');
//			    return false;
//			}
//		    $mysqli->set_charset('utf8');
//
//		    //if file doesnt exists then write header needed
//		    $write_header = ! file_exists($sql_file_path);
//
//		    //open the SQL file - append
//		    $handle = fopen($sql_file_path,'a');
//		    if (false===$handle) {
//			    WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'File could not be opened.');
//			    return false;
//		    }
//
//			// Script Header Information
//			$return  = '';
//			$return .= "-- ------------------------------------------------------\n";
//			$return .= "-- ------------------------------------------------------\n";
//			$return .= "--\n";
//			$return .= "-- WP BackItUp Manual Database Backup \n";
//			$return .= "--\n";
//			$return .= '-- Created: ' . date("Y/m/d") . ' on ' . date("h:i") . "\n";
//			$return .= "--\n";
//			$return .= "-- Database : " . DB_NAME . "\n";
//			$return .= "--\n";
//			$return .= "-- ------------------------------------------------------\n";
//			$return .= "-- ------------------------------------------------------\n";
//			$return .= 'SET AUTOCOMMIT = 0 ;' ."\n" ;
//			$return .= 'SET FOREIGN_KEY_CHECKS=0 ;' ."\n" ;
//	        $return .= "\n";
//	        $return .= '/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;' ."\n" ;
//	        $return .= '/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;' ."\n" ;
//	        $return .= '/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;' ."\n" ;
//	        $return .= '/*!40101 SET NAMES utf8 */;' ."\n" ;
//
//		    //if SQL file doesnt exist then
//		    if (true===$write_header){
//		        fwrite($handle,$return); //Write to file
//		    }
//
//		    //get list of tables from inventory
//		    foreach($item_list as $item){
//			    $table = $item->item;
//			    $tables_list[] = $table;
//			    WPBackItUp_LoggerV2::log($this->log_name,$table);
//		    }
//
//		    if (!is_array($tables_list)){
//			    WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'No tables found.');
//			    return false;
//		    }
//
//			// Cycle through each  table
//		    WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Generate script for each table.');
//			foreach($tables_list as $table) {
//				WPBackItUp_LoggerV2::log($this->log_name,$table);
//
//				// Get content of each table
//				$result = $mysqli->query('SELECT * FROM '. $table) ;
//
//				// Get number of fields (columns) of each table
//				$num_fields = $mysqli->field_count  ;
//
//				// Add table information
//				$return  = "--\n" ;
//				$return .= '-- Table structure for table `' . $table . '`' . "\n" ;
//				$return .= "--\n" ;
//				$return .= 'DROP TABLE  IF EXISTS `'.$table.'`;' . "\n" ;
//
//				// Get the table-shema
//				$schema = $mysqli->query('SHOW CREATE TABLE '.$table) ;
//
//				// Extract table shema
//				$tableschema = $schema->fetch_row() ;
//				mysqli_free_result($schema);
//
//				// Append table-shema into code
//				$return.= $tableschema[1].";" . "\n\n" ;
//
//				fwrite($handle,$return); //Write to file
//
//				// Cycle through each table-row
//				while($rowdata = $result->fetch_row()) {
//
//					$return = 'INSERT INTO '.$table.' VALUES(';
//					for($j=0; $j<$num_fields; $j++){
//					        $rowdata[$j] = addslashes($rowdata[$j]);
//							$rowdata[$j] = str_replace("\n","\\n",$rowdata[$j]);
//
//							if (isset($rowdata[$j])) {
//								 $return.= '"'.$rowdata[$j].'"' ;
//							 } else {
//							    if (is_null($rowdata[$j])) {
//							        $return.= 'NULL';//Dont think this is working but not causing issues
//							    } else {
//							        $return.= '""';
//							    }
//							  }
//
//					        if ($j<($num_fields-1)) { $return.= ','; }
//					}
//					$return.= ");\n";
//
//					fwrite($handle,$return); //Write to file
//				}
//
//				$return= "\n\n" ;
//				fwrite($handle,$return); //Write to file
//
//				mysqli_free_result($result);
//			}
//
//		    //if total open items = number in this list then done and can write footer
//		    WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'End of Items:' .count($tables_list) . '-' .$total_item_count );
//		    if (count($tables_list) == $total_item_count){
//				$return .= 'SET FOREIGN_KEY_CHECKS = 1 ; '  . "\n" ;
//				$return .= 'COMMIT ; '  . "\n" ;
//				$return .= 'SET AUTOCOMMIT = 1 ; ' . "\n"  ;
//				fwrite($handle,$return);
//		    }
//
//		    //close the file
//			fclose($handle);
//
//			clearstatcache();
//
//			//Did the export work
//			if (!file_exists($sql_file_path) || filesize($sql_file_path)<=0) {
//				WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'Failure: SQL Export file was empty or didnt exist.');
//				return false;
//			}
//
//		    WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'SQL Backup File Created:'.$sql_file_path);
//		    return true;
//
//	    }catch(Exception $e) {
//		    WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'Exception: ' .$e);
//		    return false;
//	    }
//	}

//	public function manual_export_schema($sql_file_path,$item_list) {
//		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Export Database');
//
//		try{
//			$mysqli = $this->get_mysqli();
//			if (false===$mysqli) {
//				WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'No SQL Connection');
//				return false;
//			}
//			$mysqli->set_charset('utf8');
//
//			//open the SQL file - append
//			$handle = fopen($sql_file_path,'a');
//			if (false===$handle) {
//				WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'File could not be opened.');
//				return false;
//			}
//
//			// Script Header Information
//			$return  = '';
//			$return .= "-- ------------------------------------------------------\n";
//			$return .= "-- ------------------------------------------------------\n";
//			$return .= "--\n";
//			$return .= "-- WP BackItUp Manual Database Backup \n";
//			$return .= "--\n";
//			$return .= '-- Created: ' . date("Y/m/d") . ' on ' . date("h:i") . "\n";
//			$return .= "--\n";
//			$return .= "-- Database : " . DB_NAME . "\n";
//			$return .= "--\n";
//			$return .= "-- ------------------------------------------------------\n";
//			$return .= "-- ------------------------------------------------------\n";
//			$return .= 'SET AUTOCOMMIT = 0 ;' ."\n" ;
//			$return .= 'SET FOREIGN_KEY_CHECKS=0 ;' ."\n" ;
//			$return .= "\n";
//			$return .= '/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;' ."\n" ;
//			$return .= '/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;' ."\n" ;
//			$return .= '/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;' ."\n" ;
//			$return .= '/*!40101 SET NAMES utf8 */;' ."\n" ;
//
//
//			fwrite($handle,$return); //Write to file
//
//			//get list of tables from inventory
//			foreach($item_list as $item){
//				$table = $item->item;
//				$tables_list[] = $table;
//				WPBackItUp_LoggerV2::log($this->log_name,$table);
//			}
//
//			if (!is_array($tables_list)){
//				WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'No tables found.');
//				return false;
//			}
//
//			// Cycle through each  table
//			WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Generate script for each table.');
//			foreach($tables_list as $table) {
//				WPBackItUp_LoggerV2::log($this->log_name,$table);
//
//				// Add table information
//				$return  = "--\n" ;
//				$return .= '-- Table structure for table `' . $table . '`' . "\n" ;
//				$return .= "--\n" ;
//				$return .= 'DROP TABLE  IF EXISTS `'.$table.'`;' . "\n" ;
//
//				// Get the table-shema
//				$schema = $mysqli->query('SHOW CREATE TABLE '.$table) ;
//
//				// Extract table shema
//				$tableschema = $schema->fetch_row() ;
//				mysqli_free_result($schema);
//
//				// Append table-shema into code
//				$return.= $tableschema[1].";" . "\n\n" ;
//
//				fwrite($handle,$return); //Write to file
//
//				$return= "\n\n" ;
//				fwrite($handle,$return); //Write to file
//
//			}
//
//			$return .= 'SET FOREIGN_KEY_CHECKS = 1 ; '  . "\n" ;
//			$return .= 'COMMIT ; '  . "\n" ;
//			$return .= 'SET AUTOCOMMIT = 1 ; ' . "\n"  ;
//			fwrite($handle,$return);
//
//			//close the file
//			fclose($handle);
//
//			clearstatcache();
//
//			//Did the export work
//			if (!file_exists($sql_file_path) || filesize($sql_file_path)<=0) {
//				WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'Failure: SQL Export file was empty or didnt exist.');
//				return false;
//			}
//
//			WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'SQL Backup File Created:'.$sql_file_path);
//			return true;
//
//		}catch(Exception $e) {
//			WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'Exception: ' .$e);
//			return false;
//		}
//	}

	/**
	 * mysqldump database export
	 *
	 * @param $sql_data_file_name
	 * @param $table
	 * @param $offset
	 * @param $limit
	 * @param $create_table
	 * @param bool|false $with_mysqlpath
	 *
	 * @return bool
	 */
	public function mysqldump_export_data($sql_data_file_name,$table,$offset,$limit,$create_table,$with_mysqlpath=false) {
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Begin.');

		$db_name = DB_NAME;
		$db_user = DB_USER;
		$db_pass = DB_PASSWORD;
		$db_host = $this->get_hostonly(DB_HOST);
		$db_port = $this->get_portonly(DB_HOST);

		//This is to ensure that exec() is enabled on the server
		if($this->exec_enabled()) {
			try {
				$mysql_path='';
				if ($with_mysqlpath)  {
					$mysql_path = $this->get_mysql_path();
					if ($mysql_path===false) return false;
				}

				$process = $mysql_path .'mysqldump';
				$command = $process
				           . ' --host=' . $db_host;

				//Check for port
				if (false!==$db_port){
					$command .=' --port=' . $db_port;
				}

				$create_option =' --no-create-info';
				if ($create_table) {
					$create_option =' ';
				}

				$command .=
					' --where "1 LIMIT ' . $offset . ',' . $limit .'"'
					. $create_option
					. ' --insert-ignore'
					. ' --user=' . $db_user
					. ' --password=' . $db_pass
					. ' ' . $db_name
					. ' ' . $table
					. ' >> "' . $sql_data_file_name .'"';

				if (WPBACKITUP__DEBUG) {
					$masked_command = str_replace(array($db_user,$db_pass),'XXXXXX',$command);
					WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Execute command:' . $masked_command);
				}

				exec($command,$output,$rtn_var);
				WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Execute output:');
				WPBackItUp_LoggerV2::log($this->log_name,$output);
				WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Return Value:' .$rtn_var);

				//0 is success
				if ($rtn_var>0){
					WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'EXPORT FAILED return Value:' .$rtn_var);
					return false;
				}

				//Did the export work
				clearstatcache();
				if (!file_exists($sql_data_file_name) || filesize($sql_data_file_name)<=0) {
					WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'EXPORT FAILED: Dump was empty or missing.');
					return false;
				}
			} catch(Exception $e) {
				WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'EXPORT FAILED Exception: ' .$e);
				return false;
			}
		}
		else
		{
			WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'EXPORT FAILED Exec() disabled.');
			return false;
		}

		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'SQL Dump SUCCESS.');
		return true;
	}

	/**
	 * Export a batch of rows to a SQL file
	 *
	 * @param $sql_data_file_name SQL File Name
	 * @param $table Table to be exported
	 * @param $offset Offset to start at
	 * @param $limit Number of rows to export
	 * @param $create_table drop and create table
	 *
	 * @return bool True on success/ False on error
	 */
	public function wpbackitup_export_data($sql_data_file_name,$table,$offset,$limit,$create_table) {
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Export Database Table:'.$table);

		try{

			$mysqli = $this->get_mysqli();

			if (false===$mysqli) {
				WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'No SQL Connection');
				return false;
			}

			$mysqli->set_charset('utf8');

			//Fetch the sql result set
			$sql = sprintf('SELECT * FROM %s LIMIT %s,%s',$table,$offset,$limit);
			$sql_result = $mysqli->query($sql);
			if (false===$sql_result) {
				WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'Query Error:' .var_export( $sql_result,true ));
				return false;
			}

			//export the database even when no data because want to drop and add the table during the restore
			$num_rows = $sql_result->num_rows;
			WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'ROWS to export:' .$num_rows);


			// Get number of fields (columns) of each table
			$num_fields = $mysqli->field_count;

			//if file doesnt exists then write header needed
			$write_header = ! file_exists($sql_data_file_name);

			//open the SQL file - append
			$handle = fopen($sql_data_file_name,'a');
			if (false===$handle) {
				WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'File could not be opened.');
				return false;
			}

			//if SQL file doesnt exist then
			if (true===$write_header){
				// Script Header Information
				$output_buffer  = '';
				$output_buffer .= "-- ------------------------------------------------------\n";
				$output_buffer .= "-- ------------------------------------------------------\n";
				$output_buffer .= "--\n";
				$output_buffer .= "-- WP BackItUp Manual Database Export \n";
				$output_buffer .= "--\n";
				$output_buffer .= '-- Created: ' . date("Y/m/d") . ' on ' . date("h:i") . "\n";
				$output_buffer .= "--\n";
				$output_buffer .= "-- Database : " . DB_NAME . "\n";
				$output_buffer .= "--\n";
				$output_buffer .= "-- Table : " . $table . "\n";
				$output_buffer .= "--\n";
				$output_buffer .= "-- SQL    : " . $sql . "\n";
				$output_buffer .= "-- Offset : " . $offset . "\n";
				$output_buffer .= "-- Rows   : " . $num_rows . "\n";
				$output_buffer .= "-- ------------------------------------------------------\n";
				$output_buffer .= "-- ------------------------------------------------------\n";
				$output_buffer .= 'SET AUTOCOMMIT = 0 ;' ."\n" ;
				$output_buffer .= 'SET FOREIGN_KEY_CHECKS=0 ;' ."\n" ;
				$output_buffer .= "\n";
				$output_buffer .= '/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;' ."\n" ;
				$output_buffer .= '/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;' ."\n" ;
				$output_buffer .= '/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;' ."\n" ;
				$output_buffer .= '/*!40101 SET NAMES utf8 */;' ."\n" ;
				$output_buffer .= "\n";

				if ($create_table ) {
					$output_buffer .= "--\n";
					$output_buffer .= '-- Table structure for table `' . $table . '`' . "\n";
					$output_buffer .= "--\n";
					$output_buffer .= 'DROP TABLE  IF EXISTS `' . $table . '`;' . "\n";

					// Get the table-shema
					$schema = $mysqli->query( 'SHOW CREATE TABLE ' . $table );

					// Extract table shema
					$tableschema = $schema->fetch_row();
					mysqli_free_result( $schema );

					// Append table-shema into code
					$output_buffer .= $tableschema[1] . ";" . "\n\n";
				}

				$output_buffer.= "\n\n" ;
				fwrite($handle,$output_buffer); //Write to file
			}


			$output_buffer  = ''; //reset the buffer
			$output_buffer .= "--\n";
			$output_buffer .= '-- Data for table `' . $table . '`' . "\n";
			$output_buffer .= "--\n";
			fwrite($handle,$output_buffer); //Write to file

			// Cycle through each table-row
			while($rowdata = $sql_result->fetch_row()) {

				$output_buffer = 'INSERT IGNORE INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++){
					$rowdata[$j] = addslashes($rowdata[$j]);
					$rowdata[$j] = str_replace("\n","\\n",$rowdata[$j]);

					if (isset($rowdata[$j])) {
						$output_buffer.= '"'.$rowdata[$j].'"' ;
					} else {
						if (is_null($rowdata[$j])) {
							$output_buffer.= 'NULL';//Dont think this is working but not causing issues
						} else {
							$output_buffer.= '""';
						}
					}

					if ($j<($num_fields-1)) { $output_buffer.= ','; }
				}
				$output_buffer.= ");\n";

				fwrite($handle,$output_buffer); //Write to file
			}

			$output_buffer= "\n\n" ;
			fwrite($handle,$output_buffer); //Write to file

			mysqli_free_result($sql_result);

			//turn on foreign key checking amd commit
			$output_buffer .= 'SET FOREIGN_KEY_CHECKS = 1 ; '  . "\n" ;
			$output_buffer .= 'COMMIT ; '  . "\n" ;
			$output_buffer .= 'SET AUTOCOMMIT = 1 ; ' . "\n"  ;
			fwrite($handle,$output_buffer);

			//close the file
			fclose($handle);

			clearstatcache();

			//Did the export work
			if (!file_exists($sql_data_file_name) || filesize($sql_data_file_name)<=0) {
				WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'Failure: SQL Export file was empty or didnt exist.');
				return false;
			}

			WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'SQL Backup File Created:'.$sql_data_file_name);
			return true;//Success

		}catch(Exception $e) {
			WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'Exception: ' .$e);
			return false;
		}
	}
    public function run_sql_exec($sql_file,$with_mysqlpath=false) {
	    WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'SQL Execute:' .$sql_file);

        //Is the backup sql file empty
        if (!file_exists($sql_file) || filesize($sql_file)<=0) {
	        WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'Failure: SQL File was empty:' .$sql_file);
            return false;
        }

        //This is to ensure that exec() is enabled on the server
        if(exec('echo EXEC') != 'EXEC') {
	        WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'Failure: Exec() disabled.');
            return false;
        }

        try {

            $mysql_path='';
            if ($with_mysqlpath)  {
                $mysql_path = $this->get_mysql_path();
                if ($mysql_path===false) return false;
            }

            $db_name = DB_NAME;
            $db_user = DB_USER;
            $db_pass = DB_PASSWORD;
            $db_host = $this->get_hostonly(DB_HOST);
            $db_port = $this->get_portonly(DB_HOST);

            $process = $mysql_path .'mysql';
            $command = $process
                . ' --host=' . $db_host;

            //Check for port
            if (false!==$db_port){
                $command .=' --port=' . $db_port;
            }

            $command .=
                ' --user=' . $db_user
                . ' --password=' . $db_pass
                . ' --database=' . $db_name
                . ' --execute="SOURCE ' . $sql_file .'"';

            if (WPBACKITUP__DEBUG) {
	            $masked_command = str_replace(array($db_user,$db_pass),'XXXXXX',$command);
	            WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Execute command:' . $masked_command );
            }

            //$output = shell_exec($command);
            exec($command,$output,$rtn_var);
	        WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Execute output:');
	        WPBackItUp_LoggerV2::log($this->log_name,$output);
	        WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Return Value:' .$rtn_var);

            //0 is success
            if ($rtn_var!=0){
	            WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'An Error has occurred RTNVAL: ' .$rtn_var);
                return false;
            }

        }catch(Exception $e) {
	        WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'Exception: ' .$e);
            return false;
        }

        //Success
	    WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'SQL Executed successfully');
        return true;
    }

//    public function run_sql_manual_OLD($sql_file) {
//	    WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'SQL Execute:' .$sql_file);
//
//        //Is the backup sql file empty
//        if (!file_exists($sql_file) || filesize($sql_file)<=0) {
//	        WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'Failure: SQL File was empty:' .$sql_file);
//            return false;
//        }
//
//        $query = file_get_contents($sql_file);
//        if (empty($query)) return false;
//
//        try {
//
//            $mysqli = $this->get_mysqli();
//            $mysqli->set_charset('utf8');
//
//            if (false===$mysqli) {
//                return false;
//            }
//
//            $ctr=1;
//	        WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'0: here ');
//            if($mysqli->multi_query($query))
//            {
//                do {
//	                WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'1: ' .$ctr++);
//                    /* store first result set */
//                    if($resultSet = $mysqli->store_result())
//                    {
//	                    WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'2: ');
//
//                        while($row = $resultSet->fetch_row())
//                        {
//	                        WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'3: ');
//                        }
//                        $resultSet->free();
//                    }
//
//                    if (!$mysqli->more_results()) break; //All done
//
//                } while ($mysqli->next_result());
//
//	            WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'4: ');
//                $mysqli->close();
//            }
//
//        }catch(Exception $e) {
//	        WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'Exception: ' .$e);
//            return false;
//        }
//
//        //Success
//	    WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'SQL Executed successfully:' .$sql_file);
//        return true;
//    }

    function run_sql_manual($sql_file_path, $delimiter = ';')
    {
	    WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'SQL Execute:' .$sql_file_path);

	    // Assuming set time limit don't directly work for class file.
	    if(!ini_get('safe_mode')){
	    	@set_time_limit(0);
	    }

        //Is the backup sql file empty
        if (!file_exists($sql_file_path) || filesize($sql_file_path)<=0) {
	        WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'Failure: SQL File was empty:' .$sql_file_path);
            return false;
        }

        try {
            if (is_file($sql_file_path) === true)
            {
                $sql_handle = fopen($sql_file_path, 'r');

                if (is_resource($sql_handle) === true)
                {
                    $query = array();

                    $mysqli = $this->get_mysqli();
	                if (false === $mysqli) {
		                WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'No SQL Connection');
		                return false;
	                }

                    $mysqli->set_charset('utf8');
//                  $mysqli->autocommit(FALSE);
//                  $mysqli->begin_transaction();

                    $error_count=0;
                    $total_query=0;
                    while (feof($sql_handle) === false)
                    {
                        $query[] = fgets($sql_handle);

                        if (preg_match('~' . preg_quote($delimiter, '~') . '\s*$~iS', end($query)) === 1)
                        {
                            $query = trim(implode('', $query));

                            //Execute SQL statement
                            $total_query++;
                            if ($mysqli->query($query) === false) {
                                $error_count++;

	                            WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Total Queries Executed:' .$total_query);
	                            WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Query Errors:' .$error_count);
	                            WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,' SQL ERROR: ' . $query);

                                //$mysqli->rollback();
                                $mysqli->close();

                                fclose($sql_handle);
                                return false;
                            }
//                          else {
//                              WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'SUCCESS: ' . $query);
//                          }

                            while (ob_get_level() > 0)
                            {
                                ob_end_flush();
                            }

                            flush();
                        }

                        if (is_string($query) === true)
                        {
                            $query = array();
                        }
                    }

                    //$mysqli->commit();
                    $mysqli->close();

	                WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'SQL Executed successfully:' .$sql_file_path);
	                WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Total Queries Executed:' .$total_query);
	                WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Query Errors:' .$error_count);
                    return fclose($sql_handle);
                }
            }

        }catch(Exception $e) {
	        WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'Exception: ' .$e);
            return false;
        }

	    WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'SQL File could not be opened:' .$sql_file_path);
        return false;
    }

	/**
	 * Fetch active connection or create a new one
	 *
	 * @return bool|mysqli
	 */
	private function get_mysqli() {
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Get SQL connection to database.');
		$db_name = DB_NAME; 
        $db_user = DB_USER;
        $db_pass = DB_PASSWORD; 
        $db_host = $this->get_hostonly(DB_HOST);
        $db_port = $this->get_portonly(DB_HOST);

		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Host:' . $db_host);
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'Port:' . $db_port);

		//is the connection an object & responds to a ping
		if (is_object($this->mysqli)){
			if (true === $this->mysqli->ping()) {
				return $this->mysqli;
			}
		}

		//create a new connection
      	if (false===$db_port){
      		$mysqli = new mysqli($db_host , $db_user , $db_pass , $db_name);
      	}
        else {
			$mysqli = new mysqli($db_host , $db_user , $db_pass , $db_name,$db_port);
        }
		
		if ($mysqli->connect_errno) {
			WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'Cannot connect to database.' . $mysqli->connect_error);
		   	return false;
		}

		$this->mysqli = $mysqli;
		return $this->mysqli;
    }

	private function get_hostonly($db_host) {
		//Check for port
		$host_array = explode(':',$db_host);
		if (is_array($host_array)){
			return $host_array[0];
		}
		return $db_host;
	}

	private function get_portonly($db_host) {
		//Check for port
		$host_array = explode(':',$db_host);
		if ( is_array($host_array) && isset($host_array[1]) ) {
			$port = trim($host_array[1]);
            if(!empty($port))
                return $port;
		}

		return false;
	}

    //Get SQL scalar value
    public function get_sql_scalar($sql){
        $value='';

	    $mysqli = $this->get_mysqli();
	    if (false === $mysqli) {
		    WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'No SQL Connection');
		    return false;
	    }

        if ($result = mysqli_query($mysqli, $sql)) {
            while ($row = mysqli_fetch_row($result)) {
                $value = $row[0];
            }
            mysqli_free_result($result);
        }
        return $value;
    }

    //Run SQL command
    public function run_sql_command($sql){
	    $mysqli = $this->get_mysqli();
	    if (false === $mysqli) {
		    WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'No SQL Connection');
		    return false;
	    }

        if(!mysqli_query($mysqli, $sql) ) {
	        WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'Error:SQL Command Failed:' .$sql);
            return false;
        }

        return true;
    }

    //Get path to MQSQL Bin
    private function get_mysql_path(){
        $base_directory = $this->get_sql_scalar('select @@basedir');
        if (!empty($base_directory)){
            $base_directory.='/bin/';
	        WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'MySQL install path found:' .$base_directory);
            return $base_directory;
        }
	    WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__,'MySQL install path NOT found');
        return false;
    }

	// Checking exec is disabled or not
   function exec_enabled() {
	 	$disabled = explode(',', ini_get('disable_functions'));
 	    return !in_array('exec', $disabled);
	}


    //This function is untested
//    function get_database_size($dbname) {
//        mysqli_select_db($dbname);
//        $result = mysqli_query("SHOW TABLE STATUS");
//        $dbsize = 0;
//        while($row = mysqli_fetch_array($result)) {
//            $dbsize += $row["Data_length"] + $row["Index_length"];
//        }
//        return $dbsize;
//    }

}