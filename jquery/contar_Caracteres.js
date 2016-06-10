// número máximo de caracteres
var limite = 1000;

$(document).ready(function()
{

	// cada vez que se deja de presionar una tecla
	$("#resultados").keyup(function(e)
	{

		// obtenemos el texto que está escrito en el textarea
		var box = $(this).val();

		// calculamos el porcentaje entre el texto ingresado y el límite
		var value = (box.length *100) / limite;

		// obtenemos cuántos caracteres quedan
		var resta = limite - box.length;

		// si aún no se llegó al límite
		if(box.length <= limite)
		{

			// modificamos el texto que muestra la cantidad de caracteres que restan
			$('#divContador').html(resta);

			// modificamos el ancho de la barra
			$('#divProgreso').animate({
				"width": value+'%',
			}, 1);

			// si no se llegó al 50%, hacemos que la barra sea de color verde
			if (value < 50) {
				$('#divProgreso').removeClass();
				$('#divProgreso').addClass('verde');
			}
			else if (value < 85) { // si no se llegó al 85% que sea amarilla
				$('#divProgreso').removeClass();
				$('#divProgreso').addClass('amarillo');
			}
			else { // si se superó el 85% que sea roja
				$('#divProgreso').removeClass();
				$('#divProgreso').addClass('rojo');
			};
		}
		else // si se llegó al límite no permitimos ingresar más caracteres
		{
			// evitamos que ingrese más caracteres
			e.preventDefault();
		}				
	});
});