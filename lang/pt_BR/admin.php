<?php

return [
    'resources' => [
        'project' => ['singular' => 'projeto', 'plural' => 'projetos'],
        'post' => ['singular' => 'post', 'plural' => 'posts'],
    ],

    'sections' => [
        'identity' => 'Identificação',
        'dates_links' => 'Datas e links',
        'publication' => 'Publicação',
        'content' => 'Conteúdo',
    ],

    'fields' => [
        'title' => 'Título',
        'slug' => 'Slug',
        'slug_help' => 'Parte final do endereço, igual nos dois idiomas. Gerado a partir do título em português.',
        'type' => 'Tipo',
        'status' => 'Status',
        'summary' => 'Resumo',
        'excerpt' => 'Resumo',
        'content' => 'Blocos',
        'cover_image' => 'Capa',
        'links' => 'Links',
        'link_label' => 'Rótulo',
        'link_url' => 'URL',
        'started_on' => 'Início',
        'released_on' => 'Lançamento',
        'is_featured' => 'Destaque',
        'sort_order' => 'Ordem',
        'published_at' => 'Publicar em',
        'published_at_help' => 'Vazio = rascunho. Data futura = agendado. Horário de Brasília.',
        'project' => 'Projeto',
        'publication_state' => 'Situação',
        'updated_at' => 'Atualizado em',
    ],

    'blocks' => [
        'text' => 'Texto',
        'image' => 'Imagem',
        'gallery' => 'Galeria',
        'video' => 'Vídeo',
        'game' => 'Jogo embutido',
        'quote' => 'Citação',
        'body' => 'Texto',
        'alt' => 'Texto alternativo',
        'alt_help' => 'Descrição da imagem para leitores de tela.',
        'caption' => 'Legenda',
        'layout' => 'Layout',
        'layouts' => ['contained' => 'Contido', 'wide' => 'Largo', 'full' => 'Tela cheia'],
        'images' => 'Imagens',
        'video_url' => 'URL do vídeo',
        'video_url_help' => 'YouTube ou Vimeo.',
        'game_url' => 'URL do jogo',
        'game_url_help' => 'Build web ou embed do itch.io.',
        'aspect_ratio' => 'Proporção',
        'quote_text' => 'Citação',
        'attribution' => 'Autoria',
    ],

    'actions' => [
        'copy_from_default' => [
            'label' => 'Copiar do português',
            'description' => 'Os campos traduzíveis deste idioma serão substituídos pela versão em português. As imagens são reaproveitadas; depois é só traduzir os textos. Nada é salvo até você clicar em Salvar.',
            'done' => 'Conteúdo copiado do português.',
        ],
    ],
];
