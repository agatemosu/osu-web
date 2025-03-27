// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import type ChangelogEntryJson from 'interfaces/changelog-entry-json';
import type GithubUserJson from 'interfaces/github-user-json';
import _ from 'lodash';
import React from 'react';
import { trans } from 'utils/lang';
import StringWithComponent from './string-with-component';
import UserLink from './user-link';

interface Props {
  entry: ChangelogEntryJson;
}

const icon = {
  add: 'fas fa-plus',
  fix: 'fas fa-check',
  misc: 'far fa-circle',
} as const;

const userLinkClass = 'changelog-entry__user-link';
const userLink = (githubUser: GithubUserJson) => {
  if (githubUser.osu_username != null) {
    return (
      <UserLink
        className={userLinkClass}
        user={{
          id: githubUser.user_id,
          username: githubUser.osu_username,
        }}
      />
    );
  }
  if (githubUser.github_url != null) {
    return (
      <a
        className={userLinkClass}
        href={githubUser.github_url}
      >
        {githubUser.display_name}
      </a>
    );
  }
  return githubUser.display_name;
};

export default function ChangelogEntry({ entry }: Props) {
  const titleHtml = _.escape(entry.title ?? '').replace(
    /(`+)([^`]+)\1/g,
    '<code>$2</code>',
  );

  return (
    <div key={entry.id} className='changelog-entry'>
      <div className='changelog-entry__row'>
        <div
          className={`changelog-entry__title ${entry.major ? 'changelog-entry__title--major' : ''}`}
        >
          <span className='changelog-entry__title-icon'>
            <span className={`changelog-entry__icon ${icon[entry.type]}`} />
          </span>
          {entry.url != null ? (
            <a
              dangerouslySetInnerHTML={{ __html: titleHtml }}
              className='changelog-entry__link'
              href={entry.url}
            />
          ) : (
            <span dangerouslySetInnerHTML={{ __html: titleHtml }} />
          )}
          {entry.github_url != null && (
            <span>
              {' ('}
              <a className='changelog-entry__link' href={entry.github_url}>
                {`${entry.repository?.replace(/^.*\//, '')}#${entry.github_pull_request_id}`}
              </a>
              {')'}
            </span>
          )}
          <span className='changelog-entry__user'>
            <StringWithComponent
              mappings={{
                user: userLink(entry.github_user),
              }}
              pattern={trans('changelog.entry.by')}
            />
          </span>
        </div>
      </div>
      {entry.message_html != null && (
        <div
          dangerouslySetInnerHTML={{
            __html: entry.message_html,
          }}
          className='changelog-entry__row changelog-entry__row--message'
        />
      )}
    </div>
  );
}
