define(function(require, exports, module) {
    exports.run = function() {
        var _getCount = function(obj) {
            var count = 0;
            for (var i in obj) {
                count++;
            }
            return count;
        }
        var _getScore = function(obj) {
            var score = 0;
            for (var s in obj) {
                score += Number(obj[s].score);
            }
            return score;
        }
        var _getTypeCountAndScore = function(obj) {
            var single_choice_count = 0, choice_count = 0, essay_count = 0, fill_count = 0, determine_count = 0;
            var single_choice_score = 0, choice_score = 0, determine_score = 0, essay_score = 0, fill_score = 0;
            var data = {};
            for (var o in obj) {
                switch (obj[o].type) {
                    case 'single_choice':
                        single_choice_count++;
                        single_choice_score += Number(obj[o].score);
                        break;
                    case 'choice':
                        choice_count++;
                        choice_score += Number(obj[o].score);
                        break;
                    case 'determine':
                        determine_count++;
                        determine_score += Number(obj[o].score);
                        break;
                    case 'essay':
                        essay_count++;
                        essay_score += Number(obj[o].score);
                        break;
                    case 'fill':
                        fill_count = fill_count + 1;
                        fill_score += Number(obj[o].score);
                        break;
                }
            }
            data.count = {single_choice_count: single_choice_count, choice_count: choice_count, determine_count: determine_count, essay_count: essay_count, fill_count: fill_count};
            data.score = {single_choice_score: single_choice_score, choice_score: choice_score, determine_score: determine_score, essay_score: essay_score, fill_score: fill_score}
            return data;
            //console.log(single_choice_count+'--'+single_choice_score);
        }
        var questions = window.questions; 
        var counts = window.counts = {single_choice: 0, choice: 0, determine: 0, essay: 0, fill: 0};
        var scores = window.scores = {single_choice: 0, choice: 0, determine: 0, essay: 0, fill: 0};
        var data = _getTypeCountAndScore(questions);
        $('.total-counts').text(_getCount(questions));
        // $('.total-scores').text(_getScore(questions));
        $('.single_choice_count').text(data['count'].single_choice_count);
        $('.choice_count').text(data['count'].choice_count);
        $('.determine_count').text(data['count'].determine_count);
        $('.essay_count').text(data['count'].essay_count);
        $('.fill_count').text(data['count'].fill_count);

        // $('.single_choice_score').text(data['score'].single_choice_score);
        // $('.choice_score').text(data['score'].choice_score);
        // $('.determine_score').text(data['score'].determine_score);
        // $('.essay_score').text(data['score'].essay_score);
        // $('.fill_score').text(data['score'].fill_score);

    }

});