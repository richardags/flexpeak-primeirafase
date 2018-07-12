<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/auto_load_api.php');

	function msg_error($msg){
		return '<tr align="center" style="font-weight:bold; color:red"><td colspan="3">'.$msg.'</td></tr>';
	}
	function msg_sucessfully($msg){
		return '<tr align="center" style="font-weight:bold; color:blue"><td colspan="3">'.$msg.'</td></tr>';
	}
	function msg_italic($msg){
		return '<tr align="center" style="font-weight:bold; font-style:italic"><td colspan="3">'.$msg.'</td></tr>';
	}

	/* REMOVER_PROFESSOR*/
	try {
		if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ExluirProfessor'])) {
			$id_professor = $_POST['id_professor'];

			FlexPeakAPI::deletar_professor($id_professor);

    		$msg_deletar_professor = msg_sucessfully('Professor excluido com sucesso :)');
		}
	} catch (Exception $e) {
		$msg_deletar_professor = msg_error($e->getMessage());
	}

	/* CADASTRAR_PROFESSOR*/
	try{
		function RFC3339toBR($stringtime){
			date_default_timezone_set('UTC');
			return date('d/m/Y', strtotime($stringtime));
		}

		if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btnAdicionarProfessor'])) {
    		$nome = $_POST['nome'];
    		$data_nascimento = $_POST['data_nascimento'];

    		FlexPeakAPI::cadastrar_professor($nome, RFC3339toBR($data_nascimento));

    		$msg_cadastrar_professor = msg_sucessfully('Professor cadastrado com sucesso :)');
		}
	} catch (Exception $e) {
		$msg_cadastrar_professor = msg_error($e->getMessage());
	}
	
	/* PROFESSORES_CADASTRADOS */
	try{
		$professores = FlexPeakAPI::professores_cadastrados();
		if($professores){
			$msg_professores_cadastrados = '';
			foreach ($professores as $professor) {
			    $format = '
				  	<tr align="center">
				  		<td>%s</td>
				  		<td align="left">%s</td>
				  		<td>%s</td>
				  		<td><button type="button" onclick="confirmar_deletar_professor(%s)">excluir</button></td>
				  	</tr>';
			    $msg_professores_cadastrados .= sprintf($format, $professor['id_professor'], $professor['nome'], $professor['data_nascimento'], $professor['id_professor']);
			}
		}else{
			$msg_professores_cadastrados = msg_italic('nenhum professor foi cadastrado.');
		}
	} catch (Exception $e) {
		$msg_professores_cadastrados = msg_error($e->getMessage());
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
			<td>ID</td>
			<td>Nome do Professor</td>
			<td>Data de Nascimento</td>
		</tr>

		<?php if(isset($msg_professores_cadastrados)) echo $msg_professores_cadastrados; ?>
		
		<tr align="center" style="font-weight:bold">
			<td></td>
			<form method="post">
				<td><input type="text" name="nome" required="required" size="40" style="font-weight:bold"></td>
				<td><input type="date" name="data_nascimento" required="required" value="<?php date_default_timezone_set('UTC'); echo date('Y-d-m'); ?>"></td>
				<td><button type="submit" name="btnAdicionarProfessor">Adicionar</button></td>
			</form>
		</tr>

		<?php if(isset($msg_cadastrar_professor)) echo $msg_cadastrar_professor; ?>

		<?php if(isset($msg_deletar_professor)) echo $msg_deletar_professor; ?>

	</table>

</body>
</html>