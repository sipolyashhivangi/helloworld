<?php

$pid = pcntl_fork();

echo "start\n";

if($pid) {
  // parent process runs what is here
	echo "inside parent\n";
	}
	else{
  // child process runs what is here
  echo "inside child\n";
}

echo "end\n";

?>

