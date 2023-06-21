<?php

    require_once '../connect/conn.php';
    require_once '../connect/cardapio/cardapio.php';
    require_once '../connect/cardapio/cardapioDao.php';

    session_start();
    
    Class Filtro {
        private $nome, $cod, $desc, $preco, $img, $erros = array();
        function __construct($n, $c, $d, $p, $i)
        {
            if (empty($n)) $this->erros[] = "<li>Adicione um nome ao produto!</li>";
            else $this->nome = $n; 
            if (empty($c)) $this->erros[] = "<li>Adicione um código ao produto!</li>";
            else {
                $cardapioDao = new conn\CardapioDao();
                // testando para ver se o código inserido é repetido ou não
                foreach ($cardapioDao->read() as $n) {
                    if ($n['cod'] == $c) $this->erros[] = "<li>O código inserido ao produto já existe, coloque um diferente!</li>";
                }
                if (empty($this->erros)) $this->cod = $c;
            }
            if (empty($d)) $this->erros[] = "<li>Adicione uma descrição ao produto!</li>";
            else $this->desc = $d;
            if (empty($p)) $this->erros[] = "<li>Adicione um nome ao produto!</li>";
            else $this->preco = $p;
            if (empty($i)) $this->erros[] = "<li>Adicione um nome ao produto!</li>";
        }
        public function executa () {
            if (!empty($this->erros)) {
                foreach ($this->erros as $erro) {
                    echo $erro;
                }
            }
            else {
                $cardapio = new conn\Cardapio();
                $cardapioDao = new conn\CardapioDao();
                
                $extensao = pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION);
                $novo_nome = md5(uniqid($_FILES['arquivo']['name'])).".".$extensao; 
                $dir = "uploads/";
                move_uploaded_file($_FILES['arquivo']['tmp_name'], $dir.$novo_nome);
                $this->img = $novo_nome;
            
                $cardapio->setNome($this->nome);
                $cardapio->setCod($this->cod);
                $cardapio->setDesc($this->desc);
                $cardapio->setValor($this->preco);
                $cardapio->setImg($this->img);
                
                echo $cardapio->getNome();
                echo $cardapio->getCod();
                echo $cardapio->getDesc();
                echo $cardapio->getValor();
                echo $cardapio->getImg();
                
                if ($cardapioDao->create($cardapio)) {
                    $_SESSION['msg'] = "<script>alert('Novo Item adicionado ao cardápio');</script>";
                    header("Location: ../index.php");
                } else {
                    $_SESSION['msg'] = "<script>alert('Não foi possível adicionar este item ao cardápio');</script>";
                    header("Location: ../index.php");
                }
                
            }
        }
    }
    // sessao da imagem
    if (isset($_POST['btn-add'])) {
        $filtro = new Filtro($_POST['nome'],$_POST['cod'],$_POST['descricao'],$_POST['preco'],$_FILES['arquivo']);
        $filtro->executa();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Item</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body onload="time()">
    <header class="cabecalho">
		<div class="cabecalho-nome">Novo Item</div>
        <div class="horario"></div>
        <script>
            function formata(t) {
                if (t > 9) {
                    return t
                } else {
                    return "0"+t;
                }
            }
            function time()
            {
                today=new Date();
                h=today.getHours();
                m=today.getMinutes();
                s=today.getSeconds();
                document.querySelector(".horario").innerHTML=formata(h)+":"+formata(m)+":"+formata(s);
                setTimeout('time()',500);
            }
        </script>
	</header>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" class="form">
        Código<input type="number" name="cod"><br>
        Nome<input type="name" name="nome"><br>
        Descricao <textarea name="descricao"></textarea><br>
        Preço <input name="preco" type="number" step="any"><br>
        Imagem <input type="file" required name="arquivo"><br>
        <button type="submit" name="btn-add">Enviar</button><br>
    </form>
</body>
</html>