<?php
namespace Ari_Adminer\Entities;

use Ari\Entities\Entity as Entity;
use Ari_Adminer\Utils\Db_Driver as DB_Driver;

class Connection extends Entity {
    public $connection_id;

    public $title = '';

    public $type = 'server';

    public $db_name;

    public $host = '';

    public $user = '';

    public $pass = '';

    public $created = '0000-00-00 00:00:00';
	
	public $modified = '0000-00-00 00:00:00';
	
	public $author_id = 0;

    function __construct( $db ) {
        parent::__construct( 'ariadminer_connections', 'connection_id', $db );
    }

    public function bind( $data, $ignore = array() ) {
        $result = parent::bind( $data, $ignore );

        switch ( $this->type ) {
            case DB_Driver::SQLITE:
                $this->host = '';
                $this->user = '';
                $this->pass = '';
                break;
        }

        return $result;
    }

    public function store( $force_insert = false ) {
		$now = current_time( 'mysql', 1 );
        if ( $this->is_new() ) {
            $this->created = $now;
			$this->author_id = get_current_user_id();
        } else {
        	$this->modified = $now;
        }

        return parent::store( $force_insert );
    }

    public function validate() {
        if ( empty( $this->title ) )
            return false;

        if ( ! $this->validate_connection_params() )
            return false;

        return true;
    }

    public function validate_connection_params() {
        if ( empty( $this->db_name ) )
            return false;

        return true;
    }
}
