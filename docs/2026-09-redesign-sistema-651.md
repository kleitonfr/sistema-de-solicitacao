# Telas de Acesso — Sistema 651 (Entrar / Cadastro)

**Contexto:** tela de autenticação em layout split-screen (formulário à
esquerda, painel institucional à direita), com troca de formulário via
AJAX sem reload de página.

O nome do sistema, **"Sistema 651"**, é provisório até definição oficial.

---

## 1. Histórico resumido

1. Split-screen inicial com formulário único de Cadastro (Nome, Nome
   Social, CPF, Nome da Mãe, Data de Nascimento, Sexo, Telefone completo,
   Endereço) e formulário de Entrar (e-mail + senha).
2. Criação de uma área **separada** para o e-SIC (rotas `/esic/*`,
   controllers próprios, painel deslizante) — depois **removida por
   completo**: nenhuma regra de negócio justificava duplicar controllers,
   rotas e sincronização de estado entre dois painéis para o mesmo
   conceito de domínio ("cadastro de cidadão"). Os campos próprios do
   e-SIC (Pessoa Física/Jurídica, Faixa Etária, Escolaridade, Acesso com
   senha) foram fundidos no cadastro do Portal.
3. **Arquitetura atual (este documento):** o cadastro deixou de ser um
   único formulário com campos condicionais e virou **dois formulários
   completos e independentes** — um para Pessoa Física, outro para Pessoa
   Jurídica — trocados inteiramente via AJAX ao clicar no radio
   correspondente. Cada um envia para sua própria rota/método no mesmo
   controller, ambos persistindo no mesmo destino de dados.

O motivo desta última mudança: um único formulário com blocos de campos
alternados por `hidden` (Física ↔ Jurídica) misturava dois conjuntos de
dados no mesmo `<form>`, dependendo inteiramente da validação
`required_if` no servidor para garantir que só um dos conjuntos fosse
processado. Dois formulários fisicamente separados, cada um com sua
própria rota, eliminam essa ambiguidade na origem: o `<form>` de Física
nunca contém campos de Jurídica, e vice-versa.

---

## 2. Estrutura atual

### Rotas (`routes/web.php`)

| Rota | Nome | Controller |
|---|---|---|
| `GET /entrar` | `acesso.index` | `AcessoController@index` |
| `POST /entrar` | `acesso.store` | `AcessoController@store` |
| `GET /cadastro` | `cadastro.index` | `AutocadastroController@index` |
| `POST /cadastro/fisica` | `cadastro.store.fisica` | `AutocadastroController@storeFisica` |
| `POST /cadastro/juridica` | `cadastro.store.juridica` | `AutocadastroController@storeJuridica` |
| `GET /fragmentos/entrar` | `fragmentos.entrar` | `FragmentoAcessoController@entrar` |
| `GET /fragmentos/cadastro/fisica` | `fragmentos.cadastro.fisica` | `FragmentoAcessoController@cadastroFisica` |
| `GET /fragmentos/cadastro/juridica` | `fragmentos.cadastro.juridica` | `FragmentoAcessoController@cadastroJuridica` |

`GET /cadastro` sempre carrega o formulário de **Pessoa Física** (o
padrão); não existe rota própria para carregar Jurídica diretamente — o
usuário chega lá clicando no radio depois de abrir `/cadastro`.

### Views

```
resources/views/layouts/acesso.blade.php              — layout split-screen único
resources/views/portal/entrar.blade.php                — view completa de Entrar
resources/views/portal/cadastro.blade.php              — view completa de Cadastro (sempre Física)
resources/views/portal/parciais/formulario-entrar.blade.php
resources/views/portal/parciais/formulario-cadastro-fisica.blade.php
resources/views/portal/parciais/formulario-cadastro-juridica.blade.php
```

**Decisão explícita: os dois arquivos de cadastro são independentes,
sem parcial compartilhada para os campos comuns** (Telefone, Endereço,
Acesso, Termo de Uso). Esses campos estão duplicados nos dois arquivos.
Ao alterar qualquer um desses campos comuns (por exemplo, adicionar uma
opção no select de Tipo de Telefone), a mudança precisa ser replicada
manualmente nos dois arquivos — não há mecanismo de sincronização
automática entre eles.

Cada formulário de cadastro reaproveita a mesma parcial só para si mesmo:
a view completa (`portal/cadastro.blade.php`) e o fragmento AJAX servem
o mesmo arquivo `formulario-cadastro-fisica.blade.php` — essa parte
segue DRY normalmente, a duplicação é só *entre* Física e Jurídica, não
dentro de cada um.

### Campos por formulário

**`formulario-cadastro-fisica.blade.php`** — Dados Pessoais (Nome
Completo, CPF, Nome Social opcional, Nome da Mãe opcional, Data de
Nascimento, Sexo, Faixa Etária, Escolaridade, Profissão opcional,
E-mail, Confirme E-mail), Telefone (Tipo, DDD, Telefone, Observação
opcional), Endereço (CEP, Logradouro, Bairro, Cidade, UF, Número,
Complemento opcional), Acesso (Senha, Confirme Senha), Termo de Uso.

**`formulario-cadastro-juridica.blade.php`** — Dados da Empresa (Razão
Social, CNPJ, E-mail, Confirme E-mail), depois os mesmos blocos de
Telefone, Endereço, Acesso e Termo de Uso do formulário de Física. Não
tem os campos que só fazem sentido para pessoa física (Nome Social,
Nome da Mãe, Data de Nascimento, Sexo, Faixa Etária, Escolaridade,
Profissão).

### `AutocadastroController`

Dois métodos de validação/envio, cada um só com os campos do seu tipo:
`storeFisica` (exige `nome_completo`+`cpf`) e `storeJuridica` (exige
`razao_social`+`cnpj`). Os dois terminam chamando o mesmo método privado
`redirecionarComSucesso()` — a resposta de sucesso é idêntica para os
dois tipos, já que gravam no mesmo destino de dados.

### JavaScript (`resources/js/`)

| Arquivo | Responsabilidade |
|---|---|
| `alternador-acesso.js` | Troca Entrar/Cadastro via `fetch` + `history.pushState`, sem reload. O botão "Cadastrar" sempre busca o fragmento de Pessoa Física (o padrão). |
| `alternador-tipo-pessoa.js` | Troca o formulário de cadastro inteiro (Física ↔ Jurídica) ao clicar no radio correspondente. Busca o fragmento completo via `fetch` e substitui `#acesso-conteudo-formulario` por inteiro — não é mais uma alternância de campos escondidos dentro do mesmo `<form>`, é troca de dois `<form>` diferentes. Sem reload nem mudança de URL própria (a troca Física/Jurídica não tem uma URL dedicada). |

O listener de `alternador-tipo-pessoa.js` é registrado uma vez, no
elemento `#acesso-conteudo-formulario`, e sobrevive à substituição do
`innerHTML` — o listener está no contêiner, não no radio em si, que é
recriado a cada troca.

### CSS

`resources/css/acesso.css` — layout split-screen (painel de formulário,
painel de marca).

`resources/css/pages/cadastro.css` — componentes de formulário
(`.cadastro-card`, `.cadastro-field`, `.cadastro-radio-grupo`, etc.),
compartilhados por todos os formulários (Entrar, Cadastro Física,
Cadastro Jurídica).

---

## 3. Decisões de UX

| Decisão | Racional |
|---|---|
| Dois formulários completos em vez de um com campos condicionais | Elimina a ambiguidade de um único `<form>` carregar campos de dois tipos de pessoa ao mesmo tempo; cada `<form>` só existe com os campos do seu próprio tipo. |
| Troca via `fetch` + substituição de `innerHTML`, sem URL própria para Física/Jurídica | A URL de cadastro continua sendo só `/cadastro`; a escolha do tipo é um detalhe de formulário, não de navegação — evita duplicar todo o esquema de `pushState` usado para Entrar/Cadastro. |
| Painel direito com `position: sticky` no desktop | Evita que o painel de marca (pouco conteúdo) fique esticado/vazio enquanto o formulário de cadastro, mais longo, rola por baixo. |
| Cor de destaque por seção (`--secao-cor`, definida via `:has()` no ícone) | Vermelho/verde/amarelo institucionais aplicados ao foco dos campos e ao radio, conforme a seção em que estão. |
| Labels flutuantes via `:placeholder-shown` (inputs) e `:valid` com `required` (selects) | `:valid` sozinho, sem `required`, é verdadeiro mesmo em campo vazio — por isso todo `<select>` do formulário tem `required`, mesmo quando o dado em si não é estritamente obrigatório para o negócio. |

---

## 4. Pendências conhecidas

- **Integração com API real** — `AcessoController::store`,
  `AutocadastroController::storeFisica` e `storeJuridica` continuam
  sendo simulações; nenhuma chamada real de autenticação/cadastro foi
  implementada.
- **"Esqueci minha senha"** — link presente na view, sem rota.
- **Nome do sistema** — "Sistema 651" é provisório. Quando definido,
  atualizar `layouts/acesso.blade.php` (`<title>` e o texto no painel de
  marca) e os `@section('title', ...)` de `portal/entrar.blade.php` e
  `portal/cadastro.blade.php`.
- **Duplicação consciente entre os dois formulários de cadastro** — ver
  seção 2. Qualquer mudança em Telefone, Endereço, Acesso ou Termo de Uso
  precisa ser replicada manualmente em `formulario-cadastro-fisica.blade.php`
  e `formulario-cadastro-juridica.blade.php`.
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
  - `DELETAR_views_portal_parciais_formulario-cadastro.blade.php`
    (a antiga parcial única, substituída pelas duas de Física/Jurídica)
  - `resources/views/esic/` e `resources/views/esic/parciais/` (pastas
    vazias)
  - `resources/css/auth.css` (órfão de uma limpeza anterior, já não é
    importado por `app.css`)

---

## 5. Aderência ao Design System

Todo o CSS usa exclusivamente os tokens de
`resources/css/design-system/tokens.css`. Nenhuma cor ou medida "crua"
nova foi introduzida nesta reformulação.
