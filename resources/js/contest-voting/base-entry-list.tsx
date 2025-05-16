// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import _ from 'lodash';
import React from 'react';
import { trans } from 'utils/lang';
import { nextVal } from 'utils/seq';

interface Props {
  contest: any;
  options: any;
  selected: any[];
}

export default class BaseEntryList extends React.Component<Props> {
  eventId = `contests-show-voting-${nextVal()}`;
  state = {
    contest: this.props.contest,
    options: {
      linkIcon:
        this.props.options.linkIcon != null
          ? this.props.options.linkIcon
          : false,
      showLink:
        this.props.options.showLink != null
          ? this.props.options.showLink
          : false,
      showPreview:
        this.props.options.showPreview != null
          ? this.props.options.showPreview
          : false,
    },
    selected: this.props.selected,
    showVotedOnly: false,
    waitingForResponse: false,
  };

  componentDidMount() {
    $.subscribe(`contest:vote:click.${this.eventId}`, this.handleVoteClick);
    $.subscribe(`contest:vote:done.${this.eventId}`, this.handleUpdate);
  }

  componentWillUnmount() {
    $.unsubscribe(`.${this.eventId}`);
  }

  handleUpdate = (_e: React.MouseEvent, { response, callback }) => {
    if (response.contest.id !== this.state.contest.id) return;

    this.setState(
      {
        contest: response.contest,
        selected: response.userVotes,
        waitingForResponse: false,
      },
      callback,
    );
  };

  handleVoteClick = (
    _e: React.MouseEvent,
    { contest_id, entry_id, callback },
  ) => {
    if (contest_id !== this.state.contest.id) {
      return;
    }

    const selected = _.clone(this.state.selected);

    if (_.includes(selected, entry_id)) {
      _.pull(selected, entry_id);
    } else {
      selected.push(entry_id);
    }

    this.setState(
      {
        selected,
        waitingForResponse: true,
      },
      callback,
    );
  };

  onToggleShowVotedOnlyClick = () => {
    this.setState({ showVotedOnly: !this.state.showVotedOnly });
  };

  renderToggleShowVotedOnly = () => (
    <button
      className="btn-osu-big btn-osu-big--contest-entries-toolbar"
      onClick={this.onToggleShowVotedOnlyClick}
      type="button"
    >
      <span className="btn-osu-big__icon-inline btn-osu-big__icon-inline--left">
        <i
          className={
            this.state.showVotedOnly ? 'fas fa-check-square' : 'far fa-square'
          }
        />
      </span>
      {trans('contest.voting.show_voted_only')}
    </button>
  );
}
