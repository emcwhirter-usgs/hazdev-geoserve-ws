'use string';

var TectonicSummaryView = require('geoserve/TectonicSummaryView'),

    Model = require('mvc/Model'),

    Xhr = require('util/Xhr');


var model;

model = Model({
  regions: null
});

TectonicSummaryView({
  el: document.querySelector('#example-nodata'),
  header: '<h2>Example without data</h2>',
  model: Model({
    regions: null
  })
});

TectonicSummaryView({
  el: document.querySelector('#example'),
  header: '<h2>Example with data</h2>',
  model: model
});

Xhr.ajax({
  url: 'regions.json',
  success: function (data) {
    model.set({
      regions: data
    });
  }
});
