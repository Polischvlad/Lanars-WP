var ajaxgo = false; 
jQuery(document).ready(function () {
    var userform = jQuery('.userform');
    function req_go(data, form, options) {
        if (ajaxgo) { 
            form.find('.response').html('<p class="error">Необходимо дождаться ответа от предыдущего запроса.</p>'); 
            // для ответов 
            return false; 
        }
        form.find('input[type="submit"]').attr('disabled', 'disabled').val('Подождите..');
        form.find('.response').html('');
        ajaxgo = true;
    }

    function req_come(data, statusText, xhr, form) {
        console.log(arguments); 
        if (data.success) { 
            var response = '<p class="success">' + data.data.message + '</p>'; 
            form.find('input[type="submit"]').val('Готово'); 
        } else { 
            var response = '<p class="error">' + data.data.message + '</p>'; 
            form.find('input[type="submit"]').prop('disabled', false).val('Отправить'); 
        }
        form.find('.response').html(response);
        if (data.data.redirect) window.location.href = data.data.redirect; 
        ajaxgo = false;
    }

    var args = {
        dataType: 'json',
        beforeSubmit: req_go,
        success: req_come,
        error: function (data) {
            console.log(arguments);
        },
        url: ajax_var.url 
    };
    userform.ajaxForm(args);

    jQuery('.logout').click(function (e) {
        e.preventDefault(); 
        if (ajaxgo) return false; 
        var lnk = jQuery(this);
        jQuery.ajax({
            type: 'POST', 
            url: ajax_var.url, // куда 
            dataType: 'json', 
            data: 'action=logout_me&nonce=' + jQuery(this).data('nonce'), 
            beforeSend: function (data) { 
                lnk.text('Подождите...'); 
                ajaxgo = true;
            },
            success: function (data) {
                if (data.success) { 
                    lnk.text('Выходим...');
                    window.location.reload(true); 
                } else { 
                    alert(data.data.message);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(arguments);
            },
            complete: function (data) { // при любом исходе
                ajaxgo = false; // аякс не выполняется
            }
        });
    });

});