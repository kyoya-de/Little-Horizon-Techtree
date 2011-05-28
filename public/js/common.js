function messageCheck(checkUrl)
{
    $.ajax({
        url: checkUrl,
        type: 'GET',
        success: function (data) {
            if (data.showPopup) {
                var newMsgCount = data.newMessages - $('#newMsgs').html();
                newMessageNotify(newMsgCount);
            }
            var totalSuffix = '';
            var newSuffix = '';
            if (data.totalMessages != 1) {
                totalSuffix = 'en'
            }
            if (data.newMessages != 1) {
                newSuffix = 'en';
            }
            $('#totalMsgs').html(data.totalMessages + ' Nachricht' + totalSuffix);
            $('#newMsgs').html(data.newMessages + ' neue Nachricht' + newSuffix);
        }
    });
}

function newMessageNotify(newMsgCount)
{
    var suffix = '';
    if (newMsgCount != 1) {
        suffix = 'en'
    }
    $('<div>Du hast ' + newMsgCount + ' neue Nachricht' + suffix + '.</div>').dialog({
        title: 'Neue Nachricht' + suffix + ' im Postfach',
        buttons: {
            'OK':function() {
                $(this).dialog('close');
            }
        }
    });

}
