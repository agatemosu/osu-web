// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import Main from 'changelog-build/main';
import core from 'osu-core-singleton';
import React from 'react';
import { parseJson } from 'utils/json';

core.reactTurbolinks.register('changelog-build', (container: HTMLElement) => (
  <Main
    build={parseJson('json-build')}
    container={container}
    updateStreams={parseJson('json-update-streams')}
  />
));
