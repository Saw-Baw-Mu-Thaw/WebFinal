import { changeMode } from "./mode.js";
import { changeLayout } from "./layout.js";
import { generateGrid } from "./generateGrid.js";
import { generateList } from "./generateList.js";
import {searchNote} from "./search.js";										 

function getNotesGrid() {
  $.ajax({
    url: "api/get_notes.php",
    type: "GET",
    datatype: "json",
  }).done(function (response) {
    console.log(response);

    for (var i = 0; i < response.length; i++) {
      // console.log(response[i]);
      generateGrid(response[i]);
    }
  });
}

function getNotesGrid() {
  $.ajax({
    url: "api/get_notes.php",
    type: "GET",
    datatype: "json",
  }).done(function (response) {
    // console.log(response);
	localStorage.setItem('notes', JSON.stringify(response))
    for (var i = 0; i < response.length; i++) {
      // console.log(response[i]);
      generateGrid(response[i]);
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
    generateList(response);
  });

}

function setPreferences(elemList) {
  $.ajax({
    url: "api/get_preferences.php",
    type: "GET",
    datatype: "json",
  }).done(function (response) {
    console.log(response);

    if (response["code"] == 0) {
      // console.log("setting preferences");

      // choose list or grid here
      if (response["Layout"] == "GRID") {
        getNotesGrid();
      } else {
        getNotesList();
      }

      // set font size here
      for (var i = 0; i < elemList.length; i++) {
        // console.log("setting font size")
        $(elemList[i]).css("font-size", response["FontSize"]);
      }

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

$(document).ready(function () {
  $("#errorDiv").hide();

  $("input:radio[name=mode]").on("click", changeMode);
  $("input:radio[name=layout]").on("click", changeLayout);
  // use display block to show it
  setUsernameHeading();
  // getNotes();
  setPreferences(["body", "div", "h4", "h5"]);											
											
	$('#txtSearch').on('input', searchNote);								
});

