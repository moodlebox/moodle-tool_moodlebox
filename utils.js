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

function disable_restartshutdown_buttons() {
    // Submitting the Form disables the restart button.
    var form = Y.one('#formrestartstop');
    form.on('submit', function() {
        var buttons = form.all('.btn');
        if (form.getAttribute('submitted')) return false;
        buttons.each(
            function(button){
                button.setAttribute('disabled', true);
            });
        form.setAttribute('submitted','true')
    });
}
