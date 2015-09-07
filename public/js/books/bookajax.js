function resetpassword(myself, uid) {
    $.ajax({
        url: '/admin/users/resetpassword/'+uid,
        data:{},
        type: 'get',
        success: function(result) {
            $('#user-modal-content').html(result);

        }
    });
}