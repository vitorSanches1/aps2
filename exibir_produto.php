<?php 
	require 'classes/produto_class.php';
	$p = new produto_class('formulario_produtos', 'localhost', 'root', '');

	if(isset($_GET['id']) && !empty($_GET['id'])) {
		$id = addslashes($_GET['id']);
		$dadosDoProduto = $p->buscarProdutoPorId($id);
		$imagensDoProduto = $p->buscarImagensPorId($id);

		$produto = (object)$dadosDoProduto[0];
	} else {
		header('location: produtos.ph p');
	}
?>

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
			width: 70%;
			margin: auto;
			font-family: arial;
		}
		h1{
			color: rgba(0,0,0,.8);
			border-bottom: 1px solid rgba(0,0,0,.1);
			height: 60px;
			line-height: 70px;
			margin-bottom: 40px;
		}
		#imagens{
			background-color: red;
		}
		.caixa-img{
			width: 15%;
			float: left;
			padding: 1%;
			background-color: rgb(123,104,238,.4);
			margin: 10px;
			height: 150px;
			cursor: pointer;
			height: auto;
		}
		img{
			width: 100%;
			height: auto;
		}
		p{
			width: 70%;
			text-align: justify;
			line-height: 30px;
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
		border: 1px solid rgba(0,0,0,.2);
		padding: 5px;
	}
	</style>
</head>
<body>
	<section>
		<div>
			<h1><?php echo $produto->nome_produto; ?></h1>
			<p><b>O que esse funcionário tem acesso: </b><?php echo $produto->descricao; ?></p>
		</div>
		<?php
			foreach($imagensDoProduto as $value) {
				?>
					<div id="imagens">
						<div class="caixa-img">
							<img src="imagens/<?php echo $value['nome_imagem']; ?>">
						</div>
					</div>
				<?php
			}
		?>
	<a href="index.php">Comparacao de biometria</a>
	<a href="produtos.php">Voltar a selecao de funcionários</a>
	<a href="cadastrar_imagens.php">Cadastrar biometrias</a>
	</section>
</body>
</html>