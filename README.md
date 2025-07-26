# Sistema de Pedidos 

Este projeto é uma aplicação desenvolvida em **Laravel** para gerenciar tarefas, com suporte a usuários administradores e usuários comuns. Ele conta com funcionalidades de **filtros**, **autenticação**, e controle de permissões. e execução em ambiente **Docker**

---

## Tecnologias utilizadas
- Laravel 9
- Vue.js 3
- Docker + Docker-compose
- JWT
- MySQL
- Vite
- Pinia
- Axios
- Nginx
- Quasar
- PHPUnit (unitários)

## Funcionalidades

- Autenticação com JWT
- Filtros por titulo, descrição, status, data
- Inserção, Edição, Visualização, Exclusão de tarefas (somente admins)
- Controle de permissões (usuário/admin)
- Tela de login integrada ao backend

## Pré-requisitos

Certifique-se de ter as seguintes ferramentas instaladas:

- [Docker instalado]
- [Docker Compose instalado]

---


## Instalação

1. **Clone o repositório**

```bash
git clone https://github.com/lucaasbritto/petiko.git
cd petiko
```

2. **Copie o arquivo de ambiente para produção**
```bash
  cp app/.env.example app/.env
```

3. **Configure o nome do banco em .env**
```env
DB_DATABASE=laravel
```

4. **Suba os containers com Docker**
```shell
  docker-compose up --build -d
```

5. **Entre no container**
```shell
  docker exec -it laravel_app_petiko bash
```
6. **Instale as dependências do PHP**
```shell
  composer install
```

7. **Gere a chave da aplicação**
```shell
  php artisan key:generate
```

8. **Gere a chave JWT**
```shell
  php artisan jwt:secret
```

9. **Rode as migrações e os seeders**
```shell
  php artisan migrate --seed
```

10. **Os Seeders criam**
  - 1 admin
  - 5 usuários
  - Tarefas


## Acessos
  - Front-end: http://localhost:5173
  - Back-end (API): http://localhost:8080/api


## Usuários para acesso do sistema
| Tipo    | Email                            | Senha                      |
|---------|----------------------------------|----------------------------|
| Admin   | admin@teste.com                  | 123456                     |
| Usuario | teste@teste.com                  | 123456                     |


## Rodando os Testes
- Foram criados **testes unitários**.
- Os testes usam o arquivo .env.testing
- Por segurança, o arquivo `.env.testing` **não está incluído no versionamento do Git** (ignorado via `.gitignore`).
- Cada desenvolvedor deve criá-lo localmente na pasta raiz 'petiko' com o comando:

1. **Criando o .env.testing**
```bash
cp app/.env.example app/.env.testing
```

2. **Configure o nome do banco em .env.testing**
```env
DB_DATABASE=laravel_testing
```

3. **Acesse o container**
```bash
  docker exec -it laravel_app_petiko bash
```

4. **Gere a chave da aplicação para testes**
```bash
  php artisan key:generate --env=testing
```

5. **Gere a chave JWT para testes**
```bash
  php artisan jwt:secret --env=testing
```

6. **Execute o teste**
```bash
  php artisan test
```


## Variáveis obrigatórias no .env

```env
APP_NAME=Laravel
APP_KEY=           # Gerado via php artisan key:generate
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel
JWT_SECRET=        # Gerado via php artisan jwt:secret
```
- (Essas informações do banco estão pre configuradas no Docker)


## Endpoints principais

| Método | Rota                         | Ação                                       |
|--------|------------------------------|--------------------------------------------|
| POST   | /api/login                   | Login e geração de token JWT               |
| GET    | /api/me                      | Retorna o usuário autenticado              |
| GET    | /api/task                    | Lista tarefas (com filtros)                |
| POST   | /api/task/                   | Cria uma nova tarefa                       |
| GET    | /api/task/{id}               | Detalhes de um pedido de viagem            |
| PATCH  | /api/task/{id}/updateStatus  | Atualiza o status da tarefa                |
| PUT    | /api/task/{id}/              | Atualiza a tarefa                          |
| GET    | /api/users                   | Lista todos os usuários                    |


##  Comandos úteis

- Parar containers: `docker-compose down`
- Subir containers: `docker-compose up --build -d`
- Acessar o container: `docker exec -it laravel_app_petiko bash`
- Rodar seeders novamente: `php artisan migrate:fresh --seed`