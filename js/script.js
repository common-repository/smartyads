jQuery(document).ready(function($) {

    $('#sync_btn').click(function() {
        
        var api_key = $.trim($('#sync_input').val());
        
        if (api_key != '') {

            $.get('http://smartyads.com/api/checkcode/code/' + api_key ,function(data, status) {
                if (status == 'success') {

                    var data = $.parseJSON(data);

                    if (typeof data !== 'undefined' && !$.isEmptyObject(data)) {
                        
                        data['api_key'] = api_key;
                        data['action'] = 'sync';
                        
                        $.ajax({
                           type: 'POST',
                           url: ajaxurl,
                           data: data,
                           success: function() {
                               window.location.reload();
                           }
                       });
                        
                        
                         $('.sync_input_wrap').removeClass('b-control_error');
                    }
                    else {
                        $('.sync_input_wrap').addClass('b-control_error');
                    }
                    

                    
                }
            });
               
        }
        else {

            var data = {
                type_operation: 'remove_key',
                action: 'sync'
            };



            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: data,
                success: function() {
                    window.location.reload();
                }
            });
            
            
        }
        
    });


    $('.sync_finish_button').click(function() {
        window.location.reload();
    });




});