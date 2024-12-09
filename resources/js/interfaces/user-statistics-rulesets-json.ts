// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import type { Ruleset } from 'interfaces/ruleset';
import type { UserStatisticsJson } from 'interfaces/user-statistics-json';

export type UserStatisticsRulesetsJson = Partial<Record<Ruleset, UserStatisticsJson | null>>;
