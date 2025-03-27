// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import Build from 'components/build';
import ChangelogHeaderStreams from 'components/changelog-header-streams';
import HeaderV4 from 'components/header-v4';
import ShowMoreLink from 'components/show-more-link';
import type ChangelogBuild from 'interfaces/changelog-build';
import type ChangelogListing from 'interfaces/changelog-listing';
import type UpdateStream from 'interfaces/update-stream';
import { route } from 'laroute';
import { groupBy, last, map } from 'lodash';
import moment from 'moment';
import core from 'osu-core-singleton';
import React from 'react';
import { jsonClone } from 'utils/json';
import { trans } from 'utils/lang';

interface Props {
  container: HTMLElement;
  data: ChangelogListing;
  updateStreams: UpdateStream[];
}

interface State {
  builds: ChangelogBuild[];
  hasMore: boolean;
  loading: boolean;
}

function groupChangelogBuilds(builds: ChangelogBuild[]) {
  // Assumes created_at an iso8601 datetime string and removes the time portion.
  // Example: 2018-07-06T05:43:21+00:00
  return groupBy(builds, (build) => build.created_at.substring(0, 10));
}

export default class Main extends React.Component<Props> {
  state: State;

  constructor(props: Props) {
    super(props);

    this.state = { ...this.newStateFromData(props.data), loading: false };
  }

  componentDidMount() {
    core.changelogChartLoader.initialize();
  }

  newStateFromData(data: ChangelogListing) {
    const hasMore = data.builds.length === data.search.limit;
    const builds = (this.state?.builds ?? []).concat(data.builds);
    // remove one so there's at least one more to be loaded
    if (hasMore) builds.pop();

    return { builds, hasMore };
  }

  render() {
    return (
      <>
        <HeaderV4 links={this.headerLinks} linksBreadcrumb theme='changelog' />

        <div className='osu-page osu-page--changelog'>
          <ChangelogHeaderStreams updateStreams={this.props.updateStreams} />

          <div className='js-changelog-chart' />

          <div className='builds'>
            {map(groupChangelogBuilds(this.state.builds), (builds, date) => (
              <div key={date} className='builds__group'>
                <div className='builds__date'>{moment(date).format('LL')}</div>

                {builds.map((build) => (
                  <div key={build.id} className='builds__item'>
                    <Build build={build} />
                  </div>
                ))}
              </div>
            ))}
          </div>

          <ShowMoreLink
            callback={this.showMore}
            hasMore={this.state.hasMore}
            loading={this.state.loading}
            modifiers='changelog-index'
          />
        </div>
      </>
    );
  }

  get headerLinks() {
    return [
      {
        title: trans('layout.header.changelog.index'),
        url: route('changelog.index'),
      },
    ];
  }

  readonly showMore = () => {
    if (!this.state.hasMore) return;

    const lastBuild = last(this.state.builds);
    if (lastBuild == null) return;

    const search = jsonClone(this.props.data.search);
    search.max_id = lastBuild.id - 1;
    this.setState({ loading: true });

    $.get(route('changelog.index'), search)
      .done((data: ChangelogListing) => this.setState(this.newStateFromData(data)))
      .always(() => this.setState({ loading: false }));
  };
}
