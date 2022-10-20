<!-- bootstrap  -->
<script src="<?= base_url() ?>assets/js/jquery-3.6.1.js"></script>

<script src="<?= base_url() ?>assets/js/jquery.slim.min.js"></script>
<script src="<?= base_url() ?>assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://smtpjs.com/v3/smtp.js"></script>

<script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>

<!-- script button send -->
<script>
  $(document).ready(function() {
    $("#btnsend").click(function() {
      let baseurl = "<?= base_url() ?>";
      var mailerdata = $('#inputemail').val();
      //  console.log(isEmail(mailerdata));
      if (isEmail(mailerdata) == false) {
        //  alert('The email address you entered does not match with required format')
        $('#alert-error').fadeIn(1000);
        setTimeout(function() {
          $('#alert-error').fadeOut(1000);
        }, 5000);
      } else {
        $("#btn-trigger").click();
        $.ajax({
          url: baseurl + "index.php/main/mailer",
          "type": "POST",
          "data": {
            "email": mailerdata
          },
          success: function(data) {
            // $("#btn-trigger").click();
          }
        });
      }
    })
  });

  function isEmail(mailerdata) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(mailerdata);
  }
</script>
</body>

</html>