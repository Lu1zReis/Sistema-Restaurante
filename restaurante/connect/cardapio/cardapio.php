<?php
namespace conn;
class Cardapio {
    private $id, $cod, $nome, $desc, $valor, $img;

    public function getId() {
        return $this->id;
    }

    public function  getCod() {
        return $this->cod;
    }

    public function  getNome() {
        return $this->nome;
    }

    public function  getDesc() {
        return $this->desc;
    }

    public function getValor() {
        return $this->valor;
    }

    public function getImg() {
        return $this->img;
    }

    public function setId($i) {
        $this->id = $i;
    }
    public function setCod($c) {
        $this->cod = $c;
    }
    public function setNome($n) {
        $this->nome = $n;
    }
    public function setDesc($d) {
        $this->desc = $d;
    }
    public function setValor($v) {
        $this->valor = $v;
    }
    public function setImg($i) {
        $this->img = $i;
    }
}