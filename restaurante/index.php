<?php
    require_once 'connect/conn.php';
    require_once 'connect/cardapio/cardapio.php';
    require_once 'connect/cardapio/cardapioDao.php';

    $cardapio = new conn\Cardapio();
    $cardapioDao = new conn\CardapioDao();

    /*
    $cardapio->setCod(2);
    $cardapio->setNome("suco de laranja");
    $cardapio->setDesc("suco natural de laranja");
    $cardapio->setValor(3.50);

    $cardapioDao->create($cardapio);
    */

    $tamanhoSessao=0;
    session_start();

    // mensagens
    if(isset($_SESSION['msg'])) {
        echo $_SESSION['msg'];
        unset($_SESSION['msg']);
    }

    if (isset($_SESSION['adicionado'])) {
        echo $_SESSION['adicionado'];
        header("Location: dados/pendentes.php");
    }

    // adicionando
    if (isset($_GET['adicionar'])) {
        $idProduto = (int) $_GET['adicionar'];
        $existe = false;
        $produtoNome;
        $produtoPreco;
        foreach ($cardapioDao->read() as $c) {
            if ($c['cod'] == $idProduto) {
                $existe = true;
                $produtoNome = $c['nome'];
                $produtoPreco = $c['valor'];
            }
        }
        if ($existe == true) {
            if (isset($_SESSION['carrinho'][$idProduto])) {
                $_SESSION['carrinho'][$idProduto]['qnt']++;
            } else {
                $_SESSION['carrinho'][$idProduto] = array('id'=>$idProduto, 'qnt'=>1, 'nome'=>$produtoNome, 'preco'=>$produtoPreco);
            }
        } else {
            die("Você tentou adicionar um item que não existe.");
        }
        echo "<script>window.history.pushState('', '', '/restaurante/');</script>";
    }
    
    // retirando um valor
    if (isset($_GET['menos'])) {
            $i = $_GET['menos'];
            if(isset($_SESSION['carrinho'][$i])) {
                $_SESSION['carrinho'][$i]['qnt']--;
                if($_SESSION['carrinho'][$i]['qnt'] <= 0) {
                    unset($_SESSION['carrinho'][$i]);
                }
            }
            echo "<script>window.history.pushState('', '', '/restaurante/');</script>";
    }

    // deletando
    if (isset($_GET['deletar'])) {
        $i = $_GET['deletar'];
        if(isset($_SESSION['carrinho'][$i])) {
            unset($_SESSION['carrinho'][$i]);
        }
        echo "<script>window.history.pushState('', '', '/restaurante/');</script>";
    }

?>

<!DOCTYPE html>
<html>
<head>
	<title>Sistema</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body onload="time()">
	<header class="cabecalho">
		<div class="cabecalho-nome">Cardápio</div>
        <div class="horario"></div>
        <script>
            function time()
            {
                today=new Date();
                h=today.getHours();
                m=today.getMinutes();
                s=today.getSeconds();
                document.querySelector(".horario").innerHTML=h+":"+m+":"+s;
                setTimeout('time()',500);
            }
        </script>
	</header>
	<hr>
	<form class="principal" name="formulario" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

        <div class="principal-busca">
			Busca <input type="search" id="sessao" name="btn-busca">
			<button id="sessao-busca" type="submit">buscar</button>
		</div>

        <div class="container">
            <div class="cards">
                <div class="card red">
                    <p class="tip">Dashboard</p>
                    <p class="second-text">Verificar os dados do dia</p>
                </div>
                <a href="dados/pendentes.php">
                    <div class="card blue">
	                    <p class="tip">Pendentes</p>
	                    <p class="second-text">Verificar os pedidos Pendentes</p>
                    </div>
                </a>
                <div class="card green">
                    <p class="tip">Quantidade de Itens</p>
                    <p class="second-text-Itens"></p>
                </div>
            </div>
                <div class="principal-items">
                    <?php
                    foreach($cardapioDao->read() as $c): 
                    ?>

                    <div class="principal-items-prod">
                        <div class="principal-items-detalhes">
                            <div class="principal-items-prod-cod">
                                #
                                <?php
                                echo $c['cod'];
                                ?>
                            </div>
                            <div class="principal-items-prod-detalhes">
                                <div class="principal-items-prod-detalhes-nom">
                                    Nome: 
                                    <div class="nome">
                                    <?php
                                        echo $c['nome'];
                                    ?>
                                    </div>
                                </div>
                                <div class="principal-items-prod-detalhes-preco">
                                    Preço: R$ 
                                    <?php
                                    echo $c['valor'];
                                    ?>
                                </div>
                            </div>
                        </div>
                        <fieldset>
                            <legend>Adicionar esse item</legend>
                            <a href="?adicionar=<?php echo $c['cod']; ?>">
                                <label class="switch">
                                    adicionar
                                </label>
                            </a>
                        </fieldset>
                    </div>

                    <?php
                    endforeach;
                    ?>
                </div>
                <div class="comprados">
                    <table class="comprados-items">
                    <div class="card cart">
                        <label class="title">Seu Carrinho</label>
                        <?php
                        $preco = 0;
                        if(isset($_SESSION['carrinho'])):
                            foreach($_SESSION['carrinho'] as $s) {
                        ?>
                            <div class="products">
                                <div class="product">
                                    <svg fill="none" viewBox="0 0 60 60" height="60" width="60" xmlns="http://www.w3.org/2000/svg">
                                        <rect fill="#FFF6EE" rx="8.25" height="60" width="60"></rect>
                                        <path stroke-linejoin="round" stroke-linecap="round" stroke-width="2.25" stroke="#FF8413" fill="#FFB672" d="M34.2812 18H25.7189C21.9755 18 18.7931 20.5252 17.6294 24.0434C17.2463 25.2017 17.0547 25.7808 17.536 26.3904C18.0172 27 18.8007 27 20.3675 27H39.6325C41.1993 27 41.9827 27 42.4639 26.3904C42.9453 25.7808 42.7538 25.2017 42.3707 24.0434C41.207 20.5252 38.0246 18 34.2812 18Z"></path>
                                        <path fill="#FFB672" d="M18 36H17.25C16.0074 36 15 34.9926 15 33.75C15 32.5074 16.0074 31.5 17.25 31.5H29.0916C29.6839 31.5 30.263 31.6754 30.7557 32.0039L33.668 33.9453C34.1718 34.2812 34.8282 34.2812 35.332 33.9453L38.2443 32.0039C38.7371 31.6754 39.3161 31.5 39.9084 31.5H42.75C43.9926 31.5 45 32.5074 45 33.75C45 34.9926 43.9926 36 42.75 36H42M18 36L18.6479 38.5914C19.1487 40.5947 20.9486 42 23.0135 42H36.9865C39.0514 42 40.8513 40.5947 41.3521 38.5914L42 36M18 36H28.5ZM42 36H39.75Z"></path>
                                        <path stroke-linejoin="round" stroke-linecap="round" stroke-width="2.25" stroke="#FF8413" d="M18 36H17.25C16.0074 36 15 34.9926 15 33.75C15 32.5074 16.0074 31.5 17.25 31.5H29.0916C29.6839 31.5 30.263 31.6754 30.7557 32.0039L33.668 33.9453C34.1718 34.2812 34.8282 34.2812 35.332 33.9453L38.2443 32.0039C38.7371 31.6754 39.3161 31.5 39.9084 31.5H42.75C43.9926 31.5 45 32.5074 45 33.75C45 34.9926 43.9926 36 42.75 36H42M18 36L18.6479 38.5914C19.1487 40.5947 20.9486 42 23.0135 42H36.9865C39.0514 42 40.8513 40.5947 41.3521 38.5914L42 36M18 36H28.5M42 36H39.75"></path>
                                        <path stroke-linejoin="round" stroke-linecap="round" stroke-width="3" stroke="#FF8413" d="M34.512 22.5H34.4982"></path>
                                        <path stroke-linejoin="round" stroke-linecap="round" stroke-width="2.25" stroke="#FF8413" d="M27.75 21.75L26.25 23.25"></path>
                                    </svg>
                                    <div>
                                    <span><?php echo $s['nome'] ?></span>
                                    <p>Extra Spicy</p>
                                    <p>No mayo</p>
                                    </div>
                                    <div class="quantity">
                                    <a href="?menos=<?php echo $s['id']; ?>">
                                        <svg fill="none" viewBox="0 0 24 24" height="14" width="14" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" stroke="#47484b" d="M20 12L4 12"></path>
                                        </svg>
                                    </a>
                                    <label><?php echo $s['qnt'] ?></label>
                                    <a href="?adicionar=<?php echo $s['id']; ?>">
                                        <svg fill="none" viewBox="0 0 24 24" height="14" width="14" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" stroke="#47484b" d="M12 4V20M20 12H4"></path>
                                        </svg>
                                    </a>
                                    </div>
                                    <label class="price small"><?php echo "R$ ".$s['preco'] ?></label>
                                </div>
                            </div>
                        <?php
                                $preco += $s['preco'] * $s['qnt'];
                            }
                            $_SESSION['preco_total'] = $preco;
                        endif;
                        ?>
                    </div>
                    </table>
                    <div class="card checkout">
                        <label class="title">Checkout</label>
                        <div class="checkout--footer">
                            <label class="price">total: <sup>R$</sup><?php echo $preco ?></label>

                            <a href="dados/pendentes.php?btn-continuar">
                                <button type="button" class="button" id="continuar">
                                    <span class="button__text">Continuar</span>
                                    <span class="button__icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" stroke="currentColor" height="24" fill="none" class="svg"><line y2="19" y1="5" x2="12" x1="12"></line><line y2="12" y1="12" x2="19" x1="5"></line></svg></span>
                                </button>
                            </a>
                        </div>
                    </div>
                </div>
		    </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

        <script type="text/javascript">
            var cont = 1;
            while (document.querySelector("#qnt-item" + cont)) {
                cont += 1;
            }
            const campo = document.querySelector(".second-text-Itens");
            campo.innerHTML = cont - 1;
        </script>

        
	</form>

</body>
</html>