// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import UserAvatar from 'components/user-avatar';
import { route } from 'laroute';
import React from 'react';
import { formatBytes } from 'utils/html';
import UserEntryDeleteButton from './user-entry-delete-button';

interface Props {
  entry: any;
}

export default function UserEntry(props: Props) {
  let className = 'osu-table__body-row osu-table__body-row--highlightable admin-contest-entry';
  if (props.entry.deleted) className += ' admin-contest-entry__deleted';

  return (
    <tr className={className}>
      <td className='osu-table__cell admin-contest-entry__column'>
        <a className='admin-contest-entry__user-link' href={route('users.show', { user: props.entry.user.id })}>
          <div className='admin-contest-entry__avatar'>
            <UserAvatar modifiers='full-rounded' user={props.entry.user} />
          </div>
          {props.entry.user.username}
        </a>
      </td>

      <td className='osu-table__cell admin-contest-entry__preview'>
        <a download={props.entry.original_filename} href={props.entry.url}>{props.entry.filename}</a>
      </td>

      <td className='osu-table__cell admin-contest-entry__column'>
        {formatBytes(props.entry.filesize)}
      </td>

      <td className='admin-contest-entry__column admin-contest-entry__column--button'>
        <UserEntryDeleteButton entry={props.entry} />
      </td>
    </tr>
  );
}
