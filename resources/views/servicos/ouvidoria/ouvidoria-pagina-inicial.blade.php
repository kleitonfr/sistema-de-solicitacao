@extends('layouts.app')

@section('title', 'Ouvidoria — Prefeitura Municipal de Caraguatatuba')

@section('content')
    <div class="ouvidoria-page">
        <div class="container">

            <a href="{{ route('servicos.index') }}" class="ouvidoria-voltar">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Voltar para a escolha de serviço
            </a>

            <div class="ouvidoria-faixa">
                <h1 class="ouvidoria-faixa__titulo">Ouvidoria-Geral do Município</h1>
                <p class="ouvidoria-faixa__subtitulo">O canal institucional para a melhoria contínua dos serviços públicos</p>
            </div>

            <div class="ouvidoria-conteudo">
                <p>
                    O Serviço de Ouvidoria é destinado a receber manifestações dos usuários dos
                    serviços públicos, como reclamações, denúncias, sugestões e elogios. Ele serve
                    como um canal direto de comunicação entre os usuários do serviço público e a
                    administração pública, permitindo a participação ativa na melhoria dos serviços
                    oferecidos.
                </p>

                <p>Aqui estão alguns exemplos de situações em que você pode utilizar o Serviço de Ouvidoria:</p>

                <ul class="ouvidoria-lista">
                    <li><strong>Reclamações:</strong> relatar insatisfações em relação aos serviços públicos prestados, como problemas no atendimento em hospitais, escolas, órgãos públicos, entre outros.</li>
                    <li><strong>Denúncias:</strong> comunicar irregularidades, assédio moral e sexual, abusos de poder, casos de corrupção ou outras práticas inadequadas ocorridas dentro da administração pública, envolvendo os agentes públicos.</li>
                    <li><strong>Sugestões:</strong> apresentar ideias para melhorar políticas públicas, serviços ou procedimentos internos, contribuindo para uma gestão mais eficiente e alinhada com as necessidades dos usuários.</li>
                    <li><strong>Elogios:</strong> reconhecer e valorizar o bom atendimento prestado por servidores públicos, incentivando boas práticas e motivando a excelência no serviço.</li>
                </ul>

                <p>
                    Utilize o Serviço de Ouvidoria sempre que desejar contribuir para a melhoria dos
                    serviços públicos, seja para apontar falhas, sugerir melhorias ou reconhecer boas
                    práticas. Sua participação é fundamental para promover uma administração pública
                    mais transparente, eficiente e voltada para o bem-estar da sociedade.
                </p>
            </div>

            {{-- Pendente: cada tipo de manifestação leva para "#" — os formulários
                 (que devem variar por tipo) ainda não existem. --}}
            <div class="ouvidoria-opcoes">
                <a href="#" class="ouvidoria-opcao">
                    <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                    Reclamação
                </a>
                <a href="#" class="ouvidoria-opcao">
                    <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                    Denúncia
                </a>
                <a href="#" class="ouvidoria-opcao">
                    <i class="fa-solid fa-lightbulb" aria-hidden="true"></i>
                    Sugestão
                </a>
                <a href="#" class="ouvidoria-opcao">
                    <i class="fa-solid fa-thumbs-up" aria-hidden="true"></i>
                    Elogio
                </a>
            </div>

        </div>
    </div>
@endsection
