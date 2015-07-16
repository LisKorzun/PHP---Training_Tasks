$(document).ready(function() {

    $('td.role button.add').click(function() {
        var button = $(this),
            enterField = $('<textarea/>'),
            tdRole = $(this).parent('td');

        enterField
            .css({
                position: 'absolute',
                width: 200,
                height: 30,
                left: button.offset().left + (button.outerWidth()+2),
                top: button.offset().top
            })
            .val(button.data('textContent') || 'Enter')
            .keypress(function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    button.data('textContent', this.value);
                    var key = $( "td.role" ).index( tdRole );

                    $.ajax({
                        type: "POST",
                        url: "src/save.php",
                        data: { name: "John", location: "Boston" },
                        success: function(msg){
                            alert( "Прибыли данные: " + msg );
                        }
                    });

                    alert(key);
                    alert(button.data('textContent'));
                    $(this).remove();
                }
            })
            .appendTo(document.body);
    });
});
