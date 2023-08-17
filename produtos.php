<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<style type="text/css">
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
	div{
		width: 15%;
		float: left;
		padding: 1%;
		background-color: rgb(123,104,238,.4);
		margin: 10px;
	}
	img{
		width: 100%;
		height: 150px;
	}

	h2{
		font-size: 12pt;
		color: white;
		text-align: center;
		background-color: rgba(0,0,0,.5);
		padding: 10px 0px;
		font-weight: normal;
	}
	p{
		font-size: 10pt;
	}
	.botao{
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
		<?php
			require 'classes/produto_class.php';
			$p = new produto_class('formulario_produtos', 'localhost', 'root', '');
			$dadosProduto = $p->buscarProdutos();
			if (!$dadosProduto) {
				echo 'ainda não há produtos cadastrados';
			} else {
				foreach($dadosProduto as $value) {
					?>
						<a href="exibir_produto.php?id=<?php echo $value['id_produto']; ?>">
							<div>
								<img src="imagens/<?php echo $value['foto_capa']; ?>">
								<h2><?php echo $value['nome_produto']; ?></h2>
							</div>
						</a>
					<?php
				}
				?>
					<a class='botao' href="cadastrar_imagens.php">Cadastro de funcionários</a>
					<a class='botao' href="index.php">Comparar biometrias</a>
				<?php
			}
		?>
	</section>
</body>
</html>