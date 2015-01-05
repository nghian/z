(function ($) {

    $.fn.avatar = function (options) {
        var o = $.extend({}, $.fn.avatar.defaults, options);
        var c = {
            viewer: $(this),
            dataX: $('#' + o.model.toLowerCase() + '-x'),
            dataY: $('#' + o.model.toLowerCase() + '-y'),
            dataWidth: $('#' + o.model.toLowerCase() + '-width'),
            dataHeight: $('#' + o.model.toLowerCase() + '-height'),
            form: $('#avatar-form'),
            inputFile: $('#' + o.model.toLowerCase() + '-' + o.attribute.toLowerCase()),
            modal: $('#avatar-modal'),
            wrapper: $('.avatar-wrapper'),
            cropper: undefined,
            preview: $('.avatar-preview'),
            cropping: false
        };

        var s = {
            file: undefined,
            fileReader: undefined,
            formData: undefined,
            isValid: undefined
        };
        var stopCropper = function () {
            if (c.cropper) {
                c.cropper.cropper('destroy');
            }
            c.wrapper.empty();
            c.preview.empty();
        };
        var startCropper = function () {
            s.file = c.inputFile.get(0).files[0];
            s.fileReader = new FileReader();
            s.fileReader.readAsDataURL(s.file);
            s.fileReader.onload = function () {
                c.cropper = $('<img>', {src: s.fileReader.result});
                c.wrapper.append(c.cropper);
                c.cropper.cropper({
                    aspectRatio: 1,
                    preview: c.preview.selector,
                    done: function (data) {
                        c.dataX.val(data.x);
                        c.dataY.val(data.y);
                        c.dataWidth.val(data.width);
                        c.dataHeight.val(data.height);
                    }
                });
            };
        };
        var submit = function () {
            var data = new FormData(c.form[0]);
            $.ajax({
                url: c.form.attr('action'),
                cache: false,
                data: data,
                method: 'POST',
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $('.avatar-loading').css('display', 'block');
                    c.modal.modal('hide');
                },
                success: function (data, xhr) {
                    console.log(data);
                    if (data.status) {
                        c.viewer.find('img').attr('src', data.url);
                        c.form.get(0).reset();
                    } else {
                        c.modal.modal('show');
                        c.inputFile.parents('.form-group').addClass('has-error');
                        c.inputFile.next().html(data.messages.toString());
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    alert('Failed to response');
                },
                complete: function () {
                    $('.avatar-loading').hide();
                }
            });
        };
        var init = function () {
            c.viewer.tooltip({
                trigger: 'hover focus',
                placement: 'bottom'
            });
            c.viewer.click(function (event) {
                event.preventDefault();
                stopCropper();
            });
            c.form.on('afterValidateAttribute', function (event, attribute, messages, deferred) {
                if (attribute.id == c.inputFile.attr('id') && messages.length == 0) {
                    event.preventDefault();
                    event.stopPropagation();
                    startCropper();
                } else {
                    stopCropper();
                }
            });

            c.form.on('beforeSubmit', function (event) {
                event.preventDefault();
                submit();
                return false;
            });
        };
        return init();
    };

    $.fn.avatar.defaults = {
        model: 'ChangeAvatar',
        attribute: 'file'
    };
})(jQuery);