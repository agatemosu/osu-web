// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import type ChangelogEntryJson from 'interfaces/changelog-entry-json';
import _ from 'lodash';
import * as React from 'react';
import { trans } from 'utils/lang';

const icon = {
  add: 'fas fa-plus',
  fix: 'fas fa-check',
  misc: 'far fa-circle',
} as const;

interface Props {
  entry: ChangelogEntryJson;
}

export const ChangelogEntry = ({ entry }: Props) => {
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
          {(() => {
            const user = _.escape(entry.github_user.display_name);
            const url = entry.github_user.github_url
              ? entry.github_user.github_url
              : entry.github_user.user_url;

            const link =
              url != null
                ? `<a
                    data-user-id='${entry.github_user.user_id ?? ''}'
                    class='changelog-entry__user-link js-usercard'
                    href='${_.escape(url)}'
                  >${user}</a>`
                : user;

            return (
              <span
                dangerouslySetInnerHTML={{
                  __html: trans('changelog.entry.by', { user: link }),
                }}
                className='changelog-entry__user'
              />
            );
          })()}
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
};
