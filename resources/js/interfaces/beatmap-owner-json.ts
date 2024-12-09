// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import type { UserJson } from 'interfaces/user-json';

export type BeatmapOwnerJson = Pick<UserJson, 'id' | 'username'>;
