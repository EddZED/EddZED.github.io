const rangeSlider = document.getElementById('filtr_price');
if(rangeSlider){
  noUiSlider.create(rangeSlider, {
    start: [100, 9000],
    connect: true,
    step: 100,
    range: {
        'min': [500],
        'max': [9000]
    }
});
  const input0 = document.getElementById('input_min');
  const input1 = document.getElementById('input_max');
  const inputs = [input0, input1];

  rangeSlider.noUiSlider.on('update', function(values, handle){
    inputs[handle].value = Math.round(values[handle])
  });
}
const efficiencySlider = document.getElementById('efficiency');
if(efficiencySlider){
  noUiSlider.create(efficiencySlider, {
    start: [100, 9000],
    connect: true,
    step: 100,
    range: {
        'min': [500],
        'max': [9000]
    }
});
  const input0 = document.getElementById('efficiency_min');
  const input1 = document.getElementById('efficiency_max');
  const inputs = [input0, input1];

  efficiencySlider.noUiSlider.on('update', function(values, handle){
    inputs[handle].value = Math.round(values[handle])
  });
}