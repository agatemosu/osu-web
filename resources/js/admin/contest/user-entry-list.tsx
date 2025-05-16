// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import _ from 'lodash';
import React from 'react';
import { nextVal } from 'utils/seq';
import UserArtEntry from './user-art-entry';
import UserEntry from './user-entry';

interface Props {
  contest: any;
  entries: any;
}

export default class UserEntryList extends React.Component<Props> {
  eventId = `admin-contests-show-user-entries-${nextVal()}`;
  state = {
    contest: this.props.contest,
    entries: this.props.entries,
    showDeleted: false,
  };

  componentDidMount() {
    $.subscribe(`admin:contest:entries:destroy.${this.eventId}`, this.delete);
    $.subscribe(`admin:contest:entries:restore.${this.eventId}`, this.restore);
  }

  componentWillUnmount() {
    $.unsubscribe(`.${this.eventId}`);
  }

  delete = (_e, data) => {
    this.updateEntry(data.entry, true);
  };

  render() {
    const entries = this.state.showDeleted
      ? this.state.entries
      : _.filter(this.state.entries, { deleted: false });

    const deletedEntries = _.filter(this.state.entries, { deleted: true });

    return (
      <div>
        <div className="row">
          <div className="col-md-6">
            <h4>
              {this.state.entries.length} Entries
              {deletedEntries.length && `, ${deletedEntries.length} Deleted`}
            </h4>
          </div>

          <div className="col-md-6 text-right">
            <h4>
              <a href="#" onClick={this.toggleShowDeleted}>
                {this.state.showDeleted ? 'hide' : 'show'} deleted
              </a>
            </h4>
          </div>
        </div>

        {this.props.contest.type === 'art' ? (
          <div className="osu-table osu-table--with-handle">
            <table className="osu-table__table">
              <thead>
                <tr>
                  <th className="osu-table__header admin-contest__table-column--username" />
                  <th className="osu-table__header" />
                  <th className="osu-table__header admin-contest__table-column--button" />
                </tr>
              </thead>
              <tbody>
                {entries.map((entry) => (
                  <UserArtEntry key={entry.id} entry={entry} />
                ))}
              </tbody>
            </table>
          </div>
        ) : (
          <div className="osu-table osu-table--taller-rows osu-table--with-handle">
            <table className="osu-table__table">
              <thead>
                <tr>
                  <th className="osu-table__header admin-contest__table-column--username">
                    Username
                  </th>
                  <th className="osu-table__header">Filename</th>
                  <th className="osu-table__header admin-contest__table-column--filesize">
                    Filesize
                  </th>
                  <th className="osu-table__header admin-contest__table-column--button" />
                </tr>
              </thead>
              <tbody>
                {entries.map((entry) => (
                  <UserEntry key={entry.id} entry={entry} />
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    );
  }

  restore = (_e, data) => {
    this.updateEntry(data.entry, false);
  };

  toggleShowDeleted = (e: React.MouseEvent) => {
    e.preventDefault();

    this.setState({
      showDeleted: !this.state.showDeleted,
    });
  };

  updateEntry(id, deleted: boolean) {
    const newEntries = _.clone(this.state.entries);
    _.find(newEntries, { id }).deleted = deleted;

    this.setState({ entries: newEntries });
  }
}
