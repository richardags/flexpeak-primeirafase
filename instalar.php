<?php
	try {
		echo 'Carregando API...';
		require_once($_SERVER['DOCUMENT_ROOT'].'/auto_load_api.php');

		UtilsClass::message('API carregada com sucesso - OK');
		UtilsClass::message('Arquivo de configuração: api/DataBaseAPIConfig.php');

		UtilsClass::jump();

		/* cria banco de dados */
		UtilsClass::message("Criando banco de dados: ".DataBaseAPIConfig::DB_NAME);
		DataBaseAPI::create_database();
		UtilsClass::message('Banco de dados criado com sucesso - OK');

		UtilsClass::jump();

		/* cria tabela aluno */
		UtilsClass::message("Criando tabela no banco de dados: ".DataBaseAPIConfig::TABLE_ALUNO);
		DataBaseAPI::create_table_aluno();
		UtilsClass::message('Tabela criada com sucesso - OK');

		UtilsClass::jump();

		/* cria tabela curso */
		UtilsClass::message("Criando tabela no banco de dados: ".DataBaseAPIConfig::TABLE_CURSO);
		DataBaseAPI::create_table_curso();
		UtilsClass::message('Tabela criada com sucesso - OK');

		UtilsClass::jump();

		/* cria tabela professor */
		UtilsClass::message("Criando tabela no banco de dados: ".DataBaseAPIConfig::TABLE_PROFESSOR);
		DataBaseAPI::create_table_professor();
		UtilsClass::message('Tabela criada com sucesso - OK');
	} catch (Exception $e) {
		UtilsClass::jump();
		print('Ocorreu um erro: '.$e->getMessage());
	}
?>