# Roteiro passo a passo — da Ouvidoria a "Minhas solicitações"

Guia para refazer, na ordem, tudo o que foi construído em 07/10 (formulários
da Ouvidoria) e 08/10 (acompanhamento de solicitações), mais os ajustes feitos
no meio e depois. Serve de base para atualizar o diagrama.

- Todos os caminhos partem da raiz do projeto: `D:\xampp\htdocs\novo156_frontend\novo156`.
- **Criado** = arquivo novo. **Alterado** = arquivo que já existia.
- Estado: nada disso foi executado por mim (migrations, testes e build).
  Rode `php artisan migrate`, `php artisan test` e `npm run build`.
- As datas seguem as migrations (07/10 e 08/10). Os ajustes visuais da Parte 2
  não têm data registrada nos arquivos.

---

## Visão geral em uma tela

```text
PARTE 1 (07/10)  Ouvidoria: página inicial → formulário → gravação → confirmação
PARTE 2 (entre)  Ajustes: sigilo removido, botão de denúncia no login, menu, fontes
PARTE 3 (08/10)  Acompanhamento: status + histórico → lista → detalhe → anexos
PARTE 4 (08/10)  Enxugamento (sem pastas novas) e identidade de cor por serviço
```

Três serviços, três tabelas principais, um histórico comum:

| Serviço | Prefixo do protocolo | Tabela | Model | Cor |
|---|---|---|---|---|
| Portal 156 | `156-AAAA-NNNNNN` | `solicitacoes_156` | `Solicitacao156` | amarelo |
| e-SIC | `ESIC-AAAA-NNNNNN` | `solicitacoes_esic` | `SolicitacaoEsic` | verde |
| Ouvidoria | `OUV-AAAA-NNNNNN` | `manifestacoes_ouvidoria` | `ManifestacaoOuvidoria` | vermelho |

Histórico de todos: tabela `movimentacoes_solicitacao` (model `MovimentacaoDaSolicitacao`).

---

# PARTE 1 — Ouvidoria (07/10)

Fluxo do cidadão: **página da Ouvidoria → escolhe o tipo → preenche o formulário
→ envia → vê a confirmação com o protocolo.**

## Etapa 1.1 — Banco: tabela das manifestações

- **Objetivo:** guardar cada manifestação (tipo, descrição e campos opcionais),
  ligada ao cidadão que a registrou.
- **Arquivo (criado):** `database/migrations/2026_10_07_000001_create_manifestacoes_ouvidoria_table.php`
- **Colunas:** `user_id` (FK para `users`, `restrictOnDelete`), `protocolo`
  (único, 20), `tipo` (20), `orgao_ou_setor`, `descricao`, `data_do_fato`,
  `local_do_fato`, `servidor_ou_setor` (os cinco últimos, exceto `descricao`,
  são opcionais) e `timestamps`.
- **Depois:** na Etapa 3.1 entra a coluna `status` (outra migration).

## Etapa 1.2 — Banco: tabela dos anexos

- **Objetivo:** guardar os arquivos enviados com a manifestação (até 5).
- **Arquivo (criado):** `database/migrations/2026_10_07_000002_create_anexos_manifestacao_ouvidoria_table.php`
- **Colunas:** `manifestacao_id` (FK, `cascadeOnDelete`), `nome_original`,
  `caminho` (onde o arquivo ficou no disco) e `timestamps`.
- O arquivo em si **não** vai para o banco: fica no disco privado `local`.

## Etapa 1.3 — Models

- **Objetivo:** representar as tabelas no código e concentrar o que varia por tipo.
- **Arquivos (criados):**
  - `app/Models/ManifestacaoOuvidoria.php`
    - constante `TIPOS`: para `reclamacao`, `denuncia`, `sugestao` e `elogio`,
      guarda rótulo, orientação, rótulo da descrição e quais campos
      específicos existem (`campos`).
    - `tipoTemCampo($tipo, $campo)` e `rotuloDoTipo()`.
    - relações: `usuario()`, `anexos()` e (depois) `movimentacoes()`.
  - `app/Models/AnexoManifestacaoOuvidoria.php` (relação `manifestacao()`).
- **Arquivo (alterado):** `app/Models/User.php` ganhou `manifestacoesOuvidoria()`.
- **Campos por tipo:**

| Campo | Reclamação | Denúncia | Sugestão | Elogio |
|---|---|---|---|---|
| `orgao_ou_setor` | sim | sim | sim | sim |
| `data_do_fato`, `local_do_fato` | sim | sim | não | não |
| `servidor_ou_setor` | não | sim | não | sim |

- Um formulário só serve aos quatro tipos. Não existe Enum (foi removido a
  seu pedido).

## Etapa 1.4 — Rotas

- **Objetivo:** expor a página inicial, o formulário, o envio e a confirmação.
- **Arquivo (alterado):** `routes/web.php`, dentro do grupo `auth`:

| Método | URL | Nome da rota | Método do controller |
|---|---|---|---|
| GET | `/ouvidoria` | `ouvidoria.index` | `index` |
| GET | `/ouvidoria/manifestar/{tipo}` | `ouvidoria.manifestacao.criar` | `criarManifestacao` |
| POST | `/ouvidoria/manifestar/{tipo}` | `ouvidoria.manifestacao.registrar` | `registrarManifestacao` |
| GET | `/ouvidoria/manifestacoes/{protocolo}` | `ouvidoria.manifestacao.confirmacao` | `confirmacaoManifestacao` |

- `{tipo}` é limitado, na própria rota, às quatro chaves de `TIPOS`
  (qualquer outro valor devolve 404). `{protocolo}` segue `OUV-\d{4}-\d{6}`.
- Quem não está logado é mandado para `acesso.index`
  (`bootstrap/app.php`: `redirectGuestsTo`).

## Etapa 1.5 — Página inicial da Ouvidoria

- **Objetivo:** explicar o serviço e oferecer os quatro botões (um por tipo).
- **Arquivos:**
  - `resources/views/servicos/ouvidoria/ouvidoria-pagina-inicial.blade.php`
    (alterado): cada botão aponta para `ouvidoria.manifestacao.criar` com o tipo.
  - `resources/css/pages/ouvidoria.css` (alterado): visual vermelho do serviço.

## Etapa 1.6 — Formulário (tela)

- **Objetivo:** mostrar o formulário do tipo escolhido, com só os campos que
  pertencem a ele.
- **Arquivos:**
  - `resources/views/servicos/ouvidoria/manifestacao/criar.blade.php` (criado):
    `orgao_ou_setor`, `data_do_fato`, `local_do_fato`, `servidor_ou_setor`
    (cada um só se `tipoTemCampo`), `descricao` (obrigatória) e os anexos.
    Mostra o alerta geral (`erro_geral`) e os erros junto a cada campo. Preserva
    o que foi digitado (`old()`).
  - `resources/views/components/dropzone-anexos.blade.php` (reaproveitado do 156/e-SIC):
    área de arrastar arquivos. O comportamento está em `resources/js/dropzone-anexos.js`
    e o visual em `resources/css/components/dropzone.css`.
  - Estilo dos campos: `resources/css/pages/cadastro.css` (classes `cadastro-*`).
- Não pede nome, CPF nem e-mail: o cidadão já está logado (minimização de dados).

## Etapa 1.7 — Validação do envio

- **Objetivo:** barrar dados inválidos no servidor antes de chegar ao service.
- **Arquivo (criado):** `app/Http/Requests/RegistrarManifestacaoRequest.php`
- **Regras:**
  - `descricao`: obrigatória, de 20 a 2000 caracteres.
  - Só os campos do tipo recebem regra; os outros são descartados.
  - `anexos`: até 5 arquivos, de até 10 MB cada, formatos JPG, JPEG, PNG,
    PDF, MP4, MOV, WEBM.
  - Soma dos anexos: até 25 MB (verificada em `after()`).
- Em erro, o Laravel devolve o cidadão ao formulário com as mensagens e os
  dados preenchidos.

## Etapa 1.8 — Protocolo

- **Objetivo:** gerar o número que o cidadão usa para acompanhar.
- **Arquivo (já existia):** `app/Services/GeradorDeProtocolo.php`
- Formato `OUV-AAAA-NNNNNN`. A lógica é **provisória**: sequencial do ano =
  contagem + 1, então pode repetir número se um registro for apagado.

## Etapa 1.9 — Gravação dos anexos

- **Objetivo:** guardar os arquivos com segurança.
- **Arquivo (já existia):** `app/Services/ArmazenadorDeAnexos.php`
  - `armazenar()`: grava no disco privado `local`, em
    `manifestacoes-ouvidoria/{protocolo}`, e falha com exceção se o disco não
    gravar.
  - `apagar()`: remove arquivos já gravados quando algo dá errado.
- Os arquivos **não** ficam acessíveis por URL pública.

## Etapa 1.10 — O service que registra

- **Objetivo:** executar o caso de uso "registrar manifestação" de forma
  atômica (tudo grava ou nada grava).
- **Arquivo (criado):** `app/Services/OuvidoriaService.php`
- **`registrarManifestacao(usuário, tipo, dados, anexos)`:**
  1. abre transação (`DB::beginTransaction`);
  2. cria a manifestação com o protocolo gerado;
  3. grava o recebimento no histórico (entra na Etapa 3.6);
  4. grava cada anexo no disco e cria a linha em `anexos_manifestacao_ouvidoria`;
  5. `commit`; registra `info`;
  6. em falha: `rollBack`, apaga arquivos já gravados, registra `error` e lança
     `SolicitacaoNaoPodeSerRegistrada`.
- **`buscarManifestacaoDoUsuario(usuário, protocolo)`:** só encontra se for do
  usuário; senão registra aviso de segurança e devolve 404.

## Etapa 1.11 — Controller

- **Objetivo:** só orquestrar (receber, chamar o service, devolver resposta).
- **Arquivo (criado):** `app/Http/Controllers/OuvidoriaController.php`
  - `index` → página inicial.
  - `criarManifestacao($tipo)` → formulário.
  - `registrarManifestacao(request, $tipo)` → chama o service e redireciona para
    a confirmação.
  - `confirmacaoManifestacao(request, $protocolo)` → busca e mostra.

## Etapa 1.12 — Confirmação (tela)

- **Objetivo:** mostrar o protocolo para o cidadão guardar.
- **Arquivos:**
  - `resources/views/servicos/ouvidoria/manifestacao/confirmacao.blade.php` (criado):
    protocolo, tipo, órgão e quantidade de anexos.
  - `resources/css/components/confirmacao.css` (já existia, usado também por 156 e e-SIC).

## Etapa 1.13 — Erros e logs

- **Objetivo:** o cidadão vê mensagem clara; o detalhe técnico vai só para o log.
- **Arquivos:**
  - `app/Exceptions/SolicitacaoNaoPodeSerRegistrada.php`: mensagem amigável
    ("Não foi possível concluir sua solicitação...").
  - `bootstrap/app.php`: converte essa exceção em volta ao formulário com os dados
    e o código de atendimento (`identificador_correlacao`).
  - `app/Http/Middleware/AdicionarContextoDaRequisicao.php`: cria o código
    de correlação e guarda o IP (oculto) em cada requisição.
  - `app/Logging/ContextoDaFalha.php` e `app/Logging/RegistroDeSeguranca.php`:
    contexto da falha sem dados pessoais; aviso de segurança com IP em `seguranca.log`.
- **Eventos da Ouvidoria:**

| Nível | Evento |
|---|---|
| `info` | `manifestacao_ouvidoria_registrada` |
| `error` | `manifestacao_ouvidoria_nao_registrada` |
| `warning` (`seguranca.log`) | `manifestacao_ouvidoria_nao_encontrada_para_o_usuario` |

- Nunca entram em log: descrição, órgão, local, servidor citado e nomes de arquivo.

## Etapa 1.14 — Testes e documento

- **Arquivos:** `tests/Feature/OuvidoriaManifestacaoTest.php` (criado) e
  `docs/2026-10-ouvidoria-manifestacoes.md` (criado).

## Resumo da requisição de registro (para o diagrama)

```text
Navegador
  POST /ouvidoria/manifestar/{tipo}   (formulário + anexos)
    │
    ▼
AdicionarContextoDaRequisicao  (código de correlação + IP oculto)
    ▼
Middleware auth  ── não logado ──► /entrar
    ▼
Rota (tipo restrito às 4 chaves; senão 404)
    ▼
RegistrarManifestacaoRequest  ── inválido ──► volta ao formulário com erros e dados
    ▼
OuvidoriaController::registrarManifestacao
    ▼
OuvidoriaService::registrarManifestacao   [transação]
    ├─ GeradorDeProtocolo::gerar            → OUV-AAAA-NNNNNN
    ├─ INSERT manifestacoes_ouvidoria
    ├─ StatusDaSolicitacaoService::registrarRecebimento
    │     └─ INSERT movimentacoes_solicitacao ("recebida")
    ├─ ArmazenadorDeAnexos::armazenar       → disco privado local
    ├─ INSERT anexos_manifestacao_ouvidoria
    └─ commit  (falha: rollBack + apagar arquivos + SolicitacaoNaoPodeSerRegistrada)
    ▼
302 → GET /ouvidoria/manifestacoes/{protocolo}  → tela de confirmação
```

---

# PARTE 2 — Ajustes feitos entre as duas entregas

## Etapa 2.1 — Remoção do "sigilo de identidade"

- **Objetivo:** tirar a opção "quero que minha identidade seja mantida em sigilo"
  (visual e lógica). A denúncia anônima será um caminho próprio, mais adiante.
- **Arquivos alterados:** `app/Models/ManifestacaoOuvidoria.php` (campos da denúncia
  ficam `data_do_fato`, `local_do_fato`, `servidor_ou_setor`),
  `app/Http/Requests/RegistrarManifestacaoRequest.php`,
  `app/Services/OuvidoriaService.php`, a migration `2026_10_07_000001`,
  `criar.blade.php`, `confirmacao.blade.php`, `tests/Feature/OuvidoriaManifestacaoTest.php`
  (novo teste: o formulário da denúncia não oferece sigilo) e
  `docs/2026-10-ouvidoria-manifestacoes.md`.

## Etapa 2.2 — Botão "denúncia anônima" na tela de login

- **Objetivo:** dar destaque ao botão, só no visual (ainda sem fluxo; o link é `#`).
- **Arquivos alterados:** `resources/views/portal/parciais/formulario-entrar.blade.php`
  (botão abaixo do formulário) e `resources/css/acesso.css`
  (classes `.acesso-denuncia-anonima*`, vermelho suave).

## Etapa 2.3 — Menu superior

- **Objetivo:** "Minhas solicitações" e "Sobre156" navegam entre páginas.
- **Arquivos:**
  - `resources/views/layouts/app.blade.php` (alterado): links do menu, com destaque
    (`is-active`) pela rota atual.
  - `routes/web.php` (alterado): rota pública `sobre156.index`.
  - `resources/views/portal/sobre-156.blade.php` (criado, em branco no início).
  - `resources/views/portal/minhas-solicitacoes.blade.php` (existia vazio; vira a lista na Etapa 3.12).
  - `tests/Feature/MenuPrincipalTest.php` (criado).
- Pendente: o menu mobile ainda não tem Minhas solicitações, Sobre156 e Sair.

## Etapa 2.4 — Tamanhos de fonte

- **Objetivo:** corpo do texto 20 px, botões 18 px e subtítulos 22 px.
- **Arquivos alterados:** `resources/css/design-system/tokens.css` (novo `--text-22`),
  `design-system/base.css`, `acesso.css`, `pages/cadastro.css`, `pages/servicos.css`,
  `pages/categorias-servico.css`, `pages/esic.css`, `pages/ouvidoria.css`,
  `components/confirmacao.css`.

---

# PARTE 3 — Acompanhamento das solicitações (08/10)

Fluxo do cidadão: **menu "Minhas solicitações" → lista única com abas → abre uma
solicitação → vê o andamento, os dados e baixa os anexos.**

Contexto: o projeto é só frontend. Não existe API nem sistema interno que mude
o status. Por isso a regra de mudança de status fica num service e há um comando
de desenvolvimento para simular o andamento.

## Etapa 3.1 — Banco: coluna `status`

- **Objetivo:** guardar a situação atual de cada solicitação.
- **Arquivo (criado):** `database/migrations/2026_10_08_000001_adicionar_status_as_solicitacoes.php`
- Adiciona `status` (texto de 20, padrão `recebida`, indexado) em
  `solicitacoes_156`, `solicitacoes_esic` e `manifestacoes_ouvidoria`.
- Não é preenchível por formulário: só o service altera.

## Etapa 3.2 — Banco: tabela de histórico

- **Objetivo:** registrar cada mudança de status (linha do tempo), sem sobrescrever.
- **Arquivo (criado):** `database/migrations/2026_10_08_000002_create_movimentacoes_solicitacao_table.php`
- **Colunas:** `solicitacao_type` + `solicitacao_id` (relação polimórfica, serve às
  três tabelas), `status_anterior`, `status_novo`, `mensagem` (texto mostrado ao
  cidadão), `registrado_por` e `created_at`. Só recebe inclusões.
- A migration também cria a movimentação "recebida" para as solicitações que já
  existiam, usando a data de criação delas.

## Etapa 3.3 — Model do histórico e regras de status

- **Objetivo:** guardar o vocabulário e as regras de status num lugar só.
- **Arquivo (criado):** `app/Models/MovimentacaoDaSolicitacao.php`
  - `STATUS`: recebida, em análise, encaminhada, em atendimento, concluída.
  - Transições permitidas: recebida → em análise; em análise → encaminhada,
    em atendimento ou concluída; encaminhada → em atendimento ou concluída;
    em atendimento → concluída; concluída é final.
  - `rotuloDoStatus()` e `transicaoEhPermitida()`.

## Etapa 3.4 — Relação com as três solicitações

- **Objetivo:** cada solicitação enxerga seu histórico.
- **Arquivos alterados:** `app/Models/Solicitacao156.php`, `SolicitacaoEsic.php` e
  `ManifestacaoOuvidoria.php`: cada um ganhou `movimentacoes()` (ordenado por `id`).
- `status` não está em `$fillable`.

## Etapa 3.5 — Apelidos do relacionamento polimórfico

- **Objetivo:** gravar `solicitacao_156`, `solicitacao_esic` e
  `manifestacao_ouvidoria` no banco, em vez do nome da classe.
- **Arquivo (alterado):** `app/Providers/AppServiceProvider.php` (`Relation::morphMap`).

## Etapa 3.6 — Service de status

- **Objetivo:** único ponto que muda status, para que qualquer origem futura
  (API, tela interna) use as mesmas regras.
- **Arquivo (criado):** `app/Services/StatusDaSolicitacaoService.php`
  - `registrarRecebimento($solicitacao)`: cria a movimentação "recebida" (sem
    transação própria: roda dentro da transação de quem acabou de criar).
  - `alterarStatus($solicitacao, $novoStatus, $mensagem, $registradoPor)`: abre
    transação, relê a solicitação com bloqueio (`lockForUpdate`), valida a
    transição, atualiza o status e cria a movimentação. Transição inválida lança
    `TransicaoDeStatusInvalida` (aviso no log, nada é alterado).
- **Arquivos alterados:** `app/Services/Servico156Service.php`,
  `app/Services/EsicService.php` e `app/Services/OuvidoriaService.php` passam a
  chamar `registrarRecebimento` logo após criar a solicitação, dentro da mesma
  transação.
- **Eventos:** `status_da_solicitacao_alterado` (info),
  `transicao_de_status_recusada` (warning), `status_da_solicitacao_nao_alterado` (error).

## Etapa 3.7 — Exceções

- **Arquivos (criados):**
  - `app/Exceptions/TransicaoDeStatusInvalida.php`: regra de negócio violada.
  - `app/Exceptions/AnexoIndisponivel.php`: arquivo ausente do disco; mensagem
    amigável para o cidadão.
- **Arquivo (alterado):** `bootstrap/app.php` ganhou o tratamento de `AnexoIndisponivel`:
  volta ao detalhe da solicitação com a mensagem e o código de atendimento.

## Etapa 3.8 — Download de anexo (camada de arquivos)

- **Objetivo:** entregar o arquivo sempre como download, nunca exibido pelo navegador.
- **Arquivo (alterado):** `app/Services/ArmazenadorDeAnexos.php` ganhou `baixar()`:
  confere se o arquivo existe (senão `AnexoIndisponivel`) e responde com download
  e o cabeçalho `X-Content-Type-Options: nosniff`.

## Etapa 3.9 — Service da lista e do detalhe

- **Objetivo:** juntar os três serviços numa lista e abrir uma solicitação do usuário.
- **Arquivo (criado):** `app/Services/MinhasSolicitacoesService.php`
  - `TIPOS`: lista dos três serviços (rótulo, prefixo do protocolo, model). É o
    único lugar que liga protocolo, serviço e model.
  - `chaveDoProtocolo()`: descobre o serviço pelo prefixo do protocolo.
  - `listar(usuário, serviço, busca)`: roda 3 consultas, une em PHP, ordena da mais
    recente para a mais antiga e pagina de 10 em 10.
  - `buscarDetalheDoUsuario(usuário, protocolo)`: devolve resumo, campos enviados,
    anexos (com posição 1, 2, 3...) e movimentações.
  - `baixarAnexoDoUsuario(usuário, protocolo, posição)`: confere o dono e usa o
    `ArmazenadorDeAnexos`.
  - Sempre filtra por `user_id`. Protocolo de outra pessoa = 404 + aviso em `seguranca.log`.
  - Eventos: `anexo_baixado` (info), `anexo_ausente_no_disco` (error),
    `solicitacao_nao_encontrada_para_o_usuario` (warning, `seguranca.log`).
- Os dados vão para a tela como objetos simples (sem classes próprias).

## Etapa 3.10 — Validação dos filtros

- **Arquivo (criado):** `app/Http/Requests/ListarMinhasSolicitacoesRequest.php`
  - `servico` precisa ser uma das chaves de `MinhasSolicitacoesService::TIPOS`.
  - `busca`: até 20 caracteres, só letras, números e hífen.
  - Em erro, volta para `minhas-solicitacoes.index` com a mensagem.

## Etapa 3.11 — Controller e rotas

- **Arquivo (criado):** `app/Http/Controllers/MinhasSolicitacoesController.php`
  com `index`, `show` e `baixarAnexo`.
- **Arquivo (alterado):** `routes/web.php`, grupo `auth`:

| Método | URL | Nome da rota | Método do controller |
|---|---|---|---|
| GET | `/minhas-solicitacoes` | `minhas-solicitacoes.index` | `index` |
| GET | `/minhas-solicitacoes/{protocolo}` | `minhas-solicitacoes.show` | `show` |
| GET | `/minhas-solicitacoes/{protocolo}/anexos/{posicao}` | `minhas-solicitacoes.anexos.baixar` | `baixarAnexo` |

- `{protocolo}` aceita `156-`, `ESIC-` ou `OUV-` (variável no topo de `web.php`).
  `{posicao}` é 1 a 999.

## Etapa 3.12 — Telas e estilo

- **Arquivos:**
  - `resources/views/portal/minhas-solicitacoes.blade.php` (alterado, era vazio):
    título, busca por protocolo, abas (Todas, Portal 156, e-SIC, Ouvidoria), lista,
    estados vazios (sem solicitações e busca sem resultado) e paginação.
  - `resources/views/portal/minhas-solicitacoes-detalhe.blade.php` (criado):
    cabeçalho com status, "Andamento" (linha do tempo), "Dados enviados" e "Anexos".
  - `resources/views/components/status-da-solicitacao.blade.php` (criado):
    selo do status, sempre com texto e ícone.
  - `resources/css/pages/minhas-solicitacoes.css` (criado) e a linha de importação em
    `resources/css/app.css` (alterado).

## Etapa 3.13 — Comando de simulação (só desenvolvimento)

- **Objetivo:** mudar o status sem back-office, para ver a linha do tempo funcionando.
- **Arquivo (alterado):** `routes/console.php`, comando `solicitacao:avancar-status`.
  - Uso: `php artisan solicitacao:avancar-status OUV-2026-000001 em_analise "mensagem"`.
  - Recusa em produção, status inexistente, protocolo inexistente e transição inválida.
  - Usa o `StatusDaSolicitacaoService`, com `registrado_por = 'comando artisan'`.

## Etapa 3.14 — Testes e documento

- **Arquivos (criados):** `tests/Feature/MinhasSolicitacoesTest.php`,
  `tests/Feature/StatusDaSolicitacaoTest.php`, `docs/2026-10-minhas-solicitacoes.md`.
- **Cobertura:** lista dos três serviços, abas, busca, isolamento entre usuários, 404,
  paginação, linha do tempo, download do dono e de outro usuário, anexo ausente,
  transições válidas e inválidas, instância desatualizada, comando.

## Resumo das requisições (para o diagrama)

**Lista**
```text
GET /minhas-solicitacoes?servico=&busca=&page=
  → auth → ListarMinhasSolicitacoesRequest (filtro inválido volta à lista com erro)
  → MinhasSolicitacoesController::index
  → MinhasSolicitacoesService::listar
       3 consultas por user_id (solicitacoes_156 / solicitacoes_esic / manifestacoes_ouvidoria)
       → une, ordena, pagina (10)
  → view portal/minhas-solicitacoes
```

**Detalhe**
```text
GET /minhas-solicitacoes/{protocolo}
  → auth → MinhasSolicitacoesController::show
  → MinhasSolicitacoesService::buscarDetalheDoUsuario
       prefixo do protocolo → model → busca com user_id
       não achou → aviso em seguranca.log + 404
       achou → campos + anexos + movimentacoes
  → view portal/minhas-solicitacoes-detalhe
```

**Download de anexo**
```text
GET /minhas-solicitacoes/{protocolo}/anexos/{posicao}
  → auth → MinhasSolicitacoesController::baixarAnexo
  → MinhasSolicitacoesService::baixarAnexoDoUsuario  (confere o dono)
  → ArmazenadorDeAnexos::baixar
       existe  → download + nosniff
       ausente → AnexoIndisponivel → volta ao detalhe com mensagem e código
```

**Mudança de status (simulada hoje, API no futuro)**
```text
php artisan solicitacao:avancar-status {protocolo} {status} {mensagem?}
  → routes/console.php
  → StatusDaSolicitacaoService::alterarStatus     [transação + lockForUpdate]
       valida transição (MovimentacaoDaSolicitacao::transicaoEhPermitida)
       UPDATE status da solicitação
       INSERT movimentacoes_solicitacao
```

---

# PARTE 4 — Acabamento (08/10)

## Etapa 4.1 — Estrutura enxuta

- **Objetivo:** não criar pastas novas. As pastas `app/Support`, `app/Console` e
  `app/Models/Concerns`, criadas durante a Parte 3, foram esvaziadas e seu conteúdo
  distribuído:
  - regras de status → `MovimentacaoDaSolicitacao` (Etapa 3.3);
  - lista de serviços → `MinhasSolicitacoesService::TIPOS` (Etapa 3.9);
  - histórico nos models → `movimentacoes()` em cada model (Etapa 3.4);
  - comando → `routes/console.php` (Etapa 3.13);
  - dados da tela → objetos simples.
- **Ação manual sua:** apagar as pastas vazias `app/Support`, `app/Models/Concerns`,
  `app/Console` e a pasta `_to_delete`.
- **No diagrama:** não desenhe `Support`, `Concerns` nem `AlteracaoDeStatus`,
  `ResumoDaSolicitacao`, `DetalheDaSolicitacao`, `StatusDaSolicitacao` ou
  `TiposDeSolicitacao`: essas classes deixaram de existir.

## Etapa 4.2 — Identidade de cor por serviço

- **Objetivo:** Portal 156 amarelo, e-SIC verde, Ouvidoria vermelho (mesmas cores
  dos cards de `servicos.css`).
- **Arquivos alterados:** `resources/css/pages/minhas-solicitacoes.css` (classes
  `.tipo-portal156`, `.tipo-esic`, `.tipo-ouvidoria`) e as duas views da lista e do
  detalhe (classe `tipo-{serviço}` na faixa lateral, na etiqueta e nas abas).
- Os selos de status mantêm as próprias cores. Decisão em aberto: trocá-los para
  azul e cinza, se parecerem com as cores dos serviços.

---

# Mapa final dos arquivos

```text
routes/
  web.php                       rotas das 3 áreas (Ouvidoria, Minhas solicitações, Sobre156)
  console.php                   comando solicitacao:avancar-status
bootstrap/app.php               tratamento de SolicitacaoNaoPodeSerRegistrada e AnexoIndisponivel
app/
  Http/Controllers/             OuvidoriaController, MinhasSolicitacoesController
  Http/Requests/                RegistrarManifestacaoRequest, ListarMinhasSolicitacoesRequest
  Http/Middleware/              AdicionarContextoDaRequisicao
  Services/                     OuvidoriaService, StatusDaSolicitacaoService,
                                MinhasSolicitacoesService, ArmazenadorDeAnexos, GeradorDeProtocolo
  Models/                       ManifestacaoOuvidoria, AnexoManifestacaoOuvidoria,
                                MovimentacaoDaSolicitacao (+ movimentacoes() nos 3 models)
  Exceptions/                   SolicitacaoNaoPodeSerRegistrada, TransicaoDeStatusInvalida, AnexoIndisponivel
  Providers/AppServiceProvider  morphMap
  Logging/                      ContextoDaFalha, RegistroDeSeguranca
database/migrations/            2026_10_07_000001, 2026_10_07_000002, 2026_10_08_000001, 2026_10_08_000002
resources/views/
  servicos/ouvidoria/           ouvidoria-pagina-inicial, manifestacao/criar, manifestacao/confirmacao
  portal/                       minhas-solicitacoes, minhas-solicitacoes-detalhe, sobre-156
  portal/parciais/              formulario-entrar (botão de denúncia anônima)
  components/                   status-da-solicitacao, dropzone-anexos
  layouts/app.blade.php         menu superior
resources/css/pages/            ouvidoria.css, minhas-solicitacoes.css (+ import em app.css)
tests/Feature/                  OuvidoriaManifestacaoTest, MinhasSolicitacoesTest,
                                StatusDaSolicitacaoTest, MenuPrincipalTest
docs/                           2026-10-ouvidoria-manifestacoes.md, 2026-10-minhas-solicitacoes.md
```

# Pendências já conhecidas

- Protocolo provisório (contagem + 1), que agora é a chave que o cidadão consulta.
- Prefixos do protocolo repetidos nos services (`PREFIXO_DO_PROTOCOLO`) e em `TIPOS`.
- Validação de tamanho dos anexos no navegador (`dropzone-anexos.js`).
- Menu mobile sem Minhas solicitações, Sobre156 e Sair; link "Início" ainda `#`.
- Denúncia anônima e sigilo de identidade (caminho próprio, ainda sem fluxo).
- Retenção do `seguranca.log` e destino de login/logout; limite de tentativas de login;
  páginas de erro 404, 419, 429, 500 e 503; valores do `php.ini`.
- Tela do servidor público, avaliação, cancelamento, notificações e prazos
  (fora do escopo).
