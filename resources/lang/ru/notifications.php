<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

return [
    'all_read' => 'Все уведомления прочитаны!',
    'delete' => 'Очистить',
    'loading' => 'Загрузка непрочитанных уведомлений...',
    'mark_read' => 'Прочитать',
    'none' => 'Уведомлений нет',
    'see_all' => 'см. все уведомления',
    'see_channel' => 'перейти в чат',
    'verifying' => 'Пожалуйста, проверьте сессию для просмотра уведомлений',

    'action_type' => [
        '_' => 'все',
        'beatmapset' => 'карты',
        'build' => 'билды',
        'channel' => 'чат',
        'forum_topic' => 'форум',
        'news_post' => 'новости',
        'team' => 'команда',
        'user' => 'игроки',
    ],

    'filters' => [
        '_' => 'все',
        'beatmapset' => 'карты',
        'build' => 'билды',
        'channel' => 'чат',
        'forum_topic' => 'форум',
        'news_post' => 'новости',
        'team' => 'команда',
        'user' => 'игроки',
    ],

    'item' => [
        'beatmapset' => [
            '_' => 'Карта',

            'beatmap_owner_change' => [
                '_' => 'Гостевая сложность',
                'beatmap_owner_change' => 'Вас назначили владельцем сложности ":beatmap" на карте ":title"',
                'beatmap_owner_change_compact' => 'Вас назначили владельцем сложности ":beatmap"',
            ],

            'beatmapset_discussion' => [
                '_' => 'Обсуждение карты',
                'beatmapset_discussion_lock' => 'Карта ":title" заблокирована для обсуждений',
                'beatmapset_discussion_lock_compact' => 'Обсуждение было заблокировано',
                'beatmapset_discussion_post_new' => ':username написал новое сообщение в обсуждениях карты ":title": ":content"',
                'beatmapset_discussion_post_new_empty' => 'Новый пост в ":title" от :username',
                'beatmapset_discussion_post_new_compact' => 'Новый пост от :username: ":content"',
                'beatmapset_discussion_post_new_compact_empty' => 'Новый пост от :username',
                'beatmapset_discussion_review_new' => 'Новый отзыв на ":title" от :username, содержащий :review_counts',
                'beatmapset_discussion_review_new_compact' => 'Новый отзыв от :username, содержащий :review_counts',
                'beatmapset_discussion_unlock' => 'Карта ":title" разблокирована для обсуждений',
                'beatmapset_discussion_unlock_compact' => 'Обсуждение было разблокировано',

                'review_count' => [
                    'praises' => ':count_delimited похвалу|:count_delimited похвалы|:count_delimited похвал',
                    'problems' => ':count_delimited проблему|:count_delimited проблемы|:count_delimited проблем',
                    'suggestions' => ':count_delimited предложение|:count_delimited предложения|:count_delimited предложений',
                ],
            ],

            'beatmapset_problem' => [
                '_' => 'Проблема с квалифицированной картой',
                'beatmapset_discussion_qualified_problem' => 'Жалоба от :username на ":title": ":content"',
                'beatmapset_discussion_qualified_problem_empty' => 'Жалоба от :username на ":title"',
                'beatmapset_discussion_qualified_problem_compact' => 'Жалоба от :username: ":content"',
                'beatmapset_discussion_qualified_problem_compact_empty' => 'Жалоба от :username',
            ],

            'beatmapset_state' => [
                '_' => 'Статус карты изменен',
                'beatmapset_disqualify' => 'Карта ":title" была дисквалифицирована',
                'beatmapset_disqualify_compact' => 'Карта была дисквалифицирована',
                'beatmapset_love' => 'Карте ":title" была присвоена категория Любимая',
                'beatmapset_love_compact' => 'Карте была присвоена категория Любимая',
                'beatmapset_nominate' => 'Карта ":title" была номинирована',
                'beatmapset_nominate_compact' => 'Карта была номинирована',
                'beatmapset_qualify' => 'Карта ":title" получила достаточно номинаций и вошла в очередь получения рейтинга',
                'beatmapset_qualify_compact' => 'Карта поставлена в очередь получения рейтинга',
                'beatmapset_rank' => 'Карта ":title" стала Рейтинговой',
                'beatmapset_rank_compact' => 'Карта стала Рейтинговой',
                'beatmapset_remove_from_loved' => '":title" была удалена из категории Любимая',
                'beatmapset_remove_from_loved_compact' => 'Карта была удалена из категории Любимая',
                'beatmapset_reset_nominations' => 'Номинации карты ":title"  была сброшена',
                'beatmapset_reset_nominations_compact' => 'Номинация была сброшена',
            ],

            'comment' => [
                '_' => 'Новый комментарий',

                'comment_new' => ':username прокомментировал ":content" на ":title"',
                'comment_new_compact' => ':username прокомментировал ":content"',
                'comment_reply' => ':username ответил ":content" на ":title"',
                'comment_reply_compact' => ':username ответил ":content"',
            ],
        ],

        'channel' => [
            '_' => 'Чат',

            'announcement' => [
                '_' => 'Новое объявление',

                'announce' => [
                    'channel_announcement' => ':username говорит ":title"',
                    'channel_announcement_compact' => ':title',
                    'channel_announcement_group' => 'Объявление от :username',
                ],
            ],

            'channel' => [
                '_' => 'Новое сообщение',

                'pm' => [
                    'channel_message' => ':username говорит ":title"',
                    'channel_message_compact' => ':title',
                    'channel_message_group' => 'от :username',
                ],
            ],
        ],

        'build' => [
            '_' => 'Список изменений',

            'comment' => [
                '_' => 'Новый комментарий',

                'comment_new' => ':username прокомментировал ":content" на ":title"',
                'comment_new_compact' => ':username прокомментировал ":content"',
                'comment_reply' => ':username ответил ":content" на ":title"',
                'comment_reply_compact' => ':username ответил ":content"',
            ],
        ],

        'news_post' => [
            '_' => 'Новости',

            'comment' => [
                '_' => 'Новый комментарий',

                'comment_new' => ':username прокомментировал ":content" на ":title"',
                'comment_new_compact' => ':username прокомментировал ":content"',
                'comment_reply' => ':username ответил ":content" на ":title"',
                'comment_reply_compact' => ':username ответил ":content"',
            ],
        ],

        'forum_topic' => [
            '_' => 'Тема форума',

            'forum_topic_reply' => [
                '_' => 'Новый ответ на форуме',
                'forum_topic_reply' => ':username ответил в теме ":title"',
                'forum_topic_reply_compact' => ':username ответил',
            ],
        ],

        'team' => [
            'team_application' => [
                '_' => 'Запрос на вступление в команду',

                'team_application_accept' => "Вы присоединились к команде :title",
                'team_application_accept_compact' => "Вы присоединились к команде :title",
                'team_application_reject' => 'Ваш запрос на вступление в команду :title отклонен',
                'team_application_reject_compact' => 'Ваш запрос на вступление в команду :title отклонен',
            ],
        ],

        'user' => [
            'user_beatmapset_new' => [
                '_' => 'Новая карта',

                'user_beatmapset_new' => 'Новая карта ":title" от :username',
                'user_beatmapset_new_compact' => 'Новая карта ":title"',
                'user_beatmapset_new_group' => 'Новые карты от :username',

                'user_beatmapset_revive' => 'Карта ":title" была воскрешена :username',
                'user_beatmapset_revive_compact' => 'Карта ":title" воскрешена',
            ],
        ],

        'user_achievement' => [
            '_' => 'Медали',

            'user_achievement_unlock' => [
                '_' => 'Новая медаль',
                'user_achievement_unlock' => 'Разблокировано ":title"!',
                'user_achievement_unlock_compact' => 'Разблокировано: «:title»!',
                'user_achievement_unlock_group' => 'Медали открыты!',
            ],
        ],
    ],

    'mail' => [
        'beatmapset' => [
            'beatmap_owner_change' => [
                'beatmap_owner_change' => 'Вы приглашены в ":title" ',
            ],

            'beatmapset_discussion' => [
                'beatmapset_discussion_lock' => 'Обсуждение ":title" было закрыто',
                'beatmapset_discussion_post_new' => 'Обсуждение ":title" имеет новые ответы',
                'beatmapset_discussion_unlock' => 'Обсуждение ":title" было открыто',
            ],

            'beatmapset_problem' => [
                'beatmapset_discussion_qualified_problem' => 'Сообщена новая проблема в ":title"',
            ],

            'beatmapset_state' => [
                'beatmapset_disqualify' => 'Карта ":title" была дисквалифицирована',
                'beatmapset_love' => '":title" был повышен до любимого',
                'beatmapset_nominate' => 'Карта ":title" номинирована',
                'beatmapset_qualify' => 'Карта ":title" получила достаточно номинаций и вошла в очередь получения рейтинга',
                'beatmapset_rank' => 'Карта ":title" получила категорию Рейтинговая',
                'beatmapset_remove_from_loved' => '":title" была удалена из категории Любимая',
                'beatmapset_reset_nominations' => 'Номинация ":title" была сброшена',
            ],

            'comment' => [
                'comment_new' => 'Карта ":title" имеет новые комментарии',
            ],
        ],

        'channel' => [
            'announcement' => [
                'announce' => 'Новое объявление в ":name"',
            ],

            'channel' => [
                'pm' => 'Вы получили новое сообщение от :username',
            ],
        ],

        'build' => [
            'comment' => [
                'comment_new' => 'Список изменений ":title" содержит новые комментарии',
            ],
        ],

        'news_post' => [
            'comment' => [
                'comment_new' => 'Новость ":title" имеет новые комментарии',
            ],
        ],

        'forum_topic' => [
            'forum_topic_reply' => [
                'forum_topic_reply' => 'Есть новые ответы в ":title"',
            ],
        ],

        'team' => [
            'team_application' => [
                'team_application_accept' => "Вы присоединились к команде :title",
                'team_application_reject' => 'Ваш запрос на вступление в команду :title отклонен',
            ],
        ],

        'user' => [
            'user_beatmapset_new' => [
                'user_beatmapset_new' => ':username создал новую карту',
                'user_beatmapset_revive' => ':username обновил карты',
            ],
        ],
    ],
];
