/* 
 * Copyright 2013 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */


$(document).ready(function () {
  /*
   * Initialize AJAX user-typeahead widgets
   */
  $('.user-typeahead').each(function() {
    var $this = $(this);
    var url = $this.data('link') + '?query=%QUERY';
    var template = Hogan.compile('<p>{{fullname}} <strong class="pull-right">{{username}}</strong></p>');
    var source = new Bloodhound({
      datumTokenizer: function(d) { 
        return Bloodhound.tokenizers.whitespace(d.username); 
      },
      queryTokenizer: Bloodhound.tokenizers.whitespace,
      remote: {
        url: url,
        wildcard: '%QUERY'
      }
    });
    $this.typeahead(null,{
      displayKey: 'username',
      templates: {
        suggestion: function (d) { return template.render(d); }
      },
      source: source
    });
    $this.closest('.input-group').children('span.twitter-typeahead').css("display", "table-cell");
  });

  /**
   * Initialize user-typeahead widgets added dynamically by collection API
   */
  $('body').on('click.collection.data-api', '[data-collection-add-btn]', function ( e ) {
    $('input.user-typeahead').not('.tt-hint').not('.tt-input').each(function() {
      var $this = $(this);
      var url = $this.data('link') + '?query=%QUERY';
      var template = Hogan.compile('<p>{{fullname}} <strong class="pull-right">{{username}}</strong></p>');
      var source = new Bloodhound({
        datumTokenizer: function(d) { 
          return Bloodhound.tokenizers.whitespace(d.username); 
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
          url: url,
          wildcard: '%QUERY'
        }
      });
      $this.typeahead(null,{
        displayKey: 'username',
        templates: {
          suggestion: function (d) { return template.render(d); }
        },
        source: source
      });
      $this.closest('.input-group').children('span.twitter-typeahead').css("display", "table-cell");
    });
  });
});
