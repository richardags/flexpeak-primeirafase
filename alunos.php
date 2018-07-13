<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/auto_load_api.php');

	function msg_error($msg){
		return '<tr align="center" style="font-weight:bold; color:red"><td colspan="11">'.$msg.'</td></tr>';
	}
	function msg_sucessfully($msg){
		return '<tr align="center" style="font-weight:bold; color:blue"><td colspan="11">'.$msg.'</td></tr>';
	}
	function msg_italic($msg){
		return '<tr align="center" style="font-weight:bold; font-style:italic"><td colspan="11">'.$msg.'</td></tr>';
	}

	/* GERAR_PDF */
	try {
		if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btnGerarPDF'])) {
			FlexPeakAPI::alunos_cadastrados_pdf();
		}
	} catch (Exception $e) {
		$msg_gerar_pdf = msg_error($e->getMessage());
	}

	/* SELECT_CURSOS */
	try {
		$cursos = FlexPeakAPI::cursos_cadastrados();
		$select_cursos = '';

		foreach ($cursos as $curso) {
			$format = '<option value="%s">%s</option>';
			$select_cursos .= sprintf($format, $curso['id_curso'], $curso['nome']);
		}
	} catch (Exception $e) {
		$select_cursos = msg_error($e->getMessage());
	}

	/* SELECT_PROFESSORES */
	try {
		$cursos = FlexPeakAPI::cursos_cadastrados();
		$select_professores = '';

		foreach ($cursos as $curso) {
			if($curso['nome_professor'] == NULL)
				$curso['nome_professor'] = '-';

			$format = '<option value="%s">%s</option>';
			$select_professores .= sprintf($format, $curso['id_curso'], $curso['nome_professor']);
		}
	} catch (Exception $e) {
		$select_professores = msg_error($e->getMessage());
	}

	/* REMOVER_ALUNO */
	try {
		if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ExluirAluno'])) {
			$id_aluno = $_POST['id_aluno'];

			FlexPeakAPI::deletar_aluno($id_aluno);

    		$msg_deletar_aluno = msg_sucessfully('Aluno excluido com sucesso :)');
		}
	} catch (Exception $e) {
		$msg_deletar_aluno = msg_error($e->getMessage());
	}

	/* CADASTRAR_ALUNO */
	try{
		function RFC3339toBR($stringtime){
			date_default_timezone_set('UTC');
			return date('d/m/Y', strtotime($stringtime));
		}

		if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btnAdicionarAluno'])) {
    		$nome = $_POST['nome'];
    		$data_nascimento = $_POST['data_nascimento'];
    		$id_curso = $_POST['id_curso'];
    		$numero = $_POST['numero'];
    		$cep = $_POST['cep'];

    		FlexPeakAPI::cadastrar_aluno($nome, RFC3339toBR($data_nascimento), $id_curso, $numero, $cep);

    		$msg_cadastrar_aluno = msg_sucessfully('Aluno cadastrado com sucesso :)');
		}
	} catch (Exception $e) {
		$msg_cadastrar_aluno = msg_error($e->getMessage());
	}
	
	/* ALUNOS_CADASTRADOS */
	try{
		$alunos = FlexPeakAPI::alunos_cadastrados();

		if($alunos){

			$msg_alunos_cadastrados = '';

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
				  		<td><button type="button" onclick="confirmar_deletar_aluno(%s)">excluir</button></td>
				  	</tr>';
			    $msg_alunos_cadastrados .= sprintf($format, $aluno['id_aluno'], $aluno['nome'], $aluno['data_nascimento'], $aluno['nome_curso'], $aluno['nome_professor'], $aluno['numero'], $aluno['cep'], $aluno['logradouro'], $aluno['bairro'], $aluno['cidade'], $aluno['estado'], $aluno['id_aluno']);
			}

		}else{
			$msg_alunos_cadastrados = msg_italic('nenhum aluno foi cadastrado.');
		}
	} catch (Exception $e) {
		$msg_cursos_cadastrados = msg_error($e->getMessage());
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>NOME_DO_SITE</title>
	<script type="text/javascript" src="javascript.js"></script>
</head>
<body>

	<table border="1" align="center" style="font-weight:bold; font-size: 10pt">
		<tr align="center" >
			<td><a href="professores.php">PROFESSORES</a></td>
			<td><a href="cursos.php">CURSOS</a></td>
			<td><a href="alunos.php">ALUNOS</a></td>
		</tr>
	</table>

	<br><br>

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
		</tr>

		<?php if(isset($msg_alunos_cadastrados)) echo $msg_alunos_cadastrados; ?>
		
		<tr align="center" style="font-weight:bold">
			<td></td>
			<form method="post">
				<td><input type="text" name="nome" required="required" size="40" style="font-weight:bold"></td>
				<td><input type="date" name="data_nascimento" required="required" value="<?php date_default_timezone_set('UTC'); echo date('Y-m-d'); ?>"></td>
				<td><select name="id_curso" onchange="ChangeSelectedByNameOptionByValue('id_professor', this.value)"><?php echo $select_cursos; ?></select></td>
				<td><select name="id_professor" disabled><?php echo $select_professores; ?></select></td>
				<td><input type="number" name="numero" required="required" style="font-weight:bold"></td>
				<td><input type="text" name="cep" required="required" maxlength="10" pattern="([0-9]{2}.[0-9]{3}-[0-9]{3}|[0-9]{5}-[0-9]{3}|[0-9]{8})$" title="00.000-000 ou 00000-000 ou 00000000" placeholder="00.000-000" onkeyup="getPostmon(this.value)" style="font-weight:bold"></td>
				<td><input type="text" disabled value="insira o CEP..." name="logradouro" style="font-weight:bold"></td>
				<td><input type="text" disabled value="insira o CEP..." name="bairro" style="font-weight:bold"></td>
				<td><input type="text" disabled value="insira o CEP..." name="cidade" style="font-weight:bold"></td>
				<td><input type="text" disabled value="insira o CEP..." name="estado" style="font-weight:bold"></td>
				<td><button type="submit" name="btnAdicionarAluno">Adicionar</button></td>
			</form>
		</tr>

		<?php if(isset($msg_cadastrar_aluno)) echo $msg_cadastrar_aluno; ?>

		<?php if(isset($msg_deletar_aluno)) echo $msg_deletar_aluno; ?>

	</table>

	<br>

	<?php if(isset($msg_gerar_pdf)) echo $msg_gerar_pdf; ?>

	<center><form method="post"><button type="submit" name="btnGerarPDF">Gerar arquivo PDF</button></form></center>

</body>
</html>