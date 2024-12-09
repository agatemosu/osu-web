// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import type { DiscussionType } from 'beatmap-discussions/discussion-type';
import type { BeatmapJson } from 'interfaces/beatmap-json';
import type { BeatmapsetDiscussionPostJson } from 'interfaces/beatmapset-discussion-post-json';
import type { BeatmapsetJson } from 'interfaces/beatmapset-json';

interface BeatmapsetDiscussionAvailableIncludes {
  beatmap: BeatmapJson;
  beatmapset: BeatmapsetJson;
  current_user_attributes: {
    can_destroy: boolean;
    can_moderate_kudosu: boolean;
    can_reopen: boolean;
    can_resolve: boolean;
    vote_score: number;
  };
  posts: BeatmapsetDiscussionPostJson[];
  starting_post: BeatmapsetDiscussionPostJson;
  votes: {
    down: number;
    up: number;
    voters: {
      down: number[];
      up: number[];
    };
  };
}

interface BeatmapsetDiscussionDefaultAttributes {
  beatmap_id: number | null;
  beatmapset_id: number;
  can_be_resolved: boolean;
  can_grant_kudosu: boolean;
  created_at: string;
  deleted_at: string | null;
  deleted_by_id: number | null;
  id: number;
  kudosu_denied: boolean;
  last_post_at: string;
  message_type: DiscussionType;
  parent_id: number | null;
  resolved: boolean;
  timestamp: number | null;
  updated_at: string;
  user_id: number;
}

export type BeatmapsetDiscussionJson = BeatmapsetDiscussionDefaultAttributes & Partial<BeatmapsetDiscussionAvailableIncludes>;

// bundle versions; beatmap is only on modding history events version
export type BeatmapsetDiscussionJsonForBundle =
Omit<BeatmapsetDiscussionJson, 'posts'> // bundle explicitly does not include posts; need this for type discrimination.
& Required<Pick<BeatmapsetDiscussionJson,
'starting_post'
>>;

// discussions page version
export type BeatmapsetDiscussionJsonForShow =
BeatmapsetDiscussionJson & Required<Pick<BeatmapsetDiscussionJson,
'posts'
| 'votes'
>>;
