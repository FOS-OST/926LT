var bookTool = {
    chapterContainer: '#chapter_container',
    commentContainer: '#comment_container',
    groupChilds: '#group_child',
    urlApi: '/admin/',
    book_id: '',
    chapter_id: '',
    section_id: '',
    group_id: '',
    initialize: function(book_id) {
        this.book_id = book_id;
        this.loadChapters();
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
    deleteChapter: function(myself,chapter_id) {
        var that = this;
        if(!confirm("Do you want to delete this chapter?")) {
            return false;
        }
        $.ajax({
            url: that.urlApi + 'chapters/delete',
            data: {id:chapter_id},
            type: "POST",
            beforeSend: function() {
                $(myself).button('loading');
            },
            success:function(result) {
                that.loadChapters(myself);
                $(myself).button('reset');
            },
            error: function(jqXHR){
                alertify.error("Error: Loading data");
                $(myself).button('reset');
            }
        });
    },
    loadChapters: function(myself) {
        var that = this;
        $.ajax({
            url: that.urlApi + 'chapters/index',
            data: {book_id:that.book_id},
            type: "GET",
            beforeSend: function() {
                $(myself).button('loading');
            },
            success:function(result) {
                $('#chapter_list').html(result);
                $(myself).button('reset');
            },
            error: function(jqXHR){
                alertify.error("Error: Loading data");
                $(myself).button('reset');
            }
        });
    },
    resetForm: function(myself, component) {
        //$('#chapter_form')[0].reset();
        var that = this;
        if(confirm('Are you sure cancel?')) {
            $(that.chapterContainer).empty().html('Please click to chapter to loading data ...');
            $('#chapter_left').find('.overlay_white').remove();
        } else {
            return false;
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
            $('#chapter_left').show();
            $('#chapter_right').css({width:'75%'});
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
                that.loadChapters();
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
            url: that.urlApi + 'sections/index',
            data: {chapter_id:that.chapter_id},
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
                $(that.chapterContainer).html(result);
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
    deleteSection: function(myself,section_id) {
        var that = this;
        if(!confirm("Do you want to delete this section?")) {
            return false;
        }
        $.ajax({
            url: that.urlApi + 'sections/delete',
            data: {chapter_id:that.chapter_id,id:section_id},
            type: "POST",
            beforeSend: function() {
                $(myself).button('loading');
            },
            success:function(result) {
                that.loadSections(myself,that.chapter_id);
                $(myself).button('reset');
            },
            error: function(jqXHR){
                alertify.error("Error: Loading data");
                $(myself).button('reset');
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
    editQuestion: function(myself, id, type, section_id) {
        var that = this;
        that.loading(that.chapterContainer, true);
        var urlApi = 'questions/edit';
        if(type == 'SUMMARY') {
            var urlApi = 'questions/editSummary';
        }
        if(section_id) {
            that.section_id = section_id;
        }
        $.ajax({
            url: that.urlApi + urlApi,
            data: {id:id,section_id:that.section_id,type:type,book_id:that.book_id,chapter_id:that.chapter_id},
            type: "GET",
            beforeSend: function() {
                $(myself).button('loading');
            },
            success:function(result) {
                $('#chapter_left').hide();
                $('#chapter_right').css({width:'100%'});
                $(that.chapterContainer).html(result);
                that.loading(that.chapterContainer, false);
                $(myself).button('reset');
                $('#container_tabs li:eq(1) a').tab('show');
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
                $(that.chapterContainer).html(result);
                $(myself).button('reset');
                that.loading(that.chapterContainer, false);
                alertify.success("Saved successfully.");
                //that.loadQuestions(myself,that.section_id);
                //$('#chapter_left').find('.overlay_white').remove();
                $('#chapter_left').show();
                $('#chapter_right').css({width:'75%'});
            },
            error: function(jqXHR){
                alertify.error("Error: Loading data");
                $(myself).button('reset');
                that.loading(that.chapterContainer, false);
                $('#chapter_left').find('.overlay_white').remove();
            }
        });
    },
    editComment: function(myself,comment_id) {
        var that = this;
        $.ajax({
            url: that.urlApi + 'comments/edit',
            data: {book_id:that.book_id,id:comment_id},
            type: "GET",
            beforeSend: function() {
                $(myself).button('loading');
            },
            success:function(result) {
                $('#chapter_left').prepend('<div class="overlay_white"></div>');
                $(that.commentContainer).html(result);
                $(myself).button('reset');
                that.loading(that.commentContainer, false);
            },
            error: function(jqXHR){
                alertify.error("Error: Loading data");
                $(myself).button('reset');
                that.loading(that.commentContainer, false);
            }
        });
    },
    searchQuestions: function(myself) {
        var that = this;
        var search = $('#txt_search').val();
        $.ajax({
            url: that.urlApi + 'questions/search',
            data: {book_id:that.book_id,search:search},
            type: "GET",
            beforeSend: function() {
                $(myself).button('loading');
            },
            success:function(result) {
                $('#search_result').html(result);
                $(myself).button('reset');
            },
            error: function(jqXHR){
                alertify.error("Error: Loading data");
                $(myself).button('reset');
            }
        });
        return false;
    },
    saveGroupQuestion: function(myself) {
        var that = this;
        var form = $('#question_group_form')[0];
        var formData = new FormData(form);
        $.ajax({
            url: that.urlApi + 'questions/saveGroup',
            data: formData,
            type: "POST",
            contentType: false,
            processData: false,
            beforeSend: function() {
                $(myself).button('loading');
            },
            success:function(result) {
                $(that.chapterContainer).html(result);
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
    editChildQuestion: function(myself, id, group_id, type) {
        var that = this;
        that.group_id = group_id;
        that.loading(that.groupChilds, true);
        var urlApi = 'questions/editChild';
        $.ajax({
            url: that.urlApi + urlApi,
            data: {id:id,section_id:that.section_id,book_id:that.book_id,chapter_id:that.chapter_id,type:type,group_id:group_id},
            type: "GET",
            beforeSend: function() {
                $(myself).button('loading');
            },
            success:function(result) {
                $(that.groupChilds).html(result);
                that.loading(that.groupChilds, false);
                $(myself).button('reset');
            },
            error: function(jqXHR){
                alertify.error("Error: Loading data");
                $(myself).button('reset');
                that.loading(that.groupChilds, false);
            }
        });
    },
    saveChildQuestion: function(myself) {
        var that = this;
        var form = $('#question_child_form')[0];
        var formData = new FormData(form);
        $.ajax({
            url: that.urlApi + 'questions/saveChild',
            data: formData,
            type: "POST",
            contentType: false,
            processData: false,
            beforeSend: function() {
                $(myself).button('loading');
            },
            success:function(result) {
                $(that.groupChilds).html(result);
                /*if(result.error) {
                 alertify.error(result.msg);
                 } else {
                 alertify.success(result.msg);
                 }*/
            },
            error: function(jqXHR){
                alertify.error("Error: Loading data");
                $(myself).button('reset');
            }
        });
        return false;
    },
    resetChildForm: function(myself, group_id) {
        var that = this;
        this.editQuestion(myself, group_id, 'GROUP');
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