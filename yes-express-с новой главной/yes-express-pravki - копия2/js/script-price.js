$(document).ready(function () {
	var stepsSlider = document.getElementById('steps-slider');
	var input0 = document.getElementById('input-with-keypress-0');
	var input1 = document.getElementById('input-with-keypress-1');
	var inputs = [input0, input1];

	noUiSlider.create(stepsSlider, {
		start: [20, 80],
		connect: true,
		step: 10,
		range: {
			'min': [0],
			'10%': [10, 10],
			'50%': [80, 50],
			'80%': 150,
			'max': 200
		}
		
	});

	stepsSlider.noUiSlider.on('update', function (values, handle) {
		inputs[handle].value = Math.round(values[handle]);
	});
	
	input1.addEventListener('change', function () {
		stepsSlider.noUiSlider.set([null, this.value]);
	});

	input0.addEventListener('change', function () {
		stepsSlider.noUiSlider.set([this.value, null]);
	});

});
 

