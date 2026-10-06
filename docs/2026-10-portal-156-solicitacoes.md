# Portal 156 — Catálogo e Solicitações (outubro/2026)

Registro do que foi implementado a partir do code review do Portal 156 e do
plano aprovado. Complementa `2026-09-redesign-sistema-651.md`, que cobre as
telas de acesso e não foi alterado.

Dados de categorias e serviços são **provisórios** (referência da Central 156
de Curitiba) até a definição do catálogo oficial pela equipe da API.

---

## 1. Fluxo

| Tela | Rota | Nome |
|---|---|---|
| Categorias | `GET /servicos/156/categorias` | `servicos.156.categorias` |
| Serviços da categoria | `GET /servicos/156/categorias/{slugDaCategoria}` | `servicos.156.servicos` |
| Formulário | `GET /servicos/156/solicitar/{slugDoServico}` | `servicos.156.solicitacao.criar` |
| Envio | `POST /servicos/156/solicitar/{slugDoServico}` | `servicos.156.solicitacao.registrar` |
| Confirmação (só o dono) | `GET /servicos/156/solicitacoes/{protocolo}` | `servicos.156.solicitacao.confirmacao` |

Todas dentro do grupo `auth`. e-SIC: a confirmação passou a usar o protocolo
na URL (`/e-sic/pedido/{protocolo}/confirmacao`).

## 2. Banco de dados

Migrations de criação foram **editadas** (não há dados a preservar): rodar
`php artisan migrate:fresh --seed`.

| Tabela | Observação |
|---|---|
| `categorias_servico` | ganhou `slug` (único) |
| `servicos_156` | FK `categoria_servico_id` (restrict); `slug` único; `nome` único por categoria |
| `solicitacoes_156` | FKs `user_id`, `servico156_id`, `categoria_servico_id` (todas restrict); localização em colunas (`cep`, `logradouro`, `numero`, `bairro`, `ponto_referencia`); cidade/UF fixas em `Solicitacao156::CIDADE_E_UF` |
| `anexos_solicitacao_156` | FK `solicitacao156_id` (cascade); `nome_original`, `caminho` |
| `solicitacoes_esic` | ganhou `user_id` (FK, restrict) |

Seeders: `Servico156Seeder` cria 26 serviços em 12 categorias. IPTU e Alvará
Comercial ficam sem serviços de propósito (o formulário pede localização).

## 3. Camadas

- **Controllers** só orquestram: `Servico156Controller` e `EsicController`.
- **Services** concentram o acesso ao banco: `Servico156Service`, `EsicService`
  e `GeradorDeProtocolo` (compartilhado). Quando a API assumir o catálogo, a
  troca fica restrita a essas classes.
- **Autorização:** a busca de solicitação filtra por `user_id`; quem não é dono
  recebe 404 (sem Policy por enquanto).
- **Requests:** `RegistrarSolicitacao156Request` (até 5 anexos, no máximo 25 MB no
  total; foto até 5 MB, vídeo até 10 MB; JPG, PNG, MP4, MOV, WEBM).
- **Anexos** gravados no disco privado `local`, em
  `solicitacoes-156/{protocolo}` e `solicitacoes-esic`. Não há rota de
  download ainda.
- **Protocolos:** `156-AAAA-NNNNNN` e `ESIC-AAAA-NNNNNN`.

## 4. Front

- Componente `<x-dropzone-anexos>` + `dropzone-anexos.js` + `components/dropzone.css`:
  um ou vários arquivos, cor verde (e-SIC) ou amarela (156).
- `components/confirmacao.css`: confirmação com protocolo, cor por modificador.
- Classes genéricas em `cadastro.css`: `cadastro-card--solicitacao`,
  `cadastro-label-fixo`, `cadastro-textarea`.
- Máscara `telefone-completo` (`(00) 00000-0000`) em `mascaras.js`.
- O casco de página, o link de voltar e os cards do módulo reaproveitam as classes
  `categorias-servico-*`.

## 5. Idioma

`lang/pt_BR/validation.php` (mensagens e nomes amigáveis dos campos) e
`lang/pt_BR.json` (páginas de erro). `APP_LOCALE=pt_BR` no `.env` e no
`.env.example`. Com `pt_BR`, `trans_choice` trata 0 e 1 como singular
("0 categoria").

## 6. Problemas do review tratados

| Achado | Tratamento |
|---|---|
| Solicitações sem dono, URL com id sequencial | `user_id` + busca filtrada pelo dono + protocolo na URL |
| Anexos no disco público | disco privado |
| Protocolo com `lockForUpdate` ineficaz no SQLite | **não resolvido**: lógica isolada em `GeradorDeProtocolo`, aguardando a definição oficial |
| Validação em inglês | `lang/pt_BR` |
| Busca de categorias dependente do banco | filtro e ordenação no PHP, sem acento |
| Flash `protocoloGerado` sem uso | removido |

## 7. Testes

`Servico156SolicitacaoTest`, `EsicSolicitacaoTest`, `GeradorDeProtocoloTest`,
`ValidaCpfTest`. `ListarCategoriasPortal156Test` foi ajustado (o helper passou a
informar `slug`) e `ExampleTest` agora espera o redirecionamento de `/`.
**Os testes foram escritos sem execução**: rodar `php artisan test`.

## 8. Pendências

- Protocolo definitivo (hoje `contagem do ano + 1`; repete número se um
  registro for excluído).
- Minhas Solicitações, coluna `status` e rota de download de anexo.
- Ícones das categorias, a definir pela equipe da API (hoje mapeados por nome
  no model).
- `upload_max_filesize` e `post_max_size` do PHP precisam comportar os limites
  de anexos (padrão 2M/8M). Valores definidos em
  `2026-10-padroes-de-erro-e-logs.md`. Acima do limite, o `ValidatePostSize`
  lança 413 e o cidadão vê `errors/413.blade.php`.
- ~~Arquivos gravados dentro da transação ficam órfãos se ela for desfeita.~~
  Resolvido: ver `2026-10-padroes-de-erro-e-logs.md`.
- Ao enviar com erro de validação, os anexos precisam ser selecionados de novo.

## 9. Exclusão manual

O ambiente de edição não apaga arquivos: `DELETAR_css_esic-solicitacao.css`,
`DELETAR_js_esic-anexo-dropzone.js`, `app/Http/Controllers/SolicitacaoEsicController.php`
e, se ainda existir, `DELETAR_css_auth.css`.
