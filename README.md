# Espelunca Blog

Tema editorial para o WordPress do `blog.espelunca.social`.

O projeto é um **Block Theme** nativo do WordPress, desenvolvido do zero e inspirado apenas na hierarquia editorial de blogs institucionais como o blog oficial do Mastodon. Nenhum código, marca ou asset do Mastodon é copiado por este projeto.

## Objetivos

- leitura simples e confortável;
- home editorial com destaque automático para a publicação mais recente;
- excelente comportamento em desktop e mobile;
- modo claro/escuro automático e variação manual “Noite”;
- suporte ao Site Editor do WordPress;
- nenhuma dependência de page builder;
- categorias, tags, busca, arquivos, RSS e paginação;
- identidade visual própria da Espelunca.

## Estrutura

O tema está na raiz deste repositório. Arquivos principais:

- `theme.json`: paleta, tipografia, layout e estilos globais;
- `style.css`: metadados do tema e refinamentos responsivos;
- `functions.php`: supports e carregamento do CSS;
- `templates/`: home, artigo, página, arquivo, busca e 404;
- `parts/`: cabeçalho e rodapé editáveis;
- `patterns/`: padrões reutilizáveis;
- `styles/noite.json`: variação escura manual;
- `scripts/validate-theme.py`: validação estática;
- `scripts/deploy.sh`: sincronização segura para a instalação autohospedada.

## Instalação no servidor da Espelunca

A instalação atual do WordPress foi criada em `~/blog-espelunca` e possui o diretório montado `~/blog-espelunca/theme/espelunca-blog`. O repositório Git deve ficar **fora** desse diretório publicado; o deploy copia apenas os arquivos necessários do tema e não expõe `.git` ao Apache.

### 1. Backup do WordPress

```bash
cd ~/blog-espelunca
./scripts/backup.sh
```

### 2. Clonar a fonte do tema

```bash
mkdir -p ~/blog-espelunca/theme-source
cd ~/blog-espelunca/theme-source
git clone https://github.com/lucascasanho/Espelunca-Blog.git
cd Espelunca-Blog
```

### 3. Validar e sincronizar

```bash
python3 scripts/validate-theme.py
./scripts/deploy.sh
```

O deploy cria uma cópia de segurança do diretório de tema existente antes de sincronizar e usa `rsync --delete` para manter o destino consistente. A pasta `.git` e os arquivos de desenvolvimento não são enviados para a raiz web do tema.

### 4. Verificar e ativar

```bash
cd ~/blog-espelunca
./scripts/wp.sh theme list
./scripts/wp.sh theme activate espelunca-blog
./scripts/health.sh
```

Depois, abra `https://blog.espelunca.social` e `https://blog.espelunca.social/wp-admin/` para conferir o front-end e o Site Editor.

## Atualização futura do tema

```bash
cd ~/blog-espelunca/theme-source/Espelunca-Blog
git pull --ff-only
python3 scripts/validate-theme.py
./scripts/deploy.sh
cd ~/blog-espelunca
./scripts/health.sh
```

> Nunca use `docker compose down -v` para atualizar o tema. Os volumes do WordPress e MariaDB são persistentes e não devem ser removidos.

## Rollback do tema

Cada execução de `scripts/deploy.sh` cria, quando há conteúdo anterior, um diretório semelhante a:

```text
~/blog-espelunca/theme/espelunca-blog.backup-AAAAmmdd-HHMMSS
```

Se precisar restaurar um backup, faça primeiro um backup geral do blog e sincronize o conteúdo do diretório escolhido de volta para `~/blog-espelunca/theme/espelunca-blog`. Não remova volumes Docker.

## Personalização no WordPress

Após ativar o tema, use **Aparência → Editor** para editar navegação, cabeçalho, rodapé, estilos globais e templates. A base visual permanece versionada no GitHub; mudanças feitas somente pelo Site Editor ficam armazenadas no banco do WordPress e podem sobrescrever visualmente templates do tema.

## Validação automática

O repositório inclui `.github/workflows/validate.yml`, que valida:

- sintaxe de `theme.json` e variações de estilo;
- JSON dos atributos dos blocos nos templates;
- presença dos arquivos essenciais;
- sintaxe PHP de `functions.php` e patterns.

A validação local equivalente é:

```bash
python3 scripts/validate-theme.py
find . -type f -name '*.php' -print0 | xargs -0 -n1 php -l
```

## Referências técnicas e créditos

- WordPress Theme Handbook — Theme Structure: https://developer.wordpress.org/themes/core-concepts/theme-structure/
- WordPress Theme Handbook — Templates: https://developer.wordpress.org/themes/core-concepts/templates/
- WordPress Theme Handbook — Global Settings & Styles (`theme.json`): https://developer.wordpress.org/themes/global-settings-and-styles/
- WordPress Block Themes: https://developer.wordpress.org/themes/block-themes/
- Blog oficial do Mastodon, usado somente como referência editorial/visual: https://blog.joinmastodon.org/
- Repositório público do blog oficial do Mastodon, consultado apenas para identificar a implementação de referência: https://github.com/mastodon/blog
- GitHub Actions `actions/checkout`: https://github.com/actions/checkout

## Licença

GPL-2.0-or-later. Consulte `LICENSE`.
