var WPop_shortcode_editor = {
    columns: '',
    $selector: null,
    init: function() {
      var self = this;

      jQuery('.wpop-content', WPop_shortcode_dialog.$dialog).append(
        '<div id="wpop-sc-columns-selector" class="section section-radio">' + 
          '<label class="section-label" for="wpop-sc-col-opt">Distribution</label>' +
          '<div class="option">' +
            '<div class="input" />' +
            '<div class="info"><span class="description">Select the column distribution.</span></div>' +
          '</div>' +
        '</div>'
      );
      this.$selector = jQuery('#wpop-sc-columns-selector');
      
      self.columns = jQuery('input[name=columns]:checked', WPop_shortcode_dialog.$dialog).val();
      this.showSelector();

      jQuery('input[name=columns]', WPop_shortcode_dialog.$dialog).click(function() {
        self.columns = jQuery(this).val();
        self.showSelector();
      });
    },
    showSelector: function() {
      jQuery('.input', this.$selector).html('');

      switch (this.columns) {
        case 'two':   this.showTwoOptions(); break;
        case 'three': this.showThreeOptions(); break;
        case 'four':  this.showFourOptions(); break;
        case 'five':  this.showFiveOptions(); break;
        case 'six':   this.showSixOptions(); break;
      }
    },
    showTwoOptions: function() {
      var self = this;
      var $d1 = jQuery('<input type="radio" id="wpop-sc-opt-0" name="wpop-sc-col-opt" value="1/2|1/2" checked="checked"> <label for="wpop-sc-opt-0">1/2 | 1/2</label>');
      jQuery('.input', self.$selector).append($d1);
    },
    showThreeOptions: function() {
      var self = this;
      var $d1 = jQuery('<input type="radio" id="wpop-sc-opt-0" name="wpop-sc-col-opt" value="1/3|1/3|1/3" checked="checked"> <label for="wpop-sc-opt-0">1/3 | 1/3 | 1/3</label>');
      var $d2 = jQuery('<input type="radio" id="wpop-sc-opt-1" name="wpop-sc-col-opt" value="1/3|2/3"> <label for="wpop-sc-opt-1">1/3 | 2/3</label>');
      var $d3 = jQuery('<input type="radio" id="wpop-sc-opt-2" name="wpop-sc-col-opt" value="2/3|1/3"> <label for="wpop-sc-opt-2">2/3 | 1/3</label>');
      jQuery('.input', self.$selector).append($d1).append('&nbsp;&nbsp;').append($d2).append('&nbsp;&nbsp;').append($d3);
    },
    showFourOptions: function() {
      var self = this;
      var $d1 = jQuery('<input type="radio" id="wpop-sc-opt-0" name="wpop-sc-col-opt" value="1/4|1/4|1/4|1/4" checked="checked"> <label for="wpop-sc-opt-0">1/4 | 1/4 | 1/4 | 1/4</label>');
      var $d2 = jQuery('<input type="radio" id="wpop-sc-opt-1" name="wpop-sc-col-opt" value="1/4|3/4"> <label for="wpop-sc-opt-1">1/4 | 3/4</label>');
      var $d3 = jQuery('<input type="radio" id="wpop-sc-opt-2" name="wpop-sc-col-opt" value="3/4|1/4"> <label for="wpop-sc-opt-2">3/4 | 1/4</label>');
      jQuery('.input', self.$selector).append($d1).append('&nbsp;&nbsp;').append($d2).append('&nbsp;&nbsp;').append($d3);
    },
    showFiveOptions: function() {
      var self = this;
      var $d1 = jQuery('<input type="radio" id="wpop-sc-opt-0" name="wpop-sc-col-opt" value="1/5|1/5|1/5|1/5|1/5" checked="checked"> <label for="wpop-sc-opt-0">1/5 | 1/5 | 1/5 | 1/5 | 1/5</label>');
      var $d2 = jQuery('<input type="radio" id="wpop-sc-opt-1" name="wpop-sc-col-opt" value="1/5|2/5|2/5"> <label for="wpop-sc-opt-1">1/5 | 2/5 | 2/5</label>');
      var $d3 = jQuery('<input type="radio" id="wpop-sc-opt-2" name="wpop-sc-col-opt" value="2/5|1/5|2/5"> <label for="wpop-sc-opt-2">2/5 | 1/5 | 2/5</label>');
      var $d4 = jQuery('<input type="radio" id="wpop-sc-opt-3" name="wpop-sc-col-opt" value="2/5|2/5|1/5"> <label for="wpop-sc-opt-3">2/5 | 2/5 | 1/5</label>');
      var $d5 = jQuery('<input type="radio" id="wpop-sc-opt-4" name="wpop-sc-col-opt" value="2/5|3/5"> <label for="wpop-sc-opt-4">2/5 | 3/5</label>');
      var $d6 = jQuery('<input type="radio" id="wpop-sc-opt-5" name="wpop-sc-col-opt" value="3/5|2/5"> <label for="wpop-sc-opt-5">3/5 | 2/5</label>');
      var $d7 = jQuery('<input type="radio" id="wpop-sc-opt-6" name="wpop-sc-col-opt" value="1/5|4/5"> <label for="wpop-sc-opt-6">1/5 | 4/5</label>');
      var $d8 = jQuery('<input type="radio" id="wpop-sc-opt-7" name="wpop-sc-col-opt" value="4/5|1/5"> <label for="wpop-sc-opt-7">4/5 | 1/5</label>');
      jQuery('.input', self.$selector)
      .append($d1).append('<br>')
      .append($d2).append('&nbsp;&nbsp;').append($d3).append('&nbsp;&nbsp;').append($d4).append('<br>')
      .append($d5).append('&nbsp;&nbsp;').append($d6).append('<br>')
      .append($d7).append('&nbsp;&nbsp;').append($d8);
    },
    showSixOptions: function() {
      var self = this;
      var $d1 = jQuery('<input type="radio" id="wpop-sc-opt-0" name="wpop-sc-col-opt" value="1/6|1/6|1/6|1/6|1/6|1/6" checked="checked"> <label for="wpop-sc-opt-0">1/6 | 1/6 | 1/6 | 1/6 | 1/6 | 1/6</label>');
      var $d2 = jQuery('<input type="radio" id="wpop-sc-opt-1" name="wpop-sc-col-opt" value="1/6|5/6"> <label for="wpop-sc-opt-1">1/6 | 5/6</label>');
      var $d3 = jQuery('<input type="radio" id="wpop-sc-opt-2" name="wpop-sc-col-opt" value="5/6|1/6"> <label for="wpop-sc-opt-2">5/6 | 1/6</label>');
      jQuery('.input', self.$selector).append($d1).append('&nbsp;&nbsp;').append($d2).append('&nbsp;&nbsp;').append($d3);
    },
    shortcode: function() {
      var res = '';
      switch (this.columns) {
        case 'two':
          res = "[one_half]First Column...[/one_half]<br>\n"
              + "[one_half_last]Second Column...[/one_half_last]<br>\n";
          break;

        case 'three':
          var dist = jQuery('input[name=wpop-sc-col-opt]:checked', self.$selector).val();
          switch (dist) {
            case '1/3|1/3|1/3':
              res = "[one_third]First Column...[/one_third]<br>\n"
                  + "[one_third]Second Column...[/one_third]<br>\n"
                  + "[one_third_last]Third Column...[/one_third_last]<br>\n";
              break;
            case '1/3|2/3':
              res = "[one_third]First Column...[/one_third]<br>\n"
                  + "[two_third_last]Second Column...[/two_third_last]<br>\n";
              break;
            case '2/3|1/3':
              res = "[two_third]First Column...[/two_third]<br>\n"
                  + "[one_third_last]Second Column...[/one_third_last]<br>\n";
              break;
          }
          break;

        case 'four':
          var dist = jQuery('input[name=wpop-sc-col-opt]:checked', self.$selector).val();
          switch (dist) {
            case '1/4|1/4|1/4|1/4':
              res = "[one_fourth]First Column...[/one_fourth]<br>\n"
                  + "[one_fourth]Second Column...[/one_fourth]<br>\n"
                  + "[one_fourth]Third Column...[/one_fourth]<br>\n"
                  + "[one_fourth_last]Fourth Column...[/one_fourth_last]<br>\n";
              break;
            case '1/4|3/4':
              res = "[one_fourth]First Column...[/one_fourth]<br>\n"
                  + "[three_fourth_last]Second Column...[/three_fourth_last]<br>\n";
              break;
            case '3/4|1/4':
              res = "[three_fourth]First Column...[/three_fourth]<br>\n"
                  + "[one_fourth_last]Second Column...[/one_fourth_last]<br>\n";
              break;
          }
          break;
          
        case 'five':
          var dist = jQuery('input[name=wpop-sc-col-opt]:checked', self.$selector).val();
          switch (dist) {
            case '1/5|1/5|1/5|1/5|1/5':
              res = "[one_fifth]First Column...[/one_fifth]<br>\n"
                  + "[one_fifth]Second Column...[/one_fifth]<br>\n"
                  + "[one_fifth]Third Column...[/one_fifth]<br>\n"
                  + "[one_fifth]Fourth Column...[/one_fifth]<br>\n"
                  + "[one_fifth_last]Fourth Column...[/one_fifth_last]<br>\n";
              break;
            case '1/5|2/5|2/5':
              res = "[one_fifth]First Column...[/one_fifth]<br>\n"
                  + "[two_fifth]Second Column...[/two_fifth]<br>\n"
                  + "[two_fifth_last]Third Column...[/two_fifth_last]<br>\n";
              break;
            case '2/5|1/5|2/5':
              res = "[two_fifth]First Column...[/two_fifth]<br>\n"
                  + "[one_fifth]Second Column...[/one_fifth]<br>\n"
                  + "[two_fifth_last]Third Column...[/two_fifth_last]<br>\n";
              break;
            case '2/5|2/5|1/5':
              res = "[two_fifth]First Column...[/two_fifth]<br>\n"
                  + "[two_fifth]Second Column...[/two_fifth]<br>\n"
                  + "[one_fifth_last]Third Column...[/one_fifth_last]<br>\n";
              break;
            case '2/5|3/5':
              res = "[two_fifth]First Column...[/two_fifth]<br>\n"
                  + "[three_fifth_last]Second Column...[/three_fifth_last]<br>\n";
              break;
            case '3/5|2/5':
              res = "[three_fifth]First Column...[/three_fifth]<br>\n"
                  + "[two_fifth_last]Second Column...[/two_fifth_last]<br>\n";
              break;
            case '1/5|4/5':
              res = "[one_fifth]First Column...[/one_fifth]<br>\n"
                  + "[four_fifth_last]Second Column...[/four_fifth_last]<br>\n";
              break;
            case '4/5|1/5':
              res = "[four_fifth]First Column...[/four_fifth]<br>\n"
                  + "[one_fifth_last]Second Column...[/one_fifth_last]<br>\n";
              break;
          }
          break;
  
        case 'six':
          var dist = jQuery('input[name=wpop-sc-col-opt]:checked', self.$selector).val();
          switch (dist) {
            case '1/6|1/6|1/6|1/6|1/6|1/6':
              res = "[one_sixth]First Column...[/one_sixth]<br>\n"
                  + "[one_sixth]Second Column...[/one_sixth]<br>\n"
                  + "[one_sixth]Third Column...[/one_sixth]<br>\n"
                  + "[one_sixth]Fourth Column...[/one_sixth]<br>\n"
                  + "[one_sixth]Fifth Column...[/one_sixth]<br>\n"
                  + "[one_sixth_last]Sixth Column...[/one_sixth_last]<br>\n";
              break;
            case '1/6|5/6':
              res = "[one_sixth]First Column...[/one_sixth]<br>\n"
                  + "[five_sixth_last]Second Column...[/five_sixth_last]<br>\n";
              break;
            case '5/6|1/6':
              res = "[five_sixth]First Column...[/five_sixth]<br>\n"
                  + "[one_sixth_last]Second Column...[/one_sixth_last]<br>\n";
              break;
          }
          break;
      }
      return res;
    }
};
