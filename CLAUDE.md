# CLAUDE.md

O domínio, as seções do site e a stack estão descritos no `README.md`, que é a fonte
de verdade do projeto. Este arquivo trata só de como trabalhar no código.

## Idioma
- Código, nomes, comentários e textos técnicos: inglês.
- Commits: português, com prefixo convencional (`feat:`, `fix:`, `docs:`, `chore:`...).
- Textos visíveis no site: sempre nos dois idiomas (`lang/pt_BR` e `lang/en`), nunca
  texto fixo em um idioma só nas views.

## Convenções
- Seguir os padrões do Laravel e do Filament antes de criar abstrações próprias.
- Campos de conteúdo traduzíveis usam `spatie/laravel-translatable`.
- O front é autoral: nada de temas, kits de UI ou Tailwind no site público.
  O Filament é usado só no `/admin`.

### Rotas e idiomas
- `routes/web.php` registra um grupo de rotas por idioma, em loop sobre
  `config('app.locales')` (prefixo da URL → locale interno: `pt` → `pt_BR`).
- Segmentos de URL vêm de `lang/*/routes.php`; nomes de rota levam o prefixo
  (`pt.projects.show`). Mudar um segmento quebra links já publicados.
- Nunca escrever URL fixa nas views: usar `localized_route()` e, para o seletor de
  idioma, `switch_locale_url()` (`app/helpers.php`).
- Idioma padrão: `default_locale()` / `default_locale_prefix()`. Nunca
  `config('app.locale')` em código que roda numa requisição: o `App::setLocale()`
  reescreve esse valor em toda página `/en`.
- Rotas fixas de uma seção (ex.: `.../feed`) vêm antes de `.../{slug}` no grupo.

### Conteúdo público
- Consultas do site sempre com o escopo `published()`; rascunho e agendado dão 404.
- Página de projeto ou anotação passa ao layout `:model`, `:image` (capa) e, se for
  o caso, `type="article"` e `:published-at`. O `<x-seo>` usa isso para canonical,
  hreflang e Open Graph, e só anuncia os idiomas em que o conteúdo existe.
- Datas pelo componente `<x-date>` (converte de UTC para `app.display_timezone`).
- Arquivos do disco `uploads` pelo helper `upload_url()`.
- Novo tipo de bloco: bloco no `App\Filament\Forms\ContentBuilder` + componente
  `resources/views/components/blocks/{tipo}.blade.php`. Tipo sem componente é ignorado.
- HTML do editor rico só passa para a view por `RichContentRenderer::toHtml()`, que
  sanitiza. Nunca `{!! !!}` direto em conteúdo vindo do painel.

### Front-end
- Cores e medidas como tokens em `resources/css/tokens.css`; um arquivo por
  componente em `resources/css/components/`.
- JS em módulos ES em `resources/js/`; cada módulo só age se o elemento dele existir.
- Fontes locais pelo provider `local()` do `laravel-vite-plugin`. Não trocar a
  Fraunces por `google()`: esse provider só pede os eixos `ital`/`wght` e perde
  `SOFT`, `WONK` e `opsz`.
- Respeitar `prefers-reduced-motion` em toda animação.

## Verificação (antes de dizer "pronto")
- `php artisan test`
- `vendor/bin/pint --test`
- `npm run build`
- Banco local: `docker compose up -d` (Percona 5.7)

## Restrições
- Compatibilidade com MySQL 5.7: não usar recursos do 8.0 (CTEs, window
  functions, colunas `JSON` com índice funcional, etc.).
- Deploy é via FTP, sem SSH: nada que dependa de rodar comandos no servidor
  (filas com worker, cron, `artisan` remoto) sem uma alternativa definida.
- Uploads sempre no disco `uploads` (`public/uploads`), nunca `storage:link`.
  O deploy por FTP não pode sobrescrever essa pasta.
- Nunca ler nem editar `.env`. Novas variáveis vão para o `.env.example`, e o
  usuário ajusta o `.env`.
- Sem commit ou push sem pedido explícito.
