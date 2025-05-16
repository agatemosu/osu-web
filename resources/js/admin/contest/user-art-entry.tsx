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

export default function UserArtEntry(props: Props) {
  let className = 'osu-table__body-row osu-table__body-row--highlightable admin-contest-entry';
  if (props.entry.deleted) className += ' admin-contest-entry__deleted';

  return (
    <tr className={className}>
      <td className='osu-table__cell admin-contest-entry__user-column'>
        <a href={route('users.show', { user: props.entry.user.id })}>
          <UserAvatar modifiers='profile' user={props.entry.user} />
          <div className='admin-contest-entry__username'>
            {props.entry.user.username}
          </div>
        </a>

        <dl>
          <dt className='admin-contest__meta-row'>Filename</dt>
          <dd className='admin-contest__meta-row'>
            <a download={props.entry.original_filename} href={props.entry.url}>{props.entry.filename}</a>
          </dd>
          <dt className='admin-contest__meta-row'>Filesize</dt>
          <dd className='admin-contest__meta-row'>{formatBytes(props.entry.filesize)}</dd>
        </dl>
      </td>

      <td className='osu-table__cell admin-contest-entry__preview'>
        <a download={props.entry.original_filename} href={props.entry.url}>
          <img
            className='img-responsive admin-contest-entry__thumbnail'
            src={props.entry.thumb}
          />
        </a>
      </td>

      <td className='admin-contest-entry__column admin-contest-entry__column--button'>
        <UserEntryDeleteButton entry={props.entry} />
      </td>
    </tr>
  );
}
