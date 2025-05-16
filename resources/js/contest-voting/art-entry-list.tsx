// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import _ from 'lodash';
import * as React from 'react';
import ArtEntry from './art-entry';
import BaseEntryList from './base-entry-list';
import VoteSummary from './vote-summary';

export default class ArtEntryList extends BaseEntryList {
  render() {
    if (this.state.contest.entries.length <= 0) return null;

    const selected = new Set(this.state.selected);

    let galleryIndex = -1;
    const entries = this.state.contest.entries.map((entry, index) => {
      const isSelected = selected.has(entry.id);

      if (this.state.showVotedOnly && !isSelected) return null;

      return (
        <ArtEntry
          key={entry.id}
          contest={this.state.contest}
          entry={entry}
          galleryIndex={++galleryIndex}
          index={index}
          isSelected={isSelected}
          options={this.state.options}
          selected={this.state.selected}
          waitingForResponse={this.state.waitingForResponse}
        />
      );
    });

    let partitions = null;
    if (this.state.contest.show_votes) {
      partitions = _.partition(entries, (i) => i !== null && i.props.index < 3);
    }

    return (
      <div className="contest__art-list">
        <div className="contest__vote-summary--art">
          {this.renderToggleShowVotedOnly()}
          <span className="contest__vote-summary-text contest__vote-summary-text--art">
            votes
          </span>
          <VoteSummary
            maxVotes={this.state.contest.max_votes}
            voteCount={this.state.selected.length}
          />
        </div>

        {partitions != null ? (
          <div>
            <div className="contest-art-list contest-art-list--top3">
              {partitions[0]}
            </div>
            <div className="contest-art-list">{partitions[1]}</div>
          </div>
        ) : (
          <div className="contest-art-list">{entries}</div>
        )}
      </div>
    );
  }
}
