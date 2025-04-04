<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

return [
    'page_description' => 'Featured artists v osu!',
    'title' => 'Featured Artists',

    'admin' => [
        'hidden' => 'UMĚLEC JE MOMENTÁLNĚ SKRYTÝ',
    ],

    'beatmaps' => [
        '_' => 'Beatmapy',
        'download' => 'stáhnout šablonu beatmapy',
        'download-na' => 'šablona beatmapy zatím není dostupná',
    ],

    'index' => [
        'description' => 'Oficiální umělci jsou tvůrci, se kterými spolupracujeme, abychom do osu! přinesli novou a originální hudbu. Tito umělci a výběr jejich skladeb byli ručně zvoleni týmem osu! kvůli jejich úžasnosti a vlohám pro mapování. Někteří z Oficiálních umělců navíc vytvořili nové skladby exkluzivně pro osu!.<br><br>Všechny skladby v této sekci jsou k dispozici jako předem načasované .osz soubory a jsou oficiálně licencované pro použití v osu! a obsahu s osu! souvisejícím.',
    ],

    'links' => [
        'beatmaps' => 'osu! beatmapy',
        'osu' => 'Profil osu!',
        'site' => 'Oficiální stránky',
    ],

    'songs' => [
        '_' => 'Skladby',
        'count' => ':count_delimited skladba|:count_delimited skladby|:count_delimited skladeb',
        'original' => 'osu! originál',
        'original_badge' => 'ORIGINÁL',
    ],

    'tracklist' => [
        'title' => 'název',
        'length' => 'délka',
        'bpm' => 'bpm',
        'genre' => 'žánr',
    ],

    'tracks' => [
        'index' => [
            '_' => 'hledání skladeb',

            'exclusive_only' => [
                'all' => 'Vše',
                'exclusive_only' => 'osu! originál',
            ],

            'form' => [
                'advanced' => 'Pokročilé vyhledávání',
                'album' => 'Album',
                'artist' => 'Umělec',
                'bpm_gte' => 'Minimální BPM',
                'bpm_lte' => 'Maximální BPM',
                'empty' => 'Nebyly nalezeny žádné skladby odpovídající kritériím vyhledávání.',
                'exclusive_only' => 'Typ',
                'genre' => 'Žánr',
                'genre_all' => 'Vše',
                'length_gte' => 'Minimální délka',
                'length_lte' => 'Maximální délka',
            ],
        ],
    ],
];
