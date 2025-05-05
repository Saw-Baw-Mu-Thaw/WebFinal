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





export { formatDatetime } 