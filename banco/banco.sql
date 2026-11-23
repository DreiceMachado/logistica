CREATE DATABASE IF NOT EXISTS logistica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE logistica;

CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao VARCHAR(255),
    peso DECIMAL(10,2) NOT NULL,
    valor DECIMAL(10,2) NOT NULL
);

CREATE TABLE enderecos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cep VARCHAR(9) NOT NULL,
    rua VARCHAR(150) NOT NULL,
    numero VARCHAR(10) NOT NULL,
    bairro VARCHAR(100) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    estado CHAR(2) NOT NULL
);

CREATE TABLE transportadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cnpj VARCHAR(18) NOT NULL,
    telefone VARCHAR(20),
    veiculo VARCHAR(50) NOT NULL,
    placa VARCHAR(8) NOT NULL
);

CREATE TABLE fretes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transportador_id INT NOT NULL,
    origem_id INT NOT NULL,
    destino_id INT NOT NULL,
    data_frete DATE NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (transportador_id) REFERENCES transportadores(id),
    FOREIGN KEY (origem_id) REFERENCES enderecos(id),
    FOREIGN KEY (destino_id) REFERENCES enderecos(id)
);

CREATE TABLE frete_produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    frete_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    FOREIGN KEY (frete_id) REFERENCES fretes(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);
