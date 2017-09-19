var AdminExtend = {
    add_filters: function (subject) {
        jQuery('h4.filter_legend', subject).click(function (event) {
            jQuery('div.filter_container').toggle();
        });

        jQuery(subject).ready(function () {
            $localityId = $('select.locality-change-select').val();
            if ($localityId) {
                $container = $('input.locality-change-select-village');
                $id = $($container).attr('id');
                var $field = $('#' + $id);
                var $autocompleter = $('#autocompleter_' + $id);
                var $configs = {
                    focus: function (event, ui) {
                        return false;
                    },
                    select: function (event, ui) {
                        this.value = ui.item.label;
                        terms = ui.item;
                        $field.val(JSON.stringify(terms));

                        return false;
                    }
                };

                $configs.source = function (request, response) {
                    $.getJSON('/ajax/village', {
                        term: request.term,
                        locality: $localityId
                    }, response);
                };
                $autocompleter.autocomplete($configs);
            }

            jQuery('select.local-select-admin').change(function (e) {
                var id = jQuery(this).val();
                jQuery('div.vill-select-admin a span.select2-chosen').html('');
                jQuery.getJSON('/ajax/village', {
                    noname: 1,
                    locality: id
                }, function (data) {
                    var vill = jQuery('select.vill-select-admin');
                    vill.empty();
                    if (data.length > 0) {                        
                        jQuery.each(data, function (index, item) {
                            vill.append('<option value="'+ item.value +'">'+ item.label +'</option>');
                        });                        
                    } else {
                        vill.append('<option value="">В населенном пункте нет поселков</option>');
                    }
                });

            });
            
            if(jQuery('select.local-select-admin').length > 0){
                var id = jQuery('select.local-select-admin').val();
                var vill = jQuery('select.vill-select-admin');
                vill.empty();
                if(id > 0){                    
                    $.getJSON('/ajax/village', {
                    noname: 1,
                    locality: id
                    }, function (data) {
                        if (data.length > 0) {                        
                            jQuery.each(data, function (index, item) {
                                vill.append('<option value="'+ item.value +'">'+ item.label +'</option>');
                            });                        
                        } else {
                            vill.append('<option value="">В населенном пункте нет поселков</option>');
                        }
                    });
                }else{
                    vill.append('<option value="" selected="selected">Выберите сперва населенный пункт</option>');
                }
            }

        });

        jQuery('select.locality-change-select', subject).click(function (event) {
            $container = $('input.locality-change-select-village');
            $container.addClass('hide');
            $container.val('');
            $id = $($container).attr('id');
            var $field = $('#' + $id);
            var $autocompleter = $('#autocompleter_' + $id);
            var $configs = {
                focus: function (event, ui) {
                    return false;
                },
                select: function (event, ui) {
                    this.value = ui.item.label;
                    terms = ui.item;
                    $field.val(JSON.stringify(terms));

                    return false;
                }
            };

            $localityId = $(this).val();
            $configs.source = function (request, response) {
                $.getJSON('/ajax/village', {
                    term: request.term,
                    locality: $localityId
                }, response);
            };

            $container.removeClass('hide');
            $autocompleter.autocomplete($configs);
        });
    }
};

$.extend(Admin, AdminExtend);