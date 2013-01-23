<?
error_reporting(E_ERROR | E_PARSE);
ini_set('error_reporting', E_ERROR);
$DBj = array(
        'TYPE'  => 'mysql',
        'USER'  => $account,
        'PASS'  => $password,
        'PROTO' => 'tcp',       // set to "tcp" for TCP/IP
        'HOST'  => $host,
        'NAME'  => $dbname
);

$DBj['DSN'] = sprintf('%s://%s:%s@%s+%s/%s',    $DBj['TYPE'], $DBj['USER'],
                                                $DBj['PASS'], $DBj['PROTO'],
                                                $DBj['HOST'], $DBj['NAME']);
#echo $DBj['DSN'];

function u2w($_str) {
        return iconv("UTF-8","WINDOWS-1251",$_str);
}
function w2u($_str) {
        return iconv("WINDOWS-1251","UTF-8",$_str);
}

@include "DB.php";
if (!class_exists("DB")) {
        echo "Can't find PEAR:DB extension.";
}

function isDBError($result) {
        unset($_dbg_message); $_dbg_message = false;
        if (PEAR::isError($result)) {$_dbg_message =  '<b>Standard Message:</b> ' . $result->getMessage() . "<br>";$_dbg_message .= '<b>Standard Code:</b> ' . $result->getCode() . "<br>";$_dbg_message .= '<b>DBMS/User Message:</b> ' . $result->getUserInfo() . "<br>";$_dbg_message .= '<b>DBMS/Debug Message:</b> ' . $result->getDebugInfo() . "<br>";}
        return $_dbg_message;
}

function makeQuery($q, $ret = false) {
        global $handle;
        $isDBError = isDBError($r =& $handle->query($q));
        if ($isDBError) { if ($ret) { return $isDBError; } else { die ($isDBError); } }
        return $r;
}
function makeQueryArray($q, $ret = false) {
        global $handle;
        $isDBError = isDBError($r =& $handle->getAll($q));
        if ($isDBError) { if ($ret) { return $isDBError; } else { die ($isDBError); } }
        return $r;
}
function makeQueryOneArray($q, $ret = false) {
        global $handle;
        $isDBError = isDBError($r =& $handle->getOne($q));
        if ($isDBError) { if ($ret) { return $isDBError; } else { die ($isDBError); } }
        return $r;
}
function returnArray($r) {
        $res = array();
        if ($r->numRows($r)>0) {
                for ($i=0;$i<$r->numRows($r);$i++) {
                        $row = $r->fetchRow(DB_FETCHMODE_ASSOC, $i);
                        $res[$i] = $row;
                }
        }
        return $res;
}

$handle =& DB::connect($DBj['DSN'],true);
if (DB::isError($handle)) {
        die("Database error: ".$handle->getMessage());
}
//$handle->query('SET NAMES cp1251');
//$handle->query('SET CHARACTER SET cp1251');
$handle->setFetchMode(DB_FETCHMODE_ASSOC);

?>