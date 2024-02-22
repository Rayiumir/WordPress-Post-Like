jQuery(document).ready(function($){

    $('.LikePost').click(function(){

        $.ajax({
            url : rayium.ajax_url,
            type : 'POST',
            timeout : 1000,
            data : {
                action : 'rayium_like',
                post_id : $(this).data('id'),
                like : ! $(this).hasClass('LikePosted')
            },

            success: function( result ){
                if( result.success ){
                    console.log('success');
                }else{
                    console.log('not success');
                }

            },
            error: function( xhr, status, http_error_description ){
                console.log(xhr.responseJSON);
                console.log(status);
                console.log(http_error_description);
            },

        });

    });

});