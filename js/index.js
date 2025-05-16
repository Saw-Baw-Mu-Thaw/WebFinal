import { changeMode } from "./mode.js";
import { changeLayout } from "./layout.js";
import { generateGrid } from "./generateGrid.js";
import { generateList } from "./generateList.js";
import { searchNote } from "./search.js";
import { findLabels } from "./findLabels.js";
import { showError, showOfflineWarning } from "./utilities.js";


function getNotesGrid() {
  $.ajax({
    url: "api/get_notes.php",
    type: "GET",
    datatype: "json",
  }).done(function (response) {
    // console.log(response);
    localStorage.setItem('notes', JSON.stringify(response))
    // Save to IndexedDB for offline use
    if (window.OfflineNotesManager) {
      OfflineNotesManager.saveNotesToIndexedDB(response);
    }
    for (var i = 0; i < response.length; i++) {
      // console.log(response[i]);
      generateGrid(response[i]);
    }
  }).fail(function(error) {
    console.log('Failed to fetch notes from server, trying IndexedDB');
    if (window.OfflineNotesManager) {
      OfflineNotesManager.getNotesFromIndexedDB().then(notes => {
        if (notes && notes.length > 0) {
          localStorage.setItem('notes', JSON.stringify(notes));
          for (var i = 0; i < notes.length; i++) {
            generateGrid(notes[i]);
          }
          // Show offline indicator
          showOfflineWarning();
        } else {
          showError("No cached notes available offline");
        }
      });
    }
  });
}

function getNotesList() {
  $.ajax({
    url: "api/get_notes.php",
    type: "GET",
    datatype: "json",
  }).done(function (response) {
    // console.log(response);
    localStorage.setItem('notes', JSON.stringify(response))
    // Save to IndexedDB for offline use
    if (window.OfflineNotesManager) {
      OfflineNotesManager.saveNotesToIndexedDB(response);
    }
    generateList(response);
  }).fail(function(error) {
    console.log('Failed to fetch notes from server, trying IndexedDB');
    if (window.OfflineNotesManager) {
      OfflineNotesManager.getNotesFromIndexedDB().then(notes => {
        if (notes && notes.length > 0) {
          localStorage.setItem('notes', JSON.stringify(notes));
          generateList(notes);
          // Show offline indicator
          showOfflineWarning();
        } else {
          showError("No cached notes available offline");
        }
      });
    }
  });
}

function setPreferences(elemList) {
  $.ajax({
    url: "api/get_preferences.php",
    type: "GET",
    datatype: "json",
  }).done(function (response) {
    // console.log(response);
    localStorage.setItem('Layout', response['Layout'])
    localStorage.setItem('Mode', response['Mode'])
    
    // Save font size and note color to localStorage
    const fontSize = response['FontSize'] ? response['FontSize'] : 16;
    const fontSizePx = fontSize + 'px';
    const noteColor = response['NoteColor'] || '#ffffff';
    
    // Store raw pixel value in localStorage
    localStorage.setItem('fontSize', fontSize.toString());
    localStorage.setItem('noteColor', noteColor);
    
    // Apply CSS variables
    document.documentElement.style.setProperty('--note-font-size', fontSizePx);
    document.documentElement.style.setProperty('--note-color', noteColor);
    
    if (response["code"] == 0) {
      // console.log("setting preferences");

      // choose list or grid here
      if (response["Layout"] == "GRID") {
        getNotesGrid();
      } else {
        getNotesList();
      }

      // Apply font size to specific elements
      for (var i = 0; i < elemList.length; i++) {
        $(elemList[i]).css("font-size", fontSizePx);
      }
      
      // Make sure to apply font size to note elements after they're loaded
      setTimeout(() => {
        $('.note, .card-text, .card-body, textarea').css('font-size', fontSizePx);
      }, 500);

      // set light or dark mode here
      if (response["Layout"] == "GRID") {
        checkElementExists(".card", 5).then((result) => {
          if (result) {
            if (response["Mode"] == "DARK") {
              $(".mode-target").addClass("bg-dark");
            } else {
              $(".mode-target").addClass("bg-light");
            }
          }
        });
      } else {
        checkElementExists(".table", 5).then((result) => {
          if (result) {
            if (response["Mode"] == "DARK") {
              $(".mode-target").addClass("bg-dark");
            } else {
              $(".mode-target").addClass("bg-light");
            }
          }
        });
      }
    }
  });
}

function setUsernameHeading() {
  // set username heading
  $.ajax({
    url: "api/get_user_info.php",
    type: "GET",
    datatype: "json",
  }).done(function (response) {
    $("#userHeading").text(response["username"] + "'s notes");
  });
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

function setLabelList() {
  $.ajax({
    url: 'api/get_label_list.php',
    type: 'GET',
    datatype: 'json'
  }).done((response) => {
    if (response['code'] == 0) {
      var labels = response['labels'];

      // show all button
      var labelBtn = $('<button></button>')
      $(labelBtn).addClass('list-group-item list-group-item-action')
      $(labelBtn).attr('data-label', 0);
      $(labelBtn).text("Show All");
      $(labelBtn).on('click', findLabels);
      $('#labelList').append(labelBtn);

      // then the labels
      for (var i = 0; i < labels.length; i++) {
        var labelBtn = $('<button></button>')
        $(labelBtn).addClass('list-group-item list-group-item-action')
        $(labelBtn).attr('data-label', labels[i]);
        $(labelBtn).text("#" + labels[i]);
        $(labelBtn).on('click', findLabels);
        $('#labelList').append(labelBtn);
      }
    }
  })
}

$(document).ready(function () {
  $("#errorDiv").hide();

  $("input:radio[name=mode]").on("click", changeMode);
  $("input:radio[name=layout]").on("click", changeLayout);



  // use display block to show it
  setUsernameHeading();
  // getNotes();
  setPreferences(["body", "div", "h4", "h5"]);

  setLabelList();

  $('#txtSearch').on('input', searchNote);


});

