// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import type GroupJson from 'interfaces/group-json';
import type { Ruleset } from 'interfaces/ruleset';

export default interface UserGroupJson extends GroupJson {
  playmodes?: Ruleset[];
}
