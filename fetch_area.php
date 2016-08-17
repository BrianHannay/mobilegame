<?php
	$imageNames = scandir("areas");
	//remove two elements (".", and "..").
	array_shift($imageNames);
	array_shift($imageNames);
	if(in_array($_GET["area"],$imageNames)){
		include "areas/".$_GET["area"];
	}

?>
