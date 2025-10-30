;
(function($) {
    /**
	 * filemodel set function.
	 */
    $.fn.filemodel = function(options) {
        return this.each(function() {
            var settings = $.extend({}, $.fn.filemodel.defaults, options || {});
            var $this = $(this);
            var id = $this.attr('id');
            var pjaxId = settings.pjaxId;
            var gridId = settings.gridId;
            var formId = settings.formId;
            var alertId = settings.alertId;
            
            $.fn.filemodel.settings[id] = settings;
            $this
			.on('beforeSubmit', formId, function(event) {
                $form = $(this);
                $(':submit', $form).button('loading');
                var url = $form.attr("action");
                var formData = new FormData($form[0]);
                var $bar = $('.progress-bar', $form);
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    xhr: function() {  // Custom XMLHttpRequest
                        var myXhr = $.ajaxSettings.xhr();
                        if(myXhr.upload){ // Check if upload property exists
                            myXhr.upload.addEventListener('progress', function(evt) {
                                if (evt.lengthComputable) {
                                    var progress = Math.round(evt.loaded * 100 / evt.total);
                                    var percentVal = progress + '%';
                                    $bar.css('width', percentVal).attr('aria-valuenow', progress).html(percentVal);
                                }
                            }, false); // For handling the progress of the upload
                        }
                        return myXhr;
                    },
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(data, textStatus, jqXHR) {
                        $(alertId).hide().removeClass(settings.alertSuccessClass + ' ' + settings.alertErrorClass + ' hidden');
                        if(typeof data.error === 'undefined') {
                            $(formId).toggleClass('hidden');
                            $(alertId).addClass(settings.alertSuccessClass).html(data.success).fadeIn();
                            setTimeout(function() {
                                $(alertId).fadeOut();
                            }, 3000);
                        } else {
                            $(alertId).addClass(settings.alertDangerClass).html(data.error).fadeIn();
                            setTimeout(function() {
                                $(alertId).fadeOut();
                            }, 3000);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $(alertId).hide().removeClass(settings.alertSuccessClass + ' ' + settings.alertErrorClass + ' hidden').addClass('alert-danger').html(errorThrown).fadeIn();
                        setTimeout(function() {
                            $(alertId).fadeOut();
                        }, 3000);
                    },
                    complete: function() {
                        $(':submit', $form).button('reset');
                        //For IE
                        $(":file", $form).replaceWith($(":file", $form).clone(true));
                        //For other browsers
                        $(":file", $form).val('');
//                        console.debug('#' + id + '-pjax');
                        $bar.css('width', 0).attr('aria-valuenow', 0).html('');
                        $.pjax.reload({container: pjaxId});
                    }
                });

                return false;
            })
//            .on('click', gridId + " a[data-action='delete']", function(event) {
//                var $this = $(this);
//                yii.confirm($(this).data('confirm'), function() {
////                    yii.handleAction($this, event);
//                    var url = $this.attr('href');
//                    $.post(url, function(data) {
//                        $.pjax.reload({container: pjaxId});
//                    }, 'json');
//                    return false;
//                });
//                return false;
//            });
        });
    };
        
    $.fn.filemodel.defaults = {
        alertSuccessClass: 'alert-success',
        alertErrorClass: 'alert-danger'
    };
        
    $.fn.filemodel.settings = {};
        
})(jQuery);