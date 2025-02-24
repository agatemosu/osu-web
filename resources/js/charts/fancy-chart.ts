// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import * as d3 from 'd3';
import _ from 'lodash';
import { parseJsonNullable } from 'utils/json';

interface Datum {
  x: number;
  y: number;
}

export default class FancyChart {
  private readonly area;
  private data: Datum[] = [];
  private height = 0;
  private readonly line;
  private readonly margins;
  private readonly scales;
  private readonly svg;
  private readonly svgEndCircle;
  private readonly svgLine;
  private readonly svgWrapper;
  private width = 0;

  constructor(area: HTMLElement) {
    this.scales = {
      x: d3.scaleLinear(),
      y: d3.scaleLinear(),
    };

    this.margins = {
      bottom: 10,
      left: 0,
      right: 20,
      top: 25,
    };

    this.area = d3.select(area);

    this.area.selectAll('.fancy-graph').remove();

    this.svg = this.area.append('svg').classed('fancy-graph', true);

    this.svgWrapper = this.svg.append('g');

    this.svgLine = this.svgWrapper
      .append('path')
      .classed('fancy-graph__line', true)
      .attr('opacity', 0);

    this.line = d3.line<Datum>().curve(d3.curveMonotoneX);

    this.svgEndCircle = this.svgWrapper
      .append('circle')
      .classed('fancy-graph__circle', true)
      .attr('r', 2)
      .attr('opacity', 0);

    if (area.dataset.src == null) {
      throw new Error('missing data-src attribute');
    }

    this.loadData(area.dataset.src);
  }

  resize() {
    this.recalc();
    this.drawLine();
  }

  private drawLine() {
    this.svgLine.attr('stroke-dasharray', 0).attr('d', this.line);
  }

  private hide() {
    this.svgEndCircle.attr('opacity', 0);
    this.svgLine.attr('opacity', 0);
  }

  private loadData(src: string) {
    const data = parseJsonNullable<Datum[]>(src);

    if (_.isEqual(data, this.data)) {
      return;
    }

    this.data = data ?? [];
    this.svgLine.datum(this.data);

    this.reveal();
  }

  private recalc() {
    this.setDimensions();

    this.setScalesRange();

    this.setSvgSize();
    this.setWrapperSize();
    this.setLineSize();
  }

  private reveal() {
    if (this.data[0] == null) {
      this.hide();
      return;
    }

    this.recalc();

    this.svgLine.attr('d', this.line);

    const totalLength = this.svgLine.node()!.getTotalLength();

    this.svgEndCircle.attr('opacity', 0);

    this.svgLine
      .attr('stroke-dasharray', totalLength)
      .attr('stroke-dashoffset', totalLength)
      .transition()
      .delay(400)
      .duration(1000)
      .ease(d3.easeSinOut)
      .attr('stroke-dashoffset', 0)
      .attr('opacity', 1);

    this.svgEndCircle
      .transition()
      .delay(1300)
      .duration(300)
      .ease(d3.easeSinOut)
      .attr('opacity', 1);
  }

  private setDimensions() {
    const areaDims = this.area.node()!.getBoundingClientRect();
    this.width = areaDims.width - (this.margins.left + this.margins.right);
    this.height = areaDims.height - (this.margins.top + this.margins.bottom);
  }

  private setLineSize() {
    this.line.x((d) => this.scales.x(d.x)).y((d) => this.scales.y(d.y));

    const lastPoint = _.last(this.data);

    if (lastPoint != null) {
      this.svgEndCircle.attr(
        'transform',
        `translate(${this.scales.x(lastPoint.x)}, ${this.scales.y(lastPoint.y)})`,
      );
    }
  }

  private setScalesRange() {
    const extentX = d3.extent(this.data, (d) => d.x);
    const extentY = d3.extent(this.data, (d) => d.y);

    this.scales.x.range([0, this.width]).domain([extentX[0] ?? 0, extentX[1] ?? 0]);
    this.scales.y.range([this.height, 0]).domain([extentY[0] ?? 0, extentY[1] ?? 0]);
  }

  private setSvgSize() {
    this.svg
      .attr('width', this.width + (this.margins.left + this.margins.right))
      .attr('height', this.height + (this.margins.top + this.margins.bottom));
  }

  private setWrapperSize() {
    this.svgWrapper.attr(
      'transform',
      `translate(${this.margins.left}, ${this.margins.top})`,
    );
  }
}
