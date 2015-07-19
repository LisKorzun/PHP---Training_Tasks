$(document).ready(function() {

   function getIndexOfElementInGroup(element, parentClass, currentClass) {
       var $tr = element.parent('tr');
       var $table = element.parents('table').find('tbody');
       var $trs = $table.find('tr');
       var trIndex = $trs.index($tr);
       var index = {};
       index.parent = trIndex;
       index.current = 0;
       if (!$tr.has(parentClass).length) {
           var $groupTr = $table.find('tr:lt(' + trIndex + ')').has(parentClass).last();
           index.parent = $trs.index($groupTr);
           for (var i = index.parent; i <= trIndex; i++) {
               if ($table.find('tr:eq(' + i + ')').has(currentClass).length) {
                   ++index.current;
               }
           }
           index.current = index.current - 1;
       }
       return index;
   }

    $(document).on('click', 'button.add', function() {
        var $button = $(this),
            $enterField = $('<textarea/>'),
            $tdRole = $button.parent('td'),
            $tableBody = $('table tbody'),
            indexRole = 'no',
            indexGroup = 'no',
            indexOption = 'no',
            buttonTdClass = $button.parent('td').attr("class");
        switch(buttonTdClass) {
            case 'role':
                break;
            case 'group':
                var indexes = getIndexOfElementInGroup($button.parent('td'), '.role', '.group');
                indexGroup = indexes.current;
                $tdRole = $tableBody.find('tr:eq(' + indexes.parent + ')').children('td.role');
                break;
            case 'option':
                var indexes = getIndexOfElementInGroup($button.parent('td'), '.group', '.option');
                indexOption = indexes.current;
                var $tdGroup = $tableBody.find('tr:eq(' + indexes.parent + ')').children('td.group');
                indexes = getIndexOfElementInGroup($tdGroup, '.role', '.group');
                indexGroup = indexes.current;
                $tdRole = $tableBody.find('tr:eq(' + indexes.parent + ')').children('td.role');
                break;
        }
        indexRole = $( "td.role" ).index( $tdRole );
        $enterField
            .css({
                position: 'absolute',
                width: 200,
                height: 30,
                left: $button.offset().left + ($button.outerWidth()+2),
                top: $button.offset().top
            })
            .val($button.data('textContent') || 'Enter')
            .keypress(function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    $button.data('textContent', this.value);
                    var title = $button.data('textContent');
                    $(this).remove();
                    $.ajax({
                        type: "POST",
                        url: "src/save.php",
                        data: { indexRole: indexRole, indexGroup: indexGroup, indexOption: indexOption, title: title },
                        success: function(result){
                            if(result){
                                alert("Произошла ошибка. " + result);
                            }
                        }
                    }).done(function() {
                        location.reload();
                    });
                }
            })
            .appendTo(document.body);
    });

    $(document).on('click', 'button.remove', function() {
        var $button = $(this),
            $tdRole = $button.parent('td'),
            $tableBody = $('table tbody'),
            indexRole = 'no',
            indexGroup = 'no',
            indexOption = 'no',
            indexResource = 'no',
            buttonTdClass = $button.parent('td').attr("class");
        switch(buttonTdClass) {
            case 'role':
                break;
            case 'group':
                var indexes = getIndexOfElementInGroup($button.parent('td'), '.role', '.group');
                indexGroup = indexes.current;
                $tdRole = $tableBody.find('tr:eq(' + indexes.parent + ')').children('td.role');
                break;
            case 'option':
                var indexes = getIndexOfElementInGroup($button.parent('td'), '.group', '.option');
                indexOption = indexes.current;
                var $tdGroup = $tableBody.find('tr:eq(' + indexes.parent + ')').children('td.group');
                indexes = getIndexOfElementInGroup($tdGroup, '.role', '.group');
                indexGroup = indexes.current;
                $tdRole = $tableBody.find('tr:eq(' + indexes.parent + ')').children('td.role');
                break;
            case 'resource':
                var indexes = getIndexOfElementInGroup($button.parent('td'), '.option', '.resource');
                indexResource = indexes.current;
                var $tdOption = $tableBody.find('tr:eq(' + indexes.parent + ')').children('td.option');
                indexes = getIndexOfElementInGroup($tdOption, '.group', '.option');
                indexOption = indexes.current;
                var $tdGroup = $tableBody.find('tr:eq(' + indexes.parent + ')').children('td.group');
                indexes = getIndexOfElementInGroup($tdGroup, '.role', '.group');
                indexGroup = indexes.current;
                $tdRole = $tableBody.find('tr:eq(' + indexes.parent + ')').children('td.role');
                break;
        }
        indexRole = $( "td.role" ).index( $tdRole );
        $.ajax({
            type: "POST",
            url: "src/remove.php",
            data: { indexRole: indexRole, indexGroup: indexGroup, indexOption: indexOption, indexResource: indexResource},
            success: function(result){
                if(result){
                    alert("Произошла ошибка. " + result);
                }
            }
        }).done(function() {
            location.reload();
        });
    });
});
