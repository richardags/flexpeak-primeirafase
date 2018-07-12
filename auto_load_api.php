<?php
	
	function auto_load_api($class_name){
		$class_name = "api/".$class_name.".php";

		if(file_exists($class_name)){
			include($class_name);
		}else{
			echo "ERRO: Não foi possível carregar a API/Classe ($class_name)";
		}
	}

	spl_autoload_register("auto_load_api");

?>