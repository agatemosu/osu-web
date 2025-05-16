// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import UserLink from 'components/user-link';
import _ from 'lodash';
import moment from 'moment';
import React from 'react';
import { classWithModifiers, urlPresence } from 'utils/css';
import { formatNumber } from 'utils/html';
import { transChoice } from 'utils/lang';
import { Voter } from './voter';

const bn = 'contest-art-entry';

const renderUserLink = (user) => (
  <UserLink className={`${bn}__entrant`} user={user} />
);

interface Props {
  contest: any;
  entry: any;
  galleryIndex: number;
  index: number;
  isSelected: boolean;
  selected: any[];
  waitingForResponse: boolean;
}

export default function ArtEntry(props: Props) {
  const { isSelected } = props;

  const votingOver = moment(props.contest.voting_ends_at).diff() <= 0;
  const showVotes = props.contest.show_votes;
  const showName = props.contest.show_names;
  const showUserLink = props.entry.user?.id != null;
  const thumbnailShape = props.contest.thumbnail_shape;
  const galleryId = `contest-${props.contest.id}`;
  const buttonId = `${galleryId}:${props.entry.id}`;
  const hideVoteButton =
    (props.selected.length >= props.contest.max_votes || votingOver) &&
    !isSelected;

  let place = 0;
  let top3 = false;
  let usersVotedPercentage = 0;

  if (showVotes) {
    place = props.index + 1;
    top3 = place <= 3;
    usersVotedPercentage = _.round(
      (props.entry.results.votes / props.contest.users_voted_count) * 100,
      2,
    );
  }

  const entryLinkProps: Record<string, string> = {
    className: `${bn}__thumbnail-link`,
    href: props.entry.preview,
  };

  if (props.contest.type === 'art') {
    entryLinkProps.className += ' js-gallery';
    entryLinkProps['data-width'] = props.entry.artMeta.width;
    entryLinkProps['data-height'] = props.entry.artMeta.height;
    entryLinkProps['data-gallery-id'] = galleryId;
    entryLinkProps['data-index'] = props.galleryIndex.toString();
    entryLinkProps['data-button-id'] = buttonId;
  } else {
    entryLinkProps.rel = 'nofollow noreferrer';
    entryLinkProps.target = '_blank';
  }

  return (
    <div
      className={classWithModifiers(bn, {
        [thumbnailShape]: thumbnailShape != null,
        result: showVotes,
        selected: isSelected,
        'show-name': showName && !showVotes,
        placed: showVotes && top3,
        [`placed-${place}`]: showVotes && top3,
        smaller: showVotes && !top3,
      })}
    >
      <div
        className={`${bn}__thumbnail`}
        style={{
          backgroundImage: urlPresence(this.props.entry.thumbnail),
        }}
      >
        <a {...entryLinkProps} />
        <div
          className={`${bn}__vote-link-banner ${hideVoteButton ? 'hidden' : ''}`}
        >
          <Voter
            key={props.entry.id}
            buttonId={buttonId}
            contest={props.contest}
            entry={props.entry}
            selected={props.selected}
            theme="art"
            voteCount={props.selected.length}
            waitingForResponse={props.waitingForResponse}
          />
        </div>
      </div>

      {(showName || showVotes || showUserLink) && (
        <div className={`${bn}__result`}>
          {showName && (
            <a
              className={`${bn}__result-name u-ellipsis-overflow`}
              href={props.entry.preview}
              rel="nofollow noreferrer"
              target="_blank"
            >
              {props.entry.title}
            </a>
          )}
          {showUserLink && !showVotes && renderUserLink(props.entry.user)}
          {showVotes && (
            <>
              <div className={`${bn}__result-ranking`}>
                <div className={`${bn}__result-place`}>
                  {top3 && (
                    <i className={`fas fa-fw fa-trophy ${bn}__trophy`} />
                  )}
                  <span>#{place}</span>
                </div>
                {showUserLink ? (
                  renderUserLink(props.entry.user)
                ) : (
                  <span className={`${bn}__entrant`}>
                    {props.entry.results.actual_name}
                  </span>
                )}
              </div>
              <div className={`${bn}__result-pane`}>
                <span className={`${bn}__result-votes`}>
                  {transChoice('contest.vote.count', props.entry.results.votes)}
                </span>
                {Number.isFinite(usersVotedPercentage) && (
                  <span
                    className={`${bn}__result-votes ${bn}__result-votes--percentage`}
                  >
                    ({formatNumber(usersVotedPercentage)}%)
                  </span>
                )}
              </div>
            </>
          )}
        </div>
      )}
    </div>
  );
}
