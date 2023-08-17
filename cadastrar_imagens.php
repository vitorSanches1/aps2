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
	<a href="produtos.php">Ver Funcionarios</a>
	<a href="index.php">Comparar biometrias</a>
	<form method="POST" enctype="multipart/form-data">
		<h1>Cadastro de Funcionário</h1>
		<label for="nome">Nome do Funcionário</label>
		<input type="text" name="nome" id="nome">
		<label for="desc">O que esse funcionário poderá acessar ?</label>
		<textarea name="desc" id="desc"></textarea>
		<input type="file" name="foto[]" multiple id="foto">
		<input type="submit" id="botao">
	</form>
	</section>
</body>
</html>

<?php
if (isset($_POST['nome'])) {
	$nome = addslashes($_POST['nome']);
	$descricao = addslashes($_POST['desc']);
	$fotos = [];

	if (isset($_FILES['foto'])){
		for ($i=0; $i < count($_FILES['foto']['name']); $i++) {

			//salvando dentro da pasta de imagens
			$nome_arquivo = $_FILES['foto']['name'][$i];
			move_uploaded_file($_FILES['foto']['tmp_name'][$i], 'imagens/'.$nome_arquivo);

			//salvar nomes para salvar no banco
			array_push($fotos, $nome_arquivo);
		}
	}

	//verificar se foi preeenchido todos os campos

	if (!empty($nome) && !empty($descricao) && !empty($fotos)) {
		require 'classes/produto_class.php';
		$p = new produto_class('formulario_produtos', 'localhost', 'root', '');
		$p->enviarProduto($nome, $descricao, $fotos);
	
		?>
			<script type="text/javascript">
				alert("Funcionário cadastrado com sucesso")
			</script>
		<?php
	} else {
		?>
			<script type="text/javascript">
				alert("Preencha os campos obrigatórios")
			</script>
		<?php
	}
}

?>