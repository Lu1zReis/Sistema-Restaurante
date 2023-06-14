<?php
namespace conn;
class Pedido {
    private $id, $nome, $conta, $hora, $entregue, $pago;

    public function getId() {
        return $this->id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getConta() {
        return $this->conta;
    }

    public function getHora() {
        return $this->hora;
    }

    public function getEntregue() {
        return $this->entregue;
    }

    public function getPago() {
        return $this->pago;
    }

    public function setId($i) {
        $this->id = $i;
    }
    public function setNome($n) {
        $this->nome = $n;
    }
    public function setConta($c) {
        $this->conta = $c;
    }
    public function setHora($h) {
        $this->hora = $h;
    }
    public function setEntregue($e) {
        $this->entregue = $e;
    }
    public function setPago($p) {
        $this->pago = $p;
    }
}