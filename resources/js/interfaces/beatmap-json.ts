// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import type { BeatmapOwnerJson } from 'interfaces/beatmap-owner-json';
import type { BeatmapsetJson } from 'interfaces/beatmapset-json';
import type { Ruleset } from 'interfaces/ruleset';
import type { UserJson } from 'interfaces/user-json';

interface BeatmapFailTimesArray {
  exit: number[];
  fail: number[];
}

interface BeatmapJsonAvailableIncludes {
  beatmapset: BeatmapsetJson | null;
  checksum: string | null;
  failtimes: BeatmapFailTimesArray;
  max_combo: number;
  owners: BeatmapOwnerJson[];
  user: UserJson;
}

interface BeatmapJsonDefaultAttributes {
  beatmapset_id: number;
  difficulty_rating: number;
  id: number;
  mode: Ruleset;
  status: string;
  total_length: number;
  user_id: number;
  version: string;
}

export type BeatmapJson = BeatmapJsonDefaultAttributes & Partial<BeatmapJsonAvailableIncludes>;
