// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import FancyChart from 'charts/fancy-chart';

export default class FancyGraph {
  private container?: (Element & { _chart?: FancyChart }) | null;

  constructor() {
    window.addEventListener('resize', this.resize);
    document.addEventListener('turbo:load', this.initialize);
  }

  initialize = () => {
    this.container = document.querySelector('.js-fancy-graph');
    if (this.container == null) return;

    this.container._chart ??= new FancyChart(this.container);
  };

  resize = () => {
    this.container?._chart?.resize();
  };
}
