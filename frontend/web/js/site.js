bs = (function ($) {
    var init = {
        confirm: function (message, callback) {
            if (message && callback) {
                var modal = $('<div/>', {'id': 'bs-comfirm'}).addClass('modal fade')
                    .append($('<div/>').addClass('modal-dialog')
                        .append($('<div/>').addClass('modal-content')
                            .append($('<div/>').addClass('modal-body').append(message))
                            .append($('<div/>').addClass('modal-footer')
                                .append($('<button/>', {'data-dismiss': "modal"}).addClass('btn btn-default').text('Cancel'))
                                .append($('<button/>', {
                                    'id': "bs-confirm-apply",
                                    'data-dismiss': "modal"
                                }).addClass('btn btn-primary').text('Ok')))
                    ));
                $('body').append(modal.modal('show'));
                $('#bs-confirm-apply').click(function (e) {
                    callback();
                    e.preventDefault();
                });
                modal.on('hidden.bs.modal', function () {
                    $(this).remove();
                });
            }
        },
        alert: function (message, type) {
            var alert = $('<div/>', {role: 'alert'}).addClass('alert');
            if (type) {
                alert.addClass('alert-' + type);
            }
            alert.append($('<buttom/>', {
                'data-dismiss': "alert",
                'aria-label': "Close"
            }).addClass('close').html('&times;'));
            alert.append(message);
            $('.wrapper > .container').prepend(alert);
            $('html,body').scrollTop(alert.offset().top, 1000);
        }
    };
    return init;
})(jQuery);
(function ($) {
    $("[data-toggle='popover']").popover({
        trigger: "manual",
        html: true,
        animation: false
    }).on("mouseenter", function () {
        var that = this;
        $(this).popover("show");
        $(".popover").on("mouseleave", function () {
            $(that).popover("hide");
        });
    }).on("mouseleave", function () {
        var that = this;
        setTimeout(function () {
            if (!$(".popover:hover").length) {
                $(that).popover("hide");
            }
        }, 300);
    });
    $("[data-toggle='tooltip']").tooltip();
    $('[data-toggle="ajax"]').click(function (e) {
        var el = jQuery(this);
        var ajaxOptions = $.extend({}, el.data());
        if (!ajaxOptions.data) {
            ajaxOptions.data = {};
        }
        ajaxOptions.data[yii.getCsrfParam()] = yii.getCsrfToken();
        if (!ajaxOptions.url) {
            ajaxOptions.url = el.attr('href');
        }
        if (el.attr('type') == 'submit') {
            ajaxOptions.data = el.closest('form').serialize();
            ajaxOptions.url = el.closest('form').attr('action');
            ajaxOptions.type = el.closest('form').attr('method');
        }
        ajaxOptions.beforeSend = function (xhr) {
            el.find('[class^="psi-"]').addClass('psi-spin psi-spinner9');
        };

        ajaxOptions.complete = function (xhr, statusText) {
            el.find('[class^="psi-"]').removeClass('psi-spin psi-spinner9');
        };
        ajaxOptions.error = function () {
            bs.alert('Unable to load', 'danger');
        };
        ajaxOptions.success = function (data, statusText, xhr) {
            if (data.status) {
                if (data.callback) {
                    $.globalEval(data.callback);
                }
                if (data.replace) {
                    el.removeData('alert');
                    el.removeAttr('data-alert');
                    if (data.replace.data) {
                        $.each(data.replace.data, function (index, value) {
                            el.data(index, value);
                        });
                    }
                    if (data.replace.attribute) {
                        $.each(data.replace.attribute, function (index, value) {
                            el.attr(index, value);
                        });
                    }
                    if (data.replace.html) {
                        el.empty().html(data.replace.html);
                    }
                }
                if (data.target && data.targetHtml) {
                    $(data.target).empty().html(data.targetHtml);
                    $('body').scrollTop($(data.target).offset().top);
                }
                if (data.refresh) {
                    $(docment).refresh();
                }
                if (data.redirect) {
                    $(window).location.href = data.redirect;
                }
            } else {
                if (data.alert && data.alert.message) {
                    bs.alert(data.alert.message, data.alert.type);
                }
            }
        };
        if (el.data('alert')) {
            bs.confirm(el.data('alert'), function () {
                $.ajax(ajaxOptions);
            });
        } else {
            $.ajax(ajaxOptions);
        }
        e.preventDefault();
    });
    $('button[data-toggle="comment-edit"]').click(function (e) {
        $(this).hide();
        var key = $(this).data('key');
        var target = $('.comment-detail-body[data-key=' + key + ']');
        target.after($('<div/>', {
            'data-key': key,
            'style': 'display:none'
        }).addClass('comment-body-history').html(target.html()));
        target.redactor({
            iframe: true,
            initCallback: function () {
                this.selection.restore();
                $('.comment-detail-tools[data-key=' + key + ']').show();
            }
        });
    });
    $('button[data-toggle="comment-update"]').click(function (e) {
        var key = $(this).data('key');
        var target = $('.comment-detail-body[data-key=' + key + ']');
        var postField = {};
        postField['ArticleComment[body]'] = target.redactor('code.get');
        postField[yii.getCsrfParam()] = yii.getCsrfToken();
        $.ajax({
            url: '/article/comment-update?id=' + key,
            type: 'POST',
            data: postField,
            dataType: 'json',
            cache: false,
            success: function (json) {
                if (!json.status) {
                    bs.alert(json.message, 'warning');
                } else {
                    target.redactor('core.destroy');
                    $('.comment-detail-tools[data-key=' + key + ']').hide();
                    $('button[data-key=' + key + ']').show();
                }
            },
            error: function (code, message) {
                bs.alert('Unable to update this comment', 'danger');
            }
        });
    });
    $('button[data-toggle="comment-cancel"]').click(function (e) {
        var key = $(this).data('key');
        var target = $('.comment-detail-body[data-key=' + key + ']');
        target.redactor('core.destroy');
        target.html($('.comment-body-history[data-key=' + key + ']').html());
        $('.comment-detail-tools[data-key=' + key + ']').hide();
        $('button[data-key=' + key + ']').show();
    });
    $('button[data-toggle="comment-delete"]').click(function (e) {

    });
})
(jQuery);
