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
                if(data.callback){
                    $.globalEval(data.callback);
                }
                if (data.replace) {
                    if (data.replace.data) {
                        $.each(data.replace.data, function (index, value) {
                            el.data(index, value);
                        });
                    }
                    if (!data.replace.data || (data.replace.data && !data.replace.data.alert)) {
                        el.removeData('alert');
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
})
(jQuery);
