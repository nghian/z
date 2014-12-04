jQuery("[data-toggle='popover']").popover({
    trigger: "manual",
    html: true,
    animation: false
}).on("mouseenter", function () {
    var that = this;
    jQuery(this).popover("show");
    jQuery(".popover").on("mouseleave", function () {
        jQuery(that).popover("hide");
    });
}).on("mouseleave", function () {
    var that = this;
    setTimeout(function () {
        if (!jQuery(".popover:hover").length) {
            jQuery(that).popover("hide");
        }
    }, 300);
});
jQuery("[data-toggle='tooltip']").tooltip();
jQuery("button[data-toggle='button-ajax']").click(function () {

    var that = this;
    if (jQuery(this).attr('data-confirm')) {
        if (!confirm(jQuery(this).attr('data-confirm'))) {
            return false;
        }
        jQuery(this).removeAttr('data-confirm');
    }
    jQuery.ajax({
        url: '/' + jQuery(this).attr('data-controller') + '/' + jQuery(this).attr('data-action'),
        method: 'POST',
        data: yii.getCsrfParam() + '=' + yii.getCsrfToken() + '&userId=' + jQuery(this).data('user-id'),
        dataType: 'json',
        beforeSend: function () {
            jQuery(that).addClass('disabled');
        },
        success: function (result) {
            if (result.status) {
                var label = (result.options.labelIcon != undefined) ? '<span class="glyphicon glyphicon-' + result.options.labelIcon + '"></span> ' : '';
                if (result.options.label != undefined) {
                    label += result.options.label;
                    jQuery(that).html(label);
                }
                if (result.options.action != undefined) {
                    jQuery(that).attr('data-action', result.options.action);
                }
                if (result.options.confirm != undefined) {
                    jQuery(that).attr('data-confirm', result.options.confirm);
                }
                if (result.options.color != undefined) {
                    jQuery(that).removeClass('btn-default btn-success btn-primary btn-info btn-warning btn-danger btn-link').addClass('btn-' + result.options.color);
                }
            } else {
                alert(result.message);
            }
            jQuery(that).removeClass('disabled');
        }
    });
    yii.refreshCsrfToken();
    return false;
});

(function ($) {
    $.fn.autoUpload = function (options) {
        var form = this;
        var doUpload = false;
        $(form).on('afterValidate', function (event, attribute, messages) {
            console.log(messages);
            return false;
        });
        var progressbar = function (wrapper, percent) {
            wrapper.show();
            var pgb = wrapper.find('.progress-bar');
            pgb.css('width', percent + '%').attr('aria-valuenow', percent).html(percent + '% complete');
            if (percent == 100) {
                pgb.html('complete');
            } else {
                pgb.html('uploading ' + percent + '%');
            }
        };

    };//end plugin
})(window.jQuery);

$('#cropper-apply').click(function (e) {
    console.log($('#cropper-img').cropper("getDataURL"));
});

$('#upload-file-form').on('afterValidate', function (event, messages) {
    var form = this;
    if (!$(form).find('.form-group').hasClass('has-error')) {
        var file = $(form).find('input:file');
        file.hide();
        var data = new FormData();
        data.append(file.attr('name'), file.get(0).files[0]);
        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: data,
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function (e) {
                    var percent = (e.loaded / e.total) * 100;
                    if ((percent % 10) == 0) {
                        progressbar($('.progress'), percent, file.get(0).files[0]);
                    }
                }, false);
                return xhr;
            },

            success: function (result, textStatus, jqXHR) {
                if (result.status) {
                    $('#cropper').show();
                    $(form).find('.img-preview').show();
                    $('#cropper').cropper('replace', result.files.url);
                    $(form).find('.img-preview').show();
                    $(form).find('.panel-footer').show();
                }
            }
        });
        event.preventDefault();
    }
}).on('submit', function () {
    return false;
});
var progressbar = function (wrapper, percent) {
    wrapper.show();
    var pgb = wrapper.find('.progress-bar');
    pgb.css('width', percent + '%').attr('aria-valuenow', percent);
    if (percent < 100) {
        pgb.html('Uploading: ' + percent + '%');
    } else {
        pgb.html('Upload finished');
    }

};
$("[type='reset']").click(function () {
    location.reload();
});
$("#cropper-apply").click(function () {
    console.log($('#cropper').cropper("getDataURL"));
    return false;
});
$('#uploadform-file').change(function () {
    $('#upload-file-form').trigger('submit');
});