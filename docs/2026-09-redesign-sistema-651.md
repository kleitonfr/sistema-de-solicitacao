# Telas de Acesso — Sistema 651 (Entrar / Cadastro)

**Contexto:** tela de autenticação em layout split-screen (formulário à
esquerda, painel institucional à direita), com troca de formulário via
AJAX sem reload de página. Documento reescrito após um reset de
arquitetura que **extinguiu a área separada do e-SIC** e unificou os
formulários de cadastro em um só.

O nome do sistema, **"Sistema 651"**, é provisório até definição oficial.

---

## 1. Histórico resumido

1. Criação inicial do split-screen com formulário de Cadastro do Sistema
   651 (Nome, Nome Social, CPF, Nome da Mãe, Data de Nascimento, Sexo,
   Telefone completo, Endereço) e formulário de Entrar (e-mail + senha).
2. Criação de uma área **separada** para o e-SIC: rotas `/esic/entrar` e
   `/esic/cadastro`, controllers próprios, um terceiro painel que
   deslizava por cima do painel de marca, e um formulário de cadastro
   próprio do e-SIC (Pessoa Física/Jurídica, Faixa Etária, Escolaridade,
   Acesso com senha).
3. **Reset de arquitetura (este documento):** a área separada do e-SIC
   foi removida por completo. Os campos que só existiam nela foram
   **fundidos** no único formulário de Cadastro do Sistema 651. Não há
   mais painel deslizante, botão "Acesse o ESIC" nem rotas `esic.*`.

O motivo do reset: manter dois fluxos de cadastro paralelos (Portal e
e-SIC) exigia duplicar controllers, rotas, parciais, scripts de troca de
painel e lógica de sincronização de estado entre os dois lados — nenhuma
regra de negócio justificava essa separação além da origem dos campos.
Um único formulário, com Pessoa Física/Jurídica como uma alternância
interna (Seção 36 — DRY: preferir uma solução ao problema real, não uma
arquitetura paralela para um mesmo conceito de domínio: "cadastro de
cidadão").

---

## 2. Estrutura atual

### Rotas (`routes/web.php`)

| Rota | Nome | Controller |
|---|---|---|
| `GET /entrar` | `acesso.index` | `AcessoController@index` |
| `POST /entrar` | `acesso.store` | `AcessoController@store` |
| `GET /cadastro` | `cadastro.index` | `AutocadastroController@index` |
| `POST /cadastro` | `cadastro.store` | `AutocadastroController@store` |
| `GET /fragmentos/entrar` | `fragmentos.entrar` | `FragmentoAcessoController@entrar` |
| `GET /fragmentos/cadastro` | `fragmentos.cadastro` | `FragmentoAcessoController@cadastro` |

Não há mais rotas `esic.*` nem `fragmentos.esic.*`.

### Views

```
resources/views/layouts/acesso.blade.php   — layout split-screen único
resources/views/portal/entrar.blade.php    — view completa de Entrar
resources/views/portal/cadastro.blade.php  — view completa de Cadastro
resources/views/portal/parciais/formulario-entrar.blade.php
resources/views/portal/parciais/formulario-cadastro.blade.php
```

As parciais existem para serem reaproveitadas tanto na view completa
quanto no fragmento servido via AJAX (`FragmentoAcessoController`), sem
duplicar HTML (DRY).

### JavaScript (`resources/js/`)

| Arquivo | Responsabilidade |
|---|---|
| `alternador-acesso.js` | Troca Entrar/Cadastro via `fetch` + `history.pushState`, sem reload. Único alternador na página — usa `querySelector` direto (delegação de evento não é mais necessária, já que não há mais um segundo alternador do e-SIC concorrendo). |
| `alternador-tipo-pessoa.js` | Alterna os campos de Pessoa Física/Jurídica no formulário de Cadastro (`hidden`, sem regra de negócio). Renomeado de `alternador-tipo-pessoa-esic.js` — a lógica é genérica e nunca dependeu do e-SIC como área. |

### CSS

`resources/css/acesso.css` — layout split-screen (painel de formulário,
painel de marca). Removidas todas as regras do painel do e-SIC (`.acesso-split__esic-*`,
`.acesso-marca__esic*`, `.modo-esic`) e da transição de deslize entre
painéis.

`resources/css/pages/cadastro.css` — componentes de formulário
(`.cadastro-card`, `.cadastro-field`, `.cadastro-radio-grupo`, etc.),
compartilhados pelos formulários de Entrar e Cadastro. Corrigido um erro
de sintaxe pré-existente na propriedade `border` de `.cadastro-card`.

---

## 3. Formulário único de Cadastro

Campos, por seção:

**Dados Pessoais** — Tipo de Pessoa (Física/Jurídica, radio) alterna:
- Física: Nome Completo, CPF
- Jurídica: Razão Social, CNPJ

Comuns às duas: Nome Social (opcional), Nome Completo da Mãe (opcional),
Data de Nascimento, Sexo, Faixa Etária, Escolaridade, Profissão
(opcional), E-mail, Confirme o E-mail.

**Telefone** — Tipo, DDD, Telefone, Observação (opcional).

**Endereço** — CEP, Logradouro, Bairro, Cidade, UF, Número, Complemento
(opcional).

**Acesso** — Senha, Confirme a Senha.

Mais o checkbox de Termo de Uso.

A validação server-side (`AutocadastroController::store`) usa
`required_if:tipo_pessoa,fisica` / `required_if:tipo_pessoa,juridica`
para exigir CPF+Nome ou CNPJ+Razão Social conforme o tipo escolhido, sem
tornar os dois pares obrigatórios ao mesmo tempo.

---

## 4. Decisões de UX mantidas do design original

| Decisão | Racional |
|---|---|
| Painel direito com `position: sticky` no desktop | Evita que o painel de marca (pouco conteúdo) fique esticado/vazio enquanto o formulário de cadastro, mais longo, rola por baixo. |
| Troca de formulário via `fetch` + `pushState`, não SPA completo | Mantém URL própria por estado (compartilhável, funciona com voltar/avançar do navegador) sem exigir um framework JS. |
| Cor de destaque por seção (`--secao-cor`, definida via `:has()` no ícone) | Vermelho/verde/amarelo institucionais aplicados ao foco dos campos e ao radio, conforme a seção em que estão. |
| Labels flutuantes via `:placeholder-shown` (inputs) e `:valid` com `required` (selects) | `:valid` sozinho, sem `required`, é verdadeiro mesmo em campo vazio — por isso todo `<select>` do formulário tem `required`, mesmo quando o dado em si não é estritamente obrigatório para o negócio (decisão de trade-off: comportamento visual correto teve prioridade sobre a nuance de "campo opcional"). |

---

## 5. Pendências conhecidas

- **Integração com API real** — `AcessoController::store` e
  `AutocadastroController::store` continuam sendo simulações; nenhuma
  chamada real de autenticação/cadastro foi implementada.
- **"Esqueci minha senha"** — link presente na view, sem rota.
- **Nome do sistema** — "Sistema 651" é provisório. Quando definido,
  atualizar `layouts/acesso.blade.php` (`<title>` e o texto no painel de
  marca) e os `@section('title', ...)` de `portal/entrar.blade.php` e
  `portal/cadastro.blade.php`.
- **Arquivos e pastas para exclusão manual** — o ambiente de edição usado
  neste projeto não tem uma operação de exclusão de arquivo; os itens
  abaixo foram renomeados com o prefixo `DELETAR_` (arquivos) ou ficaram
  vazios (pastas) e precisam ser apagados manualmente do disco:
  - `DELETAR_EsicAcessoController.php`
  - `DELETAR_EsicAutocadastroController.php`
  - `DELETAR_FragmentoEsicController.php`
  - `DELETAR_views_esic_entrar.blade.php`
  - `DELETAR_views_esic_cadastro.blade.php`
  - `DELETAR_views_esic_parciais_formulario-entrar.blade.php`
  - `DELETAR_views_esic_parciais_formulario-cadastro.blade.php`
  - `DELETAR_js_alternador-painel.js`
  - `resources/views/esic/` e `resources/views/esic/parciais/` (pastas
    vazias)
  - `resources/css/auth.css` (órfão de uma limpeza anterior, já não é
    importado por `app.css` desde antes deste reset)

---

## 6. Aderência ao Design System

Todo o CSS usa exclusivamente os tokens de
`resources/css/design-system/tokens.css`. Não foram introduzidas cores ou
medidas "cruas" novas neste reset além das já existentes e documentadas
no próprio `pages/cadastro.css`.
