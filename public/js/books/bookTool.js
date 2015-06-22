var bookTool = {
    chapterContainer: '#chapter_container',
    urlApi: '/',
    initialize: function() { },
    loadQuestions: function(myself, id) {
        var that = this;
        $(myself).parent().parent().find('li').removeClass('active');
        $(myself).parent().addClass('active');
        that.loading(that.chapterContainer, true);
        $.ajax({
            url: that.urlApi + 'questions/index',
            data: {},
            type: "GET",
            beforeSend: function() {
                $(myself).button('loading');
            },
            success:function(result) {
                $(that.chapterContainer).html(result);
                $(myself).button('reset');
                that.loading(that.chapterContainer, false);
            },
            error: function(jqXHR){
                alertify.error("Error: Loading data");
                $(myself).button('reset');
                that.loading(that.chapterContainer, false);
            }
        });

    },
    editQuestion: function(myself, id) {
        var that = this;
        $(myself).parent().parent().find('li').removeClass('active');
        $(myself).parent().addClass('active');
        that.loading(that.chapterContainer, true);
        $.ajax({
            url: that.urlApi + 'questions/edit/'+id,
            data: {},
            type: "GET",
            beforeSend: function() {
                $(myself).button('loading');
            },
            success:function(result) {
                $(that.chapterContainer).html(result);
                $(myself).button('reset');
                that.loading(that.chapterContainer, false);
            },
            error: function(jqXHR){
                alertify.error("Error: Loading data");
                $(myself).button('reset');
                that.loading(that.chapterContainer, false);
            }
        });
    },
    saveQuestion: function(myself) {
        alert('aaa');
    },

    loading: function(element, show) {
        var templateLoading = '<div class="overlay"><div class="loading"></div></div>';
        if(show) {
            $(element).prepend(templateLoading);
        } else {
            $(element).find('.overlay').remove();
        }
    }
};