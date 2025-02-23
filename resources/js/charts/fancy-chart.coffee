# Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
# See the LICENCE file in the repository root for full licence text.

import { parseJsonNullable } from 'utils/json'

export default class FancyChart
  constructor: (area) ->
    @scales =
      x: d3.scaleLinear()
      y: d3.scaleLinear()

    @margins =
      top: 25
      right: 20
      bottom: 10
      left: 0

    @area = d3.select(area)

    @area.selectAll('.fancy-graph').remove()

    @svg = @area
      .append 'svg'
      .classed 'fancy-graph', true

    @svgWrapper = @svg.append 'g'

    @svgLine = @svgWrapper.append 'path'
      .classed 'fancy-graph__line', true
      .attr 'opacity', 0

    @line = d3.line()
      .curve d3.curveMonotoneX

    @svgEndCircle = @svgWrapper.append 'circle'
      .classed 'fancy-graph__circle', true
      .attr 'r', 2
      .attr 'opacity', 0

    data = parseJsonNullable area.dataset.src
    @loadData data


  hide: =>
    @svgEndCircle.attr 'opacity', 0
    @svgLine.attr 'opacity', 0


  loadData: (data) =>
    return if _.isEqual data, @data

    @data = data ? []
    @svgLine.datum @data

    @reveal()


  setDimensions: =>
    areaDims = @area.node().getBoundingClientRect()
    @width = areaDims.width - (@margins.left + @margins.right)
    @height = areaDims.height - (@margins.top + @margins.bottom)


  setScalesRange: =>
    @scales.x
      .range [0, @width]
      .domain d3.extent(@data, (d) => d.x)

    @scales.y
      .range [@height, 0]
      .domain d3.extent(@data, (d) => d.y)


  setLineSize: =>
    @line
      .x (d) => @scales.x d.x
      .y (d) => @scales.y d.y

    lastPoint = _.last(@data)

    if lastPoint?
      @svgEndCircle
        .attr 'transform', "translate(#{@scales.x(lastPoint.x)}, #{@scales.y(lastPoint.y)})"


  setSvgSize: =>
    @svg
      .attr 'width', @width + (@margins.left + @margins.right)
      .attr 'height', @height + (@margins.top + @margins.bottom)


  setWrapperSize: =>
    @svgWrapper
      .attr 'transform', "translate(#{@margins.left}, #{@margins.top})"


  drawLine: =>
    @svgLine
      .attr 'stroke-dasharray', 0
      .attr 'd', @line


  reveal: =>
    return @hide() if !@data[0]?

    @recalc()

    @svgLine
      .attr 'd', @line

    totalLength = @svgLine.node().getTotalLength()

    @svgEndCircle
      .attr 'opacity', 0

    @svgLine
      .attr 'stroke-dasharray', totalLength
      .attr 'stroke-dashoffset', totalLength
      .transition()
        .delay 400
        .duration 1000
        .ease d3.easeSinOut
        .attr 'stroke-dashoffset', 0
        .attr 'opacity', 1

    @svgEndCircle
      .transition()
        .delay 1300
        .duration 300
        .ease d3.easeSinOut
        .attr 'opacity', 1


  recalc: =>
    @setDimensions()

    @setScalesRange()

    @setSvgSize()
    @setWrapperSize()
    @setLineSize()


  resize: =>
    @recalc()
    @drawLine()
