$(document).ready(function(){
	$.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       }
   });

	$('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    }) 
  

   $('.dropify').dropify();


   $('#description').summernote(
      {
        height: 100,
        focus: false
      }
    );

   $(document).on('click', '.reset-filter', function(e){
        if(confirm('Do you want to reset?'))
        {
            window.location.reload();
        }
   });

   $(document).on('input', '.numericInput', function () {
      // Get the entered value
      var enteredValue = $(this).val();

      // Check if the entered value is a valid number (float or integer)
      if (!isValidNumber(enteredValue)) {
          // If not valid, remove the last character
          $(this).val($(this).val().slice(0, -1));
      }
  });

   function isValidNumber(value) {
      // Use a regular expression to check if the value is a valid number
      // This regex allows both integers and floating-point numbers
      var regex = /^-?\d*\.?\d*$/;
      return regex.test(value);
  }
});

