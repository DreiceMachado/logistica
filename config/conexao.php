<?php
// sessao usada pra guardar o que foi escolhido no passo a passo do frete
session_start();

// conexao com o banco do xampp (usuario root sem senha)
$pdo = new PDO('mysql:host=localhost;dbname=logistica;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

require 'funcoes.php';
