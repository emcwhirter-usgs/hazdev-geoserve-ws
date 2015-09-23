'use strict';

var View = require('mvc/View'),

    Util = require('util/Util');

var _DEFAULTS = {
  header: null,
  noDataMessage: '<p class="alert info">Time zone data unavailable.</p>'
};

/**
 * Class: TimezoneRegionView
 * @param params {object}
 *      Configuration options. See _DEFAULTS for more details
 */
var TimezoneRegionView = function (params) {
  var _this,
      _initialize,

      _header,
      _noDataMessage;

  _this = View(params || {});

  /**
   * @constructor
   */
  _initialize = function () {
    params = Util.extend({}, _DEFAULTS, params);

    _header = params.header;
    _noDataMessage = params.noDataMessage;

    _this.el.classList.add('timezone-region');
    _this.render();
  };

  /**
   * updates the view to reflect the current state of the model
   */
  _this.render = function () {
    var markup,
        timeData;

    timeData = null;

    markup = [(_header !== null) ? _header : ''];

    try {
      timeData = _this.model.get('regions').timezone.features[0].properties;

      markup.push(
        '<dl>' +
          '<dt>Time Zone</dt>' +
            '<dd>' + timeData.timezone + '</dd>' +
          '<dt>Standard Offset</dt>' +
            '<dd>' + timeData.standardoffset + '</dd>' +
          '<dt>DST Start</dt>' +
            '<dd>' + timeData.dststart + '</dd>' +
          '<dt>DST End</dt>' +
            '<dd>' + timeData.dstend + '</dd>' +
          '<dt>DST Offset</dt>' +
            '<dd>' + timeData.dstoffset + '</dd>' +
        '</dl>'
      );
    } catch (e) {
      markup.push(_noDataMessage);
    }

    _this.el.innerHTML = markup.join('');
  };

  /**
   * Destroy all the things
   */
  _this.destroy = Util.compose(function () {
    _header = null;
    _noDataMessage = null;

    _initialize = null;
    _this = null;
  }, _this.destroy);

  _initialize();
  params = null;
  return _this;
};

module.exports = TimezoneRegionView;
