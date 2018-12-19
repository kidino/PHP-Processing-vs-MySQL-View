<?php 

class dbconnection {
    private $host = 'localhost';
    private $user = 'root';
    private $password = '';
    private $db = 'kehadiran';
    protected $connection;
    public function __construct($host, $user, $password, $db) {
        $this->connection = new mysqli(
            $this->host = $host ?: $this->host,
            $this->user = $user ?: $this->user,
            $this->password = $password ?: $this->password,
            $this->db = $db ?: $this->db
        );
        if ($this->connection->connect_error) {
            trigger_error('Database connection failed: '  . $this->connection->connect_error, E_USER_ERROR);
        }
    }
    public function getConnection()
    {
        return $this->connection;
    }
}

class dbmodel {

	public $conn;

	public $idcol = '';
	public $table = '';
	public $order_by = '';

	public $errmsg = '';

	function __construct(dbconnection $connection) {
		$this->conn = $connection->getConnection();
	}

	function get( $id, $limit = 1, $offset = 0 ){
		$sql = "select * from {$this->table} where {$this->idcol} = ? limit $limit offset $offset";
		if ($this->order_by != '') {
			$sql .= ' '.$this->order_by;
		}
		$stmt = $this->conn->prepare($sql);
		$this->order_by = '';
		if($stmt === false) {
		  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $this->conn->error, E_USER_ERROR);
		}
		$stmt->bind_param('i',$id);
        $stmt->execute();
		
		//$result = $stmt->get_result();
		$result = $this->get_result( $stmt );
		if ($result->num_rows > 0) {
			return $result->fetch_array(MYSQLI_ASSOC);
		}
		return false;
	}

	function get_all( $limit = 0, $offset = 0){
		$sql = "select * from {$this->table}";
		if ($limit > 0) { $sql .= " limit $limit offset $offset"; }
		
		if ($this->order_by != '') {
			$sql .= ' order by '.$this->order_by;
		}

		$stmt = $this->conn->prepare($sql);
		$this->order_by = '';
		if($stmt === false) {
		  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $this->conn->error, E_USER_ERROR);
		}
        $stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0) {
			while($row = $result->fetch_array(MYSQLI_ASSOC)) {
			  $rows[]=$row;
			}
			return $rows;
		}
		return false;
	}

	function get_where( $data ) {
		if (!is_array( $data )) {
		  trigger_error('Wrong Parameter: get_where() requires an array as parameter', E_USER_ERROR);
		}

		$sql = "select * from {$this->table} where ";

		$types = '';
		$cols = array();
		foreach( $data as $k => $v ) {
			$vals[] = &$data[$k];
			switch (gettype($v)) {
				case 'boolean':
					$types .= 'i';
				case 'integer':
					$types .= 'i';
					$cols[] = "$k = ?";
					break;
				case 'double':
					$cols[] = "$k = ?";
					$types .= 'd'; break;
				case 'string':
					$cols[] = "$k like ?";
					$types .= 's'; break;
			}
		}

		$sql .= implode(' and ', $cols);
		
		if ($this->order_by != '') {
			$sql .= ' '.$this->order_by;
		}

		$stmt = $this->conn->prepare($sql);
		$this->order_by = '';
		if($stmt === false) {
		  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $this->conn->error, E_USER_ERROR);
		}
		call_user_func_array(array($stmt, 'bind_param'), array_merge( array($types), $vals) );
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0) {
			while($row = $result->fetch_array(MYSQLI_ASSOC)) {
			  $rows[]=$row;
			}
			return $rows;
		}
		return false;
	}

	function delete($id){
		$sql = "delete from {$this->table} where {$this->idcol} = ?";
		$stmt = $this->conn->prepare($sql);
		if($stmt === false) {
		  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $this->conn->error, E_USER_ERROR);
		}
		$stmt->bind_param('i',$id);
        $stmt->execute();
		return $stmt->affected_rows;
	}

	function save( $data ) {
		if (!is_array( $data )) {
		  trigger_error('Wrong Parameter: save() requires array as parameter', E_USER_ERROR);
		}

		// modified to accept only data with id data
		if (array_key_exists( $this->idcol, $data)) {
			
			$res = $this->get($data[$this->idcol]);
			if (!$res) {
				$params = array( str_repeat("s", count($data) ) ); 
				foreach( $data as $k => $v) {
					$cl[] = "$k";
					$vl[] = "?";
					$params[] = &$data[$k];
				}

				$cols = implode(',', $cl);
				$vals = implode(',', $vl);

				$sql = "insert into {$this->table} ($cols) values ($vals)";
				$stmt = $this->conn->prepare($sql);
				if($stmt === false) {
				  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $this->conn->error, E_USER_ERROR);
				}
				call_user_func_array(array($stmt, 'bind_param'), $params );
				$stmt->execute();
				return $stmt->affected_rows;

			} else {
				$vals = array( str_repeat("s", count($data) + 1) ); // + 1 for where ?
				$cols = array();
				foreach( $data as $k => $v) {
					$cols[] = "$k = ?";
					$vals[] = &$data[$k];
				}
				$vals[] = &$data[$this->idcol]; // for the where idcol = ?

				$update_cols = implode(',', $cols);
				$sql = "update {$this->table} set {$update_cols} where {$this->idcol} = ?";
				$stmt = $this->conn->prepare($sql);
				if($stmt === false) {
				  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $this->conn->error, E_USER_ERROR);
				}
				call_user_func_array(array($stmt, 'bind_param'), $vals );

				$stmt->execute();
				return $stmt->affected_rows;
				
			}
			
		}
		return false;
	}
	
	// alternative to $stmt->get_result() that requires Mysql Native Driver
	// this one does not require that
	// http://stackoverflow.com/questions/10752815/mysqli-get-result-alternative#answer-30551477
	function get_result( $Statement ) {
		$RESULT = array();
		$Statement->store_result();
		for ( $i = 0; $i < $Statement->num_rows; $i++ ) {
			$Metadata = $Statement->result_metadata();
			$PARAMS = array();
			while ( $Field = $Metadata->fetch_field() ) {
				$PARAMS[] = &$RESULT[ $i ][ $Field->name ];
			}
			call_user_func_array( array( $Statement, 'bind_result' ), $PARAMS );
			$Statement->fetch();
		}
		return $RESULT;
	}
}
