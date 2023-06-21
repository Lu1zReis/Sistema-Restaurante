<?php
	require_once '../connect/conn.php';

	require_once '../connect/pedidos/pedido.php';
	require_once '../connect/pedidos/pedidoDao.php';
	// instanciando as classes que vamos usar
	$pedido = new conn\Pedido(); 
	$pedidoDao = new conn\PedidoDao();

	session_start();

	if (isset($_SESSION['msg'])) {
		echo $_SESSION['msg'];
		unset($_SESSION['msg']);
	}

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

			// caso o usuario esteja só editando um pedido, em vez de criarmos atualizamos 
			if (isset($_SESSION['edicao-aux'])) {
				$pedido->setId((int)$_SESSION['edicao-aux']);
				if ($pedidoDao->update($pedido)) echo "<script>alert('Pedido Atualizado!');</script>";
				else echo "<script>alert('Não Foi Possível Atualizar os Dados do Pedido!');</script>";
			}
			// criando um novo pedido    
			else {
				if ($pedidoDao->create($pedido)) echo "<script>alert('Novo Pedido Adicionado!');</script>";
				else echo "<script>alert('Não Foi Possível Adicionar o Novo Pedido!');</script>";
			}
			// apagando os dados da sessao
			session_destroy();
			
			
		}
	}

	if (isset($_GET['entregue'])) {
		$msg = "";
		foreach ($pedidoDao->read() as $p) {
			if ($_GET['entregue'] == $p['id']) {
				if ($p['entregue'] != 'Pedido Entregue!') {
					$pedido->setEntregue('Pedido Entregue!');
					$msg = "<script>alert('Pedido foi entregue!');</script>";
				}
				else {
					$pedido->setEntregue("Preparando Pedido!");
					$msg = "<script>alert('Preparando Pedido!');</script>"; 
				} 
				$pedido->setNome($p['nome']);
				$pedido->setConta($p['conta']);
				$pedido->setHora($p['hora']);
				$pedido->setPago($p['pago']);
				$pedido->setId($p['id']);
			}
		}
		if ($pedidoDao->update($pedido)) {
			echo $msg;
			echo "<script>window.history.pushState('', '', '/Sistema-Restaurante/restaurante/dados/pendentes.php');</script>";
		}
	}

	if (isset($_GET['pago'])) {
		$msg = "";
		foreach ($pedidoDao->read() as $p) {
			if ($_GET['pago'] == $p['id']) {
				$pedido->setEntregue($p['entregue']);
				$pedido->setNome($p['nome']);
				$pedido->setConta($p['conta']);
				$pedido->setHora($p['hora']);
				if ($p['pago'] != "Pedido Pago!") {
					$pedido->setPago('Pedido Pago!');
					$msg = "<script>alert('Pedido foi pago!');</script>";
				}
				else {
					$pedido->setPago('Pagamento Pendente!');
					$msg = "<script>alert('Pagamento pendente!');</script>";
				}
				$pedido->setId($p['id']);
			}
		}
		if ($pedidoDao->update($pedido)) {
			echo $msg;
			echo "<script>window.history.pushState('', '', '/Sistema-Restaurante/restaurante/dados/pendentes.php');</script>";
		}
	}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Pendentes</title>
	<style>
		<?php
		/*
		arrumando a cor para os pedidos ficarem com um detalhe de se não foi entregue ou pago laranja
		caso tenha sido tudo feito, o pedido irá ficar verde
		*/
		$cont = 1;
		foreach ($pedidoDao->read() as $p) {
			if ($p['pago'] == 'Pedido Pago!') {
		?>
				.lista-detalhes-pagamento-<?php echo $cont; ?> {
					color: white;
					background-color: green;
					padding: 5px;
					margin: 10px;
					border-radius: 10px;
				}
		<?php
			} else {
		?>
				.lista-detalhes-pagamento-<?php echo $cont; ?> {
					color: white;
					background-color: orange;
					padding: 5px;
					margin: 10px;
					border-radius: 10px;
				}
		<?php
			}
			if ($p['entregue'] == 'Pedido Entregue!') {
		?>
				.lista-detalhes-pedido-<?php echo $cont; ?> {
					color: white;
					background-color: green;
					padding: 5px;
					margin: 10px;
					border-radius: 10px;	
				}
		<?php
			} else {
		?>
				.lista-detalhes-pedido-<?php echo $cont; ?> {
					color: white;
					background-color: orange;
					padding: 5px;
					margin: 10px;
					border-radius: 10px;	
				}
		<?php
			}
		$cont++;
		}
		?>
	</style>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body onload="time()">
	<header class="cabecalho">
		<div class="cabecalho-nome">Pedidos</div>
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
                    <p class="tip">Quantidade de Pedidos</p>
                    <p class="second-text"><?php echo $cont-1; ?></p>
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
									<a href="?entregue=<?php echo $p['id']; ?>">
										<div class="lista-detalhes-pedido-<?php echo $contagem; ?>">
										<?php	
											echo $p['entregue'];
										?>
										</div>
									</a>
									<a href="?pago=<?php echo $p['id']; ?>">
										<div class="lista-detalhes-pagamento-<?php echo $contagem; ?>">
										<?php	
											echo $p['pago'];
										?>
										</div>
									</a>
									<div class="preco">
									<?php	
										echo $p['conta'];
									?>
									</div>
								</div>
							</div>
							<div class="lista-edit">
								<a href="action.php?edit=<?php echo $p['id']; ?>" >	
									<div class="lista-edit-edit">
										Editar
									</div>
								</a>
								<a href="action.php?del=<?php echo $p['id']; ?>">
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