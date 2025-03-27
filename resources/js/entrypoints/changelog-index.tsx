// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import Main from 'changelog-index/main';
import core from 'osu-core-singleton';
import React from 'react';
import { parseJson } from 'utils/json';

core.reactTurbolinks.register('changelog-index', (container) => (
  <Main
    container={container}
    data={parseJson('json-index')}
    updateStreams={parseJson('json-update-streams')}
  />
));
