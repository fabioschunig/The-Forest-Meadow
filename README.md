# The Forest Meadow

Ponto central dos meus trabalhos artísticos, com foco no estudo e no desenvolvimento
de jogos de videogame, e espaço também para textos e desenhos.

Este repositório contém o **site** do projeto: o lugar onde publico ideias, o histórico
e os avanços de cada trabalho.

## Seções do site

- **Blog**: textos sobre o processo, estudos e avanços, com imagens, links e mídia.
- **Projetos**: portfólio de jogos, desenhos e textos. Cada projeto tem seu próprio
  diário de bordo, formado pelos posts ligados a ele, que registram sua evolução
  do início ao fim.
- **Sobre / Contato**: quem sou, como falar comigo e links para as redes sociais.

O site é bilíngue: português (`/pt`) e inglês (`/en`).

## Domínio

- **Projeto**: um trabalho artístico. Tem um tipo (jogo, desenho ou escrita) e um
  status (ideia, protótipo, em desenvolvimento, lançado ou pausado).
- **Post**: uma publicação do blog. Pode estar ligado a um projeto e, nesse caso,
  passa a fazer parte do diário de bordo dele. O conteúdo é montado em blocos
  (texto, imagem, galeria, vídeo, jogo embutido, citação).

## Stack

- [Laravel](https://laravel.com) (PHP 8.3+)
- [Filament](https://filamentphp.com), como painel administrativo para escrever e
  gerenciar o conteúdo
- Front-end autoral: Blade, CSS e JS próprios, compilados com Vite
- MySQL 5.7 (Percona), rodando em Docker no ambiente local
- Hospedagem compartilhada, com deploy via FTP

## Setup local

### Pré-requisitos

- PHP 8.3+ com as extensões `intl`, `gd`, `pdo_mysql`, `mbstring`, `xml`, `curl` e `zip`
- Composer 2
- Node.js 20.19+ ou 22.12+
- Docker com Docker Compose (o banco roda em container)

### Instalação

```bash
composer install
npm install

cp .env.example .env
# Edite o .env e defina DB_PASSWORD (e, se quiser, DB_DATABASE / DB_USERNAME).
# O Docker Compose lê essas mesmas variáveis para criar o banco.
php artisan key:generate

docker compose up -d
php artisan migrate
php artisan make:filament-user
```

### Rodando

Em dois terminais:

```bash
php artisan serve
npm run dev
```

- Site: <http://localhost:8000> (redireciona para `/pt` ou `/en` conforme o idioma do navegador)
- Painel: <http://localhost:8000/admin>

### Testes e estilo

```bash
php artisan test
vendor/bin/pint --test
```

Os testes usam SQLite em memória e não dependem do container do banco.

## Status

Fundação técnica e modelo de conteúdo concluídos: projetos e posts são escritos
no painel, em blocos e nos dois idiomas. Próximo passo: identidade visual e site
público.
