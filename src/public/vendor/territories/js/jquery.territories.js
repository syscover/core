/*
 *	Territories v1.3 - 2017-11-26
 *	By José Carlos Rodríguez Palacín
 *	(c) 2017 SYSCOVER S.L. - https://syscover.com
 *	All rights reserved
 */

"use strict";

(function () {
    var Territories = {
        options: {
            id:                         null,                                       // application id
            wrapper:                    'form',                                     // element that cover territorials inputs
            urlPlugin:                  '.',
            lang:                       'es',

            highlightCountrys:          ['ES'],                                     // Countrys that you want highlight
            placeholderDisabled:        false,                                      // Disabled empty option
            useSeparatorHighlight:      false,
            textSeparatorHighlight:     '*********',

            tA1Wrapper:					'.territorial-area-1-wrapper',              // Wrapper selector territorial area 1
            tA2Wrapper:					'.territorial-area-2-wrapper',	            // Wrapper selector territorial area 2
            tA3Wrapper:					'.territorial-area-3-wrapper',		        // Wrapper selector territorial area 3

            tA1Label:                   '.territorial-area-1-label',                // label Select territorial area 1
            tA2Label:                   '.territorial-area-2-label',                // label Select territorial area 2
            tA3Label:                   '.territorial-area-3-label',                // label Select territorial area 3
            tA1LabelPrefix:             '',
            tA2LabelPrefix:             '',
            tA3LabelPrefix:             '',
            tA1LabelSuffix:             '',
            tA2LabelSuffix:             '',
            tA3LabelSuffix:             '',

            countrySelect:              'country_id',                               // select name country
            tA1Select:                  'territorial_area_1_id',                    // name Select territorial area 1
            tA2Select:                  'territorial_area_2_id',                    // name Select territorial area 2
            tA3Select:                  'territorial_area_3_id',                    // name Select territorial area 3
            prefixInput:                'prefix',                                   // input name of prefix field

            nullValue:                  '',                                         // The best option is ''
            countryValue:               'country_id_value',
            territorialArea1Value:      'territorial_area_1_id_value',
            territorialArea2Value:      'territorial_area_2_id_value',
            territorialArea3Value:      'territorial_area_3_id_value',

            trans: {
                selectCountry:		    'Select a Country',
                selectA:		        'Select a '
            }
        },
        callback: null,

        init: function(options, callback)
        {
            this.options = $.extend({}, this.options, options||{});	                // Init options

            // hide wrappers
            $(this.options.tA1Wrapper).hide();
            $(this.options.tA2Wrapper).hide();
            $(this.options.tA3Wrapper).hide();

            // set events on elements
            // when change country select
            $(`[name=${this.options.countrySelect}]`).change(($event, localCallback) => {
                // get form or wrapper
                var wrapper = $($event.target).closest(this.options.wrapper);
                var zones = null;

                // set country prefix in input prefix
                if($($event.target).find('option:selected').data('prefix'))
                    wrapper.find("[name='" + this.options.prefixInput + "']")
                        .val(wrapper.find("[name='" + this.options.countrySelect + "'] option:selected").data('prefix'));

                if($($event.target).find('option:selected').data('zones'))
                    zones = $($event.target).find('option:selected').data('zones');

                // when finish first fadeout we load name of label,
                // like that we are sure that the effect fadeOut is run
                wrapper.find(this.options.tA1Wrapper).fadeOut(400, () => {

                    //if placeholderDisabled is true, the default value is null
                    if(
                        (! this.options.placeholderDisabled && wrapper.find("[name='" + this.options.countrySelect + "']").val() !== this.options.nullValue) ||
                        (this.options.placeholderDisabled && wrapper.find("[name='" + this.options.countrySelect + "']").val() !== null)
                    ) {
                        // check that territorial area label contain words
                        if((zones === null || zones.indexOf('territorial_areas_1') > -1) && wrapper.find("[name='" + this.options.countrySelect + "'] option:selected").data('at1'))
                            wrapper.find(this.options.tA1Label).html(this.options.tA1LabelPrefix + wrapper.find("[name='" + this.options.countrySelect + "'] option:selected").data('at1') + this.options.tA1LabelSuffix);
                        if((zones === null || zones.indexOf('territorial_areas_2') > -1) && wrapper.find("[name='" + this.options.countrySelect + "'] option:selected").data('at2'))
                            $(this.options.tA2Label).html(this.options.tA2LabelPrefix + wrapper.find("[name='" + this.options.countrySelect + "'] option:selected").data('at2') + this.options.tA2LabelSuffix);
                        if((zones === null || zones.indexOf('territorial_areas_3') > -1) && wrapper.find("[name='" + this.options.countrySelect + "'] option:selected").data('at3'))
                            $(this.options.tA3Label).html(this.options.tA3LabelPrefix + wrapper.find("[name='" + this.options.countrySelect + "'] option:selected").data('at3') + this.options.tA3LabelSuffix);

                        // call method depend of zones
                        if(zones === null || zones.indexOf('territorial_areas_1') > -1)
                            this.getTerritorialArea1(wrapper, localCallback);
                        else if(zones !== null && zones.indexOf('territorial_areas_2') > -1)
                            this.getTerritorialArea2(wrapper, zones, localCallback);
                        else if(zones !== null && zones.indexOf('territorial_areas_3') > -1)
                            this.getTerritorialArea3(wrapper, zones, localCallback);
                    }

                });

                // hide ta2 y ta3
                wrapper.find(this.options.tA2Wrapper).fadeOut(400);
                wrapper.find(this.options.tA3Wrapper).fadeOut(400);
            });

            // when change territorial area 1 select
            $(`[name=${this.options.tA1Select}]`).change(($event, localCallback) => {

                // get form or wrapper
                var wrapper = $($event.target).closest(this.options.wrapper);

                if(
                    (! this.options.placeholderDisabled && wrapper.find("[name='" + this.options.tA1Select + "']").val() !== this.options.nullValue) ||
                    (this.options.placeholderDisabled && wrapper.find("[name='" + this.options.tA1Select + "']").val() !== null)
                )
                {
                    this.getTerritorialArea2(wrapper, undefined, localCallback);
                }
                else
                {
                    wrapper.find(this.options.tA2Wrapper).fadeOut();
                    wrapper.find(this.options.tA3Wrapper).fadeOut();
                }
            });

            // when change territorial area 2 select
            $(`[name=${this.options.tA2Select}]`).change(($event, localCallback) => {
                // get form or wrapper
                var wrapper = $($event.target).closest(this.options.wrapper);

                if(
                    (! this.options.placeholderDisabled && wrapper.find("[name='" + this.options.tA2Select + "']").val() !== this.options.nullValue) ||
                    (this.options.placeholderDisabled && wrapper.find("[name='" + this.options.tA2Select + "']").val() !== null)
                )
                {
                    this.getTerritorialArea3(wrapper, undefined, localCallback);
                }
                else
                {
                    wrapper.find(this.options.tA3Wrapper).fadeOut();
                }
            });

            this.getCountries();

            // check if must to show any area territorial select
            if($("[name='" + this.options.countrySelect + "']").val() != 'null' && $("[name='" + this.options.tA1Select + "'] option").length > 1)
                $(this.options.tA1Wrapper).show();

            if($("[name='" + this.options.tA1Select + "']").attr('value') != 'null' && $("[name='" + this.options.tA2Select + "'] option").length > 1)
                $(this.options.tA2Wrapper).show();

            if($("[name='" + this.options.tA2Select + "']").attr('value') != 'null' && $("[name='" + this.options.tA3Select + "'] option").length > 1)
                $(this.options.tA3Wrapper).show();

            this.callback = callback;

            if(this.callback != null)
            {
                var response = {
                    success: true,
                    message: 'Territories init'
                };

                this.callback(response);
            }

            return this;
        },

        getCountries: function() {

            $.ajax({
                type: "GET",
                url: `/api/v1/admin/country/${this.options.lang}`,
                data: {
                    sql: [
                        {
                            command: 'orderBy',
                            column: 'admin_country.name',
                            operator: 'asc'
                        }
                    ]
                },
                dataType: 'json',
                success: (response) => {

                    // These operations are applied on all forms
                    $("[name='" + this.options.countrySelect + "'] option").remove();
                    $("[name='" + this.options.countrySelect + "']").append(
                        $('<option></option>')
                            .val(this.options.nullValue)
                            .html(this.options.trans.selectCountry)
                            .prop('disabled', this.options.placeholderDisabled)
                    );

                    var highlightCountry = false;

                    for(var i in this.options.highlightCountrys)
                    {
                        for(var j in response.data)
                        {
                            // check if this country is highlight
                            if(this.options.highlightCountrys[i] == response.data[j].id)
                            {
                                $("[name='" + this.options.countrySelect + "']")
                                    .append(
                                        $('<option></option>')
                                            .val(response.data[j].id)
                                            .html(response.data[j].name)
                                            .data('zones', response.data[j].zones)
                                            .data('prefix', response.data[j].prefix)
                                            .data('at1', response.data[j].territorial_area_1)
                                            .data('at2', response.data[j].territorial_area_2)
                                            .data('at3', response.data[j].territorial_area_3)
                                    );
                                highlightCountry = true;
                            }
                        }
                    }

                    if(highlightCountry && this.options.useSeparatorHighlight)
                    {
                        $("[name='" + this.options.countrySelect + "']")
                            .append($('<option disabled></option>').html(this.options.textSeparatorHighlight));
                    }

                    for(var country of response.data)
                    {
                        // check if this country is highlight
                        if($.inArray(response.data[i].id, this.options.highlightCountrys) == -1)
                        {
                            $("[name='" + this.options.countrySelect + "']")
                                .append(
                                    $('<option></option>')
                                        .val(country.id)
                                        .html(country.name)
                                        .data('at1', country.territorial_area_1)
                                        .data('at2', country.territorial_area_2)
                                        .data('at3', country.territorial_area_3)
                                );
                        }
                    }

                    $("[name='" + this.options.countrySelect + "']").each((index, item) => {
                        // get form or wrapper
                        var wrapper = $(item).closest(this.options.wrapper);
                        // get value of country if it has
                        var countryValue = wrapper.find("[name='" + this.options.countryValue + "']").val();

                        if(countryValue !== null && countryValue !== '')
                        {
                            wrapper.find("[name='" + this.options.countrySelect + "']")
                                .val(countryValue)
                                .trigger("change");

                            // reset value to avoid trigger events, when change country
                            wrapper.find("[name='" + this.options.countryValue + "']").val('');
                        }
                        else
                        {
                            wrapper.find("[name='" + this.options.countrySelect + "']")
                                .val(this.options.nullValue)
                                .trigger("change");
                        }
                    });

                    // trigger event
                    $(this).trigger('territories:afterLoadCountries', response);

                    if(this.callback != null)
                    {
                        var response = {
                            success: true,
                            action: 'country-loaded',
                            message: 'Countries loaded'
                        };

                        this.callback(response);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    if(this.callback != null)
                    {
                        var response = {
                            success: false,
                            message: textStatus
                        };

                        this.callback(response);
                    }
                }
            });
        },

        getTerritorialArea1: function (wrapper, localCallback) {

            $.ajax({
                type: "GET",
                url: '/api/v1/admin/territorial-area-1',
                data: {
                    sql: [
                        {
                            command: 'where',
                            column: 'country_id',
                            operator: '=',
                            value: wrapper.find("[name='" + this.options.countrySelect + "']").val()
                        },
                        {
                            command: 'where',
                            column: 'admin_country.lang_id',
                            operator: '=',
                            value: this.options.lang
                        },
                        {
                            command: 'orderBy',
                            column: 'admin_territorial_area_1.name',
                            operator: 'asc'
                        }
                    ]
                },
                dataType: 'json',
                success: (response) => {

                    wrapper.find("[name='" + this.options.tA1Select + "'] option").remove();

                    if(response.data.length > 0) {
                        wrapper.find("[name='" + this.options.tA1Select + "']")
                            .append(
                                $('<option></option>')
                                    .val(this.options.nullValue)
                                    .html(this.options.trans.selectA + wrapper.find("[name='" + this.options.countrySelect + "'] option:selected").data('at1'))
                                    .prop('disabled', this.options.placeholderDisabled)
                            );

                        for(var territorialArea1 of response.data) {
                            wrapper.find("[name='" + this.options.tA1Select + "']")
                                .append(new Option(territorialArea1.name, territorialArea1.id));
                        }

                        // get value of territorialArea1 if it has
                        var territorialArea1Value = wrapper.find("[name='" + this.options.territorialArea1Value + "']").val();

                        // check if need set value from Territorial Area 1
                        if(territorialArea1Value !== null && territorialArea1Value !== '')
                        {
                            wrapper.find("[name='" + this.options.tA1Select + "']")
                                .val(territorialArea1Value)
                                .trigger("change");
                            wrapper.find("[name='" + this.options.territorialArea1Value + "']").val('');
                        }
                        else
                        {
                            // reset value territorialArea 1
                            wrapper.find("[name='" + this.options.tA1Select + "']")
                                .val(this.options.nullValue)
                                .trigger("change");
                        }

                        wrapper.find(this.options.tA1Wrapper).fadeIn();
                    }
                    else
                    {
                        wrapper.find(this.options.tA1Wrapper).fadeOut();
                        this.deleteTerritorialArea1(wrapper);
                        wrapper.find(this.options.tA2Wrapper).fadeOut();
                        this.deleteTerritorialArea2(wrapper);
                        wrapper.find(this.options.tA3Wrapper).fadeOut();
                        this.deleteTerritorialArea3(wrapper);
                    }

                    // trigger event
                    $(this).trigger('territories:afterLoadTerritorialAreas1', response);

                    var response = {
                        success: true,
                        action: 'territorialarea1-loaded',
                        message: 'TerritorialArea1 loaded'
                    };

                    if (typeof this.callback === 'function') this.callback(response);
                    if (typeof localCallback === 'function') localCallback(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    var response = {
                        success: false,
                        message: textStatus
                    };

                    if (typeof this.callback === 'function') this.callback(response);
                    if (typeof localCallback === 'function') localCallback(response);
                }
            });
        },

        getTerritorialArea2: function(wrapper, zones, localCallback) {

            var requestData = null;
            if(zones && zones.indexOf('territorial_areas_1') === -1)
            {
                requestData = {
                    sql: [
                        {
                            command: 'where',
                            column: 'admin_territorial_area_2.country_id',
                            operator: '=',
                            value: wrapper.find("[name='" + this.options.countrySelect + "']").val()
                        },
                        {
                            command: 'where',
                            column: 'admin_country.lang_id',
                            operator: '=',
                            value: this.options.lang
                        },
                        {
                            command: 'orderBy',
                            column: 'admin_territorial_area_2.name',
                            operator: 'asc'
                        }
                    ]
                };
            }
            else
            {
                requestData = {
                    sql: [
                        {
                            command: 'where',
                            column: 'territorial_area_1_id',
                            operator: '=',
                            value: wrapper.find("[name='" + this.options.tA1Select + "']").val()
                        },
                        {
                            command: 'where',
                            column: 'admin_country.lang_id',
                            operator: '=',
                            value: this.options.lang
                        },
                        {
                            command: 'orderBy',
                            column: 'admin_territorial_area_2.name',
                            operator: 'asc'
                        }
                    ]
                };
            }

            $.ajax({
                type: "GET",
                url: '/api/v1/admin/territorial-area-2',
                data: requestData,
                dataType: 'json',
                success: (response) => {

                    wrapper.find("[name='" + this.options.tA2Select + "'] option").remove();

                    if(response.data.length > 0)
                    {
                        wrapper.find("[name='" + this.options.tA2Select + "']")
                            .append(
                                $('<option></option>')
                                    .val(this.options.nullValue)
                                    .html(this.options.trans.selectA + wrapper.find("[name='" + this.options.countrySelect + "'] option:selected").data('at2'))
                                    .prop('disabled', this.options.placeholderDisabled)
                            );

                        for(var territorialArea2 of response.data)
                        {
                            wrapper.find("[name='" + this.options.tA2Select + "']")
                                .append(new Option(territorialArea2.name, territorialArea2.id));
                        }

                        // get value of territorialArea2 if it has
                        var territorialArea2Value = wrapper.find("[name='" + this.options.territorialArea2Value + "']").val();

                        // check if need set value from Territorial Area 2
                        if(territorialArea2Value !== null && territorialArea2Value !== '')
                        {
                            wrapper.find("[name='" + this.options.tA2Select + "']")
                                .val(territorialArea2Value)
                                .trigger("change");

                            // reset value to avoid load
                            wrapper.find("[name='" + this.options.territorialArea2Value + "']").val('');
                        }
                        else
                        {
                            // reset value territorialArea 2
                            wrapper.find("[name='" + this.options.tA2Select + "']")
                                .val(this.options.nullValue)
                                .trigger("change");
                        }

                        wrapper.find(this.options.tA2Wrapper).fadeIn();
                    }
                    else
                    {
                        wrapper.find(this.options.tA2Wrapper).fadeOut();
                        this.deleteTerritorialArea2(wrapper);
                        wrapper.find(this.options.tA3Wrapper).fadeOut();
                        this.deleteTerritorialArea3(wrapper);
                    }

                    // trigger event
                    $(this).trigger('territories:afterLoadTerritorialAreas2', response);

                    var response = {
                        success: true,
                        action: 'territorialarea2-loaded',
                        message: 'TerritorialArea2 loaded'
                    };

                    if (typeof this.callback === 'function') this.callback(response);
                    if (typeof localCallback === 'function') localCallback(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    var response = {
                        success: false,
                        message: textStatus
                    };

                    if (typeof this.callback === 'function') this.callback(response);
                    if (typeof localCallback === 'function') localCallback(response);
                }
            });
        },

        getTerritorialArea3: function(wrapper, zones, localCallback)
        {
            var requestData = null;
            if(zones && (zones.indexOf('territorial_areas_1') === -1 && zones.indexOf('territorial_areas_2') === -1))
            {
                requestData = {
                    sql: [
                        {
                            command: 'where',
                            column: 'admin_territorial_area_3.country_id',
                            operator: '=',
                            value: wrapper.find("[name='" + this.options.countrySelect + "']").val()
                        },
                        {
                            command: 'where',
                            column: 'admin_country.lang_id',
                            operator: '=',
                            value: this.options.lang
                        },
                        {
                            command: 'orderBy',
                            column: 'admin_territorial_area_3.name',
                            operator: 'asc'
                        }
                    ]
                };
            }
            else if(zones && zones.indexOf('territorial_areas_2') === -1)
            {
                requestData = {
                    sql: [
                        {
                            command: 'where',
                            column: 'admin_territorial_area_3.territorial_area_1',
                            operator: '=',
                            value: wrapper.find("[name='" + this.options.countrySelect + "']").val()
                        },
                        {
                            command: 'where',
                            column: 'admin_country.lang_id',
                            operator: '=',
                            value: this.options.lang
                        },
                        {
                            command: 'orderBy',
                            column: 'admin_territorial_area_3.name',
                            operator: 'asc'
                        }
                    ]
                };
            }
            else
            {
                requestData = {
                    sql: [
                        {
                            command: 'where',
                            column: 'territorial_area_2_id',
                            operator: '=',
                            value: wrapper.find("[name='" + this.options.tA2Select + "']").val()
                        },
                        {
                            command: 'where',
                            column: 'admin_country.lang_id',
                            operator: '=',
                            value: this.options.lang
                        },
                        {
                            command: 'orderBy',
                            column: 'admin_territorial_area_3.name',
                            operator: 'asc'
                        }
                    ]
                };
            }

            $.ajax({
                type: "GET",
                url: '/api/v1/admin/territorial-area-3',
                data: requestData,
                dataType: 'json',
                success: (response) => {

                    wrapper.find("[name='" + this.options.tA3Select + "'] option").remove();

                    if(response.data.length > 0)
                    {
                        wrapper.find("[name='" + this.options.tA3Select + "']")
                            .append(
                                $('<option></option>')
                                    .val(this.options.nullValue)
                                    .html(this.options.trans.selectA + wrapper.find("[name='" + this.options.countrySelect + "'] option:selected").data('at3'))
                                    .prop('disabled', this.options.placeholderDisabled)
                            );

                        for(var territorialArea3 of response.data)
                        {
                            $("[name='" + this.options.tA3Select + "']").append(new Option(territorialArea3.name, territorialArea3.id));
                        }

                        // get value of territorialArea3 if it has
                        var territorialArea3Value = wrapper.find("[name='" + this.options.territorialArea3Value + "']").val();

                        // check if need set value from Territorial Area 3
                        if(territorialArea3Value !== null && territorialArea3Value !== '')
                        {
                            wrapper.find("[name='" + this.options.tA3Select + "']")
                                .val(this.options.territorialArea3Value)
                                .trigger("change");
                            // reset value to avoid load
                            wrapper.find("[name='" + this.options.territorialArea3Value + "']").val('');
                        }
                        else
                        {
                            // reset value territorialArea 3
                            wrapper.find("[name='" + this.options.tA3Select + "']")
                                .val(this.options.nullValue)
                                .trigger("change");
                        }

                        wrapper.find(this.options.tA3Wrapper).fadeIn();
                    }
                    else
                    {
                        wrapper.find(this.options.tA3Wrapper).fadeOut();
                        this.deleteTerritorialArea3(wrapper);
                    }

                    // trigger event
                    $(this).trigger('territories:afterLoadTerritorialAreas3', response);

                    var response = {
                        success: true,
                        action: 'territorialarea3-loaded',
                        message: 'TerritorialArea3 loaded'
                    };

                    if (typeof this.callback === 'function') this.callback(response);
                    if (typeof localCallback === 'function') localCallback(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    var response = {
                        success: false,
                        message: textStatus
                    };

                    if (typeof this.callback === 'function') this.callback(response);
                    if (typeof localCallback === 'function') localCallback(response);
                }
            });
        },

        deleteTerritorialArea1: function(wrapper)
        {
            wrapper.find("[name='" + this.options.tA1Select + "'] option").remove();
        },

        deleteTerritorialArea2: function(wrapper)
        {
            wrapper.find("[name='" + this.options.tA2Select + "'] option").remove();
        },

        deleteTerritorialArea3: function(wrapper)
        {
            wrapper.find("[name='" + this.options.tA3Select + "'] option").remove();
        }
    };

    /*
     * Make sure Object.create is available in the browser (for our prototypal inheritance)
     * Note this is not entirely equal to native Object.create, but compatible with our use-case
     */
    if (typeof Object.create !== 'function') {
        Object.create = function (o) {
            function F() {}
            F.prototype = o;
            return new F();
        };
    }

    /*
     * Start the plugin
     */
    $.territories = (options, callback) => {
        var object;
        if(options.id === null) {
            if (! $.data(document, 'territories')) {
                object = $.data(document, 'territories', Object.create(Territories).init(options, callback));
                return $(object);
            } else {
                return $($.data(document, 'territories'));
            }
        } else {
            if (! $.data(document, 'territories' + options.id)) {
                object = $.data(document, 'territories' + options.id, Object.create(Territories).init(options, callback));
                return $(object);
            } else {

                return $($.data(document, 'territories' + options.id));
            }
        }
    };

}( jQuery ));