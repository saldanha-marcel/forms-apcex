# Personas ApcéX - Sistema PHP com PostgreSQL

Sistema de mapeamento de personas desenvolvido em PHP com banco de dados PostgreSQL.

## Requisitos

- PHP 7.4 ou superior
- PostgreSQL 12 ou superior
- Extensão PDO para PostgreSQL habilitada

## Instalação

1. **Configurar o banco de dados:**
   ```bash
   psql -U postgres -c "CREATE DATABASE personas_db;"
   psql -U postgres -d personas_db -f create_table.sql
   ```

2. **Configurar credenciais:**
   - Edite o arquivo `.env` com suas credenciais do PostgreSQL:
   ```
   DB_HOST=localhost
   DB_PORT=5432
   DB_NAME=personas_db
   DB_USER=seu_usuario
   DB_PASS=sua_senha
   ```

3. **Executar o servidor PHP:**
   ```bash
   php -S localhost:8000
   ```

4. **Acessar:**
   Abra `http://localhost:8000/personas-apcex.php` no navegador.

## Estrutura do Banco

A tabela `personas` contém os seguintes campos:

- `id`: Chave primária (auto-incremento)
- `genero`: Gênero do cliente
- `idade`: Faixa etária
- `ticket`: Ticket médio (inteiro)
- `fv`, `fc`, `fg`: Frequência de consumo (vinhos, cervejas, gourmet)
- `forma`: Formas de consumo (JSON array)
- `atrai`: O que atrai na loja (JSON array)
- `produtos`: Produtos consumidos (JSON array)
- `motivo`: Motivações de compra (JSON array)
- `escolha`: Critérios de escolha (texto)
- `dor`: Dores e dificuldades (texto)
- `nome`: Nome da persona (opcional)
- `created_at`: Data de criação

## Funcionalidades

- Formulário responsivo com validação
- Salvamento automático no PostgreSQL
- Interface clean com design ApceX
- Progresso visual do preenchimento
- Modal de confirmação

## Desenvolvimento

Desenvolvido por ApceX - Tecnologia e estratégia.