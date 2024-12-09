// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import type BeatmapExtendedJson from 'interfaces/beatmap-extended-json';
import type { BeatmapsetExtendedJson } from 'interfaces/beatmapset-extended-json';

export default interface BeatmapPlaycountJson {
  beatmap?: BeatmapExtendedJson;
  beatmap_id: number;
  beatmapset?: BeatmapsetExtendedJson;
  count: number;
}
