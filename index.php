<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<style>
	body{
		padding-top: 200px;
		font-family: arial;
		background-color: rgb(123,104,238,.4);
	}
	section{
		background-color: rgb(123,104,238,.4);
		width: 70%;
		margin: auto;
	}
	input, label, textarea{
		display: block;
		width: 100%;
		height: 30px;
	}
	label{
		line-height: 30px;
		margin-top: 10px;
	}
	textarea{
		height: 150px;
	}
	form{
		
		width: 60%;
		margin: auto;
		box-sizing: border-box;
		padding: 20px;
	}
	#botao{
		margin-bottom: 10px;
		width: 50%;
		background-color: rgba(0,0,0,.8);
		color: white;
		height: 40px;
		cursor: pointer;
		border: none;
		font-size: 15pt;
	}
	h1{
		text-align: center;
	}
	#foto{
		margin-top: 20px;
		margin-bottom: 20px;
	}

	a{
		background-color: rgb(0,255,127);
		display: block;
		width: auto;
		height: auto;
		color: black;
		text-decoration: none;
		float: right;
		text-align: center;
		line-height: 50px;
		margin: 20px;
		padding: 5px;
		border: 1px solid rgba(0,0,0,.2);
	}

	</style>
</head>
<body>
	<section>
	<a href="produtos.php">Ver funcionários</a>
	<a href="cadastrar_imagens.php">Cadastrar Funcionários</a>
	<form method="POST" enctype="multipart/form-data">
		<h1>Comparar biometria</h1>
		<label for="nome">Nome do funcionario</label>
		<input type="text" name="nome" id="nome">
		<input type="file" name="foto[]" multiple id="foto">
		<input type="submit" id="botao">
	</form>
	</section>
</body>
</html>

<?php
if (isset($_POST['nome'])) {
	$nome = addslashes($_POST['nome']);
	$fotos = [];

	if (isset($_FILES['foto'])){
		for ($i=0; $i < count($_FILES['foto']['name']); $i++) {

			//salvando dentro da pasta de imagens
			$nome_arquivo = $_FILES['foto']['name'][$i];

			//salvar nomes para salvar no banco
			array_push($fotos, $nome_arquivo);
		}
	}

	//verificar se foi preeenchido todos os campos

	if (!empty($nome) && !empty($fotos)) {
		require 'classes/produto_class.php';
		$p = new produto_class('formulario_produtos', 'localhost', 'root', '');
		$p = $p->compararImagens($nome, $fotos);

		$bool = is_int($p);
		if ($bool) {
			?>
				<meta http-equiv="refresh" content="0; URL='http://localhost/APS/exibir_produto.php?id=<?php echo $p; ?>'"/>
			<?php
		} else {
			?>
				<script type="text/javascript">
					alert("Este nome de funcionario não existe")
				</script>
			<?php
			die;
		}
	} else {
		?>
			<script type="text/javascript">
				alert("Preencha os campos obrigatórios")
			</script>
		<?php
	}
}

?>