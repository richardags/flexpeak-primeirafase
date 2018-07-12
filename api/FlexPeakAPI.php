<?php

	class FlexPeakAPI {

		/* POSTMON */
		static function getPostmon($cep) {
			//CEP Format Accepted: 12345-678 or 12345678
			//CEP Format Not Accepted: 12.345-678
			$postmon = UtilsClass::getHTTP("https://api.postmon.com.br/v1/cep/$cep");
			//convert response to JSON
			$postmonJSON = json_decode($postmon);
			//check error
			if(json_last_error() != JSON_ERROR_NONE)
				throw new Exception(json_last_error_msg());
			//return
			return $postmonJSON;
		}

		/* ALUNO */
		//cadastrar_aluno
		static private function check_aluno_by_nome($nome) {
			$SQL = 'SELECT * FROM '
					.DataBaseAPIConfig::TABLE_ALUNO
					." WHERE nome='$nome'";

			return DataBaseAPI::check_mysqli_query($SQL);
		}
		static private function check_all_fields_aluno($nome, $data_nascimento, $numero, $cep, $id_curso) {
			if(empty($nome))
				throw new Exception('O campo NOME é inválido!');
			if(!UtilsClass::validateDate($data_nascimento))
				throw new Exception('O campo DATA DE NASCIMENTO é inválido!');
			if(!UtilsClass::validateNumber($numero))
				throw new Exception('O campo NÚMERO é inválido!');
			if(!UtilsClass::validateCEP($cep))
				throw new Exception('O campo CEP é inválido!');
			//verifica se o curso existe
			if(!FlexPeakAPI::check_curso_by_id($id_curso))
				throw new Exception('O CURSO não existe!');
			//verifica se o aluno já foi cadastrado
			if(FlexPeakAPI::check_aluno_by_nome($nome))
				throw new Exception('O NOME já existe!');
		}
		static private function insert_aluno_sql($nome, $data_nascimento, $logradouro, $numero, $bairro, $cidade, $estado, $cep, $id_curso) {
			$SQL = 'INSERT INTO '
					.DataBaseAPIConfig::TABLE_ALUNO
					.' (nome, data_nascimento, logradouro, numero, bairro, cidade, estado, cep, id_curso) '
					."VALUES ('$nome', STR_TO_DATE('$data_nascimento', '%d/%m/%Y'), '$logradouro', '$numero', '$bairro', '$cidade', '$estado', '$cep', '$id_curso')";

			DataBaseAPI::create_mysqli_query($SQL);
		}
		static function cadastrar_aluno($nome,
									$data_nascimento,
									$id_curso,
									$numero,
									$cep){
			//create connection
			$connection = DataBaseAPI::open_database();
			//SECURITY: prevent SQL Injection
			$nome = DataBaseAPI::escapeString($connection, $nome);
			$data_nascimento = DataBaseAPI::escapeString($connection, $data_nascimento);
			$numero = DataBaseAPI::escapeString($connection, $numero);
			$cep = DataBaseAPI::escapeString($connection, $cep);
			$id_curso = DataBaseAPI::escapeString($connection, $id_curso);

			//unmask cep
			$cep = UtilsClass::unmaskCEP($cep);

			//check fields
			FlexPeakAPI::check_all_fields_aluno($nome, $data_nascimento, $numero, $cep, $id_curso);

			//get cep info
			$getPostmon = FlexPeakAPI::getPostmon($cep);
			$logradouro = $getPostmon->logradouro;
			$bairro = $getPostmon->bairro;
			$cidade = $getPostmon->cidade;
			$estado = $getPostmon->estado;

			//insert aluno
			FlexPeakAPI::insert_aluno_sql($nome, $data_nascimento, $logradouro, $numero, $bairro, $cidade, $estado, $cep, $id_curso);
		}
		//alunos_cadastrados
		static function alunos_cadastrados(){
			$SQL = 'SELECT aluno.id_aluno, aluno.nome, aluno.data_nascimento, aluno.logradouro, aluno.numero, aluno.bairro, aluno.cidade, aluno.estado, aluno.cep, aluno.id_curso, curso.nome AS nome_curso, professor.nome AS nome_professor, aluno.data_criacao FROM '
					.DataBaseAPIConfig::TABLE_ALUNO.' AS aluno'
					.' LEFT JOIN '.DataBaseAPIConfig::TABLE_CURSO.' AS curso ON curso.id_curso=aluno.id_curso'
					.' LEFT JOIN '.DataBaseAPIConfig::TABLE_PROFESSOR.' AS professor ON professor.id_professor=curso.id_professor'
					.' ORDER BY aluno.nome';
			return DataBaseAPI::get_rows_mysqli_query($SQL);
		}
		//deletar_aluno
		static private function delete_aluno_sql($id_aluno){
			$SQL = 'DELETE FROM '
					.DataBaseAPIConfig::TABLE_ALUNO
					." WHERE id_aluno='$id_aluno'";

			DataBaseAPI::create_mysqli_query($SQL);
		}
		static private function deletar_aluno_check($id_aluno){
			if(!UtilsClass::validateNumber($id_aluno))
				throw new Exception('O ID do ALUNO é inválido!');
		}
		static function deletar_aluno($id_aluno){
			//create connection
			$connection = DataBaseAPI::open_database();
			//SECURITY: prevent SQL Injection
			$id_aluno = DataBaseAPI::escapeString($connection, $id_aluno);

			//check fields
			FlexPeakAPI::deletar_aluno_check($id_aluno);

			//delete professor
			FlexPeakAPI::delete_aluno_sql($id_aluno);
		}

		/* CURSO */
		//cadastrar_curso
		static private function check_curso_by_id($id_curso) {
			$SQL = 'SELECT * FROM '
					.DataBaseAPIConfig::TABLE_CURSO
					." WHERE id_curso='$id_curso'";

			return DataBaseAPI::check_mysqli_query($SQL);
		}
		static private function check_curso_by_nome($nome) {
			$SQL = 'SELECT * FROM '
					.DataBaseAPIConfig::TABLE_CURSO
					." WHERE nome='$nome'";

			return DataBaseAPI::check_mysqli_query($SQL);
		}
		static private function check_all_fields_curso($nome, $id_professor) {
			if(empty($nome))
				throw new Exception('O campo NOME é inválido!');
			if(!UtilsClass::validateNumber($id_professor))
				throw new Exception('O campo PROFESSOR é inválido!');
			//verifica se o curso existe
			if(FlexPeakAPI::check_curso_by_nome($nome))
				throw new Exception('O CURSO já existe!');
			//verifica se o aluno já foi cadastrado
			if(!FlexPeakAPI::check_professor_by_id($id_professor))
				throw new Exception('O PROFESSOR não existe!');
		}
		static private function insert_curso_sql($nome, $id_professor) {
			$SQL = 'INSERT INTO '
					.DataBaseAPIConfig::TABLE_CURSO
					.' (nome, id_professor) '
					."VALUES ('$nome', '$id_professor')";

			DataBaseAPI::create_mysqli_query($SQL);
		}
		static function cadastrar_curso($nome, $id_professor) {
			//create connection
			$connection = DataBaseAPI::open_database();
			//SECURITY: prevent SQL Injection
			$nome = DataBaseAPI::escapeString($connection, $nome);
			$id_professor = DataBaseAPI::escapeString($connection, $id_professor);

			//check fields
			FlexPeakAPI::check_all_fields_curso($nome, $id_professor);

			//insert curso
			FlexPeakAPI::insert_curso_sql($nome, $id_professor);
		}
		//cursos_cadastrados
		static function cursos_cadastrados(){
			$SQL = 'SELECT curso.id_curso, curso.nome, curso.id_professor, professor.nome AS nome_professor, curso.data_criacao FROM '
					.DataBaseAPIConfig::TABLE_CURSO.' AS curso'
					.' LEFT JOIN '.DataBaseAPIConfig::TABLE_PROFESSOR.' AS professor ON professor.id_professor=curso.id_professor'
					.' ORDER BY curso.nome';

			return DataBaseAPI::get_rows_mysqli_query($SQL);
		}
		//deletar_curso
		static private function delete_curso_sql($id_curso){
			$SQL = 'DELETE FROM '
					.DataBaseAPIConfig::TABLE_CURSO
					." WHERE id_curso='$id_curso'";

			DataBaseAPI::create_mysqli_query($SQL);
		}
		static private function deletar_curso_check($id_curso){
			if(!UtilsClass::validateNumber($id_curso))
				throw new Exception('O ID do CURSO é inválido!');
		}
		static function deletar_curso($id_curso){
			//create connection
			$connection = DataBaseAPI::open_database();
			//SECURITY: prevent SQL Injection
			$id_curso = DataBaseAPI::escapeString($connection, $id_curso);

			//check fields
			FlexPeakAPI::deletar_curso_check($id_curso);

			//delete professor
			FlexPeakAPI::delete_curso_sql($id_curso);
		}

		/* PROFESSOR */
		//professores_cadastrados
		static private function check_professor_by_id($id_professor) {
			$SQL = 'SELECT * FROM '
					.DataBaseAPIConfig::TABLE_PROFESSOR
					." WHERE id_professor='$id_professor'";

			return DataBaseAPI::check_mysqli_query($SQL);
		}
		static private function check_professor_by_nome($nome) {
			$SQL = 'SELECT * FROM '
					.DataBaseAPIConfig::TABLE_PROFESSOR
					." WHERE nome='$nome'";

			return DataBaseAPI::check_mysqli_query($SQL);
		}
		static private function check_all_fields_professor($nome, $data_nascimento) {
			if(empty($nome))
				throw new Exception('O campo NOME é inválido!');
			if(!UtilsClass::validateDate($data_nascimento))
				throw new Exception('O campo DATA DE NASCIMENTO é inválido!');
			//verifica se o professor existe
			if(FlexPeakAPI::check_professor_by_nome($nome))
				throw new Exception('O PROFESSOR já existe!');
		}
		static private function insert_professor_sql($nome, $data_nascimento) {
			$SQL = 'INSERT INTO '
					.DataBaseAPIConfig::TABLE_PROFESSOR
					.' (nome, data_nascimento) '
					."VALUES ('$nome', STR_TO_DATE('$data_nascimento', '%d/%m/%Y'))";

			DataBaseAPI::create_mysqli_query($SQL);
		}
		static function cadastrar_professor($nome, $data_nascimento) {
			//create connection
			$connection = DataBaseAPI::open_database();
			//SECURITY: prevent SQL Injection
			$nome = DataBaseAPI::escapeString($connection, $nome);
			$data_nascimento = DataBaseAPI::escapeString($connection, $data_nascimento);

			//check fields
			FlexPeakAPI::check_all_fields_professor($nome, $data_nascimento);

			//insert curso
			FlexPeakAPI::insert_professor_sql($nome, $data_nascimento);
		}
		//professores_cadastrados
		static function professores_cadastrados(){
			$SQL = 'SELECT * FROM '.DataBaseAPIConfig::TABLE_PROFESSOR.' ORDER BY nome';

			return DataBaseAPI::get_rows_mysqli_query($SQL);
		}
		//deletar_professor
		static private function delete_professor_sql($id_professor){
			$SQL = 'DELETE FROM '
					.DataBaseAPIConfig::TABLE_PROFESSOR
					." WHERE id_professor='$id_professor'";

			DataBaseAPI::create_mysqli_query($SQL);
		}
		static private function deletar_professor_check($id_professor){
			if(!UtilsClass::validateNumber($id_professor))
				throw new Exception('O ID do PROFESSOR é inválido!');
		}
		static function deletar_professor($id_professor){
			//create connection
			$connection = DataBaseAPI::open_database();
			//SECURITY: prevent SQL Injection
			$id_professor = DataBaseAPI::escapeString($connection, $id_professor);

			//check fields
			FlexPeakAPI::deletar_professor_check($id_professor);

			//delete professor
			FlexPeakAPI::delete_professor_sql($id_professor);
		}

		/* GERAR_PDF */
		static private function get_table_alunos_cadastrados(){
			$alunos = FlexPeakAPI::alunos_cadastrados();

			$result = '
				<table border="1" align="center">

					<tr align="center" style="font-weight:bold">
						<td rowspan="2" ="2">ID</td>
						<td rowspan="2">Nome do Aluno</td>
						<td rowspan="2">Data de Nascimento</td>
						<td rowspan="2">CURSO</td>
						<td rowspan="2">PROFESSOR</td>
						<td colspan="6">ENDEREÇO</td>
					</tr>

					<tr align="center">
						<td>Número</td>
						<td>CEP</td>
						<td>Logradouro</td>
						<td>Bairro</td>
						<td>Cidade</td>
						<td>Estado</td>
					</tr>';

			if($alunos){

				foreach ($alunos as $aluno) {

					if($aluno['nome_curso'] == NULL)
						$aluno['nome_curso'] = '-';
					if($aluno['nome_professor'] == NULL)
						$aluno['nome_professor'] = '-';

				    $format = '
						<tr align="center">
						  	<td>%s</td>
						  	<td align="left">%s</td>
						  	<td>%s</td>
						  	<td>%s</td>
						  	<td>%s</td>
						  	<td>%s</td>
						  	<td>%s</td>
						  	<td>%s</td>
						  	<td>%s</td>
						  	<td>%s</td>
						  	<td>%s</td>
						</tr>';
						
				    $result .= sprintf($format, $aluno['id_aluno'], $aluno['nome'], $aluno['data_nascimento'], $aluno['nome_curso'], $aluno['nome_professor'], $aluno['numero'], $aluno['cep'], $aluno['logradouro'], $aluno['bairro'], $aluno['cidade'], $aluno['estado']);
				}

			}else{
				$result .= '<tr align="center" style="font-weight:bold; font-style:italic"><td colspan="11">nenhum aluno foi cadastrado.</td></tr>';
			}

			$result .= '</table>';

			return $result;
		}
		static private function generate_pdf_by_pdfcrowd(){
			//load API
			require_once $_SERVER['DOCUMENT_ROOT'].'/api/PdfCrowd.php';

			try{
			    // create the API client instance
			    $client = new \Pdfcrowd\HtmlToPdfClient("username", "api_key");
			    $client->setUseHttp(true);

			    // run the conversion and write the result to a file
			    $pdf = $client->convertString('<html><body>'.FlexPeakAPI::get_table_alunos_cadastrados().'</body></html>');

				//send the generated pdf to the browser
		        header("Content-Type: application/pdf");
		        header("Cache-Control: no-cache");
		        header("Accept-Ranges: none");
		        header("Content-Disposition: attachment; filename=\"alunos_cadastrados.pdf\"");

				echo $pdf;
			}catch(\Pdfcrowd\Error $why){
			    // report the error
			    error_log("Pdfcrowd Error: {$why}\n");
			    // handle the exception here or rethrow and handle it at a higher level
			    throw $why;
			}
		}
		static function alunos_cadastrados_pdf(){
			FlexPeakAPI::generate_pdf_by_pdfcrowd();
		}
	}

?>