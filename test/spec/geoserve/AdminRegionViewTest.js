/* global after, before, chai, describe, it */
'use strict';

var AdminRegionView = require('geoserve/AdminRegionView'),

    Model = require('mvc/Model'),

    Xhr = require('util/Xhr');


var expect,
    regions;

expect = chai.expect;


describe('AdminRegionView test suite.', function () {

  before(function (done) {
    Xhr.ajax({
      url: 'regions.json',
      success: function (data) {
        regions = data;
        done();
      }
    });
  });

  after(function () {
    regions = null;
  });


  describe('Constructor', function () {
    it('is defined', function () {
      /* jshint -W030 */
      expect(AdminRegionView).not.to.be.null;
      expect(AdminRegionView).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('can be instantiated', function () {
      var c = new AdminRegionView();
      /* jshint -W030 */
      expect(c).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('can be created and destroyed', function () {
      var createDestroy;

      createDestroy = function () {
        var view = AdminRegionView();
        view.destroy();
      };

      expect(createDestroy).to.not.throw(Error);
    });
  });

  describe('Render', function () {
    it('shows data when data is available', function () {
      var div;

      div = document.createElement('div');

      AdminRegionView({
        el: div,
        model: Model({
          regions: regions
        })
      });

      expect(div.innerHTML).to.be.equal('<dl class="horizontal">' +
          '<dt>ISO</dt><dd>USA</dd>' +
          '<dt>Country</dt><dd>United States</dd>' +
          '<dt>Region</dt><dd>California</dd>' +
        '</dl>');
    });

    it('shows custom message when no data is available', function () {
      var div,
          text;

      div = document.createElement('div');
      text = '<p class="alert">hello</p>';

      AdminRegionView({
          el: div,
          model: Model(),
          noDataMessage: text
        });

      expect(div.innerHTML).to.be.equal(text);
    });

    it('shows custom header when header is passed', function () {
      var div,
          header,
          text;

      div = document.createElement('div');
      text = 'Header';
      header = '<h3 class="header">' + text + '</h3>';

      AdminRegionView({
          el: div,
          header: header,
          model: Model()
        });

      expect(div.querySelector('.header').innerHTML).to.be.equal(text);
    });
  });

});
