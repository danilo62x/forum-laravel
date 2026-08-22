# Comunidade & Fórum / Laravel 11 + Blade

[Read in English](./README.md)

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](./LICENSE) ![Grátis](https://img.shields.io/badge/pre%C3%A7o-gr%C3%A1tis-brightgreen)

Um fórum de comunidade com os dois lados, feito em Laravel 11, Blade, Alpine.js e Tailwind CSS v4: o fórum que os membros leem e a mesa de moderação que o mantém saudável. O visitante encontra categorias, tópicos com respostas aninhadas em dois níveis, votos para cima e para baixo, melhor resposta que marca o tópico como resolvido, perfis com reputação e distintivos, e uma busca que mostra o trecho que casou. O moderador tem fila de denúncias que salta direto para a resposta denunciada, ações de fixar/fechar/mover, CRUD de categorias e gestão de membros com silenciar e banir. Reputação, o score de "em alta" e todos os distintivos são calculados a partir dos dados, nunca gravados — então os números não divergem. Seis categorias, vinte e dois tópicos e sessenta e duas respostas vêm como dados de exemplo, persistidos em `localStorage`, sem backend nenhum.

Prévia ao vivo: https://template.dev.br/preview/forum-laravel/

## Telas incluídas

15 telas, cobrindo o site público e a área autenticada:

- Início: busca em destaque, categorias em cartões com contadores, tópicos em alta, últimos tópicos e membros em destaque
- Categoria: descrição, moderadores da categoria, lista de tópicos por recentes, mais votados ou sem resposta, com filtro por tag
- Tópico: post de abertura, respostas aninhadas em dois níveis, votos, melhor resposta, caixa de resposta e tópicos relacionados
- Membro: reputação, distintivos, os tópicos e respostas que escreveu e atividade por mês
- Busca: resultado com o trecho destacado, filtrado por categoria, tag ou só sem resposta
- Regras: diretrizes da comunidade em seções mais um FAQ em acordeão
- Login e Registro: telas de autenticação em layout próprio
- Visão geral da moderação: tópicos abertos, respostas hoje, denúncias na fila, novos membros, gráfico de atividade, tópicos sem resposta e denúncias recentes
- Tópicos: tabela com busca e filtros por categoria, status e autor, mais fixar, fechar, mover e excluir
- Detalhe do tópico: o tópico e suas respostas em modo moderação — aprovar, ocultar, marcar como spam ou responder como equipe
- Denúncias: fila por motivo (spam, ofensa, fora do tema, duplicado) com resolver, descartar e histórico reabrível
- Categorias: CRUD com ícone, cor, descrição, ordem e permissão de postagem
- Membros: tabela com reputação e papel, mais silenciar e banir com prazo
- Configurações: perfil, comunidade, moderação, notificações, tema e idioma
- Não encontrado: página 404

## Stack

- Laravel 11 em PHP 8.2+, com componentes e layouts Blade
- Alpine.js 3 (mais o plugin collapse) nas partes interativas
- Tailwind CSS v4 compilado pelo Vite 6
- Rotas no servidor, nomeadas, com middleware de auth simulada
- i18n no cliente com três idiomas (en, es, pt-BR)
- ApexCharts 5
- Manifest e service worker para o PWA instalável

## Requisitos

- PHP 8.2 ou mais novo, com Composer
- Node.js 18 ou mais novo
- npm

## Como rodar

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run dev
php artisan serve
```

Não precisa de banco: os dados ficam em `app/Support/` e a autenticação por sessão é simulada por middleware. Credenciais de demonstração:

- `moderador@agora.com` / `demo123`

Qualquer e-mail e senha não vazios entram; o par acima é a conta sugerida na tela de login e a dona dos dados de exemplo.

## Build de produção

```bash
npm run build
```

Compila os assets em `public/build`. Sirva a aplicação com qualquer servidor web com PHP.

## Estrutura do projeto

```
app/
├── Http/Controllers/  SiteController, AppController, AuthController
├── Http/Middleware/   AuthenticateMock, RedirectIfAuthenticated
└── Support/           the seed data and the mock users
resources/
├── js/                app.js (Alpine stores and magics), i18n.js, locales
└── views/{layouts,components,pages}/
routes/web.php
```

## Tema e customização

O Tailwind CSS v4 é configurado em CSS, não em arquivo de configuração JavaScript. A folha de estilo importa o Tailwind, declara a variante dark e define os tokens dentro de `@theme`: a fonte Outfit, breakpoints extras e a rampa de cor da marca (`--color-brand-25` até `--color-brand-950`). Os tokens semânticos de superfície usam o prefixo `--tx-`. O dark mode é uma classe no elemento raiz, alternada em runtime e persistida. A cor e o ícone da categoria são dados, então acrescentar uma categoria é mudança de dado, não de template.

## Internacionalização

O template vem com três idiomas: inglês, espanhol e português do Brasil. O idioma do navegador é detectado no primeiro acesso e o seletor do cabeçalho guarda a escolha. Datas, números e durações são formatados por idioma.

## O mesmo sistema em outras tecnologias

O mesmo sistema existe em cinco tecnologias, com as mesmas telas e a mesma identidade visual, e todas são gratuitas:

- React 19: https://github.com/danilo62x/forum-react
- Vue 3.5: https://github.com/danilo62x/forum-vue3
- Angular 19: https://github.com/danilo62x/forum-angular
- HTML estático: https://github.com/danilo62x/forum-html

O catálogo completo, de templates grátis e pagos, está em https://template.dev.br

## Apoie este projeto

Este template é gratuito e licenciado sob MIT. Se ele te poupou tempo, você pode apoiar o trabalho com uma doação em https://template.dev.br/doar?template=forum-laravel

## Licença

[MIT](./LICENSE), copyright 2026 Danilo Quinelato.
