<?php
    require_once 'connect/conn.php';
    require_once 'connect/cardapio/cardapio.php';
    require_once 'connect/cardapio/cardapioDao.php';

    $cardapio = new conn\Cardapio();
    $cardapioDao = new conn\CardapioDao();

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
            if ($c['id'] == $idProduto) {
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
        echo "<script>window.history.pushState('', '', 'index.php');</script>";
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
            echo "<script>window.history.pushState('', '', 'index.php');</script>";
    }

    // deletando
    if (isset($_GET['deletar'])) {
        $i = $_GET['deletar'];
        if(isset($_SESSION['carrinho'][$i])) {
            unset($_SESSION['carrinho'][$i]);
        }
        echo "<script>window.history.pushState('', '', 'index.php');</script>";
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
                <a href="choose.html">
                    <div class="card green">
                        <p class="tip">Novo-Editar</p>
                        <p class="second-text-Itens">Adicionar ou editar do Cardápio</p>
                    </div>
                </a>
            </div>
                <div class="principal-items">
                    <?php
                    foreach($cardapioDao->read() as $c): 
                    ?>
                    
                        <div class="principal-items-prod">
                            <div class="card-prod">
                                <div class="card-prod-img">
                                    <img class="img" src="<?php echo "uploads/".$c['imagem']; ?>">
                                </div>
                                <div class="card-prod-info">
                                    <p class="text-prod-title"><?php echo $c['nome']; ?></p>
                                    <p class="text-prod-body"><?php echo $c['descricao']; ?></p>
                                </div>
                                <div class="card-prod-footer">
                                <span class="text-title">R$ <?php echo $c['valor']; ?></span>
                                <a href="?adicionar=<?php echo $c['id']; ?>">
                                <div class="card-prod-button">
                                    <svg class="svg-prod-icon" viewBox="0 0 20 20">
                                    <path d="M17.72,5.011H8.026c-0.271,0-0.49,0.219-0.49,0.489c0,0.271,0.219,0.489,0.49,0.489h8.962l-1.979,4.773H6.763L4.935,5.343C4.926,5.316,4.897,5.309,4.884,5.286c-0.011-0.024,0-0.051-0.017-0.074C4.833,5.166,4.025,4.081,2.33,3.908C2.068,3.883,1.822,4.075,1.795,4.344C1.767,4.612,1.962,4.853,2.231,4.88c1.143,0.118,1.703,0.738,1.808,0.866l1.91,5.661c0.066,0.199,0.252,0.333,0.463,0.333h8.924c0.116,0,0.22-0.053,0.308-0.128c0.027-0.023,0.042-0.048,0.063-0.076c0.026-0.034,0.063-0.058,0.08-0.099l2.384-5.75c0.062-0.151,0.046-0.323-0.045-0.458C18.036,5.092,17.883,5.011,17.72,5.011z"></path>
                                    <path d="M8.251,12.386c-1.023,0-1.856,0.834-1.856,1.856s0.833,1.853,1.856,1.853c1.021,0,1.853-0.83,1.853-1.853S9.273,12.386,8.251,12.386z M8.251,15.116c-0.484,0-0.877-0.393-0.877-0.874c0-0.484,0.394-0.878,0.877-0.878c0.482,0,0.875,0.394,0.875,0.878C9.126,14.724,8.733,15.116,8.251,15.116z"></path>
                                    <path d="M13.972,12.386c-1.022,0-1.855,0.834-1.855,1.856s0.833,1.853,1.855,1.853s1.854-0.83,1.854-1.853S14.994,12.386,13.972,12.386z M13.972,15.116c-0.484,0-0.878-0.393-0.878-0.874c0-0.484,0.394-0.878,0.878-0.878c0.482,0,0.875,0.394,0.875,0.878C14.847,14.724,14.454,15.116,13.972,15.116z"></path>
                                    </svg>
                                </div>
                                </a>
                                </div></div>
                            </div>
                    
                    <?php
                    endforeach;
                    ?>
                </div>
                <div class="comprados">
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
	</form>

</body>
</html>