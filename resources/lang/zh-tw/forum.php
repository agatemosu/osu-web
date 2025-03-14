<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

return [
    'pinned_topics' => '置頂主題',
    'slogan' => "獨樂樂不如眾樂樂~",
    'subforums' => '其他相關討論',
    'title' => 'osu! 論壇',

    'covers' => [
        'edit' => '編輯封面',

        'create' => [
            '_' => '新增封面',
            'button' => '上傳圖片',
            'info' => '封面大小應為 :dimensions。您也可以將圖片拖曳至此以上傳。',
        ],

        'destroy' => [
            '_' => '移除封面',
            'confirm' => '您確定要刪除封面嗎？',
        ],
    ],

    'forums' => [
        'forums' => '論壇',
        'latest_post' => '最新貼文',

        'index' => [
            'title' => '論壇索引',
        ],

        'topics' => [
            'empty' => '沒有主題！',
        ],
    ],

    'mark_as_read' => [
        'forum' => '將該板塊標記為已讀',
        'forums' => '將該板塊標記為已讀',
        'busy' => '標記為已讀…',
    ],

    'post' => [
        'confirm_destroy' => '刪除此回覆？',
        'confirm_restore' => '確定要還原貼文嗎？',
        'edited' => '最後由 :user 於 :when 編輯，總共編輯了 :count 次。',
        'posted_at' => '發表於 :when',
        'posted_by_in' => ':username 在 ":forum" 中發表了貼文',

        'actions' => [
            'destroy' => '刪除回覆',
            'edit' => '編輯回覆',
            'report' => '檢舉貼文',
            'restore' => '恢復回覆',
        ],

        'create' => [
            'title' => [
                'reply' => '新回覆',
            ],
        ],

        'info' => [
            'post_count' => ':count_delimited 主題',
            'topic_starter' => '主題開啟者',
        ],
    ],

    'search' => [
        'go_to_post' => '前往該樓層',
        'post_number_input' => '輸入樓層數',
        'total_posts' => '一共有 :posts_count 樓',
    ],

    'topic' => [
        'confirm_destroy' => '確定要刪除這個主題嗎？',
        'confirm_restore' => '確定要還原這個主題嗎？',
        'deleted' => '已刪除的主題',
        'go_to_latest' => '查看最後的貼文',
        'go_to_unread' => '查看第一篇未讀帖子

',
        'has_replied' => '您已回覆此主題',
        'in_forum' => '目前看板[ :forum ]',
        'latest_post' => ':when :user',
        'latest_reply_by' => '最後回覆: :user',
        'new_topic' => '發表新主題',
        'new_topic_login' => '登入以發表新主題',
        'post_reply' => '發表',
        'reply_box_placeholder' => '輸入回覆',
        'reply_title_prefix' => 'Re',
        'started_by' => '發文者：:user',
        'started_by_verbose' => '由 :user 發起',

        'actions' => [
            'destroy' => '刪除主題',
            'restore' => '復原主題',
        ],

        'create' => [
            'close' => '關閉',
            'preview' => '預覽',
            // TL note: this is used in the topic reply preview, when
            // the user goes back from previewing to editing the reply
            'preview_hide' => '編輯',
            'submit' => '發表',

            'necropost' => [
                'default' => '這個主題已經閒置一段時間了。除非你有特別的原因，否則請不要在這裡回覆。',

                'new_topic' => [
                    '_' => "這個主題已經閒置一段時間了。如果你沒有特別的原因在這裡發文，請使用 :create 發表新的主題。",
                    'create' => '建立新主題',
                ],
            ],

            'placeholder' => [
                'body' => '在這裡輸入內文',
                'title' => '按這裡編輯標題',
            ],
        ],

        'jump' => [
            'enter' => '按這裡跳至指定的貼文編號',
            'first' => '返回頂部',
            'last' => '跳至最後',
            'next' => '往後 10 篇',
            'previous' => '往前 10 篇',
        ],

        'logs' => [
            '_' => '主題日誌',
            'button' => '瀏覽主題日誌',

            'columns' => [
                'action' => '動作',
                'date' => '日期',
                'user' => '使用者',
            ],

            'data' => [
                'add_tag' => '已加入「:tag」標籤',
                'announcement' => '已置頂主題，並標記為公告',
                'edit_topic' => '到 :title',
                'fork' => '源自 :topic',
                'pin' => '已置頂主題',
                'post_operation' => '由 :username 發表',
                'remove_tag' => '已移除「:tag」標籤',
                'source_forum_operation' => '源自 :forum',
                'unpin' => '已取消置頂主題',
            ],

            'no_results' => '找不到記錄檔…',

            'operations' => [
                'delete_post' => '已刪除貼文',
                'delete_topic' => '已刪除主題',
                'edit_topic' => '已變更主題標題',
                'edit_poll' => '已編輯主題投票',
                'fork' => '已複製主題',
                'issue_tag' => '已打上標籤',
                'lock' => '已鎖定主題',
                'merge' => '已將貼文合併進這則主題',
                'move' => '已移動主題',
                'pin' => '已置頂主題',
                'post_edited' => '已編輯貼文',
                'restore_post' => '已還原貼文',
                'restore_topic' => '已還原主題',
                'split_destination' => '已移動分割的貼文',
                'split_source' => '已分割貼文',
                'topic_type' => '已設定主題類型',
                'topic_type_changed' => '已變更主題類型',
                'unlock' => '已解鎖主題',
                'unpin' => '已取消置頂主題',
                'user_lock' => '已鎖定自己的主題',
                'user_unlock' => '已解鎖自己的主題',
            ],
        ],

        'post_edit' => [
            'cancel' => '取消',
            'post' => '儲存',
        ],
    ],

    'topic_watches' => [
        'index' => [
            'title_compact' => '訂閱',

            'box' => [
                'total' => '訂閱的主題',
                'unread' => '主題有新回覆',
            ],

            'info' => [
                'total' => '共訂閱了 :total 個主題',
                'unread' => '有 :unread 個未讀回覆',
            ],
        ],

        'topic_buttons' => [
            'remove' => [
                'confirmation' => '取消訂閱該主題？',
                'title' => '取消訂閱',
            ],
        ],
    ],

    'topics' => [
        '_' => '主題',

        'actions' => [
            'login_reply' => '登入以回覆',
            'reply' => '回覆',
            'reply_with_quote' => '引用此回覆',
            'search' => '搜尋',
        ],

        'create' => [
            'create_poll' => '建立投票',

            'preview' => '主題預覽',

            'create_poll_button' => [
                'add' => '建立投票',
                'remove' => '取消建立投票',
            ],

            'poll' => [
                'hide_results' => '隱藏投票結果。',
                'hide_results_info' => '這些內容只在投票結束後顯示。',
                'length' => '投票持續',
                'length_days_suffix' => '天',
                'length_info' => '若無截止期限則留空',
                'max_options' => '最多可選數',
                'max_options_info' => '每個人最多可選的數量。',
                'options' => '選項',
                'options_info' => '一個選項佔一行，最多10個選項。',
                'title' => '問題',
                'vote_change' => '允許修改',
                'vote_change_info' => '允許投票者修改他們的投票。',
            ],
        ],

        'edit_title' => [
            'start' => '編輯標題',
        ],

        'index' => [
            'feature_votes' => '星級優先',
            'replies' => '回覆數',
            'views' => '瀏覽數',
        ],

        'issue_tag_added' => [
            'to_0' => '移除 "added" 標籤',
            'to_0_done' => '已移除 "added" 標籤',
            'to_1' => '新增 "added" 標籤',
            'to_1_done' => '已新增 "added" 標籤',
        ],

        'issue_tag_assigned' => [
            'to_0' => '移除 "assigned" 標籤',
            'to_0_done' => '已移除 "assigned" 標籤',
            'to_1' => '新增 "assigned" 標籤',
            'to_1_done' => '已新增 "assigned" 標籤',
        ],

        'issue_tag_confirmed' => [
            'to_0' => '移除 "confirmed" 標籤',
            'to_0_done' => '已移除 "confirmed" 標籤',
            'to_1' => '新增 "confirmed" 標籤',
            'to_1_done' => '已新增 "confirmed" 標籤',
        ],

        'issue_tag_duplicate' => [
            'to_0' => '移除 "duplicate" 標籤',
            'to_0_done' => '已移除 "duplicate" 標籤',
            'to_1' => '新增 "duplicate" 標籤',
            'to_1_done' => '已新增 "duplicate" 標籤',
        ],

        'issue_tag_invalid' => [
            'to_0' => '移除 "invalid" 標籤',
            'to_0_done' => '已移除 "invalid" 標籤',
            'to_1' => '新增 "invalid" 標籤',
            'to_1_done' => '已新增 "invalid" 標籤',
        ],

        'issue_tag_resolved' => [
            'to_0' => '移除 "resolved" 標籤',
            'to_0_done' => '已移除 "resolved" 標籤',
            'to_1' => '新增 "resolved" 標籤',
            'to_1_done' => '已新增 "resolved" 標籤',
        ],

        'issue_tag_osulazer' => [
            'to_0' => '移除「osu!lazer」標籤',
            'to_0_done' => '已移除「osu!lazer」標籤',
            'to_1' => '加入「osu!lazer」標籤',
            'to_1_done' => '已加入「osu!lazer」標籤',
        ],

        'issue_tag_osustable' => [
            'to_0' => '移除「osu!stable」標籤',
            'to_0_done' => '已移除「osu!stable」標籤',
            'to_1' => '加入「osu!stable」標籤',
            'to_1_done' => '已加入「osu!stable」標籤',
        ],

        'issue_tag_osuweb' => [
            'to_0' => '移除「osu!web」標籤',
            'to_0_done' => '已移除「osu!web」標籤',
            'to_1' => '加入「osu!web」標籤',
            'to_1_done' => '已加入「osu!web」標籤',
        ],

        'lock' => [
            'is_locked' => '主題已被鎖定，不能回覆',
            'to_0' => '解鎖主題',
            'to_0_confirm' => '解鎖主題?',
            'to_0_done' => '主题已經解鎖',
            'to_1' => '鎖定主題',
            'to_1_confirm' => '鎖定主題?',
            'to_1_done' => '主題已被鎖定',
        ],

        'moderate_move' => [
            'title' => '將主題移動至其他討論',
        ],

        'moderate_pin' => [
            'to_0' => '取消置頂',
            'to_0_confirm' => '取消置頂主題?',
            'to_0_done' => '該主題已取消置頂',
            'to_1' => '置頂',
            'to_1_confirm' => '置頂主題?',
            'to_1_done' => '該主題已置頂',
            'to_2' => '置頂並標記為公告',
            'to_2_confirm' => '置頂主題並設為公告?',
            'to_2_done' => '該主題已置頂並標記為公告',
        ],

        'moderate_toggle_deleted' => [
            'show' => '顯示已刪除帖子',
            'hide' => '隱藏已刪除帖子',
        ],

        'show' => [
            'deleted-posts' => '刪除主題',
            'total_posts' => '總主題數量',

            'feature_vote' => [
                'current' => '目前優先順序：+:count',
                'do' => '提升這個請求',

                'info' => [
                    '_' => '這是一個:feature_request。:supporters 可為新功能建議投票。',
                    'feature_request' => '新功能建議',
                    'supporters' => '贊助者',
                ],

                'user' => [
                    'count' => '{0} 沒有票|[1,*] :count 票',
                    'current' => '你還有 :votes 票.',
                    'not_enough' => "沒有票了",
                ],
            ],

            'poll' => [
                'edit' => '編輯投票',
                'edit_warning' => '編輯投票將會清除目前結果！',
                'vote' => '投票',

                'button' => [
                    'change_vote' => '變更投票',
                    'edit' => '編輯投票',
                    'view_results' => '直接跳到結果',
                    'vote' => '投票',
                ],

                'detail' => [
                    'end_time' => '將於 :time 結束',
                    'ended' => '結束於 :time',
                    'results_hidden' => '結果將於投票結束後顯示。',
                    'total' => '總票數: :count',
                ],
            ],
        ],

        'watch' => [
            'to_not_watching' => '未訂閱',
            'to_watching' => '訂閱',
            'to_watching_mail' => '訂閱並開啟電子郵件通知',
            'tooltip_mail_disable' => '通知已啟用。按這裡停用。',
            'tooltip_mail_enable' => '通知已停用。按這裡啟用。',
        ],
    ],
];
