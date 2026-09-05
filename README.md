# Espelunca Blog

Tema editorial para o WordPress do `blog.espelunca.social`.

O projeto é um **Block Theme** nativo do WordPress, desenvolvido do zero e inspirado apenas na hierarquia editorial de blogs institucionais como o blog oficial do Mastodon. Nenhum código, marca ou asset do Mastodon é copiado por este projeto.

## Objetivos

- leitura simples e confortável;
- home editorial com destaque e posts recentes;
- excelente comportamento em desktop e mobile;
- modo claro/escuro automático;
- suporte ao Site Editor do WordPress;
- poucas dependências e nenhum page builder;
- categorias, tags, busca, arquivos, RSS e paginação;
- identidade visual própria da Espelunca.

## Estrutura

O tema está na raiz deste repositório e pode ser instalado diretamente como `espelunca-blog`.

## Instalação no servidor da Espelunca

A instalação atual do WordPress foi criada em `~/blog-espelunca` e já possui um diretório de tema montado em `~/blog-espelunca/theme/espelunca-blog`.

Antes de substituir qualquer conteúdo local existente, faça backup:

```bash
cd ~/blog-espelunca
./scripts/backup.sh
```

Depois, para uma primeira instalação segura:

```bash
cd ~/blog-espelunca/theme
mv espelunca-blog "espelunca-blog.pre-git.$(date +%Y%m%d-%H%M%S)" 2>/dev/null || true
git clone https://github.com/lucascasanho/Espelunca-Blog.git espelunca-blog
cd ~/blog-espelunca
./scripts/wp.sh theme list
./scripts/wp.sh theme activate espelunca-blog
./scripts/health.sh
```

Se o tema já tiver sido clonado anteriormente:

```bash
cd ~/blog-espelunca/theme/espelunca-blog
git pull --ff-only
cd ~/blog-espelunca
./scripts/health.sh
```

> Não use `docker compose down -v`. Os volumes do WordPress e MariaDB são persistentes e não devem ser removidos em uma atualização comum.

## Personalização

Após ativar o tema, use **Aparência → Editor** no WordPress para editar navegação, cabeçalho, rodapé, estilos globais e templates. O `theme.json` define a base visual e os templates usam blocos nativos.

## Referências técnicas e créditos

- WordPress Theme Handbook — Theme Structure: https://developer.wordpress.org/themes/core-concepts/theme-structure/
- WordPress Theme Handbook — Templates: https://developer.wordpress.org/themes/core-concepts/templates/
- WordPress Theme Handbook — Global Settings & Styles (`theme.json`): https://developer.wordpress.org/themes/global-settings-and-styles/
- Blog oficial do Mastodon, usado somente como referência editorial/visual: https://blog.joinmastodon.org/
- Repositório público do blog oficial do Mastodon, consultado apenas para identificar que o projeto de referência é Hugo: https://github.com/mastodon/blog

## Licença

Consulte `LICENSE`.
