// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import { route } from 'laroute';
import React from 'react';
import { onError } from 'utils/ajax';

interface Props {
  entry: any;
}

export default class UserEntryDeleteButton extends React.Component<Props> {
  delete = (e: React.MouseEvent) => {
    e.preventDefault();
    this.update(true);
  };

  render() {
    let icon: string;
    let label: string;
    let onClick: React.MouseEventHandler;
    if (this.props.entry.deleted) {
      icon = 'fas fa-magic';
      label = 'Restore';
      onClick = this.restore;
    } else {
      icon = 'far fa-trash-alt';
      label = 'Delete';
      onClick = this.delete;
    }

    return (
      <button
        className='btn-osu-big'
        onClick={onClick}
        title={label}
        type='button'
      >
        <i className={`fa-fw ${icon}`} />
      </button>
    );
  }

  restore = (e: React.MouseEvent) => {
    e.preventDefault();
    this.update(false);
  };

  update(destroy: boolean) {
    let destroyOrRestore: 'destroy' | 'restore';
    const params = { dataType: 'json' };

    if (destroy) {
      params.method = 'DELETE';
      destroyOrRestore = 'destroy';
    } else {
      params.method = 'POST';
      destroyOrRestore = 'restore';
    }

    $.ajax(
      route(`admin.user-contest-entries.${destroyOrRestore}`, {
        user_contest_entry: this.props.entry.id,
      }),
      params,
    )
      .done(() =>
        $.publish(`admin:contest:entries:${destroyOrRestore}`, {
          entry: this.props.entry.id,
        }),
      )
      .fail(onError);
  }
}
