# Minhas solicitações — acompanhamento (outubro/2026)

Complementa `2026-10-portal-156-solicitacoes.md`,
`2026-10-ouvidoria-manifestacoes.md` e `2026-10-padroes-de-erro-e-logs.md`.

---

## 1. Problema

O cidadão registra solicitações no Portal 156, no e-SIC e na Ouvidoria, mas
não tem onde vê-las depois nem como saber em que etapa estão. A página
"Minhas solicitações" existia em branco. Também não havia status nem
histórico em nenhuma das três tabelas.

## 2. Decisões

### Contexto: projeto só de frontend

Não há API nem sistema interno que altere o status. A tela do servidor
público não existe neste projeto. Por isso:

- A regra de mudança de status fica em um service (`StatusDaSolicitacaoService`),
  que qualquer origem futura (API, tela interna) deve chamar.
- Para testar e demonstrar o acompanhamento existe o comando
  `php artisan solicitacao:avancar-status`, definido em `routes/console.php`,
  que usa esse mesmo service. Ele não funciona em produção.

### Estrutura enxuta

Nenhuma pasta nova foi criada. As regras ficam em arquivos que já existiam:
status e transições em `MovimentacaoDaSolicitacao`; lista dos três serviços
(rótulo, prefixo do protocolo, model) em `MinhasSolicitacoesService::TIPOS`;
dados entregues às telas como objetos simples, sem classes próprias. Se o
projeto crescer a ponto de pedir isso, extrair é uma mudança pequena.

### Status (os mesmos para os três serviços)

`recebida` → `em_analise` → `encaminhada` → `em_atendimento` → `concluida`

Transições permitidas (`MovimentacaoDaSolicitacao::TRANSICOES_PERMITIDAS`):

| De | Para |
|---|---|
| recebida | em_analise |
| em_analise | encaminhada, em_atendimento, concluida |
| encaminhada | em_atendimento, concluida |
| em_atendimento | concluida |
| concluida | — (final) |

Não há retrocesso nem salto sobre "em_analise". Quando o e-SIC ou a Ouvidoria
precisarem de status próprios (por exemplo, "indeferida"), entram como novos
itens e novas transições nessa mesma classe, sem alterar a estrutura.

### Dados

- Coluna `status` (padrão `recebida`, indexada) em `solicitacoes_156`,
  `solicitacoes_esic` e `manifestacoes_ouvidoria`. Não é preenchível por
  formulário: só o service a altera.
- Tabela `movimentacoes_solicitacao`, só com inclusões (nunca alterada):
  `solicitacao_type` e `solicitacao_id` (relação polimórfica com apelidos
  `solicitacao_156`, `solicitacao_esic` e `manifestacao_ouvidoria`),
  `status_anterior`, `status_novo`, `mensagem` (texto mostrado ao cidadão),
  `registrado_por` e `created_at`.
- Migration de preenchimento: as solicitações que já existiam ganham a
  movimentação "recebida", com a data em que foram criadas.
- Cada service de registro grava a movimentação inicial dentro da mesma
  transação da solicitação.

### Mudança de status e concorrência

`StatusDaSolicitacaoService::alterarStatus` roda em transação, relê a
solicitação com `lockForUpdate` e só então valida a transição. Duas mudanças
simultâneas não conseguem passar pela mesma etapa. Transição inválida lança
`TransicaoDeStatusInvalida`, com aviso no log e sem alterar nada.

### Lista (`GET /minhas-solicitacoes`)

- Lista única com os três serviços, da mais recente para a mais antiga.
- Abas: Todas, Portal 156, e-SIC e Ouvidoria. Busca por protocolo.
- Cada linha: serviço, título (nome do serviço 156, assunto do e-SIC ou tipo da
  manifestação), protocolo, data e status (texto + ícone, nunca só cor).
- As três consultas são unidas em PHP e paginadas depois (10 por página). O
  volume por cidadão é pequeno; se crescer, troca-se por uma consulta única.
- Estados: lista vazia, busca sem resultado.

### Detalhe (`GET /minhas-solicitacoes/{protocolo}`)

- O prefixo do protocolo (`156-`, `ESIC-`, `OUV-`) indica a tabela.
- A busca é sempre limitada ao usuário logado. Protocolo inexistente ou de
  outra pessoa devolve 404 e grava aviso no `seguranca.log`.
- Mostra os dados enviados, os anexos e a linha do tempo (lista ordenada, com
  data, hora, status e mensagem).

### Anexos (`GET /minhas-solicitacoes/{protocolo}/anexos/{posicao}`)

- Só o dono baixa. O arquivo sai do disco privado como download
  (`Content-Disposition: attachment`), com `X-Content-Type-Options: nosniff`,
  para que o navegador nunca o execute ou exiba como página.
- `posicao` é a ordem do anexo na solicitação (começa em 1). O e-SIC guarda
  um anexo na própria linha; os outros dois, em tabela própria; a posição
  unifica os dois formatos.
- Arquivo ausente do disco: erro registrado no log e mensagem ao cidadão sem
  detalhe técnico.

### Logs

| Nível | Evento | Contexto |
|---|---|---|
| `info` | `status_da_solicitacao_alterado` | protocolo, status anterior e novo, quem registrou |
| `warning` | `transicao_de_status_recusada` | protocolo, status atual e pedido |
| `error` | `status_da_solicitacao_nao_alterado` | exceção sem dados pessoais, protocolo |
| `info` | `anexo_baixado` | usuario_id, protocolo, posição |
| `error` | `anexo_ausente_no_disco` | usuario_id, protocolo, posição |
| `warning` (`seguranca.log`) | `solicitacao_nao_encontrada_para_o_usuario` | usuario_id, protocolo, IP |

Mensagens digitadas para o cidadão e nomes de arquivo nunca entram em log.

## 3. Fora do escopo

- Tela do servidor público e integração com API (projeto só de frontend).
- Avaliação do atendimento (só após "concluída").
- Cancelamento pelo cidadão.
- Notificações por e-mail ou SMS quando o status mudar.
- Prazos de atendimento.
- Protocolo oficial: o gerador continua provisório (contagem + 1), e agora é
  o identificador que o cidadão consulta.
