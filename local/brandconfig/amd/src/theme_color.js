(function() {
    // Get the color input field
    var colorField = document.getElementById('id_theme_color');
    var textcolorfield = document.getElementById('id_textcolor');
    var backgroundcolorfield = document.getElementById('id_backgroundcolor');
  
    var colorPicker = createColorPicker();
    var textColorPicker = createColorPicker();
    var backgroundColorPicker = createColorPicker();

      // Add event listeners to update the values of the input fields when a color is selected
  colorPicker.addEventListener('input', function() {
    colorField.value = colorPicker.value;
  });
  textColorPicker.addEventListener('input', function() {
    textcolorfield.value = textColorPicker.value;
  });
  backgroundColorPicker.addEventListener('input', function() {
    backgroundcolorfield.value = backgroundColorPicker.value;
  });

      // Insert the color picker elements before the corresponding input fields
  if (colorField) {
    colorField.parentNode.insertBefore(colorPicker, colorField);
  }
  if (textcolorfield) {
    textcolorfield.parentNode.insertBefore(textColorPicker, textcolorfield);
  }
  if (backgroundcolorfield) {
    backgroundcolorfield.parentNode.insertBefore(backgroundColorPicker, backgroundcolorfield);
  }

   // Function to create a color picker element
   function createColorPicker() {
    var colorPicker = document.createElement('input');
    colorPicker.type = 'color';
    return colorPicker;
  }

    // Insert the color picker element before the color input field
  })();
  