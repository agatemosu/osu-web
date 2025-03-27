// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import Build from 'components/build';
import ChangelogHeaderStreams from 'components/changelog-header-streams';
import Comments from 'components/comments';
import HeaderV4 from 'components/header-v4';
import type ChangelogBuild from 'interfaces/changelog-build';
import type UpdateStream from 'interfaces/update-stream';
import { route } from 'laroute';
import core from 'osu-core-singleton';
import React from 'react';
import { trans } from 'utils/lang';
import { changelogBuild } from 'utils/url';

interface Props {
  build: ChangelogBuild;
  container: HTMLElement;
  updateStreams: UpdateStream[];
}

export default class Main extends React.PureComponent<Props> {
  componentDidMount() {
    core.changelogChartLoader.initialize();
  }

  render() {
    return (
      <>
        <HeaderV4 links={this.headerLinks} linksBreadcrumb theme='changelog' />

        <div className='osu-page osu-page--changelog'>
          <ChangelogHeaderStreams
            currentStreamId={this.props.build.update_stream.id}
            updateStreams={this.props.updateStreams}
          />

          <div className='js-changelog-chart' style={{ height: '100px' }} />

          <div className='builds'>
            <div className='builds__group'>
              <div className='builds__item'>
                <Build
                  build={this.props.build}
                  modifiers={['build']}
                  showDate
                  showVideo
                />
              </div>

              <div className='page-nav'>
                <div className='page-nav__item page-nav__item--left'>
                  {this.props.build.versions?.previous != null && (
                    <a href={changelogBuild(this.props.build.versions.previous)}>
                      <i className='fas fa-chevron-left'>
                        <span className='page-nav__label'>
                          {this.props.build.versions.previous.display_version}
                        </span>
                      </i>
                    </a>
                  )}
                </div>
                <div className='page-nav__item page-nav__item--right'>
                  {this.props.build.versions?.next != null && (
                    <a href={changelogBuild(this.props.build.versions.next)}>
                      {this.props.build.versions.next.display_version}
                      <span className='page-nav__label'>
                        <i className='fas fa-chevron-right' />
                      </span>
                    </a>
                  )}
                </div>
              </div>
            </div>

            {!core.currentUser?.is_supporter && (
              <div className='builds__group'>{this.renderSupporterPromo()}</div>
            )}

            <div className='builds__group builds__group--discussions'>
              <Comments
                baseCommentableMeta={{
                  id: this.props.build.id,
                  type: 'build',
                }}
                controllerStateSelector='#json-comments'
                modifiers='changelog'
              />
            </div>
          </div>
        </div>
      </>
    );
  }

  renderSupporterPromo() {
    return (
      <div className='supporter-promo'>
        <div className='supporter-promo__pippi'>
          <div className='supporter-promo__heart' />
        </div>

        <div className='supporter-promo__text-box'>
          <h2 className='supporter-promo__heading'>
            {trans('changelog.support.heading')}
          </h2>

          <div>
            <p
              className='supporter-promo__text'
              dangerouslySetInnerHTML={{
                __html: trans('changelog.support.text_1', {
                  link: `<a href='${route('support-the-game')}' class='supporter-promo__link'>${trans('changelog.support.text_1_link')}</a>`,
                }),
              }}
            />
            <p className='supporter-promo__text supporter-promo__text--small'>
              {trans('changelog.support.text_2')}
            </p>
          </div>
        </div>
      </div>
    );
  }

  get headerLinks() {
    return [
      {
        title: trans('layout.header.changelog.index'),
        url: route('changelog.index'),
      },
      {
        title: `${this.props.build.update_stream.display_name} ${this.props.build.display_version}`,
        url: changelogBuild(this.props.build),
      },
    ];
  }
}
