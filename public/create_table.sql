-- Criar banco de dados
CREATE DATABASE personas_db;

-- Usar o banco
\c personas_db;

-- Criar tabela para personas
CREATE TABLE personas (
    id SERIAL PRIMARY KEY,
    genero VARCHAR(20),
    idade VARCHAR(20),
    ticket INTEGER,
    fv VARCHAR(20),
    fc VARCHAR(20),
    fg VARCHAR(20),
    forma JSONB,
    atrai JSONB,
    produtos JSONB,
    motivo JSONB,
    escolha TEXT,
    dor TEXT,
    nome VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Criar índices para melhor performance
CREATE INDEX idx_genero ON personas(genero);
CREATE INDEX idx_idade ON personas(idade);
CREATE INDEX idx_created_at ON personas(created_at);