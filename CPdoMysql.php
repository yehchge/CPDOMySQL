<?php 

/**
 *  @desc PDOMySQL class 版本
 *  @created 2020/06/07
 */

class CPdoMysql {

	// Variables
	var $m_sDb		=	"";
	var $m_sHost    =   '';
	var $m_sUser    =   '';
	var $m_sPass    =   '';
	var $m_iDbh		=	0;
	var $m_iRs 		= 	0;
	var $m_character	= "utf8";
	var $m_sPort	=	"3306";
	var $m_connect  =   true; // 是否長連
	private $mode;

	public function __construct($sDb='',$sHost='',$sUser='',$sPass=''){
		$this->m_sDb=defined('_MYSQL_DB')?_MYSQL_DB:null;
		$this->m_sHost=defined('_MYSQL_HOST')?_MYSQL_HOST:null;
		$this->m_sUser=defined('_MYSQL_USER')?_MYSQL_USER:null;
		$this->m_sPass=defined('_MYSQL_PASS')?_MYSQL_PASS:null;

		if($sDb) $this->m_sDb=$sDb;
		if($sHost) $this->m_sHost=$sHost;
		if($sUser) $this->m_sUser=$sUser;
		if($sPass) $this->m_sPass=$sPass;
		if(!$this->m_iDbh) {
			$this->vConnect();
		}
		if($this->m_iDbh){
			$this->bSetCharacter($this->m_character);
		}
	}

	public function __destruct() {
		$this->vClose();
	}

	/**
	 *  @desc 設定 MySQL 連結為 UTF-8
	 *  @created 2014/11/14
	 */
	function bSetCharacter($encode = "utf8") {
		$this->iQuery("SET character_set_client = $encode");
		$this->iQuery("SET character_set_results = $encode");
		$this->iQuery("SET character_set_connection = $encode");
	}
	/**
	 *  @desc 連線資料庫
	 */
	public function vConnect() {

		 try {
            $this->m_iDbh = new PDO('mysql:host='.$this->m_sHost.';port='.$this->m_sPort
            	.';dbname='.$this->m_sDb,$this->m_sUser,$this->m_sPass, array(PDO::
                ATTR_PERSISTENT => $this->m_connect));
        }
        catch (PDOException $e) {
            die("Connect Error Infomation:" . $e->getMessage());
        }
	}

	/**
	 *  @desc 關閉資料庫
	 */
	function vClose() {
		$this->m_iDbh = NULL;
	}

	/**
	 *  @desc query db
	 *  @param $sSql SQL語法
	 *  @return value of variable $m_iRs
	 */
	function iQuery($sSql){
		$this->m_iRs = $this->m_iDbh->query($sSql);
		return $this->m_iRs;
	}

	/**
	 * @desc 執行不須回傳值的語法
	 * @created 2017/04/18
	 */
	function vExec($sSql){
		$this->m_iRs = $this->m_iDbh->query($sSql);
		return $this->m_iRs;
	}

	/**
	 *  @desc 取得sql結果
	 *  @param $iRs resource result
	 *  @param result_type: MYSQLI_BOTH, MYSQLI_ASSOC, MYSQLI_NUM
	 *  @return Fetch a result row as an associative array, a numeric array, or both.
	 */
	function aFetchArray($iRs) {
	}

	/**
	* @param $iRs resource result
	* @return Fetch a result row as an associative array, a numeric array, or both.
	* @desc 取得sql結果
	*/
	function aFetchAssoc($iRs=0) {
	}

	/**
	* @return Get the ID generated from the previous INSERT operation
	* @desc
	*/
	function iGetInsertId() {
	}

	/**
	 * @desc 資料庫更動序號(取得insert後的自動流水號)
	 * @created 2017/04/18
	 */
	function iGetChangeRowID(){
	}

	/**
	 * delete
	 *
	 * @param string $table
	 * @param string $where
	 * @param integer $limit
	 * @return integer Affected Rows
	 */
    function vDelete($sTable,$sWhere){
    	if (!$sWhere) throw new Exception("CSQLite3->vDelete: fail no where. table: $sTable");
    	$this->iQuery("DELETE FROM $sTable WHERE $sWhere");
		if(!$this->m_iRs){
			throw new Exception("CSQLite3->vDelete: fail to delete data in $sTable");
		}
		$iChangeRows = $this->iGetChangeRowID();
		return $iChangeRows;
    }

    /**
	* @param $sTable db table $aField field array $aValue value array
	* @return if return sql is ok  "" is failure
	* @desc insert into table
	*/
	function sInsert($sTable,$aField,$aValue) {
		if(!is_array($aField)) return 0;
		if(!is_array($aValue)) return 0;

		count($aField)==count($aValue) or die(count($aField) .":". count($aValue) );

		$sSql="INSERT INTO $sTable ( ";
		for($i=1;$i<=count($aField);$i++) {
			$sSql.="`".$aField[$i-1]."`";
			if($i!=count($aField)) $sSql.=",";
		}

		$sSql.=") values(";

		for($i=1;$i<=count($aValue);$i++) {
			$sSql.="'".$this->escapeString($aValue[$i-1])."'";
			if($i!=count($aValue)) $sSql.=",";
		}
		$sSql.=")";

		$this->iQuery($sSql);

		//if(!$this->m_iRs) return NULL;
		if(!$this->m_iRs) throw new Exception("CSQLite3->sInsert: fail to insert data into $sTable");
		else return $sSql;
	}

	/**
	* @param $sTable db table $aField field array $aValue value array $sWhere trem
	* @return if return sql is ok  "" is failure
	* @desc update  table
	*/
	function sUpdate($sTable,$aField,$aValue,$sWhere) {
		if(!is_array($aField)) return 0;
		if(!is_array($aValue)) return 0;

		if(count($aField)!=count($aValue)) return 0;

		$sSql="update $sTable set ";
		for($i=0;$i<count($aField);$i++) {
			$sSql.="`".$aField[$i]."`='".$this->escapeString($aValue[$i])."'";
			if(($i+1)!=count($aField)) $sSql.=",";
		}

		$sSql.=" where ".$sWhere;
		$this->sSql = $sSql;
		$this->iQuery($sSql);
		if(!$this->m_iRs) throw new Exception("CSQLite3->sUpdate: fail to update data in $sTable");
		else return $sSql;
    }

    /**
	* @param string $sTable The table name, array $aAdd The add data array
	* @return boolean
	* @desc insert into table
	*/
	function bInsert( $sTable , $aAdd ) {
		$sSql="INSERT INTO $sTable (";
		foreach( $aAdd AS $key => $value ) {
			$sSql.="`".$key."`,";
		}
		$sSql = substr($sSql,0,-1);
		$sSql.=") values (";
		foreach( $aAdd AS $key => $value ) {
			$sSql.="'".$value."',";
		}
		$sSql = substr($sSql,0,-1);
		$sSql.=")";

		$this->sSql = $sSql;
		$this->vExec( $sSql );
		if(!$this->m_iRs) throw new Exception("CSQLite3->bInsert: fail to insert data in $sTable");
		return $this->iGetInsertId();
	}

	/**
	* @param string $sTable The table name, array $aSrc The source data array, array $aTar The target data array
	* @return boolean
	* @desc update table
	*/
	function bUpdate( $sTable , $aSrc , $aTar ) {
		$aWhere = array();
		foreach( $aSrc AS $key => $value ) {
			$aWhere[] = "$key = '".$this->escapeString($value)."'";
		}
		$aSrc = array();
		foreach( $aTar AS $key => $value ) {
			$aSet[] = "$key = '".$this->escapeString($value)."'";
		}
		$sSQL = "UPDATE $sTable SET " . implode( "," , $aSet ) . " WHERE " . ( count( $aWhere ) > 0 ? implode( " AND " , $aWhere ) : "1" );

		$this->sSql = $sSQL;
		$this->vExec( $sSQL );
		if(!$this->m_iRs) throw new Exception("CSQLite3->bUpdate: fail to update data in $sTable");
		$iChangeRows = $this->iGetChangeRowID();
		return $iChangeRows;
	}

	public function escapeString($tar) {
        if( !is_array($tar) )
            return ini_set("magic_quotes_runtime",0) ?  trim($tar) : addslashes(trim($tar));

        return array_map($this->escapeString, $tar); //pass ref to function
    }

}
