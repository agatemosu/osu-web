// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import type ChangelogBuild from './changelog-build';

export default interface UpdateStream {
  display_name: string;
  id: number;
  is_featured: boolean;
  latest_build: ChangelogBuild;
  name: string;
  user_count: number;
}
