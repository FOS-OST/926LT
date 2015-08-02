var bookAnswer = {
    addAnswer: function(myself) {
        var answerElements = $(myself).siblings("ul"), answerElement = answerElements.find("li:eq(0)").clone();
        answerElement.removeClass('has-error');
        answerElement.find(".answer_html").removeAttr("checked");
        answerElement.find(".answer_correct").removeAttr("checked");
        answerElement.find(".answer_text").val("");
        answerElement.find(".answer_sl").val(1);
        answerElement.find(".answer_correct_img").val('/img/favicon.png');
        answerElement.find(".answer_img").attr('src', '/img/favicon.png');
        answerElement.find(".answer_variable").val("");
        answerElement.appendTo(answerElements);
        answerElements.find("li").each(function(index){
            $(this).find(".answer_correct").val(index);
            $(this).find(".answer_html").val(index);
        });
        return !1
    },
    answerRemove: function (myself) {
        var answerElements = $(myself).parent().parent().parent();
        var answerElement = $(myself).parent().parent();
        if (2 > answerElement.parent().children().length)return !1;
        answerElement.remove();
        answerElements.find("li").each(function(index){
            $(this).find(".answer_radio").val(index);
            $(this).find(".answer_checkbox").val(index);
        });
        return !1
    },
    selectImage: function(myself){
        var finder = new CKFinder();
        finder.selectActionFunction = selectImage;
        finder.popup();

        // This is a sample function which is called when a file is selected in CKFinder.
        function selectImage(fileUrl) {
            $(myself).parent().find(".answer_correct_img").val(fileUrl);
            $(myself).parent().find("img").attr('src',fileUrl);
        }
    },
    radioSelect: function(myself) {
        /*$('.answer_radio').each(function(index) {
            $(this).val(index);
        });*/
    },
    checkboxSelect: function(myself) {
        /*$('.answer_checkbox').each(function(index) {
            $(this).val(index);
        });*/
    }
};