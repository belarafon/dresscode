jQuery(document).ready(function ($) {
    "use strict";

    $.add_new_range = function () {
        var range_filter = $('#widgets-right').find('.range-filter'),
            input_field = range_filter.find('input:last-child'),
            field_name = range_filter.data('field_name'),
            position = parseInt(input_field.data('position')) + 1,
            html = '<input type="text" placeholder="min" name="' + field_name + '[' + position + '][min]" value="" class="yith-wcan-price-filter-input widefat" data-position="' + position + '"/>' +
                '<input type="text" placeholder="max" name="' + field_name + '[' + position + '][max]" value="" class="yith-wcan-price-filter-input widefat" data-position="' + position + '"/>';

        range_filter.append(html);
    };

    var nexio_toolkit_file_frame = null;  // variable for the wp.media nexio_toolkit_file_frame
    function nexio_toolkit_open_media_uploader() {
        // attach a click event (or whatever you want) to some element on your page
        $(document).on('click', '.nexio_toolkit-upload-wrap .nexio_toolkit-upload-btn', function (e) {

            var $this = $(this);
            var thisWrap = $this.closest('.nexio_toolkit-upload-wrap');
            var multi = thisWrap.attr('data-multi') == 'yes';
            var results_selector = $this.attr('data-results_selector');

            // if the nexio_toolkit_file_frame has already been created, just reuse it
            if (nexio_toolkit_file_frame) {
                nexio_toolkit_file_frame.open();
                return;
            }

            nexio_toolkit_file_frame = wp.media.frames.file_frame = wp.media({
                title: $(this).attr('data-uploader_title'),
                button: {
                    text: $(this).attr('data-uploader_button_text')
                },
                library: {
                    type: 'image'
                    // uploadedTo: wp.media.view.settings.post.id
                },
                multiple: multi // set this to true for multiple file selection
            });

            nexio_toolkit_file_frame.on('select', function () {
                var selection_imgs = nexio_toolkit_file_frame.state().get('selection').toJSON();
                var remove_img_btn_html = '<a href="#" class="nexio_toolkit-remove-img-btn nexio_toolkit-remove-btn"><i class="fa fa-times"></i></a>';

                if (!thisWrap.find('.nexio_toolkit-imgs-preview-wrap').length) {
                    thisWrap.prepend('<div class="nexio_toolkit-imgs-preview-wrap nexio_toolkit-sortable"></div>');
                }

                var attachment_ids = '';
                for (var i = 0; i < selection_imgs.length; i++) {
                    var attachment = selection_imgs[i];

                    var img_full = {};
                    var img_url_full = attachment['url'];
                    var img_url = img_url_full;
                    var width = attachment['width'];
                    var height = '';

                    if (typeof attachment['sizes']['thumbnail'] != 'undefined' && typeof attachment['sizes']['thumbnail'] != false) {
                        img_url = attachment['sizes']['thumbnail']['url'];
                        width = attachment['sizes']['thumbnail']['width'];
                        height = attachment['sizes']['thumbnail']['height'];
                    }
                    else {

                    }

                    if ($(results_selector).length) {
                        if (attachment_ids == '') {
                            attachment_ids = attachment['id'];
                        }
                        else {
                            attachment_ids += ',' + attachment['id'];
                        }
                    }

                    if (!thisWrap.find('.nexio_toolkit-img-preview-' + attachment['id']).length) {
                        if (typeof attachment['sizes']['full'] != 'undefined' && typeof attachment['sizes']['full'] != false) {
                            img_full = attachment['sizes']['full'];
                        }
                        else {
                            img_full = {
                                url: img_url_full,
                                height: '',
                                width: ''
                            }
                        }
                        img_full = JSON.stringify(img_full);
                        if (multi) {
                            thisWrap.find('.nexio_toolkit-imgs-preview-wrap').append('<div class="nexio_toolkit-img-preview-wrap">' +
                                '<img width="' + width + '" height="' + height + '" data-attachment_id="' + attachment['id'] + '" data-img_full="' + encodeURIComponent(img_full) + '" class="nexio_toolkit-img-preview nexio_toolkit-img-preview-' + attachment['id'] + '" src="' + img_url + '" /> ' + remove_img_btn_html + '</div>');
                        }
                        else {
                            thisWrap.find('.nexio_toolkit-imgs-preview-wrap').html('<div class="nexio_toolkit-img-preview-wrap">' +
                                '<img width="' + width + '" height="' + height + '" data-attachment_id="' + attachment['id'] + '" data-img_full="' + encodeURIComponent(img_full) + '" class="nexio_toolkit-img-preview nexio_toolkit-img-preview-' + attachment['id'] + '" src="' + img_url + '" /> ' + remove_img_btn_html + '</div>');
                        }
                    }
                    else {

                    }

                    if ($(results_selector).length) {
                        $(results_selector).val(attachment_ids);
                    }

                }
                // nexio_toolkit_update_main_img_preview(thisWrap);
            });

            nexio_toolkit_file_frame.open();

            e.preventDefault();
        });

        // Remove preview image
        $(document).on('click', '.nexio_toolkit-img-preview-wrap .nexio_toolkit-remove-img-btn', function (e) {
            var $this = $(this);
            var thisImgWrap = $this.closest('.nexio_toolkit-img-preview-wrap');
            var thisUploadWrap = $this.closest('.nexio_toolkit-upload-wrap');
            var results_selector = thisUploadWrap.find('.nexio_toolkit-upload-btn').attr('data-results_selector');
            thisImgWrap.remove();
            if ($(results_selector).length) {
                $(results_selector).val('');
            }

            e.preventDefault();
        });
    }

    nexio_toolkit_open_media_uploader();

});
