// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import type ChangelogEntryJson from './changelog-entry-json';
import type UpdateStream from './update-stream';

export default interface ChangelogBuild {
  changelog_entries: ChangelogEntryJson[];
  created_at: string;
  display_version: string;
  id: number;
  update_stream: UpdateStream;
  users: number;
  version: string;
  versions: {
    next: ChangelogBuild | null;
    previous: ChangelogBuild | null;
  } | null;
  youtube_id: string | null;
}
