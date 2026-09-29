$(function() {
  'use strict';

  $('#tags').tagsInput({
    'width': '100%',
    'height': '100%',
    'interactive': true,
    'defaultText': 'Enter tags',
    'removeWithBackspace': true,
    'minChars': 0,
    'maxChars': 50,
    'placeholderColor': '#666666'
  });
  
  $('#author-tag').tagsInput({
    'width': '100%',
    'height': '100%',
    'interactive': true,
    'defaultText': 'Enter authors',
    'removeWithBackspace': true,
    'minChars': 0,
    'maxChars': 50,
    'placeholderColor': '#666666'
  });
});