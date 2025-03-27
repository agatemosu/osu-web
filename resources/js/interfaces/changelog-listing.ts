// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import type ChangelogBuild from './changelog-build';
import type UpdateStream from './update-stream';

export default interface ChangelogListing {
  builds: ChangelogBuild[];
  search: {
    from: string | null;
    limit: number;
    max_id: number | null;
    stream: string | null;
    to: string | null;
  };
  streams: UpdateStream[];
}
