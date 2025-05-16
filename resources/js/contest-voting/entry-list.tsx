// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import _ from 'lodash';
import core from 'osu-core-singleton';
import React from 'react';
import { classWithModifiers } from 'utils/css';
import { trans } from 'utils/lang';
import BaseEntryList from './base-entry-list';
import { Entry } from './entry';
import VoteSummary from './vote-summary';

export default class EntryList extends BaseEntryList {
  render() {
    if (this.state.contest.best_of && this.state.contest.entries.length === 0) {
      return (
        <div className="contest__voting-notice">
          {core.currentUser != null
            ? trans('contest.voting.best_of.none_played')
            : trans('contest.voting.login_required')}
        </div>
      );
    }

    if (this.state.contest.entries.length <= 0) return null;

    const entries = this.state.contest.entries.map((entry, index) => (
      <Entry
        key={entry.id}
        rank={index + 1}
        entry={entry}
        hideIfNotVoted={this.state.showVotedOnly}
        waitingForResponse={this.state.waitingForResponse}
        options={this.state.options}
        contest={this.state.contest}
        selected={this.state.selected}
        winnerVotes={
          this.state.contest.show_votes
            ? _.maxBy(this.state.contest.entries, (i) => i.results.votes)
                .results.votes
            : undefined
        }
      />
    ));

    return (
      <div className="contest-voting-list__table">
        <div className="contest-voting-list__header">
          {this.state.contest.show_votes && (
            <div className="contest-voting-list__rank contest-voting-list__rank--blank" />
          )}

          {this.state.options.showPreview && (
            <div className="contest-voting-list__icon" />
          )}

          {this.state.options.showLink && (
            <div
              className={classWithModifiers('contest-voting-list__icon', {
                'submitted-beatmaps': this.state.contest.submitted_beatmaps,
              })}
            />
          )}

          <div className="contest-voting-list__header-wrapper">
            <div className="contest-voting-list__header-title">
              {trans('contest.entry._')}
            </div>

            {!this.state.contest.judged && (
              <>
                <div className="contest-voting-list__header-voted-toggle-button">
                  {this.renderToggleShowVotedOnly()}
                </div>
                <div className="contest-voting-list__header-votesummary">
                  <div className="contest__vote-summary-text">
                    {trans('contest.vote.list')}
                  </div>
                  <VoteSummary
                    maxVotes={this.state.contest.max_votes}
                    voteCount={this.state.selected.length}
                  />
                </div>
              </>
            )}
          </div>
        </div>

        <div>{entries}</div>
      </div>
    );
  }
}
