var localMode = "";
var interval = null;

function changeMode(e) {
    var mode = $(e.target).val()
    localStorage.setItem('Mode', mode);
    $(e.target).prop('checked', true)

    $.ajax({
        url: 'api/update_mode.php?mode=' + mode,
        type: 'GET',
        datatype: 'json'
    }).done(function (response) {
        window.clearInterval(interval)
        interval = null

        if (response['code'] == 0) {
            $('.mode-target').removeClass('bg-light bg-dark')
            if (mode === 'LIGHT') {
                $('.mode-target').addClass('bg-light')
            } else {
                $('.mode-target').addClass('bg-dark')
            }
        }
    }).fail(() => {
        alert('Couldn\'t connect to server')
        localMode = mode;
        if (localMode.lenght > 0) {
            $('.mode-target').removeClass('bg-light bg-dark')
            if (mode === 'LIGHT') {
                $('.mode-target').addClass('bg-light')
            } else {
                $('.mode-target').addClass('bg-dark')
            }
        }

        if (interval == null) {
            interval = window.setInterval(sendModeUpdate, 5000, localMode)
        }
    })
}

function sendModeUpdate(mode) {
    $.ajax({
        url: 'api/update_mode.php?mode=' + mode,
        type: 'GET',
        datatype: 'json'
    }).done(function (response) {

        window.clearInterval(interval)
        interval = null

        if (response['code'] == 0) {
            $('.mode-target').removeClass('bg-light bg-dark')
            if (mode === 'LIGHT') {
                $('.mode-target').addClass('bg-light')
            } else {
                $('.mode-target').addClass('bg-dark')
            }
        }
    })
}

export { changeMode }