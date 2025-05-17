function formatDatetime(datetime) {
    var difference = Math.floor((Date.now() - Date.parse(datetime)) / 1000);

    var days = Math.floor(difference / 86400);
    difference = Math.floor(difference % 86400);

    var hours = Math.floor(difference / 3600);
    difference = Math.floor(difference % 3600);

    var minutes = Math.floor(difference / 60);
    difference = Math.floor(difference / 60);

    if (days > 0) {
        return days.toString() + (days == 1 ? " day ago" : " days ago")
    } else if (hours > 0) {
        return hours.toString() + (hours == 1 ? " hour ago" : " hours ago")
    } else if (minutes > 0) {
        return minutes.toString() + (minutes == 1 ? " minute ago" : " minutes ago")
    } else {
        return difference.toString() + "seconds ago"
    }
}

function showError(msg) {
    $("#errorDiv").show();
    $("#errorDiv").text(msg);

    window.setTimeout(function () { $("#errorDiv").hide() }, 2500)
}

async function checkElementExists(element, timeout = Infinity) {
    let startTime = Date.now();
    return new Promise((resolve) => {
        const intervalId = setInterval(() => {
            if (document.querySelector(element)) {
                clearInterval(intervalId);
                resolve(true);
            } else if (Date.now() - startTime >= timeout * 1000) {
                clearInterval(intervalId);
                resolve(false);
            }
        }, 100);
    });
}



export { formatDatetime, showError, checkElementExists } 