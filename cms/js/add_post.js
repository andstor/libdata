function readURL(input) {

  var url = input.value;

  var reader = new FileReader();

  reader.onload = function(e) {
    $('#profileImg > img').attr('src', e.target.result);
  };
  reader.readAsDataURL(input.files[0]);

  $('#fileName').val(input.files[0].name);

}


$("#upload").change(function() {
  readURL(this);
});
