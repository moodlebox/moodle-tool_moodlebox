checktime = function(Y,servertime) {
        usertime = Math.round(M.pageloadstarttime.getTime()/1000);
        if ( Math.abs(usertime - servertime) > 0 ) {
//             alert('Browser: ' + usertime + '__ __' + 'Box: ' + servertime);
            require(['core/str', 'core/notification'], function(str, notification) {
                            str.get_strings([
                                    {'key' : 'datetimesetmessage', component : 'local_moodlebox'},
                                ]).done(function(s) {
                                    notification.addNotification({
                                        message: s[0],
                                        type: 'error'
                                    });
                                }
                            ).fail(notification.exception);
            });
        }
        //return M.pageloadstarttime.getTime();
    }

/*
require(['core/notification'], function(notification) {
    notification.alert('Hello', 'Welcome to my site!', 'Continue');
});

require(['core/str'], function(str) {
    str.get_string('datetimesetmessage', 'local_moodlebox', stringargument).done(function(s) {
       console.log(s);
    }).fail(console.log(e));
    console.log('bla')
});

require(['core/str', 'core/notification'], function(str, notification) {
                str.get_strings([
                        {'key' : 'delete'},
                        {'key' : 'confirmdeletetag', component : 'tag'},
                        {'key' : 'yes'},
                        {'key' : 'no'},
                    ]).done(function(s) {
                        notification.confirm(s[0], s[1], s[2], s[3], function() {
                            window.location.href = href;
                        });
                    }
                ).fail(notification.exception);
});
*/