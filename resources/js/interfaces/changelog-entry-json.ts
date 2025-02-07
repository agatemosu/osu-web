// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import type GithubUserJson from './github-user-json';

export default interface ChangelogEntryJson {
  category: string;
  created_at: string | null;
  github_pull_request_id: number | null;
  github_url: string | null;
  github_user: GithubUserJson;
  id: number | null;
  major: boolean;
  message: string | null;
  message_html: string | null;
  repository: string | null;
  title: string | null;
  type: 'add' | 'fix' | 'misc';
  url: string | null;
}
