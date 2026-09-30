<?php require __DIR__.'/common.php'; $action=$_GET['action']??'';
if($action==='status') json_out(['authenticated'=>!empty($_SESSION['dashboard_admin'])]);
if($_SERVER['REQUEST_METHOD']!=='POST') json_out(['error'=>'Method'],405); $in=body();
if($action==='login'){ $auth=json_decode((string)@file_get_contents(AUTH_FILE),true); $hash=$auth['password_hash']??password_hash('homelab',PASSWORD_DEFAULT); if(password_verify((string)($in['password']??''),$hash)){session_regenerate_id(true);$_SESSION['dashboard_admin']=true;json_out(['ok'=>true]);}json_out(['error'=>'Invalid password'],401); }
if($action==='password'){require_auth();$p=(string)($in['password']??'');if(strlen($p)<8)json_out(['error'=>'Use at least 8 characters'],400);if(file_put_contents(AUTH_FILE,json_encode(['password_hash'=>password_hash($p,PASSWORD_DEFAULT)],JSON_PRETTY_PRINT),LOCK_EX)===false)json_out(['error'=>'Cannot write auth file'],500);json_out(['ok'=>true]);}
json_out(['error'=>'Unknown action'],400);