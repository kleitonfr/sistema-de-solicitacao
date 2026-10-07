# Ouvidoria — formulário de manifestações (outubro/2026)

Complementa `2026-10-portal-156-solicitacoes.md` e
`2026-10-padroes-de-erro-e-logs.md`.

---

## 1. Problema

A página da Ouvidoria só informava. Os quatro botões (Reclamação, Denúncia,
Sugestão, Elogio) levavam para `#`, e o cidadão não tinha como registrar uma
manifestação.

## 2. Decisões

### Um formulário só, orquestrado pelo tipo na rota

- Rotas (todas no grupo `auth`):
  - `GET  /ouvidoria/manifestar/{tipo}` → `ouvidoria.manifestacao.criar`
  - `POST /ouvidoria/manifestar/{tipo}` → `ouvidoria.manifestacao.registrar`
  - `GET  /ouvidoria/manifestacoes/{protocolo}` → `ouvidoria.manifestacao.confirmacao`
- `{tipo}` é restrito, na própria rota, às chaves de
  `ManifestacaoOuvidoria::TIPOS` (`reclamacao`, `denuncia`, `sugestao`,
  `elogio`). Qualquer outro valor devolve 404.
- A constante `ManifestacaoOuvidoria::TIPOS` concentra o que varia por tipo:
  rótulos, textos e quais campos específicos existem, no mesmo estilo de
  `SolicitacaoEsic::ASSUNTOS`. Um tipo novo é uma entrada nova no mapa, sem
  formulário novo.
- `OuvidoriaController` só orquestra; `OuvidoriaService` registra e busca.

### Campos

| Campo | Reclamação | Denúncia | Sugestão | Elogio |
|---|---|---|---|---|
| `orgao_ou_setor` (opcional) | sim | sim | sim | sim |
| `descricao` (obrigatório, 20 a 2000) | sim | sim | sim | sim |
| `data_do_fato` e `local_do_fato` (opcionais) | sim | sim | não | não |
| `servidor_ou_setor` (opcional) | não | sim ("envolvido") | não | sim ("elogiado") |

- Só a descrição é obrigatória, para que as exigências do formulário não
  inviabilizem a manifestação.
- Só os campos do tipo recebem regra de validação; os demais são descartados
  (teste `test_campos_que_nao_pertencem_ao_tipo_sao_descartados`).
- Nome, CPF e e-mail não são pedidos: o cidadão já está autenticado
  (minimização de dados).
- Não há pedido de sigilo de identidade nesta entrega: manifestação
  identificada e manifestação anônima são caminhos distintos. O sigilo/anonimato
  será tratado junto com a denúncia anônima (seção 3).

### Anexos

| Regra | Valor |
|---|---|
| Quantidade | até 5 arquivos |
| Tamanho por arquivo | até 10 MB (sem distinção entre foto e vídeo) |
| Total por manifestação | até 25 MB (aceita exatamente 25 MB) |
| Formatos | JPG, PNG, PDF, MP4, MOV, WEBM |

- Gravados no disco privado `local`, em `manifestacoes-ouvidoria/{protocolo}`.
- Os valores de `php.ini` e do servidor web são os do doc de erros e logs
  (`upload_max_filesize=10M`, `post_max_size=32M`, `max_input_time=120`).
- A verificação do total no `RegistrarManifestacaoRequest` repete a do
  `RegistrarSolicitacao156Request`. Extrair as duas para um componente comum
  fica para depois que os testes passarem, para não alterar o fluxo do 156 sem
  poder executá-los.

### Dados e protocolo

- Tabelas `manifestacoes_ouvidoria` e `anexos_manifestacao_ouvidoria`
  (migrations `2026_10_07_000001` e `000002`).
- Protocolo `OUV-AAAA-NNNNNN`, pelo mesmo `GeradorDeProtocolo`. Ele continua
  provisório (contagem + 1): vale a pendência registrada no doc do 156.
- Nome do conceito: `ManifestacaoOuvidoria`, o termo usado pela página e pela
  legislação da Ouvidoria. É um termo distinto de `Solicitacao156` e
  `SolicitacaoEsic`; deve entrar no glossário do projeto.

### Erros e logs

Segue o padrão de `2026-10-padroes-de-erro-e-logs.md`:

- `OuvidoriaService::registrarManifestacao` usa `DB::beginTransaction()`,
  `DB::commit()` e `DB::rollBack()` dentro de `try/catch (Throwable)`. No
  `catch`: desfaz a transação, apaga os arquivos já gravados, registra
  `Log::error` e lança `SolicitacaoNaoPodeSerRegistrada`.
- O cidadão volta ao formulário com os dados preenchidos e a mensagem com o
  código de atendimento.
- `buscarManifestacaoDoUsuario` registra aviso de segurança (com IP) quando o
  protocolo não existe ou é de outro usuário, e relança o 404.

| Nível | Evento | Contexto |
|---|---|---|
| `info` | `manifestacao_ouvidoria_registrada` | `usuario_id`, `protocolo`, `tipo`, `quantidade_de_anexos` |
| `error` | `manifestacao_ouvidoria_nao_registrada` | exceção sem dados pessoais, `usuario_id`, `tipo`, `quantidade_de_anexos` |
| `warning` (`seguranca.log`) | `manifestacao_ouvidoria_nao_encontrada_para_o_usuario` | `usuario_id`, `protocolo`, IP |

Nunca entram em log: descrição, órgão, local, servidor citado e nome dos anexos.

## 3. Fora do escopo desta entrega

- **Denúncia anônima (e qualquer sigilo de identidade):** caso à parte, a ser
  tratado depois. Exige rota fora do `auth`, acompanhamento por código e nenhum
  registro de IP associado.
- **Status, prazo e histórico:** a tabela ainda não tem `status`. Entram junto
  com "Minhas solicitações", no mesmo desenho para 156, e-SIC e Ouvidoria.
- **Lista oficial de órgãos:** `orgao_ou_setor` é texto livre até a Ouvidoria
  fornecer a lista.
- **Validação de tamanho no navegador** (`dropzone-anexos.js`) e páginas de erro
  404, 419, 429, 500 e 503, já pendentes no doc de erros e logs.
