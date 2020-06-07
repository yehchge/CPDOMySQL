<?php

/**
	CREATE TABLE IF NOT EXISTS `tbl1` (
	  `id` integer(11) auto_increment primary key,
	  `one` varchar(50) DEFAULT '',
	  `two` varchar(50) DEFAULT '',
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/ 

echo '<h1><b>PDO Class 類別展示</b></h1>';

require 'CPdoMysql.php';

try{

	$oPDO = new CPdoMysql('pdomysql','172.88.2.12','root','123456');

	// create db
	$sSql = "CREATE TABLE IF NOT EXISTS `tbl1` (
	  `id` integer(11) auto_increment primary key,
	  `one` varchar(50) DEFAULT '',
	  `two` varchar(50) DEFAULT ''
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

	$oPDO->iQuery($sSql);

	// Add
	$data = array();
	$data['one'] = sRandomString('',7);
	$data['two'] = rand(1,100);
	$id = $oPDO->bInsert('tbl1', $data);
	if ($id){
	    echo 'Insert ID is : ', $id."\r\n";
	}

	// Update
	$aWhere = array();
	$aData = array();
	$aWhere['two'] = 10;
	$aData['one'] = sRandomString('',10);
	$iChangeRows = $oPDO->bUpdate('tbl1', $aWhere , $aData);
	if ($iChangeRows) {
	    echo 'Number of rows modified: ', $iChangeRows."\r\n";
	}

	// Delete
	// $iDbq2 = $oPDO->vDelete('tbl1', "two=10");
	// if ($iDbq2) {
	//     echo 'Number of rows deleted: ', $iDbq2."\r\n";
	// }

	// List
	$sBrLf = "<br>";
	$results = $oPDO->iQuery("SELECT * FROM tbl1 ORDER BY id DESC LIMIT 0,10");
	while($row = $oPDO->aFetchAssoc($results)){
	    echo $row['one']." , ".$row['two'].$sBrLf;
	}

}catch(Exception $e){
	echo $e->getMessage();
}

function sRandomString($sString,$sNum){ //(字元,回傳幾位)
    if(strlen($sString)==0){
        $s="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $s.="abcdefghijklmnopqrstuvwxyz";
        $s.="0123456789";
    } else {
        $s=$sString;
    }
    $rs = '';
    for($i=0;$i<$sNum;$i++){
        $rs.=$s[rand(0,strlen($s)-1)];
    }
    return $rs;
}
