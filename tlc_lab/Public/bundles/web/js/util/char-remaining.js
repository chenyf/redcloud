define(function(require, exports, module) {
    exports.char = function()
    {
        var textArea = $(".char-remaining-stem");
        var words = $(".char-remaining-words");

        statInputNum(textArea, words);

        function statInputNum(textArea, numItem) {
            var max = numItem.text(),
                    curLength;
            textArea[0].setAttribute("maxlength", max);
            curLength = textArea.val().length;
            numItem.text(max - curLength);
            textArea.on('input propertychange', function() {
                numItem.text(max - $(this).val().length);
            });
        }
    }

});