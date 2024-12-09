// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import type BeatmapExtendedJson from 'interfaces/beatmap-extended-json';
import type { BeatmapsetDiscussionJsonForShow } from 'interfaces/beatmapset-discussion-json';
import type { BeatmapsetExtendedJson } from 'interfaces/beatmapset-extended-json';
import type { WithBeatmapOwners } from 'interfaces/with-beatmap-owners';

type DiscussionsRequiredAttributes = 'current_user_attributes' | 'eligible_main_rulesets' | 'events' | 'nominations' | 'related_users';
export type BeatmapsetWithDiscussionsJson =
  Omit<BeatmapsetExtendedJson, keyof OverrideIncludes>
  & OverrideIncludes
  & Required<Pick<BeatmapsetExtendedJson, DiscussionsRequiredAttributes>>;

interface OverrideIncludes {
  beatmaps: WithBeatmapOwners<BeatmapExtendedJson>[];
  discussions: BeatmapsetDiscussionJsonForShow[];
}
