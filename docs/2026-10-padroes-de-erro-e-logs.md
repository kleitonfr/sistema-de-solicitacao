# Padrões de erro, logs e limites de anexos (outubro/2026)

Complementa `2026-10-portal-156-solicitacoes.md`.

---

## 1. Problema

- Nenhum `try/catch` nem log em `app/`; falhas apareciam como erro 500 padrão.
- O disco `local` tem `throw => false`: quando a gravação falha, `store()`
  devolve `false` sem lançar exceção. A solicitação era salva com anexo
  inexistente, sem erro e sem log.
- Arquivos gravados dentro da transação ficavam órfãos se ela fosse desfeita.
- O teto de uma requisição era 150 MB (5 × 30 MB), sem limite total.

## 2. Decisões

### Limites de anexos (Portal 156)

| Regra | Valor |
|---|---|
| Quantidade | 5 arquivos |
| Foto | até 5 MB |
| Vídeo | até 10 MB |
| Total por solicitação | até 25 MB (aceita exatamente 25 MB) |
| e-SIC | 1 arquivo, até 10 MB (inalterado) |

Constantes em `RegistrarSolicitacao156Request`. O total soma só arquivos
válidos (upload quebrado já recebe a mensagem da regra `file`).

**Configuração de PHP e servidor web (a aplicar em cada ambiente):**

| Diretiva | Valor | Motivo |
|---|---|---|
| `upload_max_filesize` | `10M` | maior arquivo individual |
| `post_max_size` | `32M` | 25 MB de anexos + campos e overhead |
| `max_input_time` | `120` | 25 MB em rede móvel lenta |
| Apache `LimitRequestBody` / Nginx `client_max_body_size` | no mínimo o `post_max_size` | o servidor web não pode barrar antes do PHP |
| `zend.exception_ignore_args` | `On` em produção | evita argumentos de funções no stack trace dos logs |

### Padrão de transação em services

Todo service que grava no banco segue o mesmo formato:

```
DB::beginTransaction();
try {
    ... gravações ...
    DB::commit();
} catch (Throwable $erro) {
    DB::rollBack();
    ... compensar o que não é transacional (arquivos) ...
    Log::error('<evento>_nao_<acao>', [...ContextoDaFalha::deExcecao($erro), ...contexto]);
    throw <ExcecaoDeDominio>::por($erro);
}
Log::info('<evento>_<acao>', [...contexto]);
```

- Controllers não capturam exceções.
- O service converte a falha em exceção de domínio
  (`SolicitacaoNaoPodeSerRegistrada`); `bootstrap/app.php` a transforma em
  redirecionamento de volta ao formulário, com os dados preenchidos e a mensagem
  em `erro_geral`, que inclui o código de atendimento.
- `SolicitacaoNaoPodeSerRegistrada::report()` devolve `true` para o Laravel não
  reportar de novo: o service já registrou a falha, e a causa original pode ter
  valores do formulário na mensagem.
- Anexos passam por `ArmazenadorDeAnexos`, que trata `store() === false` como
  `FalhaAoArmazenarAnexo` e apaga os arquivos já gravados na compensação.

### Logs

| Nível | Uso | Exemplo |
|---|---|---|
| `info` | evento de negócio concluído (sucesso) | `solicitacao_156_registrada`, `login_realizado` |
| `warning` | anomalia esperada | `anexo_orfao_nao_removido`, eventos de segurança |
| `error` | falha inesperada | `solicitacao_156_nao_registrada` |

- Contexto padrão em português: `usuario_id`, `protocolo`, `servico_id`,
  `quantidade_de_anexos`, `identificador_correlacao`.
- **Nunca** entram em log: senha, CPF, e-mail, telefone, descrição, endereço e o
  nome original dos anexos. `ContextoDaFalha` descarta o SQL com valores de uma
  `QueryException`.
- `AdicionarContextoDaRequisicao` (primeiro middleware global) gera o
  identificador de correlação, grava no `Context` do Laravel (entra em todos os
  logs) e o devolve no cabeçalho `X-Identificador-Correlacao`.
- **IP só no canal `seguranca`** (`storage/logs/seguranca.log`), via
  `RegistroDeSeguranca`, para: login falho e protocolo não encontrado para o
  usuário (inexistente ou de outro cidadão). O IP vem do contexto oculto do
  middleware; os services não conhecem HTTP.

| Canal | Arquivo | Retenção padrão |
|---|---|---|
| `daily` (padrão via `LOG_STACK=daily`) | `laravel.log` | 30 dias (`LOG_DAILY_DAYS`) |
| `seguranca` | `seguranca.log` | 90 dias (`LOG_SEGURANCA_DAYS`) |

Os arquivos antigos são apagados automaticamente pela rotação.

## 3. Consequências e pendências de deploy

- Os prazos de retenção são provisórios: **validar com o DPO e o jurídico**.
- `trustProxies`: se houver proxy, balanceador ou WAF em produção, configurar,
  senão o IP gravado será o do proxy.
- Restringir o acesso a `storage/logs` no servidor e nos backups (há IP e
  identificador de usuário).
- `LOG_LEVEL=info` em produção.
- O `.env` existente precisa de `LOG_STACK=daily`; o `.env.example` já foi atualizado.
- O `CadastroService` continua sendo um stub (sem transação nem log).
- Falta limite de tentativas no login.
- Ainda não existem: validação de tamanho no `dropzone-anexos.js` e páginas de
  erro 404, 419, 429, 500 e 503 (só a 413 foi criada).
