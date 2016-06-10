$(function () {
    $('#btnAdd').click(function () {
        var num     = $('.clonedInput').length, 
            newNum  = new Number(num + 1), 
            newElem = $('#input' + num).clone().attr('id', 'input' + newNum).fadeIn('slow');
        newElem.find('.heading-reference').attr('id', 'ID' + newNum + '_reference').attr('name', 'ID' + newNum + '_reference').html('Integrante #' + newNum);


        newElem.find('.label_ttl').attr('for', 'ID' + newNum + '_title');
        newElem.find('#integrante').attr('id', 'integrante' + newNum).attr('name', 'integrante' + newNum).val('');
		
		
		
        $('#input' + num).after(newElem);
        $('#ID' + newNum + '_title').focus();


        $('#btnDel').attr('disabled', false);


        if (newNum == 10)
        $('#btnAdd').attr('disabled', true).prop('value', "Limite 10 integrantes por operativo"); 
    });

    $('#btnDel').click(function () {

        if (confirm("ESTA SEGURO DE QUITAR INTEGRANTE"))
            {
                var num = $('.clonedInput').length;

                $('#input' + num).slideUp('slow', function () {$(this).remove();

                    if (num -1 === 1)
                $('#btnDel').attr('disabled', true);

                $('#btnAdd').attr('disabled', false).prop('value', "Agregar");});
            }
        return false; 
    });

    $('#btnAdd').attr('disabled', false);

    $('#btnDel').attr('disabled', true);
});