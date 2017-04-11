var checktime = function(Y, servertime) {
    var usertime = Math.round(M.pageloadstarttime.getTime() / 1000);
    if (Math.abs(usertime - servertime) >= 60) { // time difference greater than 1 minute
        require(['core/str', 'core/notification'], function(str, notification) {
            str.get_strings([{'key': 'datetimesetmessage', component: 'tool_moodlebox'},]).done(function(s) {
                notification.addNotification({
                    message: s[0],
                    type: 'error'
                });
            }).fail(notification.exception);
        });
    }
    return usertime;
}
