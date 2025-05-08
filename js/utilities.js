function showError(msg) {
    $("#errorDiv").show();
    $("#errorDiv").text(msg);

    window.setTimeout(function () { $("#errorDiv").hide() }, 2500)
}

export { showError }