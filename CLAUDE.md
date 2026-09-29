# CLAUDE.md

O domínio, as seções do site e a stack estão descritos no `README.md`, que é a fonte
de verdade do projeto. Este arquivo trata só de como trabalhar no código.

## Idioma
- Código, nomes, comentários e textos técnicos: inglês.
- Commits: português, com prefixo convencional (`feat:`, `fix:`, `docs:`, `chore:`...).
- Textos visíveis no site: sempre nos dois idiomas (`lang/pt` e `lang/en`), nunca
  texto fixo em um idioma só nas views.

## Convenções
- Seguir os padrões do Laravel e do Filament antes de criar abstrações próprias.
- Rotas públicas ficam no grupo com prefixo `{locale}` (`pt|en`).
- Campos de conteúdo traduzíveis usam `spatie/laravel-translatable`.
- O front é autoral: nada de temas ou kits de UI prontos no site público.
  O Filament é usado só no `/admin`.

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
- Nunca ler nem editar `.env`. Novas variáveis vão para o `.env.example`, e o
  usuário ajusta o `.env`.
- Sem commit ou push sem pedido explícito.
