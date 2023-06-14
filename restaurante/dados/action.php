<?php
    require_once '../connect/conn.php';

    require_once '../connect/pedidos/pedido.php';
    require_once '../connect/pedidos/pedidoDao.php';
    require_once '../connect/cardapio/cardapio.php';
    require_once '../connect/cardapio/cardapioDao.php';
    
    session_start();

    // funcao para pegar os elementos corretos do banco de dados
    function getElementos ($frase,$id) {
        $objeto = array();
        $aux = 0;
        $pos = 0;
        while ($aux < strlen($frase)-1) {
            $nome = "";
            $qtd = "";
       
            if ($frase[$aux] == '.') $aux += 2;
            while ($aux < strlen($frase) and $frase[$aux] != 'x') {
                
                $qtd = $qtd.$frase[$aux];
                $aux += 1;  
            }
            
            $aux += 2;
            while ( $aux < strlen($frase) and $frase[$aux] != '.') {
                
                $nome = $nome.$frase[$aux];
                $aux += 1;
            }
            
            if ($qtd > 0) {
                $cardapioDao = new conn\CardapioDao();
                foreach ($cardapioDao->read() as $c) {
                    if ($c['nome'] == $nome) {
                        $objeto[$pos] = array ('id' => $c['cod'], 'nome' => $nome, 'qtd' => $qtd, 'preco' => $c['valor']);
                        $pos += 1;
                    }
                }
            }
        }
        return $objeto;
    }

    if (isset($_GET['edit'])) {
        $pedidos = new conn\PedidoDao();
        $elementos = array();
        $cont = 0; 
        foreach ($pedidos->read() as $p) {
            if ($p['id'] == $_GET['edit']) $elementos[] = getElementos($p['nome'], $cont);
        }
        
        for ($i = 0; $i < sizeof($elementos); $i++) {
            for ($j = 0; $j < sizeof($elementos[$i]); $j++) {
                // colocando os itens de volta ao carrinho para fazer a edição
                unset($_SESSION['carrinho']); // limpando a ultima sessao de carrinho para não ter outro pedido em andamento ao mesmo tempo
                $_SESSION['carrinho'][$elementos[$i][$j]['id']] = array(
                    'id'=>$elementos[$i][$j]['id'], 
                    'qnt'=>$elementos[$i][$j]['qtd'], 
                    'nome'=>$elementos[$i][$j]['nome'], 
                    'preco'=>$elementos[$i][$j]['preco']
                );
            }
        }
        // criando uma nova sessao para auxiliar na edicao de um pedido já existente, em vez de apagarmos o pedido atual e criar um novo
        $_SESSION['edicao-aux'] =  (int) $_GET['edit'];
        header ("Location: ../index.php");
    }
    else if (isset($_GET['del'])) {
        $pedidos = new conn\PedidoDao();
        $pedidos->delete($_GET['del']);
        $_SESSION['msg'] = "<script>alert('Pedido Deletado Com Sucesso!');</script>";
        header("Location: pendentes.php");
    }
?>