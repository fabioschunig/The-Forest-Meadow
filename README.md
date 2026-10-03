# The Forest Meadow

Ponto central dos meus trabalhos artísticos, com foco no estudo e no desenvolvimento
de jogos de videogame, e espaço também para textos e desenhos.

Este repositório contém o **site** do projeto: o lugar onde publico ideias, o histórico
e os avanços de cada trabalho.

## Seções do site

- **Início**: a clareira animada, o projeto em destaque, as últimas anotações e os
  projetos.
- **Projetos**: portfólio de jogos, desenhos e textos. Cada projeto tem suas próprias
  anotações, formadas pelas anotações ligadas a ele, que registram sua evolução do
  início ao fim.
- **Anotações**: todas as anotações — processo, estudos e avanços, com imagens, links e mídia.
- **Sobre**: quem sou, como falar comigo e links para as redes sociais.

O site é bilíngue, com os endereços traduzidos:

| Página | Português | Inglês |
|---|---|---|
| Início | `/pt` | `/en` |
| Projetos | `/pt/projetos/{slug}` | `/en/projects/{slug}` |
| Anotações | `/pt/anotacoes/{slug}` | `/en/notes/{slug}` |
| Sobre | `/pt/sobre` | `/en/about` |
| RSS das anotações | `/pt/anotacoes/feed` | `/en/notes/feed` |

O feed em inglês traz só as anotações traduzidas. O mapa do site fica em
`/sitemap.xml`, e cada página informa aos buscadores e às redes suas versões nos
dois idiomas.

A raiz (`/`) leva ao idioma do navegador. Conteúdo sem versão em inglês aparece em
português no `/en`, com um aviso.

## Domínio

- **Projeto**: um trabalho artístico. Tem um tipo (jogo, desenho ou escrita) e um
  status (ideia, protótipo, em desenvolvimento, lançado ou pausado). Um projeto
  marcado como destaque aparece no pedestal da página inicial.
- **Anotação** (`Note`): pode estar ligada a um projeto e, nesse caso, passa a fazer
  parte das anotações dele. O conteúdo é montado em blocos
  (texto, imagem, galeria, vídeo, jogo embutido, citação).
- **Publicação**: definida pela data. Sem data é rascunho, com data futura é
  agendado, com data passada é publicado. Só o que está publicado aparece no site.

## Identidade visual

Uma clareira ensolarada ao lado de uma floresta viva, com ruínas antigas que a mata
está retomando. Tema escuro, com a luz como linguagem da interface: o que importa
está iluminado. Referências: uma foto de clareira com raios de sol e o Sacred Forest
Meadow de *Zelda: Ocarina of Time* (só como atmosfera).

As árvores, o pedestal e as ruínas da abertura ainda são formas feitas em código,
que serão substituídas por desenhos próprios. A imagem usada ao compartilhar links
(`public/images/og-default.jpg`, 1200×630) é um quadro dessa cena e deve ser
trocada junto.

## Stack

- [Laravel](https://laravel.com) (PHP 8.3+)
- [Filament](https://filamentphp.com), como painel administrativo para escrever e
  gerenciar o conteúdo
- Front-end autoral: Blade, CSS e JS próprios, compilados com Vite
- Fontes servidas pelo próprio site: Fraunces, Literata (licença OFL, em
  `resources/fonts`) e IBM Plex Mono
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

E-mail e redes sociais do rodapé e da página "Sobre" ficam em `config/site.php`.

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

## Deploy

Hospedagem compartilhada com cPanel, sem SSH. O código fica fora do `public_html`
e o domínio aponta para a pasta `public`:

```
/home/<usuario>/the-forest-meadow/          código
/home/<usuario>/the-forest-meadow/public    raiz do domínio
/home/<usuario>/the-forest-meadow/.env      só no servidor, criado à mão
/home/<usuario>/the-forest-meadow/public/uploads   imagens enviadas pelo painel
```

### Preparação (uma vez)

1. **cPanel > Selecionar versão do PHP**: 8.3 ou superior, com as extensões `intl`,
   `gd`, `pdo_mysql`, `mbstring`, `fileinfo`, `zip` e `openssl`.
2. **cPanel > Domínios**: raiz do documento em `the-forest-meadow/public` e
   "Forçar redirecionamento HTTPS" ativado.
3. **cPanel > Bancos de dados MySQL**: criar o banco e o usuário.
4. **cPanel > MySQL remoto**: liberar o seu IP, para rodar as migrations daqui.
5. Gerar a chave da aplicação com `php artisan key:generate --show` (só mostra a
   chave, não grava nada).
6. Criar o **`.env` do servidor** pelo gerenciador de arquivos, a partir do
   `.env.example`, com:
   - `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://<domínio>`
   - `APP_KEY=` a chave gerada
   - `DB_HOST=localhost` e as credenciais do banco
   - `SESSION_SECURE_COOKIE=true`, `LOG_LEVEL=warning`
7. Criar localmente um **`.env.production`** (ignorado pelo git) igual ao do
   servidor, mas com `DB_HOST` apontando para o host do MySQL remoto.
8. Depois do primeiro deploy: `php artisan migrate --env=production --force` e
   `php artisan make:filament-user --env=production`.

### A cada deploy

1. Com tudo commitado: `scripts/release.sh`. Gera
   `release/the-forest-meadow-<data>-<commit>.zip` com as dependências de produção,
   os assets compilados e os do painel.
2. Se houver migrations novas: `php artisan migrate --env=production --force`.
3. Enviar o `.zip` para `the-forest-meadow/` pelo gerenciador de arquivos,
   extrair por cima (sobrescrevendo) e apagar o `.zip` do servidor.

O pacote nunca contém `.env` nem `public/uploads`, então extrair por cima preserva
a configuração e as imagens. Para voltar atrás, extraia o pacote anterior (fica em
`release/`). Arquivos antigos em `public/build/assets` sobram a cada deploy e podem
ser apagados de vez em quando.

Vale ativar os backups do cPanel para o banco e para `public/uploads`.

## Status

Fundação técnica, modelo de conteúdo, site público e SEO concluídos: projetos e
anotações são escritos no painel e publicados no site nos dois idiomas, com RSS,
sitemap e prévias de compartilhamento. Pacote de deploy pronto; falta a primeira
publicação na hospedagem.
