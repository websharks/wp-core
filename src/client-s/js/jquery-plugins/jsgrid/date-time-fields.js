(function ($) {
  var momentData = rgvfbtgzxqrdbpcdjvzpcrfrsbtgpdvpMomentData;
  var pickadateData = bvtnafpwxwhxtzqwqumtmwfywfmmgffdPickadateData;

  if (typeof momentData.i18n[momentData.locale] === 'object') {
    moment.updateLocale(momentData.locale, momentData.i18n[momentData.locale]);
  } // This updates a global locale if the key exists.

  jsGridDateTimeFields = function (config) {
    jsGrid.Field.call(this, config);
  };
  jsGridDateTimeFields.prototype = new jsGrid.Field({
    subType: 'date-time',

    datePickerOptions: {},
    timePickerOptions: {},

    emptyDateTimeItemText: '—',
    emptyDateItemText: '—',
    emptyTimeItemText: '—',

    datePlaceholderText: 'date',
    timePlaceholderText: 'time',

    // Applies to `subType=date-time` only.
    noTimeEquals: 'startOfDay', // Or `endOfDay`.

    _datePickerOptions: null,
    _timePickerOptions: null,

    _$dateEditInput: null,
    _$dateInsertInput: null,

    _$timeEditInput: null,
    _$timeInsertInput: null,

    editValue: function () {
      return this._actionTimestamp('edit', this.subType);
    },
    insertValue: function () {
      return this._actionTimestamp('insert', this.subType);
    },
    insertTemplate: function () {
      return this._actionTemplate('insert', this.subType);
    },
    editTemplate: function (timestamp) {
      return this._actionTemplate('edit', this.subType, timestamp);
    },
    itemTemplate: function (timestamp) {
      return this._timestampFormat(timestamp, this.subType, true);
    },
    sorter: function (timestamp1, timestamp2) {
      return timestamp1 - timestamp2;
    },

    _pickerFunctionName: function (subType) {
      return 'picka' + subType; // e.g., `pickadate` or `pickatime`.
    },

    _pickerOptions: function (subType) {
      if (!this['_' + subType + 'PickerOptions']) {
        this['_' + subType + 'PickerOptions'] = $.extend({}, pickadateData['default' + this._ucf(subType) + 'Options'], this[subType + 'PickerOptions']);
        this['_' + subType + 'PickerOptions'].container = this._grid._container.parent();
      }
      return this['_' + subType + 'PickerOptions'];
    },

    _actionTimestamp: function (action, subType) {
      if (subType === 'date-time') {
        var date = $.trim(this['_$date' + this._ucf(action) + 'Input'].val());
        var time = $.trim(this['_$time' + this._ucf(action) + 'Input'].val());
        date = date === '0' ? '' : date, time = time === '0' ? '' : time;

        if (date && !time && this.noTimeEquals === 'startOfDay') {
          time = moment.utc().startOf('day').format(this._pickerOptions('time').momentFormat);
        } else if (date && !time && this.noTimeEquals === 'endOfDay') {
          time = moment.utc().endOf('day').format(this._pickerOptions('time').momentFormat);
        }
        if (date && time) { // A date and a time?
          return this._formatToTimestamp(date + ' ' + time, subType);
        } else if (date) { // Do we have at least the date?
          return this._formatToTimestamp(date, 'date');
        } else if (time) { // The date will be today.
          return this._formatToTimestamp(time, 'time');
        }
        return 0; // Empty timestamp value (default behavior).
      } else {
        return this._formatToTimestamp(this['_$' + subType + this._ucf(action) + 'Input'].val(), subType);
      }
    },

    _actionTemplate: function (action, subType, timestamp) {
      if (action === 'insert' && !this.inserting) {
        return null; // Not applicable.
      } else if (action === 'edit' && !this.editing) {
        return this.itemTemplate(timestamp);
      }
      if (subType === 'date-time') {
        if (this['_$date' + this._ucf(action) + 'Input']) {
          this['_$date' + this._ucf(action) + 'Input'][this._pickerFunctionName('date')]('stop');
        }
        if (this['_$time' + this._ucf(action) + 'Input']) {
          this['_$time' + this._ucf(action) + 'Input'][this._pickerFunctionName('time')]('stop');
        }
        this['_$date' + this._ucf(action) + 'Input'] = $('<input placeholder="' + this.datePlaceholderText + '" value="' + (action === 'edit' && timestamp ? this._timestampFormat(timestamp, 'date') : '') + '" />');
        this['_$time' + this._ucf(action) + 'Input'] = $('<input placeholder="' + this.timePlaceholderText + '" value="' + (action === 'edit' && timestamp ? this._timestampFormat(timestamp, 'time') : '') + '" />');

        setTimeout(function () { // Requires DOM insertion.
          this['_$date' + this._ucf(action) + 'Input'][this._pickerFunctionName('date')](this._pickerOptions('date'));
          this['_$time' + this._ucf(action) + 'Input'][this._pickerFunctionName('time')](this._pickerOptions('time'));
        }.bind(this), 100); // Short delay between now and when the appendage occurs in jsGrid upstream.

        var $table = $( // Both fields at the same time.
          '<table style="box-sizing:border-box; width:100%; border:0; padding:0; margin:0;">' +
          ' <tbody>' +
          '   <tr style="border:0; padding:0; margin:0;">' +
          '     <td class="-date" style="box-sizing:border-box; width:70%; border:0; padding:0; margin:0;"></td>' +
          '     <td class="-time" style="box-sizing:border-box; width:30%; border:0; padding:0; margin:0;"></td>' +
          '   </tr>' +
          ' </tbody>' +
          '</table>');
        $table.find('.-date').append(this['_$date' + this._ucf(action) + 'Input']);
        $table.find('.-time').append(this['_$time' + this._ucf(action) + 'Input']);

        return $table; // Both fields together in a table.
        //
      } else { // Only a single field in this case.
        if (this['_$' + subType + this._ucf(action) + 'Input']) {
          this['_$' + subType + this._ucf(action) + 'Input'][this._pickerFunctionName(subType)]('stop');
        }
        this['_$' + subType + this._ucf(action) + 'Input'] = $('<input placeholder="' + this[subType + 'PlaceholderText'] + '" value="' + (action === 'edit' && timestamp ? this._timestampFormat(timestamp, subType) : '') + '" />');

        setTimeout(function () { // Requires DOM insertion.
          this['_$' + subType + this._ucf(action) + 'Input'][this._pickerFunctionName(subType)](this._pickerOptions(subType));
        }.bind(this), 50); // Short delay between now and when the appendage occurs in jsGrid upstream.

        return this['_$' + subType + this._ucf(action) + 'Input'];
      }
    },

    _timestampFormat: function (timestamp, subType, forDisplay) {
      if (!(timestamp = parseInt(timestamp))) {
        if (forDisplay && subType === 'date-time') {
          return this.emptyDateTimeItemText;
        } else if (forDisplay) {
          return this['empty' + this._ucf(subType) + 'ItemText'];
        }
        return ''; // Default behavior on empty timestamp.
      }
      if (subType === 'date-time') { // Both the date & the time.
        return moment.utc(timestamp, 'X', momentData.locale).format(this._pickerOptions('date').momentFormat + ' ' + this._pickerOptions('time').momentFormat) + (forDisplay ? ' ' + momentData.i18n.utc : '');
      } else {
        return moment.utc(timestamp, 'X', momentData.locale).format(this._pickerOptions(subType).momentFormat) + (forDisplay ? ' ' + momentData.i18n.utc : '');
      }
    },

    _formatToTimestamp: function (formatted, subType) {
      if (!(formatted = $.trim(formatted)) || formatted === '0') {
        return 0; // Nothing to do here.
      }
      if (subType === 'date-time') {
        return parseInt(moment.utc(formatted, this._pickerOptions('date').momentFormat + ' ' + this._pickerOptions('time').momentFormat, momentData.locale).format('X'));
      } else {
        return parseInt(moment.utc(formatted, this._pickerOptions(subType).momentFormat, momentData.locale).format('X'));
      }
    },

    _ucf: function (string) {
      return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
    }
  });
  jsGrid.fields.dateTime = jsGridDateTimeFields;
})(jQuery);