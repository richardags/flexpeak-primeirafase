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

	/* SELECT_PROFESSORES*/
	try {
		$professores = FlexPeakAPI::professores_cadastrados();
		$select_professores = '';
		foreach ($professores as $professor) {
			$format = '<option value="%s">%s</option>';
			$select_professores .= sprintf($format, $professor['id_professor'], $professor['nome']);
		}
	} catch (Exception $e) {
		$select_professores = msg_error($e->getMessage());
	}

	/* REMOVER_CURSO*/
	try {
		if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ExluirCurso'])) {
			$id_curso = $_POST['id_curso'];

			FlexPeakAPI::deletar_curso($id_curso);

    		$msg_deletar_professor = msg_sucessfully('Curso excluido com sucesso :)');
		}
	} catch (Exception $e) {
		$msg_deletar_professor = msg_error($e->getMessage());
	}

	/* CADASTRAR_CURSO*/
	try{
		if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btnAdicionarCurso'])) {
    		$nome = $_POST['nome'];
    		$id_professor = $_POST['id_professor'];

    		FlexPeakAPI::cadastrar_curso($nome, $id_professor);

    		$msg_cadastrar_curso = msg_sucessfully('Curso cadastrado com sucesso :)');
		}
	} catch (Exception $e) {
		$msg_cadastrar_curso = msg_error($e->getMessage());
	}
	
	/* CURSOS_CADASTRADOS */
	try{
		$cursos = FlexPeakAPI::cursos_cadastrados();
		$professores = FlexPeakAPI::professores_cadastrados();

		if($cursos){
			$msg_cursos_cadastrados = '';
			foreach ($cursos as $curso) {
				$nome_professor = 'SEM PROFESSOR';
				foreach ($professores as $professor) {
					if($professor['id_professor'] == $curso['id_professor']){
						$nome_professor = $professor['nome'];
						break;
					}
				}
			    $format = '
				  	<tr align="center">
				  		<td>%s</td>
				  		<td align="left">%s</td>
				  		<td>%s</td>
				  		<td><button type="button" onclick="confirmar_deletar_curso(%s)">excluir</button></td>
				  	</tr>';
			    $msg_cursos_cadastrados .= sprintf($format, $curso['id_curso'], $curso['nome'], $nome_professor, $curso['id_curso']);
			}
		}else{
			$msg_cursos_cadastrados = msg_italic('nenhum curso foi cadastrado.');
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
			<td>ID</td>
			<td>Nome do Curso</td>
			<td>Professor</td>
		</tr>

		<?php if(isset($msg_cursos_cadastrados)) echo $msg_cursos_cadastrados; ?>
		
		<tr align="center" style="font-weight:bold">
			<td></td>
			<form method="post">
				<td><input type="text" name="nome" required="required" size="40" style="font-weight:bold"></td>
				<td><select name="id_professor"><?php echo $select_professores; ?></select></td>
				<td><button type="submit" name="btnAdicionarCurso">Adicionar</button></td>
			</form>
		</tr>

		<?php if(isset($msg_cadastrar_curso)) echo $msg_cadastrar_curso; ?>

		<?php if(isset($msg_deletar_professor)) echo $msg_deletar_professor; ?>

	</table>

</body>
</html>