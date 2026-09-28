# Telas de Acesso e Escolha de Serviço — Sistema 651

**Contexto:** tela de autenticação em layout split-screen (formulário à
esquerda, painel institucional à direita), com troca de formulário via
AJAX sem reload de página, e uma tela de escolha de serviço exibida
após o login.

O nome do sistema, **"Sistema 651"**, é provisório até definição oficial.

---

## 1. Histórico resumido

1. Split-screen inicial com formulário único de Cadastro e formulário de
   Entrar (e-mail + senha).
2. Criação de uma área **separada** para o e-SIC (rotas `/esic/*`,
   controllers próprios, painel deslizante) — depois **removida por
   completo**: nenhuma regra de negócio justificava duplicar controllers,
   rotas e sincronização de estado entre dois painéis para o mesmo
   conceito de domínio ("cadastro de cidadão").
3. O cadastro virou **dois formulários completos e independentes** —
   Pessoa Física e Pessoa Jurídica — trocados via AJAX ao clicar no radio
   correspondente, cada um com sua própria rota de envio.
4. Criação da tela de **escolha de serviço** (`/servicos`) e de um login
   funcional com usuário de teste, para validar o fluxo antes da API.
5. **Limpeza de código morto** (ver seção 6).

---

## 2. Estrutura atual

### Rotas (`routes/web.php`)

| Rota | Nome | Controller | Proteção |
|---|---|---|---|
| `GET /` | — | redireciona para `cadastro.index` | — |
| `GET /entrar` | `acesso.index` | `AcessoController@index` | — |
| `POST /entrar` | `acesso.store` | `AcessoController@store` | — |
| `GET /cadastro` | `cadastro.index` | `AutocadastroController@index` | — |
| `POST /cadastro/fisica` | `cadastro.store.fisica` | `AutocadastroController@storeFisica` | — |
| `POST /cadastro/juridica` | `cadastro.store.juridica` | `AutocadastroController@storeJuridica` | — |
| `GET /fragmentos/entrar` | `fragmentos.entrar` | `FragmentoAcessoController@entrar` | só AJAX |
| `GET /fragmentos/cadastro/fisica` | `fragmentos.cadastro.fisica` | `FragmentoAcessoController@cadastroFisica` | só AJAX |
| `GET /fragmentos/cadastro/juridica` | `fragmentos.cadastro.juridica` | `FragmentoAcessoController@cadastroJuridica` | só AJAX |
| `GET /servicos` | `servicos.index` | `ServicoController@index` | `auth` |

Visitantes não autenticados que acessam `/servicos` são redirecionados
para `acesso.index` (`bootstrap/app.php`, `redirectGuestsTo`).

### Autenticação de teste

`AcessoController::store` usa `Auth::attempt()` com sessão real do
Laravel (`SESSION_DRIVER=file`). O usuário de teste é criado por
`DatabaseSeeder`:

- e-mail: `teste@teste.com`
- senha: `12345678`

Para criá-lo: `php artisan migrate:fresh --seed`.

**Temporário:** será substituído pela chamada à API de autenticação
quando o backend definir o contrato.

### Views

```
resources/views/layouts/acesso.blade.php   — layout split-screen (entrar/cadastro)
resources/views/layouts/app.blade.php      — layout institucional (header/menu/footer)
resources/views/portal/entrar.blade.php
resources/views/portal/cadastro.blade.php  — sempre Pessoa Física por padrão
resources/views/portal/parciais/formulario-entrar.blade.php
resources/views/portal/parciais/formulario-cadastro-fisica.blade.php
resources/views/portal/parciais/formulario-cadastro-juridica.blade.php
resources/views/servicos/index.blade.php   — escolha: Ouvidoria / Portal 156 / e-SIC
```

**Decisão explícita:** os dois arquivos de cadastro são independentes,
sem parcial compartilhada para os campos comuns (Telefone, Endereço,
Acesso, Termo de Uso). Esses campos estão duplicados. Ao alterar qualquer
um deles, a mudança precisa ser replicada manualmente nos dois arquivos.

### JavaScript (`resources/js/`)

| Arquivo | Responsabilidade |
|---|---|
| `alternador-acesso.js` | Troca Entrar/Cadastro via `fetch` + `history.pushState`. |
| `alternador-tipo-pessoa.js` | Troca o formulário de cadastro inteiro (Física ↔ Jurídica) ao clicar no radio. |
| `accessibility.js`, `accessibility-panel.js` | Widget de acessibilidade (fonte, contraste). |
| `bootstrap.js` | Configura o `axios` (dependência do boilerplate). |

### CSS (`resources/css/`)

| Arquivo | Uso |
|---|---|
| `acesso.css` | Layout split-screen de entrar/cadastro. |
| `layout.css` | Layout institucional (`layouts/app.blade.php`). |
| `pages/cadastro.css` | Componentes dos formulários de entrar e cadastro. |
| `pages/servicos.css` | Tela de escolha de serviço. |
| `design-system/*` | Tokens, reset, utilitários e grid do design system STII. |

---

## 3. Decisões de UX

| Decisão | Racional |
|---|---|
| Dois formulários completos em vez de um com campos condicionais | Elimina a ambiguidade de um único `<form>` carregar campos de dois tipos de pessoa. |
| Troca via `fetch` + `innerHTML`, sem URL própria para Física/Jurídica | A escolha do tipo é detalhe de formulário, não de navegação. |
| Painel de marca com `position: sticky` no desktop | Evita que o painel fique esticado enquanto o formulário rola. |
| Cor de destaque por seção (`--secao-cor`, via `:has()`) | Vermelho/verde/amarelo institucionais no foco dos campos. |
| Labels flutuantes via `:placeholder-shown` (inputs) e `:valid` com `required` (selects) | `:valid` sem `required` é verdadeiro em campo vazio; por isso todo `<select>` tem `required`. |

---

## 4. Pendências conhecidas

- **Integração com a API real** — `AcessoController::store`,
  `storeFisica` e `storeJuridica` ainda são simulações.
- **Alternar Física/Jurídica após Entrar→Cadastrar** — quem abre
  `/entrar` e clica em "Cadastrar" não consegue trocar para Jurídica:
  `alternador-tipo-pessoa.js` lê as URLs dos fragmentos uma única vez no
  carregamento, quando o formulário de Entrar (sem radios) está na tela.
  Abrir `/cadastro` direto funciona.
- **"Esqueci minha senha" e "Denúncia anônima"** — links com `href="#"`.
- **Cards de Ouvidoria e e-SIC** (`/servicos`) — `href="#"`, os
  sistemas ainda não existem.
- **Nome do sistema** — "Sistema 651" é provisório (`layouts/acesso.blade.php`
  e `@section('title')` de `portal/entrar` e `portal/cadastro`).
- **`tests/Feature/ExampleTest.php`** — espera `GET /` com status 200,
  mas `/` redireciona (302); o teste está falhando.
- **`welcome.blade.php`** — view padrão do Laravel, sem rota que a
  renderize.
- **Tokens inexistentes em `utilities.css`** — as classes `.heading-1` a
  `.heading-4`, `.body-text` e `.caption` usam `--text-4xl`, `--text-3xl`,
  `--text-2xl`, `--text-xl`, `--text-base` e `--text-sm`, que não existem
  em `tokens.css` (só há `--text-12` a `--text-40`).
- **Dependência sem uso** — `@tailwindcss/vite` e `tailwindcss` constam no
  `package.json`, mas o `vite.config.js` não carrega o plugin do Tailwind.
- **Arquivo para excluir manualmente** — o ambiente de edição não tem
  operação de exclusão: `DELETAR_css_auth.css` (raiz do projeto).

---

## 5. Aderência ao Design System

O CSS usa os tokens de `resources/css/design-system/tokens.css`. Os
valores fixos que restam (ex.: `#bbc7bb` na borda dos campos, o
`rgba(...)` das sombras do painel de marca) são acabamento visual sem
token equivalente.

---

## 6. Limpeza de código morto

Realizada com verificação de dependência: cada item foi buscado nas
views, no JS e nas rotas antes de ser removido.

| Arquivo | Removido | Motivo |
|---|---|---|
| `resources/css/auth.css` | arquivo inteiro (movido para `DELETAR_css_auth.css`) | Substituído por `acesso.css`; nenhum `@import` nem referência. |
| `acesso.css` | `.acesso-cabecalho-formulario` e `__titulo`, `__subtitulo` | Nenhuma parcial usa mais esse cabeçalho. |
| `acesso.css` | `.acesso-marca__linha-superior`, `.acesso-marca__titulo` | O HTML do painel de marca não usa mais essas classes. |
| `layout.css` | `.app-header__brand-text` | O header não tem mais esse elemento. |
| `pages/cadastro.css` | `border: solid 1px var(--color-primary-600)` | Declaração sobrescrita na linha seguinte. |
| `pages/cadastro.css` | comentário `min-height` e linhas em branco duplicadas | Sem efeito. |
| `alternador-tipo-pessoa.js` | função `atualizarUrlsFragmento` e sua chamada | Relia valores idênticos aos já guardados; nunca alterava nada. |
| vários CSS | comentários de cabeçalho | Descreviam estilos antigos (underline, listras diagonais, "sem header"). |

**Mantido de propósito:** o design system (`utilities.css`,
`responsive.css`, `base.css`, `tokens.css`) — classes utilitárias sem uso
hoje são vocabulário do sistema, não código morto.
