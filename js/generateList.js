import { formatDatetime } from "./utils.js";

function generateList(response) {

    var table = $("<table class='table col-12 mode-target'></table>")
    var thead = $("<thead></thead>")
    var headRow = $("<tr></tr>")
    $(headRow).append("<th>Index</th>", "<th>Title</th>", "<th>Last Modified</th>", "<th>Actions</th>", "<th>Labels</th>", "<th>Pin</th>");
    $(thead).append(headRow)
    $(table).append(thead)

    var tbody = $("<tbody></tbody>");

    for (var i = 0; i < response.length; i++) {
        var obj = response[i];
        var trow = $("<tr></tr>")
        var index = $(`<td>${i + 1}</td>`)
        var title = $(`<td>${obj['Title']}</td>`)
        var lastModified = $(`<td>${formatDatetime(obj['LastModified'])}</td>`)
        var actionCell = $("<td></td>");
        var openBtn = $(`<button class='btn btn-primary mx-1' onclick='openNote(${obj['NoteID']}, ${obj['Locked']})'><i class="far fa-edit"></i></button>`);
        var delBtn = $(`<button class='btn btn-danger mx-1'  onclick='deleteNote(${obj['NoteID']}, "${obj['Title']}")'><i class="fas fa-trash"></i></button>`);
        var lockBtn;
        if (obj['Locked'] == false) {
            lockBtn = $(`<button class='btn btn-info mx-1' onclick='lockNote(${obj['NoteID']})'>Lock Note</button>`)
        } else {
            lockBtn = $(`<button class='btn btn-info mx-1' onclick='removeLock(${obj['NoteID']})'>Remove Lock</button>`)
        }

        var pinCell = $("<td></td>");
        var pinBtn;
        if (obj['Pinned'] == true) {
            pinBtn = $(`<button class='btn' onclick='removePin(${obj['NoteID']})'>
                <span style='color:red;'><i class="fas fa-thumbtack"></i></span>
                </button>`)
        } else {
            pinBtn = $(`<button class='btn' onclick='addPin(${obj['NoteID']})'>
                <span style='color:blue;'><i class="fas fa-thumbtack"></i></span>
                </button>`)
        }
        $(pinCell).append(pinBtn);

        var labelCell = $("<td></td>")
        var labelString = ""
        var labelList = obj['Labels']
        // console.log(labelList)
        for (var j = 0; j < labelList.length; j++) {
            labelString += "#" + labelList[j]['Label'] + " "
        }
        $(labelCell).text(labelString)

        if (obj['SharedNote'] == true) {
            $(actionCell).append(openBtn);
        } else {
            $(actionCell).append(openBtn, delBtn, lockBtn);
        }
        $(trow).append(index, title, lastModified, actionCell, labelCell, pinCell);
        $(tbody).append(trow);
    }

    $(table).append(tbody)
    $("#mainContent").append(table)
}

export { generateList }