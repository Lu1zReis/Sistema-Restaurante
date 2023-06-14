<?php
	require_once '../connect/conn.php';

	require_once '../connect/pedidos/pedido.php';
	require_once '../connect/pedidos/pedidoDao.php';
	// instanciando as classes que vamos usar
	$pedido = new conn\Pedido(); 
	$pedidoDao = new conn\PedidoDao();

	session_start();

	if(isset($_GET['btn-continuar'])) {
		if(empty($_SESSION['carrinho'])) {
			$_SESSION['msg'] = "<script>alert('Adicione algo ao carrinho antes de continuar!');</script>";
			header("Location: ../index.php");
		} else {
			// instanciando as classes que vamos usar
			$datetime = new DateTime();
			// mudando o horario para o horario local
			$timezone = new DateTimeZone('America/Cuiaba');
			
			$datetime->setTimezone($timezone);
			// formatando o horario
			$hora = $datetime->format("H:i");

			$lista = ""; // variavel aux para colocarmos os valores como uma string direta
			foreach ($_SESSION['carrinho'] as $s) {
				$lista = $lista.$s['qnt']."x ".$s['nome'].". ";
			}
			
			// setando os valores no objeto pedido
			$pedido->setNome($lista);
			$pedido->setConta($_SESSION['preco_total']);
			$pedido->setHora($hora);
			$pedido->setEntregue("Preparando Pedido!");
			$pedido->setPago("Pagamento Pendente!");
			// criando um novo pedido
			$pedidoDao->create($pedido);
			// apagando os dados da sessao
			session_destroy();
			// redirecionando
			$_SESSION['adicionado'] = "<script>alert('Novo Pedido Adicionado!');</script>";
			header('Location: ../index.php');
		}
	}

?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Pendentes</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body onload="time()">
	<header class="cabecalho">
		<div class="cabecalho-nome">Pedidos</div>
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
	<main>
		<div class="principal-busca">
			Busca <input type="search" name="">
		</div>

        <div class="container">
            <div class="cards">
                <div class="card red">
                    <p class="tip">Dashboard</p>
                    <p class="second-text">Verificar os dados do dia</p>
                </div>
				<a href="../index.php">
                	<div class="card blue">
                    	<p class="tip">Cardápio</p>
                    	<p class="second-text">Adicionar uma novo pedido</p>
					</div>
				</a>
                <div class="card green">
                    <p class="tip">Hover me</p>
                    <p class="second-text">Lorem Ipsum</p>
                </div>
            </div>
			<div class="organizando">
				<?php
				$contagem = 1;
				foreach ($pedidoDao->read() as $p) {
				?>
					<div class="lista">
						<div class="lista-elemento">
							<div class="lista-elemento-info">
								<div class="numero">
									#
									<?php
									echo $contagem;
									?>
								</div>
								<div class="horas">
									<?php
									echo date('H:i', strtotime($p['hora']));
									?>
								</div>
							</div>
							<div class="lista-elemento-juncao">

								<div class="lista-linha">
								<?php	
									echo $p['nome'];
								?>
								</div>
								<div class="lista-detalhes">
									<div class="lista-detalhes-pedido">
									<?php	
										echo $p['entregue'];
									?>
									</div>
									<div class="lista-detalhes-pagamento">
									<?php	
										echo $p['pago'];
									?>
									</div>
									<div class="preco">
									<?php	
										echo $p['conta'];
									?>
									</div>
								</div>
							</div>
							<div class="lista-edit">
								<a href="action.php?edit=<?php echo $p['id']; ?>">	
									<div class="lista-edit-edit">
										Editar
									</div>
								</a>
								<a href="">
									<div class="lista-edit-del">
										Deletar
									</div>
								</a>
							</div>
						</div>
					</div>
				<?php
				$contagem += 1;
				}
				?>
			</div>
    	</div>

	</main>
</body>
</html>