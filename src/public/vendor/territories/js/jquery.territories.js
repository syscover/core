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
            id:                             null,                                       // application id
            wrapper:                        'form',                                     // element that cover territorials inputs
            urlPlugin:                      '.',
            lang:                           'es',

            highlightCountries:             ['ES'],                                     // Countries that you want highlight
            addHighlightCountriesToList:    false,                                      // Add highlight countries to general list
            placeholderDisabled:            false,                                      // Disabled empty option
            showPlaceholderText:            true,                                       // Show text of TA in empty option
            useSeparatorHighlight:          false,
            textSeparatorHighlight:         '*********',

            tA1Wrapper:					    '.territorial-area-1-wrapper',              // Wrapper selector territorial area 1
            tA2Wrapper:					    '.territorial-area-2-wrapper',	            // Wrapper selector territorial area 2
            tA3Wrapper:					    '.territorial-area-3-wrapper',		        // Wrapper selector territorial area 3

            tA1Label:                       '.territorial-area-1-label',                // label Select territorial area 1
            tA2Label:                       '.territorial-area-2-label',                // label Select territorial area 2
            tA3Label:                       '.territorial-area-3-label',                // label Select territorial area 3
            tA1LabelPrefix:                 '',
            tA2LabelPrefix:                 '',
            tA3LabelPrefix:                 '',
            tA1LabelSuffix:                 '',
            tA2LabelSuffix:                 '',
            tA3LabelSuffix:                 '',

            countrySelect:                  'country_id',                               // select name country
            tA1Select:                      'territorial_area_1_id',                    // name Select territorial area 1
            tA2Select:                      'territorial_area_2_id',                    // name Select territorial area 2
            tA3Select:                      'territorial_area_3_id',                    // name Select territorial area 3
            prefixInput:                    'prefix',                                   // input name of prefix field

            nullValue:                      '',                                         // The best option is ''
            countryValue:                   'country_id_value',
            territorialArea1Value:          'territorial_area_1_id_value',
            territorialArea2Value:          'territorial_area_2_id_value',
            territorialArea3Value:          'territorial_area_3_id_value',

            trans: {
                selectCountry:		        'Select a Country',
                selectA:		            'Select a '
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

            var that = this;

            // set events on elements
            // when change country select
            $('[name=' + this.options.countrySelect + ']').change(function($event, localCallback) {
                // get form or wrapper
                var wrapper = $($event.target).closest(that.options.wrapper);
                var zones = null;

                // set country prefix in input prefix
                if($($event.target).find('option:selected').data('prefix'))
                    wrapper.find("[name='" + that.options.prefixInput + "']")
                        .val(wrapper.find("[name='" + that.options.countrySelect + "'] option:selected").data('prefix'));

                if($($event.target).find('option:selected').data('zones'))
                    zones = $($event.target).find('option:selected').data('zones');

                // when finish first fadeout we load name of label,
                // like that we are sure that the effect fadeOut is run
                wrapper.find(that.options.tA1Wrapper).fadeOut(400, function() {

                    //if placeholderDisabled is true, the default value is null
                    if(
                        (! that.options.placeholderDisabled && wrapper.find("[name='" + that.options.countrySelect + "']").val() !== that.options.nullValue) ||
                        (that.options.placeholderDisabled && wrapper.find("[name='" + that.options.countrySelect + "']").val() !== null)
                    ) {
                        // check that territorial area label contain words
                        if((zones === null || zones.indexOf('territorial_areas_1') > -1) && wrapper.find("[name='" + that.options.countrySelect + "'] option:selected").data('at1'))
                            wrapper.find(that.options.tA1Label).html(that.options.tA1LabelPrefix + wrapper.find("[name='" + that.options.countrySelect + "'] option:selected").data('at1') + that.options.tA1LabelSuffix);
                        if((zones === null || zones.indexOf('territorial_areas_2') > -1) && wrapper.find("[name='" + that.options.countrySelect + "'] option:selected").data('at2'))
                            $(that.options.tA2Label).html(that.options.tA2LabelPrefix + wrapper.find("[name='" + that.options.countrySelect + "'] option:selected").data('at2') + that.options.tA2LabelSuffix);
                        if((zones === null || zones.indexOf('territorial_areas_3') > -1) && wrapper.find("[name='" + that.options.countrySelect + "'] option:selected").data('at3'))
                            $(that.options.tA3Label).html(that.options.tA3LabelPrefix + wrapper.find("[name='" + that.options.countrySelect + "'] option:selected").data('at3') + that.options.tA3LabelSuffix);

                        // call method depend of zones
                        if(zones === null || zones.indexOf('territorial_areas_1') > -1)
                            that.getTerritorialArea1(wrapper, localCallback);
                        else if(zones !== null && zones.indexOf('territorial_areas_2') > -1)
                            that.getTerritorialArea2(wrapper, zones, localCallback);
                        else if(zones !== null && zones.indexOf('territorial_areas_3') > -1)
                            that.getTerritorialArea3(wrapper, zones, localCallback);
                    }

                });

                // hide ta2 y ta3
                wrapper.find(that.options.tA2Wrapper).fadeOut(400);
                wrapper.find(that.options.tA3Wrapper).fadeOut(400);
            });

            // when change territorial area 1 select
            $('[name=' + this.options.tA1Select + ']').change(function($event, localCallback) {

                // get form or wrapper
                var wrapper = $($event.target).closest(that.options.wrapper);

                if(
                    (! that.options.placeholderDisabled && wrapper.find("[name='" + that.options.tA1Select + "']").val() !== that.options.nullValue) ||
                    (that.options.placeholderDisabled && wrapper.find("[name='" + that.options.tA1Select + "']").val() !== null)
                )
                {
                    that.getTerritorialArea2(wrapper, undefined, localCallback);
                }
                else
                {
                    wrapper.find(that.options.tA2Wrapper).fadeOut();
                    wrapper.find(that.options.tA3Wrapper).fadeOut();
                }
            });

            // when change territorial area 2 select
            $('[name=' + this.options.tA2Select + ']').change(function($event, localCallback) {
                // get form or wrapper
                var wrapper = $($event.target).closest(that.options.wrapper);

                if(
                    (! that.options.placeholderDisabled && wrapper.find("[name='" + that.options.tA2Select + "']").val() !== that.options.nullValue) ||
                    (that.options.placeholderDisabled && wrapper.find("[name='" + that.options.tA2Select + "']").val() !== null)
                )
                {
                    that.getTerritorialArea3(wrapper, undefined, localCallback);
                }
                else
                {
                    wrapper.find(that.options.tA3Wrapper).fadeOut();
                }
            });

            that.getCountries();

            // check if must to show any area territorial select
            if($("[name='" + that.options.countrySelect + "']").val() != 'null' && $("[name='" + that.options.tA1Select + "'] option").length > 1)
                $(that.options.tA1Wrapper).show();

            if($("[name='" + that.options.tA1Select + "']").attr('value') != 'null' && $("[name='" + that.options.tA2Select + "'] option").length > 1)
                $(that.options.tA2Wrapper).show();

            if($("[name='" + that.options.tA2Select + "']").attr('value') != 'null' && $("[name='" + that.options.tA3Select + "'] option").length > 1)
                $(that.options.tA3Wrapper).show();

            that.callback = callback;

            if(that.callback != null)
            {
                var response = {
                    success: true,
                    message: 'Territories init'
                };

                that.callback(response);
            }

            return that;
        },

        getCountries: function() {

            var that = this;

            $.ajax({
                type: "GET",
                url: '/api/v1/admin/country/' + this.options.lang,
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
                success: function(response) {

                    // These operations are applied on all forms
                    $("[name='" + that.options.countrySelect + "'] option").remove();
                    $("[name='" + that.options.countrySelect + "']").append(
                        $('<option></option>')
                            .val(that.options.nullValue)
                            .html(that.options.trans.selectCountry)
                            .prop('disabled', that.options.placeholderDisabled)
                    );

                    var highlightCountry = false;

                    for(var i in that.options.highlightCountries)
                    {
                        for(var j in response.data)
                        {
                            // check if this country is highlight
                            if(that.options.highlightCountries[i] == response.data[j].id)
                            {
                                $("[name='" + that.options.countrySelect + "']")
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

                    if(highlightCountry && that.options.useSeparatorHighlight)
                    {
                        $("[name='" + that.options.countrySelect + "']")
                            .append($('<option disabled></option>').html(that.options.textSeparatorHighlight));
                    }

                    for(var i in response.data)
                    {
                        // check if this country is highlight
                        if(that.options.addHighlightCountriesToList || $.inArray(response.data[i].id, that.options.highlightCountries) == -1)
                        {
                            $("[name='" + that.options.countrySelect + "']")
                                .append(
                                    $('<option></option>')
                                        .val(response.data[i].id)
                                        .html(response.data[i].name)
                                        .data('at1', response.data[i].territorial_area_1)
                                        .data('at2', response.data[i].territorial_area_2)
                                        .data('at3', response.data[i].territorial_area_3)
                                );
                        }
                    }

                    $("[name='" + that.options.countrySelect + "']").each(function(index, item) {
                        // get form or wrapper
                        var wrapper = $(item).closest(that.options.wrapper);
                        // get value of country if it has
                        var countryValue = wrapper.find("[name='" + that.options.countryValue + "']").val();

                        if(countryValue !== null && countryValue !== '')
                        {
                            wrapper.find("[name='" + that.options.countrySelect + "']")
                                .val(countryValue)
                                .trigger("change");

                            // reset value to avoid trigger events, when change country
                            wrapper.find("[name='" + that.options.countryValue + "']").val('');
                        }
                        else
                        {
                            wrapper.find("[name='" + that.options.countrySelect + "']")
                                .val(that.options.nullValue)
                                .trigger("change");
                        }
                    });

                    // trigger event
                    $(that).trigger('territories:afterLoadCountries', response);

                    if(that.callback != null)
                    {
                        var response = {
                            success: true,
                            action: 'country-loaded',
                            message: 'Countries loaded'
                        };

                        that.callback(response);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    if(that.callback != null)
                    {
                        var response = {
                            success: false,
                            message: textStatus
                        };

                        that.callback(response);
                    }
                }
            });
        },

        getTerritorialArea1: function (wrapper, localCallback) {

            var that = this;

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
                success: function(response) {

                    wrapper.find("[name='" + that.options.tA1Select + "'] option").remove();

                    if(response.data.length > 0)
                    {
                        var defaultOption = $('<option></option>')
                            .val(that.options.nullValue)
                            .prop('disabled', that.options.placeholderDisabled);

                        if(that.options.showPlaceholderText) defaultOption.html(that.options.trans.selectA + wrapper.find("[name='" + that.options.countrySelect + "'] option:selected").data('at1'));

                        wrapper.find("[name='" + that.options.tA1Select + "']")
                            .append(defaultOption);

                        for(var i in response.data) {
                            wrapper.find("[name='" + that.options.tA1Select + "']")
                                .append(new Option(response.data[i].name, response.data[i].id));
                        }

                        // get value of territorialArea1 if it has
                        var territorialArea1Value = wrapper.find("[name='" + that.options.territorialArea1Value + "']").val();

                        // check if need set value from Territorial Area 1
                        if(territorialArea1Value !== null && territorialArea1Value !== '')
                        {
                            wrapper.find("[name='" + that.options.tA1Select + "']")
                                .val(territorialArea1Value)
                                .trigger("change");
                            wrapper.find("[name='" + that.options.territorialArea1Value + "']").val('');
                        }
                        else
                        {
                            // reset value territorialArea 1
                            wrapper.find("[name='" + that.options.tA1Select + "']")
                                .val(that.options.nullValue)
                                .trigger("change");
                        }

                        wrapper.find(that.options.tA1Wrapper).fadeIn();
                    }
                    else
                    {
                        wrapper.find(that.options.tA1Wrapper).fadeOut();
                        that.deleteTerritorialArea1(wrapper);
                        wrapper.find(that.options.tA2Wrapper).fadeOut();
                        that.deleteTerritorialArea2(wrapper);
                        wrapper.find(that.options.tA3Wrapper).fadeOut();
                        that.deleteTerritorialArea3(wrapper);
                    }

                    // trigger event
                    $(that).trigger('territories:afterLoadTerritorialAreas1', response);

                    var response = {
                        success: true,
                        action: 'territorialarea1-loaded',
                        message: 'TerritorialArea1 loaded'
                    };

                    if (typeof that.callback === 'function') that.callback(response);
                    if (typeof localCallback === 'function') localCallback(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    var response = {
                        success: false,
                        message: textStatus
                    };

                    if (typeof that.callback === 'function') that.callback(response);
                    if (typeof localCallback === 'function') localCallback(response);
                }
            });
        },

        getTerritorialArea2: function(wrapper, zones, localCallback) {

            var that = this;
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
                success: function(response) {

                    wrapper.find("[name='" + that.options.tA2Select + "'] option").remove();

                    if(response.data.length > 0)
                    {
                        var defaultOption = $('<option></option>')
                            .val(that.options.nullValue)
                            .prop('disabled', that.options.placeholderDisabled);

                        if(that.options.showPlaceholderText) defaultOption.html(that.options.trans.selectA + wrapper.find("[name='" + that.options.countrySelect + "'] option:selected").data('at2'));

                        wrapper.find("[name='" + that.options.tA2Select + "']")
                            .append(defaultOption);

                        for(var i in response.data)
                        {
                            wrapper.find("[name='" + that.options.tA2Select + "']")
                                .append(new Option(response.data[i].name, response.data[i].id));
                        }

                        // get value of territorialArea2 if it has
                        var territorialArea2Value = wrapper.find("[name='" + that.options.territorialArea2Value + "']").val();

                        // check if need set value from Territorial Area 2
                        if(territorialArea2Value !== null && territorialArea2Value !== '')
                        {
                            wrapper.find("[name='" + that.options.tA2Select + "']")
                                .val(territorialArea2Value)
                                .trigger("change");

                            // reset value to avoid load
                            wrapper.find("[name='" + that.options.territorialArea2Value + "']").val('');
                        }
                        else
                        {
                            // reset value territorialArea 2
                            wrapper.find("[name='" + that.options.tA2Select + "']")
                                .val(that.options.nullValue)
                                .trigger("change");
                        }

                        wrapper.find(that.options.tA2Wrapper).fadeIn();
                    }
                    else
                    {
                        wrapper.find(that.options.tA2Wrapper).fadeOut();
                        that.deleteTerritorialArea2(wrapper);
                        wrapper.find(that.options.tA3Wrapper).fadeOut();
                        that.deleteTerritorialArea3(wrapper);
                    }

                    // trigger event
                    $(that).trigger('territories:afterLoadTerritorialAreas2', response);

                    var response = {
                        success: true,
                        action: 'territorialarea2-loaded',
                        message: 'TerritorialArea2 loaded'
                    };

                    if (typeof that.callback === 'function') that.callback(response);
                    if (typeof localCallback === 'function') localCallback(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    var response = {
                        success: false,
                        message: textStatus
                    };

                    if (typeof that.callback === 'function') that.callback(response);
                    if (typeof localCallback === 'function') localCallback(response);
                }
            });
        },

        getTerritorialArea3: function(wrapper, zones, localCallback)
        {
            var that = this;
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
                success: function(response) {

                    wrapper.find("[name='" + that.options.tA3Select + "'] option").remove();

                    if(response.data.length > 0)
                    {
                        var defaultOption = $('<option></option>')
                            .val(that.options.nullValue)
                            .prop('disabled', that.options.placeholderDisabled);

                        if(that.options.showPlaceholderText) defaultOption.html(that.options.trans.selectA + wrapper.find("[name='" + that.options.countrySelect + "'] option:selected").data('at3'));

                        wrapper.find("[name='" + that.options.tA3Select + "']")
                            .append(defaultOption);

                        for(var i in response.data)
                        {
                            $("[name='" + that.options.tA3Select + "']").append(new Option(response.data[i].name, response.data[i].id));
                        }

                        // get value of territorialArea3 if it has
                        var territorialArea3Value = wrapper.find("[name='" + that.options.territorialArea3Value + "']").val();

                        // check if need set value from Territorial Area 3
                        if(territorialArea3Value !== null && territorialArea3Value !== '')
                        {
                            wrapper.find("[name='" + that.options.tA3Select + "']")
                                .val(that.options.territorialArea3Value)
                                .trigger("change");
                            // reset value to avoid load
                            wrapper.find("[name='" + that.options.territorialArea3Value + "']").val('');
                        }
                        else
                        {
                            // reset value territorialArea 3
                            wrapper.find("[name='" + that.options.tA3Select + "']")
                                .val(that.options.nullValue)
                                .trigger("change");
                        }

                        wrapper.find(that.options.tA3Wrapper).fadeIn();
                    }
                    else
                    {
                        wrapper.find(that.options.tA3Wrapper).fadeOut();
                        that.deleteTerritorialArea3(wrapper);
                    }

                    // trigger event
                    $(that).trigger('territories:afterLoadTerritorialAreas3', response);

                    var response = {
                        success: true,
                        action: 'territorialarea3-loaded',
                        message: 'TerritorialArea3 loaded'
                    };

                    if (typeof that.callback === 'function') that.callback(response);
                    if (typeof localCallback === 'function') localCallback(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    var response = {
                        success: false,
                        message: textStatus
                    };

                    if (typeof that.callback === 'function') that.callback(response);
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
    $.territories = function(options, callback) {
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