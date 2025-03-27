// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import type ChangelogBuild from 'interfaces/changelog-build';
import _ from 'lodash';
import moment from 'moment';
import React from 'react';
import { type Modifiers, classWithModifiers } from 'utils/css';
import { changelogBuild } from 'utils/url';
import ChangelogEntry from './changelog-entry';

interface Props {
  build: ChangelogBuild;
  modifiers?: Modifiers;
  showDate?: boolean;
  showVideo?: boolean;
}

export default class Build extends React.PureComponent<Props> {
  render() {
    const blockClass = classWithModifiers('build', this.props.modifiers);
    const entries = _.groupBy(this.props.build.changelog_entries, 'category');
    const categories = _(entries).keys().sort().value();

    return (
      <div
        className={`${blockClass} t-changelog-stream--${_.kebabCase(this.props.build.update_stream.display_name)}`}
      >
        <div className='build__version'>
          {this.renderNav('previous', 'fas fa-chevron-left')}

          <a
            className='build__version-link'
            href={changelogBuild(this.props.build)}
          >
            <span className='build__stream'>
              {this.props.build.update_stream.display_name}
            </span>{' '}
            <span className='u-changelog-stream--text'>
              {this.props.build.display_version}
            </span>
          </a>

          {this.renderNav('next', 'fas fa-chevron-right')}
        </div>

        {this.props.showDate && (
          <div className='build__date'>
            {moment(this.props.build.created_at).format('LL')}
          </div>
        )}

        {this.props.build.youtube_id != null && this.props.showVideo && (
          <div className='build__video'>
            <iframe
              allowFullScreen
              className='u-embed-wide'
              src={`https://www.youtube.com/embed/${this.props.build.youtube_id}?rel=0`}
            />
          </div>
        )}

        {categories.map((category) => (
          <div key={category} className='build__changelog-entries-container'>
            <div className='build__changelog-entries-category'>{category}</div>
            <div className='build__changelog-entries'>
              {entries[category].map((entry) => (
                <div
                  key={
                    entry.id != null ? entry.id : `${entry.created_at}-${entry.title}`
                  }
                  className='build__changelog-entry'
                >
                  <ChangelogEntry entry={entry} />
                </div>
              ))}
            </div>
          </div>
        ))}
      </div>
    );
  }

  renderNav(version: 'previous' | 'next', icon: string) {
    if (!this.props.build.versions) return;

    const build = this.props.build.versions[version];

    return build ? (
      <a
        className='build__version-link'
        href={changelogBuild(build)}
        title={build.display_version}
      >
        <i className={icon} />
      </a>
    ) : (
      <span className='build__version-link build__version-link--disabled'>
        <i className={icon} />
      </span>
    );
  }
}
