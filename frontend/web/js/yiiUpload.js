;
(function ($) {
    jQuery.event.props.push('dataTransfer');
    $.fn.avatar = function (options) {
        var defaultOptions = {
            uploadingUrl: '#',
            croppingUrl: '#',
            picture: 'noAvatar.png',
            uploadModel: 'UploadModel',
            cropModel: 'CropModel',
            field: 'file'
        };

        var ps = {
            s: $(this),
            thumb: undefined,
            form: undefined,
            input: undefined,
            modal: undefined,
            progress: undefined,
            crop: undefined,
            prefix: undefined,
            file: undefined,
            errorSummary: undefined,
            isValid: true
        };
        var settings = $.extend({}, defaultOptions, options);
        var timer;
        var dEvents = {
            drop: function (event) {
                event.preventDefault();
                if (!event.dataTransfer) {
                    return;
                }
                ps.file = event.dataTransfer.files[0];
                if (validator.validate()) {
                    uploading();
                }
            },
            dragOver: function (event) {
                clearTimeout(timer);
                //todo
                return false;
            },
            dragLeave: function (event) {
                timer = setTimeout(function () {
                    //todo
                }, 200);
            }
        };

        var validator = {
            validate: function () {
                validator.clearError();
                if (ps.file == undefined) {
                    validator.showError('Please browse or drag an image from computer');
                    ps.isValid = false;
                    return false;
                }
                var maxSize = ps.s.data('max-size');
                if (maxSize != undefined && ps.file.size > maxSize) {
                    validator.showError('This file is too large (' + ps.file.size + 'byte), only allows (' + maxSize + 'byte)')
                    ps.isValid = false;
                    return false;
                }
                if (!ps.file.type.match('image.*')) {
                    validator.showError('This file is not image, only allows file type .jpg,.png,.gif ...')
                    ps.isValid = false;
                    return false;
                }
                return true;
            },
            showError: function (message) {
                if (ps.modal == undefined) {
                    initModal();
                }
                setModalTitle('The validation error');
                ps.modal.find('#' + ps.prefix + '-modal-cancel').text('Ok! Try agian');
                ps.modal.find('.modal-body').html($('<div/>').addClass('error').html(message));
                ps.modal.modal('show');
            },
            clearError: function () {
                if (ps.modal != undefined) {
                    setModalTitle('Initial modal');
                    ps.modal.find('#' + ps.prefix + '-modal-cancel').text('Cencal');
                    ps.modal.find('.modal-body').html('');
                }
            }

        };

        var cropping = function () {
            setModalTitle('Cropping Avatar');
            ps.modal.find('.modal-footer').append($('<button/>', {
                id: ps.prefix + '-modal-apply',
                'data-dismiss': "modal"
            }).addClass('btn btn-primary').text('Apply'));
            ps.crop.cropper({
                aspectRatio: 1
            });
            $('#' + ps.prefix + '-modal-apply').click(function (event) {
                event.preventDefault();
                var params = {};
                params[settings.cropModel] = {};
                $.each($.extend({'source': ps.crop.attr('src')}, ps.crop.cropper('getData')), function (index, val) {
                    params[settings.cropModel][index] = val;
                });
                $.post(settings.croppingUrl, params, function (data, textStatus, xhr) {
                    ps.thumb.attr('src', data.url);
                }, 'json');
            });
        };

        var uploading = function () {
            initModal();
            initProgressBar();
            //todo
            var data = new FormData();
            data.append(ps.input.attr('name'), ps.file);
            $.ajax({
                url: settings.uploadingUrl,
                processData: false,
                contentType: false,
                cache: false,
                type: 'POST',
                data: data,
                dataType: 'json',
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function (e) {
                        var percent = (e.loaded / e.total) * 100;
                        if ((percent % 10) == 0) {
                            ps.progress.find('.progress-bar').css('width', percent + '%').attr('aria-valuenow', percent).html(percent + '% Complete');
                        }
                    }, false);
                    return xhr;
                },
                beforeSend: function () {
                    setModalTitle('Uploading');
                    ps.modal.modal('show');
                },
                success: function (data, status, xhr) {
                    setModalTitle('Uploaded');
                    ps.progress.fadeOut(800);
                    initCropperWrapper(data.url);
                    cropping();
                },
                error: function (xhr, status, errorThrown) {
                    //todo modal alert error
                }
            });
            return true;
        };

        var setModalTitle = function (title) {
            ps.modal.find('.modal-title').html(title);
        };

        var initWrapper = function () {
            if (!ps.s.hasClass('avatar')) {
                ps.s.addClass('avatar');
            }
            ps.s.addClass('thumbnail');
            var $img = $('<img>', {
                id: ps.prefix + '_thumb',
                title: 'Click or Drag image here to change'
            }).attr('src', settings.picture);
            ps.thumb = $img;
            ps.s.append($img);
        };

        var initForm = function () {
            var $form = $('<form/>', {
                id: ps.prefix + '_form',
                action: settings.uploadingUrl,
                method: 'POST',
                enctype: 'multipart/form-data'
            });
            var $input = $('<input>', {
                name: settings.uploadModel + '[' + settings.field + ']',
                type: 'file',
                accept: 'image/*',
                required: 'required',
                style: 'display:none'
            });
            $form.append($input);
            $('body').append($form);
            ps.form = $form;
            ps.input = $input;
        };

        var initModal = function () {
            var $modal = $('<div/>', {
                id: ps.prefix + '_modal',
                role: 'dialog'
            }).addClass('modal face').append($('<div/>').addClass('modal-dialog')
                .append($('<div/>').addClass('modal-content')
                    .append($('<div/>').addClass('modal-header')
                        .append($('<h4>').addClass('modal-title').text('Initial modal')))
                    .append($('<div/>').addClass('modal-body'))
                    .append($('<div>').addClass('modal-footer')
                        .append($('<button/>', {
                            id: ps.prefix + '-modal-cancel',
                            'data-dismiss': "modal"
                        }).addClass('btn btn-default').text('Cancel')))));

            ps.modal = $modal;
            ps.s.append($modal);
        };

        var initProgressBar = function () {
            var $progress = $('<div/>', {
                id: ps.prefix + '_progress'
            }).addClass('progress').append($('<div/>', {
                role: 'progressbar',
                'aria-valuenow': 0,
                'aria-valuemax': 100,
                'aria-valuemin': 0,
                style: 'width: 0%'
            }).addClass('progress-bar').append($('<span/>').addClass('sr-only').text('0% Complete')));

            if (ps.progress == undefined) {
                ps.progress = $progress;
                ps.modal.find('.modal-body').append($progress);
            }
        };

        var initCropperWrapper = function (img) {
            var $cropContainer = $('<div/>').addClass('cropper-container');
            var $crop = $('<img>', {id: ps.prefix + '_cropper'}).attr('src', img);
            $cropContainer.append($crop);
            if (ps.modal != undefined) {
                ps.modal.find('.modal-body').append($cropContainer);
                ps.crop = $crop;
            }
        };

        var init = function () {
            if (ps.prefix == undefined) {
                ps.prefix = (ps.s.attr('id') != undefined) ? ps.s.attr('id') : 'avatar';
            }
            initWrapper();
            initForm();
            ps.thumb.click(function (event) {
                event.stopPropagation();
                ps.input.trigger('click');
            });
            ps.form.change(function (event) {
                ps.file = ps.input.get(0).files[0];
                if (validator.validate()) {
                    uploading();
                }
            });

            ps.s.on('drop', dEvents.drop).on('dragover', dEvents.dragOver).on('dragleave', dEvents.dragLeave);
            return ps.s;
        };
        return init();
    };
})(jQuery);