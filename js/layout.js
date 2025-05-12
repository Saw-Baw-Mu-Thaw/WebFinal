function changeLayout(e) {
    var layout = $(e.target).val();

    // console.log(layout);
    $.ajax({
        url: 'api/update_layout.php?layout=' + layout,
        type: 'GET',
        datatype: 'json'
    }).done((response) => {
        // console.log(response);
        location.reload();
    })
}

export { changeLayout }