<?php
// Optional: copy this file to another PHP host and put its URL in a host's metrics_url field.
header('Content-Type: application/json');
$load=sys_getloadavg();$cores=1;if(is_readable('/proc/cpuinfo'))$cores=max(1,substr_count((string)file_get_contents('/proc/cpuinfo'),'processor\t:'));
$path=(string)($_GET['path']??'/');$total=@disk_total_space($path);$free=@disk_free_space($path);
$storage=['disk_path'=>$path,'disk'=>null,'disk_free_gb'=>null,'disk_total_gb'=>null,'disk_percent'=>null];
if($total!==false&&$total>0&&$free!==false)$storage=['disk_path'=>$path,'disk'=>round(($total-$free)/$total*100,1),'disk_free_gb'=>round($free/1073741824,1),'disk_total_gb'=>round($total/1073741824,1),'disk_percent'=>round(($total-$free)/$total*100,1)];
echo json_encode(['cpu'=>isset($load[0])?round(min(100,$load[0]/$cores*100),1):null,...$storage]);
