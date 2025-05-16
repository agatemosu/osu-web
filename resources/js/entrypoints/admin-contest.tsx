// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import UserEntryList from 'admin/contest/user-entry-list';
import core from 'osu-core-singleton';
import React from 'react';
import { parseJson } from 'utils/json';

core.reactTurbolinks.register('admin-contest-user-entry-list', () => (
  <UserEntryList
    contest={parseJson('json-contest')}
    entries={parseJson('json-contest-entries')}
  />
));
