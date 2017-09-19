/*-----------------------------------------------------------------------------------*/
/*	Custom Script
 /*-----------------------------------------------------------------------------------*/

jQuery.noConflict();
jQuery(document).ready(function () {
    jQuery(window).load(function () {

        jQuery('.page-container .page-content').fadeTo(300, 1);

        jQuery('#featured-list').fadeTo(300, 1);

        jQuery('#big-map').fadeTo(300, 1);

        jQuery('#featured-ads-author').fadeTo(300, 1);

        jQuery('#featured-ads-category').fadeTo(300, 1);

    });

    jQuery("#catID").change(function () {
        $val = jQuery("#catID").val();
        jQuery(this).parent().parent().parent().find(".wrap-content").css({"display": "none"});
        jQuery(this).parent().parent().parent().find("#cat-" + $val).css({"display": "block"});
    });

    jQuery("#projects-carousel .span3").each(function () {

        var $thisItem = jQuery(this);
        var $thisWidth = $thisItem.parents().parents().width();

        if ($thisWidth <= 714) {

            $thisItem.css("width", $thisWidth);

        } else {

            $thisItem.css("width", "");

        }

    });

    jQuery(".form-select").chosen();

    jQuery(window).bind('resize', function () {

        jQuery("#projects-carousel .span3").each(function () {

            var $thisItem = jQuery(this);
            var $thisWidth = $thisItem.parents().parents().width();

            if ($thisWidth <= 714) {

                $thisItem.css("width", $thisWidth);

            } else {

                $thisItem.css("width", "");

            }
        });
    });

    // Add Image
    jQuery('#template_image_criterion').hide();
    jQuery('#submit_add_image').on('click', function () {
        $newItem = jQuery('#template_image_criterion .option_item').clone().appendTo('#images_criteria').show();
        if ($newItem.prev('.option_item').size() == 1) {
            var id = parseInt($newItem.prev('.option_item').attr('id')) + 1;
        } else {
            var id = 0;
        }
        $newItem.attr('id', id);

        var nameText = 'listing_image_url[' + id + '][0]';
        $newItem.find('.listing_image_url').attr('id', nameText).attr('name', nameText);

        var nameText = 'listing_image_url[' + id + '][1]';
        $newItem.find('.listing_image_id').attr('id', nameText).attr('name', nameText);

        //event handler for newly created element
        $newItem.children('.button_del_image').on('click', function () {
            jQuery(this).parent('.option_item').remove();
        });

        jQuery('#listing_total_images').attr({value: id});

    });

    // Delete Ingredient
    jQuery('.button_del_image').on('click', function () {
        jQuery(this).parent('.option_item').remove();
    });




    jQuery('.remImage').live('click', function () {

        jQuery(this).parent().parent().fadeOut();
        jQuery(this).parent().find('input').attr('name', 'att_remove[]');

    });

    jQuery(document).ready(function () {
        jQuery(".target-blank").attr({"target": "_blank"});
    });

    jQuery(window).scroll(function () {
        if (jQuery(this).scrollTop() > 200) {
            jQuery('.backtop').fadeIn(200);
        } else {
            jQuery('.backtop').fadeOut(200);
        }
    });

    // scroll body to 0px on click
    jQuery(".backtop a").click(function () {
        jQuery("body,html").animate({
            scrollTop: 0
        }, 800);
        return false;
    });

    jQuery('#tag-index-page').isotope({
        itemSelector: '.tag-group',
        layoutMode: 'masonry'
    });

    //Toggle
    jQuery(".togglebox").hide();
    //slide up and down when click over heading 2

    jQuery("h4").click(function () {

        // slide toggle effect set to slow you can set it to fast too.
        jQuery(this).toggleClass("active").next(".togglebox").slideToggle("slow");

        return true;

    });

    jQuery(function () {
        // setup ul.tabs to work as tabs for each div directly under div.panes
        jQuery("ul.custom-tabs").tabs("div.custom-panes > div");
    });

    if (jQuery('#galleria').length) {
        // Initialize Galleria
        Galleria.loadTheme('/assets/compiled/layout_bodу.js');
        Galleria.configure({
            transition: 'fade',
            imageCrop: true,
            //imagePosition: 'left top',
            imagePosition: 'center center',
            imagePan: true,
            thumbCrop: false,
            fullscreenCrop: false

            //transition: 'fade',
            //imageCrop: true,
            //imagePosition: 'left top',
            //maxScaleRatio: 1,
            //imagePan: true,
            //thumbCrop: false,
            //fullscreenCrop: false

            //imageCrop: false,
            //maxScaleRatio: 1,
            //imagePan: true,
            //thumbCrop: false,
            //fullscreenCrop: true
        });
        Galleria.run('#galleria');

        Galleria.ready(function() {
            var gallery = this;
            this.addElement('fscr');
            this.appendChild('stage','fscr');
            var fscr = this.$('fscr')
                .click(function() {
                    gallery.toggleFullscreen();
                });
        });
    }

    jQuery('.show-long-number-button').click(function () {

        jQuery(".show-long-number-button").css("display", "none");
        jQuery(".short-phone-number").css("display", "none");
        jQuery(".long-phone-number").css("display", "block");

    });

    jQuery('.subscribe').click(function () {
        if (jQuery('.subscribe-input').val()) {
            var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
            if (!emailReg.test(jQuery('.subscribe-input').val())) {
                jQuery('#subscribe-message').html("Введите корректный Email");
            } else {
                var url = jQuery(this).data('link-subscribe');
                jQuery.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        'email': jQuery('.subscribe-input').val()
                    },
                    success: function (data) {
                        if (data.success) {
                            var mail = jQuery('.subscribe-input').val();
                            jQuery('#subscribe-message').css('color', 'green').html('Email ' + mail + ' добавлен');
                            jQuery('.subscribe-input').remove();
                        } else {
                            jQuery('#subscribe-message').html('Такой email уже существует');
                        }
                    }
                });
            }
        } else {
            jQuery('#subscribe-message').html('Адрес электронной почты не может быть пустым!');
        }
    });

    jQuery(document).on("click", "a.feature-add", function (event, ui) {
        var owner = this;
        var url = this.href;
        var id = jQuery(this).data('item');
        var newurl = jQuery(this).data('perform-url');
        var label = jQuery('#label-favorite_'+ id);
        var favColor = label.attr('data-fav-color');
        var unfavColor = label.attr('data-infav-color');
        jQuery.ajax({
            url: url,
            data: {
                'id': id
            },
            success: function (result) {                
                jQuery('#fetured_count').html(result.count);
                jQuery(owner).data('perform-url', jQuery(owner).attr('href'));
                jQuery(owner).attr('href', newurl);
                if (label.css('color') == unfavColor) {
                    color = favColor;
                }
                if (label.css('color') == favColor) {
                    color = unfavColor;
                }
                label.css('color', color);
            },
            error: function (error) {
                alert('Ошибка сохранения. ' + error + ' Попробуйте позже.');
            }
        });
        return false;
    });

    if (jQuery('#views-exposed-form-search-view-other-ads-page').length > 0) {

        if (jQuery('#search-field-road').val() > 0) {
            loadItems(jQuery('#search-field-locatity'), jQuery('#search-field-road').val());
        }

        var select = jQuery('#search-field-locatity');
        select.empty();
        select.append('<option value="" selected="selected">Наленный пункт</option><option value="">Выберите сперва шоссе</option>')
                .trigger("chosen:updated");

        jQuery('#search-field-road').change(function () {
            loadItems(select, jQuery(this).val());
            searchEvent(true);
        });

        jQuery('#search-field-village').change(function () {
            searchEvent(true);
        });

        jQuery('#search-field-locatity').change(function () {
            searchEvent(true);
        });

        jQuery('#search-field-type').change(function () {
            searchEvent(true);
        });

        jQuery('#search-field-dealtype').change(function () {
            searchEvent(true);
        });

        jQuery('#search-field-interval').change(function () {
            searchEvent(true);
        });

        jQuery('#geo-radius').change(function () {
            searchEvent(true);
        });

        jQuery('#isSecurity').change(function () {
            searchEvent(true);
        });

        jQuery('#isHasPool').change(function () {
            searchEvent(true);
        });

        jQuery('#isFull').change(function () {
            searchEvent(true);
        });

        jQuery('#isUnderFinish').change(function () {
            searchEvent(true);
        });

        jQuery('#isFurnished').change(function () {
            searchEvent(true);
        });

        jQuery('#isUndeveloped').change(function () {
            searchEvent(true);
        });

        jQuery('#max_area')
            .on('chosen:hiding_dropdown', function() {
                searchEvent(true);
            })
            .change(function () {
                searchEvent(true);
            });

        jQuery('#min_area')
            .on('chosen:hiding_dropdown', function() {
                searchEvent(true);
            })
            .change(function () {
                searchEvent(true);
            });

        jQuery('#min_house')
            .on('chosen:hiding_dropdown', function() {
                searchEvent(true);
            })
            .change(function () {
                searchEvent(true);
            });

        jQuery('#max_house')
            .on('chosen:hiding_dropdown', function() {
                searchEvent(true);
            })
            .change(function () {
                searchEvent(true);
            });

        jQuery('#price_min')
            .on('chosen:hiding_dropdown', function() {
                searchEvent(true);
            })
            .change(function () {
                searchEvent(true);
            });

        jQuery('#price_max')
            .on('chosen:hiding_dropdown', function() {
                searchEvent(true);
            })
            .change(function () {
                searchEvent(true);
            });
    }

    jQuery("#contact-form").submit(function (event) {
        event.preventDefault();
        jQuery.post(jQuery(this).attr("action"), jQuery("#contact-form").serialize() + "&ajaxMode=1", function (data, textStatus) {
            if (textStatus == 'success') {
                if (data.message) {
                    jQuery('#contact-message').html(data.message)
                            .css('color', 'green')
                            .css('padding-bottom', 10)
                            .css('font-size', 14);
                    jQuery("#contact-form input").remove();
                    jQuery("#contact_submit").remove();
                } else {
                    jQuery('#contact-message').html('Ошибка отправки почты. Попробуйте позже.')
                            .css('color', 'red')
                            .css('padding-bottom', 10)
                            .css('font-size', 14);
                }
            } else {
                jQuery('#contact-message').html('Ошибка отправки почты. Попробуйте позже.')
                        .css('color', 'red')
                        .css('padding-bottom', 10)
                        .css('font-size', 14);
            }
        });
    });

    jQuery("#frend-form").submit(function (event) {
        event.preventDefault();
        jQuery.post(jQuery(this).attr("action"), jQuery("#frend-form").serialize() + "&ajaxMode=2", function (data, textStatus) {
            if (textStatus == 'success') {
                if (data.message) {
                    jQuery('#frend-message').html(data.message)
                            .css('color', 'green')
                            .css('padding-bottom', 10)
                            .css('font-size', 14);
                    jQuery("#frend-form input#frend_email, textarea#frend_body").val('');                    
                } else {
                    jQuery('#frend-message').html(data.error)
                            .css('color', 'red')
                            .css('padding-bottom', 10)
                            .css('font-size', 14);
                }
            } else {
                jQuery('#frend-message').html('Ошибка отправки почты. Попробуйте позже.')
                        .css('color', 'red')
                        .css('padding-bottom', 10)
                        .css('font-size', 14);
            }
        });
    });
    
    jQuery("#frend_button").on('click', function(e){
        jQuery('#frend-message').empty();
    });
});

searchEvent = function (page) {
    var form = jQuery('#views-exposed-form-search-view-other-ads-page');
    jQuery.post(form.data('ajax-serach-action'), form.serialize()).done(function (data) {
        if (page) {
            jQuery('#list-objects-insert').html(data.page);
        }
        loadSearchMap(data);
    });
};

loadSearchMap = function (data) {
    var mapDiv, map, infobox;
    mapDiv = jQuery("#flatads-main-map");
    mapDiv.height(500).gmap3({
        clear: {},
        map: {
            options: {
                "draggable": true
                , "mapTypeControl": true
                , "mapTypeId": google.maps.MapTypeId.ROADMAP
                , "scrollwheel": true
                , "panControl": true
                , "rotateControl": false
                , "scaleControl": true
                , "streetViewControl": true
                , "zoomControl": true
                , "maxZoom": 14
                , "center": [55.74114, 36.897034]
            }
        },
        marker: {
            values: data.map,
            options: {
                draggable: false
            },
            cluster: {
                radius: 20,
                // This style will be used for clusters with more than 0 markers
                5: {
                    content: "<div class='cluster cluster-1'>CLUSTER_COUNT</div>",
                    width: 62,
                    height: 62
                },
                // This style will be used for clusters with more than 20 markers
                20: {
                    content: "<div class='cluster cluster-2'>CLUSTER_COUNT</div>",
                    width: 82,
                    height: 82
                },
                // This style will be used for clusters with more than 50 markers
                50: {
                    content: "<div class='cluster cluster-3'>CLUSTER_COUNT</div>",
                    width: 102,
                    height: 102
                },
                events: {
                    click: function (cluster) {
                        map.panTo(cluster.main.getPosition());
                        map.setZoom(map.getZoom() + 2);
                        map.setOptions({"maxZoom": false});
                    }
                }
            },
            events: {
                click: function (marker, event, context) {
                    map.panTo(marker.getPosition());
                    map.setOptions({"maxZoom": false});
                    var ibOptions = {
                        pixelOffset: new google.maps.Size(-125, -88),
                        alignBottom: true
                    };
                    infobox.setOptions(ibOptions);

                    infobox.setContent(context.data);
                    infobox.open(map, marker);
                    // if map is small
                    var iWidth = 260;
                    var iHeight = 300;
                    if ((mapDiv.width() / 2) < iWidth) {
                        var offsetX = iWidth - (mapDiv.width() / 2);
                        map.panBy(offsetX, 0);
                    }
                    if ((mapDiv.height() / 2) < iHeight) {
                        var offsetY = -(iHeight - (mapDiv.height() / 2));
                        map.panBy(0, offsetY);
                    }

                }
            }
        }
    }, "autofit");


    map = mapDiv.gmap3("get");
    infobox = new InfoBox({
        pixelOffset: new google.maps.Size(-50, -65),
        closeBoxURL: '',
        enableEventPropagation: true
    });
    mapDiv.delegate('.infoBox .close', 'click', function () {
        infobox.close();
    });

    mapDiv.delegate('.infoBox .close', 'zoom_change', function () {
        infobox.close();
    });

    map.addListener('zoom_changed', function () {
        infobox.close();
    });
    if (Modernizr.touch) {
        map.setOptions({draggable: false});
        var draggableClass = 'inactive';
        var draggableTitle = "Activate map";
        var draggableButton = $('<div class="draggable-toggle-button ' + draggableClass + '">' + draggableTitle + '</div>')
                .appendTo(mapDiv);
        draggableButton.click(function () {
            if ($(this).hasClass('active')) {
                $(this).removeClass('active').addClass('inactive').text("Activate map");
                map.setOptions({draggable: false});
            } else {
                $(this).removeClass('inactive').addClass('active').text("Deactivate map");
                map.setOptions({draggable: true});
            }
        });
    }
};

loadItems = function (select, id) {
    var url = select.data('ajax-link');
    jQuery.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: {
            'id': id
        },
        beforeSend: function (xhr) {
            select.empty();
            select.append('<img id="preloader" src="/bundles/sipresource/images/classic-loader.gif" />');
        },
        success: function (data) {
            select.empty();
            jQuery('#preloader').remove();
            if (data.length > 0) {
                select.append('<option value="">Наленный пункт</option>');
                var active = select.data('request-id');
                jQuery.each(data, function (index, value) {
                    if (active == value.id) {
                        select.append('<option value="' + value.id + '" selected="selected">' + value.name + '</option>');
                    } else {
                        select.append('<option value="' + value.id + '">' + value.name + '</option>');
                    }
                });
            } else {
                select.append('<option value="">Нет поселков</option>');
            }
            select.trigger("chosen:updated");
        },
        error: function (text) {
            jQuery('#preloader').remove();
        }
    });
};
