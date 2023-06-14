<?php 

namespace conn;

class cardapioDao {
    public function create(Cardapio $d) {
        $sql = 'INSERT INTO cardapio (cod, nome, descricao, valor) VALUES (?, ?, ?, ?)';

        $stmt = Conexão::getConn()->prepare($sql);

        $stmt->bindValue(1, $d->getCod());
        $stmt->bindValue(2, $d->getNome());
        $stmt->bindValue(3, $d->getDesc());
        $stmt->bindValue(4, $d->getValor());

        if($stmt->execute()):
            return true;
        else:
            return false;
        endif;
    }

    public function read() {
        $sql = 'SELECT * FROM cardapio';

        $stmt = Conexão::getConn()->prepare($sql);
        $stmt->execute();

        if($stmt->rowCount() > 0):
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // enviando o array de forma invertida para mostrar o último cadastrado em primeiro
            return $resultado;
        else:
            return [];
        endif;
    }

    public function update(Cardapio $d) {
        $sql = 'UPDATE cardapio SET cod = ?, nome = ?, descricao = ?, valor = ? WHERE id = ?';

        $stmt = Conexão::getConn()->prepare($sql);
        $stmt->bindValue(1, $d->getCod());
        $stmt->bindValue(2, $d->getNome());
        $stmt->bindValue(3, $d->getDesc());
        $stmt->bindValue(4, $d->getValor());
        $stmt->bindValue(5, $d->getId());

        if($stmt->execute()):
            return true;
        else:
            return false;
        endif;
    }

    public function delete($id) {
        $sql = 'DELETE FROM cardapio WHERE id = ?';
        $stmt = Conexão::getConn()->prepare($sql);
        $stmt->bindValue(1, $id);
        if($stmt->execute()):
            return true;
        else:
            return false;
        endif;
    }

    public function len () {
        $sql = "SELECT * FROM cardapio";
        $stmt = Conexão::getConn()->prepare($sql);
        $stmt->execute();

        return mysqli_num_rows($stmt);
    }

}
