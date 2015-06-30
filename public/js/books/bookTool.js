var bookTool = {
    chapterContainer: '#chapter_container',
    urlApi: '/',
    book_id: '',
    chapter_id: '',
    section_id: '',
    initialize: function(book_id) {
        this.book_id = book_id;
    },
    editChapter: function(myself,chapter_id) {
        var that = this;
        $.ajax({
            url: that.urlApi + 'chapters/edit',
            data: {book_id:that.book_id,id:chapter_id},
            type: "GET",
            beforeSend: function() {
                $(myself).button('loading');
            },
            success:function(result) {
                $('#chapter_left').prepend('<div class="overlay_white"></div>');
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
    resetForm: function(myself, component) {
        //$('#chapter_form')[0].reset();
        var that = this;
        if(confirm('Are you sure cancel?')) {
            $('#chapter_container').empty().html('Please click to chapter to loading data ...');
            $('#chapter_left').find('.overlay_white').remove();
        }
        if(component == 'chapter') {
            if(that.chapter_id != '') {
                that.loadSections(myself, that.chapter_id);
            }
        }else if(component == 'section') {
            if(that.chapter_id != '') {
                that.loadSections(myself, this.chapter_id);
            }
        }else if(component == 'question') {
            if(that.section_id != '') {
                this.loadQuestions(myself, this.section_id);
            }
        }
    },
    saveChapter: function(myself){
        var that = this;
        var form = $('#chapter_form')[0];
        var formData = new FormData(form);
        $.ajax({
            url: that.urlApi + 'chapters/save',
            data: formData,
            type: "POST",
            contentType: false,
            processData: false,
            beforeSend: function() {
                $(myself).button('loading');
            },
            success:function(result) {
                $('#chapter').html(result);
                $(myself).button('reset');
                that.loading(that.chapterContainer, false);
                alertify.success("Saved successfully.");
            },
            error: function(jqXHR){
                alertify.error("Error: Loading data");
                $(myself).button('reset');
                that.loading(that.chapterContainer, false);
            }
        });
    },
    loadSections: function(myself, chapter_id) {
        var that = this;
        $(myself).parent().parent().find('li').removeClass('active');
        $(myself).parent().addClass('active');
        that.chapter_id = chapter_id;
        that.loading(that.chapterContainer, true);
        $.ajax({
            url: that.urlApi + 'chapters/sections',
            data: {id:that.chapter_id},
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
    findSections: function(myself){
        var that = this;
        that.loading('#find_sections_container', true);
        $.ajax({
            url: that.urlApi + 'sections/find',
            data: {id:that.chapter_id},
            type: "GET",
            beforeSend: function() {
            },
            success:function(result) {
                $('#find_sections_container').html(result);
                that.loading('#find_sections_container', false);
            },
            error: function(jqXHR){
                alertify.error("Error: Loading data");
                that.loading('#find_sections_container', false);
            }
        });
    },
    editSection: function(myself,section_id) {
        var that = this;
        $.ajax({
            url: that.urlApi + 'sections/edit',
            data: {chapter_id:that.chapter_id,id:section_id},
            type: "GET",
            beforeSend: function() {
                $(myself).button('loading');
            },
            success:function(result) {
                $('#chapter_left').prepend('<div class="overlay_white"></div>');
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
    saveSection: function(myself){
        var that = this;
        var form = $('#section_form')[0];
        var formData = new FormData(form);
        $.ajax({
            url: that.urlApi + 'sections/save',
            data: formData,
            type: "POST",
            contentType: false,
            processData: false,
            beforeSend: function() {
                $(myself).button('loading');
            },
            success:function(result) {
                $('#chapter_container').html(result);
                $(myself).button('reset');
                that.loading(that.chapterContainer, false);
                alertify.success("Saved successfully.");
                $('#chapter_left').find('.overlay_white').remove();
            },
            error: function(jqXHR){
                alertify.error("Error: Loading data");
                $(myself).button('reset');
                that.loading(that.chapterContainer, false);
                $('#chapter_left').find('.overlay_white').remove();
            }
        });
    },
    loadQuestions: function(myself, section_id) {
        var that = this;
        $(myself).parent().parent().find('li').removeClass('active');
        $(myself).parent().addClass('active');
        that.section_id = section_id;
        that.loading(that.chapterContainer, true);
        $.ajax({
            url: that.urlApi + 'questions/index',
            data: {section_id: that.section_id},
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
    editQuestion: function(myself, id, type) {
        var that = this;
        that.loading(that.chapterContainer, true);
        $.ajax({
            url: that.urlApi + 'questions/edit',
            data: {id:id,section_id:that.section_id,type:type},
            type: "GET",
            beforeSend: function() {
                $(myself).button('loading');
            },
            success:function(result) {
                $('#chapter_left').prepend('<div class="overlay_white"></div>');
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
        var that = this;
        var form = $('#question_form')[0];
        var formData = new FormData(form);
        $.ajax({
            url: that.urlApi + 'questions/save',
            data: formData,
            type: "POST",
            contentType: false,
            processData: false,
            beforeSend: function() {
                $(myself).button('loading');
            },
            success:function(result) {
                $('#chapter_container').html(result);
                $(myself).button('reset');
                that.loading(that.chapterContainer, false);
                alertify.success("Saved successfully.");
                //that.loadQuestions(myself,that.section_id);
                $('#chapter_left').find('.overlay_white').remove();
            },
            error: function(jqXHR){
                alertify.error("Error: Loading data");
                $(myself).button('reset');
                that.loading(that.chapterContainer, false);
                $('#chapter_left').find('.overlay_white').remove();
            }
        });
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