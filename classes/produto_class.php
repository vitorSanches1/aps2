<?php

function var_dump_pretty($data, $label='', $return = false) {
    $debug = debug_backtrace();
    $callingFile = $debug[0]['file'];
    $callingFileLine = $debug[0]['line'];

    ob_start();
    var_dump($data);
    $c = ob_get_contents();
    ob_end_clean();

    $c = preg_replace("/\r\n|\r/", "\n", $c);
    $c = str_replace("]=>\n", '] = ', $c);
    $c = preg_replace('/= {2,}/', '= ', $c);
    $c = preg_replace("/\[\"(.*?)\"\] = /i", "[$1] = ", $c);
    $c = preg_replace('/  /', "    ", $c);
    $c = preg_replace("/\"\"(.*?)\"/i", "\"$1\"", $c);
    $c = preg_replace("/(int|float)\(([0-9\.]+)\)/i", "$1() <span class=\"number\">$2</span>", $c);

    // Syntax Highlighting of Strings. This seems cryptic, but it will also allow non-terminated strings to get parsed.
    $c = preg_replace("/(\[[\w ]+\] = string\([0-9]+\) )\"(.*?)/sim", "$1<span class=\"string\">\"", $c);
    $c = preg_replace("/(\"\n{1,})( {0,}\})/sim", "$1</span>$2", $c);
    $c = preg_replace("/(\"\n{1,})( {0,}\[)/sim", "$1</span>$2", $c);
    $c = preg_replace("/(string\([0-9]+\) )\"(.*?)\"\n/sim", "$1<span class=\"string\">\"$2\"</span>\n", $c);

    $regex = array(
        // Numberrs
        'numbers' => array('/(^|] = )(array|float|int|string|resource|object\(.\)|\&amp;object\(.\))\(([0-9\.]+)\)/i', '$1$2(<span class="number">$3</span>)'),
        // Keywords
        'null' => array('/(^|] = )(null)/i', '$1<span class="keyword">$2</span>'),
        'bool' => array('/(bool)\((true|false)\)/i', '$1(<span class="keyword">$2</span>)'),
        // Types
        'types' => array('/(of type )\((.*)\)/i', '$1(<span class="type">$2</span>)'),
        // Objects
        'object' => array('/(object|\&amp;object)\(([\w]+)\)/i', '$1(<span class="object">$2</span>)'),
        // Function
        'function' => array('/(^|] = )(array|string|int|float|bool|resource|object|\&amp;object)\(/i', '$1<span class="function">$2</span>('),
    );

    foreach ($regex as $x) {
        $c = preg_replace($x[0], $x[1], $c);
    }

    $style = '
    /* outside div - it will float and match the screen */
    .dumpr {
        margin: 2px;
        padding: 2px;
        background-color: #fbfbfb;
        float: left;
        clear: both;
    }
    /* font size and family */
    .dumpr pre {
        color: #000000;
        font-size: 9pt;
        font-family: "Courier New",Courier,Monaco,monospace;
        margin: 0px;
        padding-top: 5px;
        padding-bottom: 7px;
        padding-left: 9px;
        padding-right: 9px;
    }
    /* inside div */
    .dumpr div {
        background-color: #fcfcfc;
        border: 1px solid #d9d9d9;
        float: left;
        clear: both;
    }
    /* syntax highlighting */
    .dumpr span.string {color: #c40000;}
    .dumpr span.number {color: #ff0000;}
    .dumpr span.keyword {color: #007200;}
    .dumpr span.function {color: #0000c4;}
    .dumpr span.object {color: #ac00ac;}
    .dumpr span.type {color: #0072c4;}
    ';

    $style = preg_replace("/ {2,}/", "", $style);
    $style = preg_replace("/\t|\r\n|\r|\n/", "", $style);
    $style = preg_replace("/\/\.?\*\//i", '', $style);
    $style = str_replace('}', '} ', $style);
    $style = str_replace(' {', '{', $style);
    $style = trim($style);

    $c = trim($c);
    $c = preg_replace("/\n<\/span>/", "</span>\n", $c);

    if ($label == ''){
        $line1 = '';
    } else {
        $line1 = "<strong>$label</strong> \n";
    }

    $out = "\n<!-- Dumpr Begin -->\n".
        "<style type=\"text/css\">".$style."</style>\n".
        "<div class=\"dumpr\">
        <div><pre>$line1 $callingFile : $callingFileLine \n$c\n</pre></div></div><div style=\"clear:both;\">&nbsp;</div>".
        "\n<!-- Dumpr End -->\n";
    if($return) {
        return $out;
    } else {
        echo $out;
    }
}

class produto_class{

    private $pdo;

    public function __construct($dbname, $host, $user, $senha) {
        try {
            $this->pdo = new PDO("mysql:dbname=".$dbname.";host=".$host, $user, $senha);
        } catch(PDOException $e) {
            echo "erro com banco de dados: " . $e->getMessage();
        } catch (Exception $e) {
            echo "erro generico: " . $e->getMessage();
        }
    }

    public function enviarProduto($nome, $descricao, $fotos = []) {
        //inserir produto (tabela produto)
        $cmd = $this->pdo->prepare('INSERT INTO produtos (nome_produto, descricao) values (:n, :d)');
        $cmd->bindValue(':n', $nome);
        $cmd->bindValue(':d', $descricao);
        $cmd->execute();
        $id_produto = $this->pdo->LastInsertId();

        if(count($fotos) > 0) {
            for ($i=0; $i < count($fotos); $i++) {
                //inserir imagens do produto (tabela imagens)
                $nome_foto = $fotos[$i];

                $cmd = $this->pdo->prepare('INSERT INTO imagens (nome_imagem, fk_id_produto) values (:n, :fk)');
                $cmd->bindValue(':n', $nome_foto);
                $cmd->bindValue('fk', $id_produto);
                $cmd->execute();
            }
        }
    }

    public function buscarProdutos() {
        $cmd = $this->pdo->prepare('SELECT *, (SELECT nome_imagem from imagens where fk_id_produto = produtos.id_produto LIMIT 1) as foto_capa from produtos');
        $cmd->execute();

        if($cmd->rowCount() > 0) {
            $dados = $cmd->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $dados= [];
        }
        return $dados;

    }

    public function buscarProdutoPorId($id) {
        $cmd = $this->pdo->prepare('SELECT * FROM produtos where id_produto = :id');
        $cmd->bindValue(':id', $id);
        $cmd->execute();

        if($cmd->rowCount() > 0) {
            $dados = $cmd->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $dados= [];
        }
        return $dados;
    }

    public function buscarImagensPorId($id) {
        $cmd = $this->pdo->prepare('SELECT nome_imagem FROM imagens where fk_id_produto = :id');
        $cmd->bindValue(':id', $id);
        $cmd->execute();

        if($cmd->rowCount() > 0) {
            $dados = $cmd->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $dados= [];
        }
        return $dados;
    }

    public function compararImagens ($nome, $fotos=[]) {
        $cmd = $this->pdo->prepare('SELECT nome_imagem, fk_id_produto FROM imagens');
        $cmd->execute();

        $cmp = $this->pdo->prepare('SELECT nome_produto FROM produtos');
        $cmp->execute();

        if($cmd->rowCount() > 0 && $cmp->rowCount() > 0) {
            $dadosImagens = $cmd->fetchAll(PDO::FETCH_ASSOC);
            $dadosProdutos = $cmp->fetchAll(PDO::FETCH_ASSOC);

            foreach ($dadosProdutos as $dados) {
                foreach($dados as $dadosArray) {
                    $arraySimplificadoDadosProdutos[] = $dadosArray;
                }
            }

            $bool = in_array($nome, $arraySimplificadoDadosProdutos);
            if ($bool) {
                foreach ($dadosImagens as $dados) {
                    foreach($dados as $dadosArray) {
                        $arraySimplificadoDadosImagens[] = $dadosArray;
                    }
                }

                foreach ($fotos as $foto) {
                    if (in_array($foto, $arraySimplificadoDadosImagens)) {
                        $key = array_search($foto, $arraySimplificadoDadosImagens);
                        return $arraySimplificadoDadosImagens[$key+1];
                    }
                }
            } else {
                $e = 'Este funcionario nome de funcionario não existe';
                return $e;
            }
        }
    }
}
?>