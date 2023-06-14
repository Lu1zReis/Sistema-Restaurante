<?php 

namespace conn;

class PedidoDao {
    public function create(Pedido $d) {
        $sql = 'INSERT INTO pedidos (nome, conta, hora, entregue, pago) VALUES (?, ?, ?, ?, ?)';

        $stmt = Conexão::getConn()->prepare($sql);

        $stmt->bindValue(1, $d->getNome());
        $stmt->bindValue(2, $d->getConta());
        $stmt->bindValue(3, $d->getHora());
        $stmt->bindValue(4, $d->getEntregue());
        $stmt->bindValue(5, $d->getPago());

        if($stmt->execute()):
            return true;
        else:
            return false;
        endif;
    }

    public function read() {
        $sql = 'SELECT * FROM pedidos';

        $stmt = Conexão::getConn()->prepare($sql);
        $stmt->execute();

        if($stmt->rowCount() > 0):
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // enviando o array de forma invertida para mostrar o último cadastrado em primeiro
            // return array_reverse($resultado);
            return $resultado;
        else:
            return [];
        endif;
    }

    public function update(Pedido $d) {
        $sql = 'UPDATE pedidos SET nome = ?, conta = ?, hora = ?, entregue = ?, pago = ? WHERE id = ?';

        $stmt = Conexão::getConn()->prepare($sql);
        $stmt->bindValue(1, $d->getNome());
        $stmt->bindValue(2, $d->getConta());
        $stmt->bindValue(3, $d->getHora());
        $stmt->bindValue(4, $d->getEntregue());
        $stmt->bindValue(5, $d->getPago());
        $stmt->bindValue(6, $d->getId());
        if($stmt->execute()):
            return true;
        else:
            return false;
        endif;
    }

    public function delete($id) {
        $sql = 'DELETE FROM pedidos WHERE id = ?';
        $stmt = Conexão::getConn()->prepare($sql);
        $stmt->bindValue(1, $id);
        if($stmt->execute()):
            return true;
        else:
            return false;
        endif;
    }

    public function len () {
        $QUERY = "SELECT * FROM pedidos";
        $stmt = Conexão::getConn()->prepare($sql);
        $stmt->execute();

        return mysqli_num_rows($stmt);
    }

}
