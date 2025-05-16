// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import React from 'react';
import { classWithModifiers } from 'utils/css';

const baseClass = classWithModifiers('contest__voting-star', 'smaller');
const selectedClass = 'contest__voting-star--selected';

interface Props {
  maxVotes: number;
  voteCount: number;
}

export default function VoteSummary({ maxVotes, voteCount }: Props) {
  return (
    <div
      className="js-contest-vote-summary"
      data-contest-vote-summary={JSON.stringify({ maxVotes, voteCount })}
    >
      {Array.from({ length: maxVotes }, (_, i) => (
        <div
          key={`vote-${i}`}
          className={
            i < voteCount ? `${baseClass} ${selectedClass}` : baseClass
          }
        >
          <span className="fas fa-fw fa-star" />
        </div>
      ))}
    </div>
  );
}
