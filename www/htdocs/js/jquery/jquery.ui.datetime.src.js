(function($, undefined){

  $.widget("ui.datetime", {

    ready: false,
    childChain: false,

    widgetEventPrefix: "datetime",

    options: {
      value: null,
      format: 'yy-mm-dd hh:ii',
      altField: null,
      altFormat: null,
      inline: 'auto',
      withDate: true,
      minDate: null,
      maxDate: null,
      showWeek: false,
      numMonths: 1,
      withTime: true,
      stepHours: 1,
      stepMins: 5,
      chainTo: null,
      chainFrom: null,
      chainOptions: {}
    },

    _create: function(){
      this._insert();
      this._position();
      this._prepare();
      this._update();
      this._generate();
      this._events();
      this._chain();
    },

    _prepare: function(){
      var o = this.options;
      this.today = this._clean(new Date());
      if(!o.altFormat) this.options.altFormat = o.format;
      value = (!o.value) ? (o.altField) ? $(o.altField).val() : this.element.val() : o.value;
      format = (!value || value == o.value || value == this.element.val()) ? o.format : o.altFormat;
      this.date = (!value) ? new Date() : this._parse(value, format);
      this._limits();
      if(value) this.options.value = this.date.format(o.format);
    },

    _limits: function(){
      var o = this.options, date = null;
      this.minDate = (o.minDate) ? this._parse(o.minDate, o.format) : new Date(1970, 0, 1, 0, 0, 0, 0);
      this.maxDate = (o.maxDate) ? this._parse(o.maxDate, o.format) : new Date(9999, 11, 31, 23, 59, 59, 0);
      date = (this.current) ? this._clean(this.current) : this._clean(this.date);
      date.setTime(Math.max(Math.max(date.getTime(), this.minDate.getTime()), Math.min(date.getTime(), this.maxDate.getTime())));
      date.setHours(this.date.getHours());
      date.setMinutes(this.date.getMinutes());
      if(!this.current || this.current != date.getTime()) this.date.setTime(date.getTime());
      this.current = date.getTime();
    },

    _insert: function(){
      this.tag = this.element.get(0).tagName.toLowerCase();
      this.inline = (this.options.inline != 'auto') ? this.options.inline : ($.inArray(this.tag, [ 'input' ]) > -1) ? false : true;
      this.container = (this.tag == 'div') ? this.element.addClass('ui-datetime') : $('<div>').addClass('ui-datetime').insertAfter(this.element);
      if(this.inline) this.container.addClass('ui-datetime-inline ui-helper-clearfix');
      else this.container.hide();
    },

    _position: function(){
      var left = 0;
      if(this.inline){
        left = this.element.position().left - this.container.position().left;
        this.container.css('marginLeft', left);
      }else{
        left = this.element.position().left;
        this.container.css('left', left);
      }
    },

    _generate: function(){
      this.calendar = $('<div>').addClass('ui-datetime-calendar ui-widget ui-widget-content ui-corner-all').appendTo(this.container);
      this.calendar.css('width', (17*this.options.numMonths).toString() + 'em');
      withDate = (this.options.withDate) ? this._calendar() : this.calendar.hide();
      this.clock = $('<div>').addClass('ui-datetime-clock ui-widget ui-widget-content ui-corner-all').appendTo(this.container);
      withTime = (this.options.withTime) ? this._clock() : this.clock.hide();
    },

    _chain: function(){
      var o = $.extend({}, this.options,
        { value: null, chainTo: null, chainFrom: this.element, altField: null, minDate: this.options.value }, this.options.chainOptions),
          e = (this.options.chainTo) ? (this.options.chainTo == 'self') ? this.element : $(this.options.chainTo) : null;
      if(e != null && e.get(0) === this.element.get(0)){
        this.childChain = true;
        this.options.chainTo = $('<div>').appendTo(this.container);
        o.inline = true;
      }
      this.chainedTo = (e) ? $(this.options.chainTo).datetime(o) : null;
      this.chainedFrom = (this.options.chainFrom) ? $(this.options.chainFrom).datetime('widget') : null;
      if(this.chainedFrom)
      {
        this.container.addClass('ui-datetime-to');
        this.chainedFrom.datetime('option', { maxDate: this.options.value });
      }
    },

    _calendar: function(){
      var o = this.options, date = null, html = '', year = null, box = null, i = 0, d = 0,
          active = this._clean(new Date(this.current)).getTime(),
          min = this._clean(this.minDate).getTime(),
          max = this._clean(this.maxDate).getTime(),
          days = new Date(this.date.getFullYear(), this.date.getMonth(), 0);
      if(days.getTime() < this.minDate.getTime()) this.date.setMonth(this.minDate.getMonth());
      year = this.date.getFullYear(), month = this.date.getMonth();
      for(i=0;i<o.numMonths;i++){
        date = new Date(year, month);
        date.setDate(date.getDate() - date.getDay());
        className = ((o.numMonths == 1) ? 'all' : (i==0) ? 'first' : (i==o.numMonths-1) ? 'last' : 'middle');
        corners = 'ui-corner-' + ((o.numMonths == 1) ? 'all' : (i==0) ? 'left' : (i==o.numMonths-1) ? 'right' : 'none');
        html += '<div class="ui-datetime-calendar-'+className+'"><div class="ui-datetime-header ui-widget-header '+corners+'"><div class="ui-datetime-title">';
        html += ($.inArray(className, [ 'last', 'middle' ]) > -1) ? '' : '<a title="Prev" class="ui-datetime-prev ui-corner-all'+
          ((min > date.getTime()) ? ' ui-state-disabled' : '')+'"><span class="ui-icon ui-icon-circle-triangle-w">Prev</span></a>';
        html += ($.inArray(className, [ 'first', 'middle' ]) > -1) ? '' : '<a title="Next" class="ui-datetime-next ui-corner-all'+
          ((max < date.getTime()) ? ' ui-state-disabled' : '')+'"><span class="ui-icon ui-icon-circle-triangle-e">Next</span></a>';
        html += '<span class="ui-datetime-month">' + Date.monthNames[month] + '</span>&nbsp;<span class="ui-datetime-year">' + year + '</span></div></div>';
        html += '<table><thead><tr>';
        if(o.showWeek) html += '<th><span>Wk</span></th>';
        $.each(Date.dayNamesMin, function(index, value){ html += '<th><span>' + value + '</span></th>'; });
        html += '</tr></thead><tbody>';
        for(d=0;d<42;d++){
          other = (date.getMonth() != month || date.getTime() < min || date.getTime() > max);
          html += ((d%7==0) ? '<tr>' + ((o.showWeek) ? '<td class="ui-datetime-week">' + date.getWeek() + '</td>' : '') : '')
          html += '<td class="' + ((other) ? 'ui-datetime-unselectable ui-state-disabled' : '') + '">';
          box = (date.getTime() == this.today.getTime()) ? ' ui-state-highlight' : (date.getTime() == active) ? ' ui-state-active' : '';
          if(other) html += '<span class="ui-state-default">' + date.getDate() + '</span>'
          else html += '<a class="ui-state-default' + box + '" day="'+date.getDate()+'" month="'+date.getMonth()+'">' + date.getDate() + '</a>';
          html += '</td>' + ((d%7==6) ? '</tr>' : '');
          date.setDate(date.getDate() + 1);
        }
        html += '</tbody></table></div>';
        if(month == 11) year += 1;
        month = (month == 11) ? 0 : month + 1;
      }

      this.calendar.html(html);
      this._calendarEvents();
      return this;
    },

    _calendarEvents: function(){
      var self = this;
      this.calendar.find(".ui-datetime-prev:not('.ui-state-disabled'), .ui-datetime-next:not('.ui-state-disabled'), td a")
       .bind("mouseout", function(){ $(this).removeClass('ui-state-hover'); })
       .bind("mouseover", function(){ $(this).addClass('ui-state-hover'); });
      this.calendar.find(".ui-datetime-prev:not('.ui-state-disabled')").click(function(){ self._calendarUpdate('months', -1); });
      this.calendar.find(".ui-datetime-next:not('.ui-state-disabled')").click(function(){ self._calendarUpdate('months', 1); });
      this.calendar.find("td a").click(function(e){
        e.preventDefault();
        self.date.setDate($(this).attr('day'));
        self.date.setMonth($(this).attr('month'));
        self._value(self.date);
      });
    },

    _calendarUpdate: function(unit, offset){
      switch(unit){
        case 'months': this.date.setMonth(this.date.getMonth() + offset); break;
        case 'years': this.date.setFullYear(this.date.getFullYear() + offset); break;
      }
      this._calendar();
    },

    _clock: function(){
      var o = this.options,
          html = '<div class="ui-datetime-header ui-widget-header ui-corner-all"><div class="ui-datetime-title ui-datetime-time">'+
        this.date.format("'<span class=\"ui-datetime-time-hour\">'hh'</span>:<span class=\"ui-datetime-time-mins\">'ii'</span>'")+
        '</div></div><table><thead><tr><th><span>Hr</span></th><th><span>Mn</span></th></tr></thead><tbody>'+
        '<tr><td class="ui-datetime-slider-hour"></td><td class="ui-datetime-slider-mins"></td></tr></tbody></table>';
      this.clock.html(html).height(this.options.withDate ? this.calendar.height() : '14.2em');
      if(this.options.withDate) this.clock.css('marginLeft', '.2em');
      this._clockSlider(this.date.getHours(), 0, (24-o.stepHours), o.stepHours, 'hour');
      this._clockSlider(this.date.getMinutes(), 0, (60-o.stepMins), o.stepMins, 'mins');
      return this;
    },

    _clockSlider: function(v, min, max, step, field){
      var self = this, start = null,
          slider = $('<div>').addClass('ui-datetime-slider ui-datetime-slider-vertical ui-widget ui-widget-content ui-corner-all'),
          handle = $('<a>').addClass('ui-datetime-slider-handle ui-state-default ui-corner-all').css('top', '0').appendTo(slider);
      this.clock.find('.ui-datetime-slider-'+field).html(slider);
      slider.height(this.clock.height() - (slider.parent().offset().top - this.clock.offset().top) - 6);
      increment = (slider.height() - handle.height()) / ((max - min) / step);
      value = (v > max) ? max : (v < min) ? min : v;
      handle.css('top', Math.round((value-min)/step)*increment);
      handle.mousedown(function(d){ d.preventDefault();
        $("body").css('cursor', 'move');
        increment = (slider.height() - handle.height()) / ((max - min) / step);
        start = d.pageY - slider.offset().top - (handle.height()/2) - d.pageY;
        $(document).bind('mousemove.datetimeclock', function(m){
          value = Math.round((start + m.pageY)/increment)*step+min;
          value = (value > max) ? max : (value < min) ? min : value;
          handle.css('top', (value-min)/step*increment);
          self._clockUpdate(field, value);
        }).bind('mouseup.datetimeclock', function(u){
          $("body").css('cursor', 'auto');
          $(document).unbind('.datetimeclock');
          self._timeUpdate(field, value);
        });
      });
      return slider;
    },

    _clockUpdate: function(field, value){
      value = ('0' + value).substrOffset(-2);
      this.clock.find('.ui-datetime-time-'+field).html(value);
    },

    _timeUpdate: function(unit, value){
      if(unit == 'hour') this.date.setHours(value);
      else if(unit == 'mins') this.date.setMinutes(value);
      date = new Date(this.current);
      date.setHours(this.date.getHours());
      date.setMinutes(this.date.getMinutes());
      this._value(date);
    },

    _events: function(){
      var self = this;
      if(!this.inline) this.element.bind('click', function(){ self.show(); });
      $(window).resize(function(){ self._position(); });
    },

    _update: function(){
      if(!this.options.value) return true;
      var date = new Date(this.current);
      if(this.options.altField) $(this.options.altField).val(date.format(this.options.altFormat));
      if(this.tag == 'input'){ this.element.val(date.format(this.options.format)); }
      if(this.chainedTo) this.chainedTo.datetime('option', { minDate: this.options.value });
      if(this.chainedFrom) this.chainedFrom.datetime('option', { maxDate: this.options.value });
      this._trigger('change', null, { value: this.options.value });
      return this;
    },

    _value: function(date){
      this.current = date.getTime();
      this.options.value = date.format(this.options.format);
      this._update()._calendar()._clock();
      return this;
    },

    _clean: function(date){
      if(typeof date != 'object') date = new Date(date);
      return new Date(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0, 0);
    },

    _parse: function(value, format){
      if(value.constructor == Date) return value;
      return Date.strtodate(value, format);
    },

    value: function(value){
      if(value !== undefined){
        this.date = this._parse(value, this.options.format);
        this._limits();
        return this._value(this.date);
      }
      return new Date(this.current).format(this.options.format);
    },

    timestamp: function(){
      return this.current;
    },

    show: function(){
      var self = this;
      if(this.option('disabled')) return;
      this.container.fadeIn();
      $(document).bind('mousedown.datetimecalendar', function(event){ if($(event.target).parents(".ui-datetime").length == 0) self.hide(); })
        .bind('keydown.datetimecalendar', function(event){ if(event.which == 27) self.hide(); });
      this._calendar()._clock();
      if(this.chainedTo && this.childChain) this.chainedTo.datetime('show');
      this._trigger('show', null, { value: this.options.value });
      this.ready = true;
      return this;
    },

    hide: function(){
      $(document).unbind('.datetimecalendar');
      this.container.fadeOut('fast');
      this._trigger('hide', null, { value: this.options.value });
      return this;
    },

    disable: function() {
      $.Widget.prototype.disable.apply(this, arguments);
      this.element.attr('disabled', 'disabled');
      this.container.hide();
    },

    enable: function() {
      $.Widget.prototype.enable.apply(this, arguments);
      this.element.attr('disabled', '');
      if(this.inline) this.container.show();
    },

    destroy: function() {
      $.Widget.prototype.destroy.apply(this, arguments);
      if(arguments[0] != undefined && arguments[0]['unchain'] !== false){
        if(this.chainedTo) this.chainedTo.datetime('destroy');
        if(this.chainedFrom) this.chainedFrom.datetime('destroy');
      }
      this.container.remove();
    },

    _setOption: function(key, value) {
      $.Widget.prototype._setOption.apply(this, arguments);
      switch(key){
        case 'value': this.value(value); break;
        case 'format': this._update(); break;
        case 'showWeek': this._calendar(); break;
        case 'minDate':
        case 'maxDate': this._limits(); this._calendar(); break;
      }
      return this;
    }

  });

  $.extend(Date, {

    W3CDTF:  'yy-mm-dd hh:ii',
    ISO8601: 'yy-mm-dd hh:ii O',
    RFC822:  'D, d M yy hh:ii',
    RFC1123: 'D, d M yy hh:ii',
    RFC2822: 'D, d M yy hh:ii',
    RFC1036: 'D, d M y hh:ii',
    RFC850:  'DD, dd-M-y hh:ii',
    USASCII: 'mm/dd/yy g:ii A',

    dayNames: ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
    dayNamesShort: ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],
    dayNamesMin: ['Su','Mo','Tu','We','Th','Fr','Sa'],
    monthNames: ['January','February','March','April','May','June','July','August','September','October','November','December'],
    monthNamesShort: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],

    //yy|y|mm|m|MM|M|dd|d|DD|D|hh|h|gg|g|ii|i|a|A|O
    strtodate: function(value, format){
      var date = new Date(),
      codes = { 'yyyy' : [ 4, 'FullYear' ],
        'MM': [ Date.monthNames, 'Month' ],
        'M' : [ Date.monthNamesShort, 'Month' ],
        'DD': [ Date.dayNames, 'Day' ],
        'D' : [ Date.dayNamesShort, 'Day' ],
        'mm': [ 2, 'Month' ],
        'dd': [ 2, 'Date' ],
        'hh': [ 2, 'Hours' ],
        'ii': [ 2, 'Minutes' ],
        'm' : [ 1, 'Month', 12 ],
        'd' : [ 1, 'Date', 31 ],
        'h' : [ 1, 'Hours', 59 ],
        'i' : [ 1, 'Minutes', 59 ]
      };

      var aggregate = new RegExp("([+-])[ ]{0,1}([0-9]+)[ ]{0,1}(min|hour|day|week|month|year)", "gi");
      if((match = aggregate.exec(value))){
        match[2] = parseInt(match[2]);
        var diff = (match[1] == '+') ? match[2] : match[2]*-1;
        switch(match[3]){
          case 'min': date.setMinutes(date.getMinutes()+diff); break;
          case 'hour': date.setHours(date.getHours()+diff); break;
          case 'day': date.setDate(date.getDate()+diff); break;
          case 'week': date.setDate(date.getDate()+(diff*7)); break;
          case 'month': date.setMonth(date.getMonth()+diff); break;
          case 'year': date.setFullYear(date.getFullYear()+diff); break;
        }
      }else{
        format = format.replace(new RegExp("('[^']*)?(yy|')", "g"), function(needle, match){ return match ? needle : 'yyyy'; });
        $.each(codes, function(code, args){
          var pos = 0, c = 0, i = 0, literal = false, occurs = 0;
          while((pos = format.indexOf(code, ((pos) ? pos+1 : 0)   )) > -1){
            literal = false, occurs = 0;
            format = format.replace(new RegExp("('[^']*)?("+code+"|')", "g"), function(needle, match){
              if(match && (hide = needle.replace(code, code.replace(/./g, '?'))) != needle) literal = true;
              return match ? hide : (!literal && (occurs+=1) == 1) ? needle.replace(/./g, '?') : needle;
            });
            if(literal) continue;
            c = pos - format.substrCount("'", 0, pos);
            if(typeof args[0] === 'object'){
              $.each(args[0], function(i, v){
                if(value.indexOf(v) > -1){
                  value = value.replace('/'+v+'/g', code);
                  date['set'+args[1]](i);
                }
              });
            }else if(typeof args[0] === 'number'){
              if(args[0] == 1){
                i = parseInt(value.substr(c, 2).replace(/[^0-9]/g, ''), 10);
                if(i > args[2]) i = parseInt(value.substr(c, 1).replace(/[^0-9]/g, ''), 10);
              }else i = parseInt(value.substr(c, args[0]).replace(/[^0-9]/g, ''), 10);
              value = value.splice(c, Math.max(i.toString().length, args[0]), code);
              if(!isNaN(i)) date['set'+args[1]]((args[1]=='Month') ? i-1 : i);
              else date['set'+args[1]](0);
            }
          }
        });
      }

      date.setSeconds(0, 0);
      return date;
    },

    strtotime: function(value, format){
      return Date.strtodate(value, format).getTime();
    }

  });

  $.extend(Date.prototype, {

    format: function(format){
      var date = this;
      format = format.replace(new RegExp("('[^']*)?((yy|y|mm|m|MM|M|dd|d|DD|D|hh|h|gg|g|ii|i|a|A|O)|')", "g"),
        function(needle, match){
          if(!match){
            switch(needle){
              case 'yy': return date.getFullYear();
              case 'y' : return date.getShortYear();
              case 'mm': return ('0' + (date.getMonth()+1)).substrOffset(-2);
              case 'm' : return date.getMonth()+1;
              case 'MM': return Date.monthNames[date.getMonth()];
              case 'M' : return Date.monthNamesShort[date.getMonth()];
              case 'dd': return ('0' + date.getDate()).substrOffset(-2);
              case 'd' : return date.getDate();
              case 'DD': return Date.dayNames[date.getDay()];
              case 'D' : return Date.dayNamesShort[date.getDay()];
              case 'hh': return ('0' + date.getHours()).substrOffset(-2);
              case 'h' : return date.getHours();
              case 'gg': return ('0' + date.get12Hours()).substrOffset(-2);
              case 'g' : return date.get12Hours();
              case 'ii': return ('0' + date.getMinutes()).substrOffset(-2);
              case 'i' : return date.getMinutes();
              case 'a' : return date.getMeridiem();
              case 'A' : return date.getMeridiem().toUpperCase();
              case 'O' : return date.getUTCTimezone();
            }
          }
          return needle;
        });
      return format.replace(/'/g, '');
    },

    setDay: function(day){
      if(day < 7 && this.getDay() != day){
        diff = day - this.getDay()
        if(diff > 0) return this.setDate(this.getDate() + diff);
        return this.setDate(this.getDate() + diff);
      }
    },

    setShortYear: function(year, cutoff){
      var century = new Date().getFullYear().toString().substr(0, 2);
      century = (parseInt(year) <= parseInt(cutoff)) ? century : parseInt(century)-1;
      this.setFullYear(century + year);
    },

    setMeridiem: function(meridiem){
      //var hours = (meridiem.toLowerCase() == 'pm') ? this.getHours() + 12 : (this.getHours() == 12) ? 0 : this.getHours();
      //this.setHours(hours);
    },

    get12Hours: function(){
      return (this.getHours() == 0) ? 12 : (this.getHours() > 12) ? this.getHours() - 12 : this.getHours();
    },

    getMeridiem: function(){
      return (this.getHours() >= 12) ? 'pm' : 'am';
    },

    getShortYear: function(){
      return this.getFullYear().toString().substrOffset(-2);
    },

    // http://en.wikipedia.org/wiki/ISO_8601#Week_dates
    getWeek: function(){
      var test = new Date(this.getFullYear(), 0, 1);
      first = (test.getDay() == 0 || test.getDay() > 4) ? 0 : 1;
      return Math.ceil(this.getOrdinal() / 7) + first;
    },

    // http://en.wikipedia.org/wiki/ISO_8601#Ordinal_dates
    getOrdinal: function(){
      var first = new Date(this.getFullYear(), 0, 1);
      return Math.ceil((this.getTime() - first.getTime()) / 86400000);
    },

    getUTCTimezone: function(){
      offset = this.getTimezoneOffset();
      mins = ('0' + (offset%60*-1)).substrOffset(-2);
      hours = ('0' + ((offset-mins)/60*-1)).substrOffset(-2);
      return '+' + hours + mins;
    }

  });

  $.extend(String.prototype, {

    splice: function(index, count, replace){
      return this.substr(0, index) + replace + this.substr(index+count);
    },

    substrCount: function(needle, offset, length){
      var nextIndex = 0, count = 0, string = this.substr(offset, length);
      while((nextIndex = string.indexOf(needle, nextIndex) + 1) > 0) count++;
      return count;
    },

    substrOffset: function(offset){
      return this.substr(this.length+offset, this.length);
    }

  });

})(jQuery);