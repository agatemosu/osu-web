// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import type { Direction, VoteSummary } from 'modding-profile/votes';
import type BeatmapExtendedJson from 'interfaces/beatmap-extended-json';
import type { BeatmapsetDiscussionJsonForBundle } from 'interfaces/beatmapset-discussion-json';
import type { BeatmapsetDiscussionMessagePostJson } from 'interfaces/beatmapset-discussion-post-json';
import type { BeatmapsetEventJson } from 'interfaces/beatmapset-event-json';
import type { BeatmapsetExtendedJson } from 'interfaces/beatmapset-extended-json';
import type KudosuHistoryJson from 'interfaces/kudosu-history-json';
import type { UserJson } from 'interfaces/user-json';
import type { UserModdingProfileJson } from 'interfaces/user-modding-profile-json';

export default interface BeatmapsetDiscussionsBundleJson {
  beatmaps: BeatmapExtendedJson[];
  beatmapsets: BeatmapsetExtendedJson[];
  discussions: BeatmapsetDiscussionJsonForBundle[];
  included_discussions: BeatmapsetDiscussionJsonForBundle[];
  users: UserJson[];
}

export interface BeatmapsetDiscussionsBundleJsonForModdingProfile {
  beatmaps: BeatmapExtendedJson[];
  beatmapsets: BeatmapsetExtendedJson[];
  discussions: BeatmapsetDiscussionJsonForBundle[];
  events: BeatmapsetEventJson[];
  extras: {
    recentlyReceivedKudosu: KudosuHistoryJson[];
  };
  perPage: {
    recentlyReceivedKudosu: number;
  };
  posts: BeatmapsetDiscussionMessagePostJson[];
  user: UserModdingProfileJson;
  users: UserJson[];
  votes: Record<Direction, VoteSummary[]>;
}
