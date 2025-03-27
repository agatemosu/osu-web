// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import type UpdateStream from 'interfaces/update-stream';
import _ from 'lodash';
import React from 'react';
import { classWithModifiers } from 'utils/css';
import { transChoice } from 'utils/lang';
import { changelogBuild } from 'utils/url';

interface Props {
  currentStreamId?: number;
  updateStreams: UpdateStream[];
}

export default class ChangelogHeaderStreams extends React.PureComponent<Props> {
  render() {
    return (
      <div
        className={classWithModifiers('update-streams-v2', [
          this.props.currentStreamId != null ? 'with-active' : undefined,
        ])}
      >
        <div className='update-streams-v2__container'>
          {this.props.updateStreams.map(this.renderHeaderStream)}
        </div>
      </div>
    );
  }

  readonly renderHeaderStream = (stream: UpdateStream) => {
    const streamNameClass = _.kebabCase(stream.display_name ?? '');
    let mainClass = classWithModifiers('update-streams-v2__item', [
      streamNameClass,
      stream.is_featured ? 'featured' : undefined,
      this.props.currentStreamId === stream.id ? 'active' : undefined,
    ]);
    mainClass += ` t-changelog-stream--${streamNameClass}`;

    return (
      <a
        key={stream.id}
        className={mainClass}
        href={changelogBuild(stream.latest_build)}
      >
        <div className='update-streams-v2__bar u-changelog-stream--bg' />
        <p className='update-streams-v2__row update-streams-v2__row--name'>
          {stream.display_name}
        </p>
        <p className='update-streams-v2__row update-streams-v2__row--version'>
          {stream.latest_build.display_version}
        </p>
        {stream.user_count > 0 && (
          <p className='update-streams-v2__row update-streams-v2__row--users'>
            {transChoice('changelog.builds.users_online', stream.user_count)}
          </p>
        )}
      </a>
    );
  };
}
