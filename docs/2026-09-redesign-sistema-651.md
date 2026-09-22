# Redesign — Sistema 651 (tela de Login/Cadastro em split-screen)

**Data:** ver histórico do Git
**Contexto:** redesign da experiência de autenticação (login e cadastro),
inspirado em uma referência visual de layout editorial split-screen (painel
de conteúdo claro à esquerda + painel de marca em bloco de cor à direita).
Adaptado à paleta institucional da Prefeitura de Caraguatatuba e ao Design
System STII já existente no projeto.

O nome do sistema foi definido como **"Sistema 651"** — nome provisório
até que a equipe/produto defina o nome oficial.

---

## 1. Visão geral da mudança

Antes, a página de cadastro (`/cadastro`) usava o layout institucional
completo (`layouts/app.blade.php`): header fixo com brasão + menu de
navegação, e footer completo com colunas de links e redes sociais.

Agora, `/cadastro` e a nova página `/login` usam um **layout dedicado de
autenticação** (`layouts/auth.blade.php`), sem header, sem menu de
navegação e sem footer institucional — apenas:

- **Painel esquerdo:** formulário ativo (login OU cadastro, nunca os
  dois ao mesmo tempo) + um toggle switch para alternar entre eles.
- **Painel direito:** identidade do "Sistema 651" (brasão + nome do
  sistema), uma frase de propósito, e um resumo de contatos institucionais
  (telefones), reaproveitando os dados que já existiam no footer antigo.

O footer institucional completo (5 colunas de links, redes sociais)
**não foi alterado** e continua existindo em `layouts/app.blade.php`,
disponível para outras páginas do sistema que venham a usá-lo no futuro.

---

## 2. Decisões de UX

| Decisão | Racional |
|---|---|
| Painel direito com `position: sticky` no desktop | O formulário de cadastro é longo (3 seções); a página inteira rola normalmente (decisão do time). Sem `sticky`, o painel de marca — que tem pouco conteúdo — ficaria esticado e com espaço vazio ao final do scroll. Com `sticky`, ele acompanha o scroll até o fim da sua própria altura (100vh) e depois é ultrapassado pelo formulário, sem parecer vazio. |
| Toggle como **navegação real** (`<a href>` entre `/login` e `/cadastro`), não JS de exibir/ocultar | Mais robusto: funciona sem JavaScript, com URL própria por estado (compartilhável, indexável, funciona com botão voltar do navegador), e evita duplicar dois `<form>` completos na mesma página. |
| `role="tablist"/"tab"` **não foi usado** no toggle | Esse padrão ARIA é para abas que trocam conteúdo *sem* navegação (um único painel). Como aqui há navegação real entre páginas, o padrão correto é uma `<nav>` simples com `aria-current="page"` no link ativo. |
| Card de formulário sem glassmorphism/blur | No layout anterior (fundo com imagem de mural), o glass fazia sentido. No novo layout, o fundo do painel esquerdo é sólido e claro (`--bg-surface`) — um card com blur não teria nada relevante para "borrar" atrás dele, então virou um card sólido, limpo, com sombra e borda sutil (mais alinhado ao tom "editorial" da referência). |
| Formulário de cadastro mantido **completo** (3 seções) dentro do novo layout | Decisão do time: não simplificar o formulário nesta fase, apenas o layout ao redor dele. |
| Rota e Controller de Login criados como **stub visual** | Login ainda não existe no sistema. Criado `LoginController` com `index()` (renderiza o formulário) e `store()` (simula um erro de "aguardando integração", já que a autenticação real será implementada pela equipe de backend). |

---

## 3. Arquivos criados

| Arquivo | Descrição |
|---|---|
| `resources/views/layouts/auth.blade.php` | Novo layout split-screen. Sem header/menu/footer institucional. Contém o painel de marca (brasão, nome do sistema, contatos) e o toggle Login/Cadastro. |
| `resources/views/auth/login.blade.php` | Nova view do formulário de login (e-mail + senha), usando o layout `auth`. |
| `resources/css/auth.css` | Novo arquivo de estilos: `.auth-split`, `.auth-toggle` (switch deslizante), `.auth-split__brand-pane` (painel direito, incluindo a textura diagonal sutil), `.auth-form-header`, etc. |
| `app/Http/Controllers/LoginController.php` | Controller stub: `index()` renderiza a view; `store()` valida e retorna erro de integração pendente. |
| `docs/2026-09-redesign-sistema-651.md` | Este documento. |

---

## 4. Arquivos alterados

| Arquivo | O que mudou |
|---|---|
| `routes/web.php` | Adicionadas as rotas `GET /login` (`login.index`) e `POST /login` (`login.store`). A rota `/` passou a redirecionar para `cadastro.index` em vez de renderizar a view diretamente. |
| `resources/views/cadastro/index.blade.php` | Trocado `@extends('layouts.app')` → `@extends('layouts.auth')`. Removido o wrapper antigo (`.cadastro-page` + `.container` + `.cadastro-heading`), substituído pelo novo `.auth-form-header` (título + subtítulo), que agora vive dentro do painel esquerdo do split-screen. |
| `resources/css/pages/cadastro.css` | Removidas as regras `.cadastro-page` (fundo com gradiente de página inteira, sem uso no novo layout) e `.cadastro-heading*` (substituídas por `.auth-form-header*` em `auth.css`). O componente `.cadastro-card` foi simplificado: sem glassmorphism/blur, sem `max-width`/`margin-inline: auto` (esse controle agora é do painel esquerdo), fundo sólido branco. Também removida a regra órfã `.cadastro-card__title` (não usada em nenhuma view). |
| `resources/css/app.css` | Adicionado `@import './auth.css';`, na sequência de imports do design system. |

---

## 5. Arquivos **não** alterados (por decisão explícita)

- `resources/views/layouts/app.blade.php` — o layout institucional com
  header, menu de navegação e footer completo permanece intacto, para uso
  futuro em outras páginas do sistema que não sejam de autenticação.
- `resources/css/layout.css` — estilos do header/footer institucional,
  sem mudanças.
- Footer (conteúdo e estrutura) — mantido 100% como estava, apenas
  **reaproveitado em resumo** (nome, telefones) no painel direito do novo
  layout de autenticação, sem alterar o footer original.

---

## 6. Uso de imagens/assets

O brasão usado no painel de marca é o arquivo já existente
`public/assets/img/prefeitura-de-caraguatatuba.png` — o mesmo já usado no
header e footer do layout institucional. Nenhuma imagem nova foi
adicionada.

---

## 7. Aderência ao Design System

Todo o CSS novo (`auth.css`) usa exclusivamente os tokens definidos em
`resources/css/design-system/tokens.css` (cores, espaçamento, tipografia,
superfície, breakpoints) — nenhuma cor ou medida "crua" foi introduzida,
exceto o padrão diagonal de textura do painel direito
(`repeating-linear-gradient` com `rgba(255,255,255,0.035)`), que é um
efeito puramente decorativo sem token equivalente no design system atual.

---

## 8. Pendências / próximos passos

- **Login real:** `LoginController::store()` está com uma validação
  mínima e resposta simulada. Precisa ser substituído pela chamada real
  à API de autenticação assim que o backend definir o contrato
  (endpoint, formato de payload/erro, gestão de sessão/token).
- **"Esqueci minha senha":** o link existe na view (`auth-form-links__link`)
  mas aponta para `#` — sem rota ainda.
- **Nome do sistema:** "Sistema 651" é um nome provisório. Quando o nome
  oficial for definido, atualizar:
  - `layouts/auth.blade.php` (`<title>` padrão e `.auth-brand__title`)
  - `cadastro/index.blade.php` e `auth/login.blade.php` (`@section('title', ...)`)
