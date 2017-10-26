define(function(require, exports, module) {
    var batProcess = require('./batProcess');
    exports.run = function(){
        batProcess.triggerStartTask();
        batProcess.triggerStopTask();
        batProcess.triggerItemTaskList();
        batProcess.triggerItemPollList();
    }
});