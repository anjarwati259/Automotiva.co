<div class="mb-50 w-100 d-flex justify-content-center flex-column align-items-center mt-5">
  <img src="<?= base_url() ?>assets/img/Automotiva base logo 1.png" alt="" />
  <h1 class="test display-1 text-white">COMING SOON</h1>
  <p class="w-50 text-center mt-4 test2 text-white">
    A dedicated page to inform you Automotive-Lifestyle-Passion with
    engaging visuals and creative manners
  </p>
  <div class="mb-10 w-25 position-relative d-flex justify-content-center test6">
    <input id="inputemail" type="email" class="p-3 rounded-pill test4" placeholder="Please input" />
    <div class="p-2 test8">
      <button id="btnsend" class="bg-dark rounded-pill test3" type="submit">
        Notify Me
      </button>
    </div>
  </div>
  <div class="mini-alert">
    <div class=" alert alert-danger rounded-pill" role="alert" style="display: none;" id="alert-error">
      <img src="<?= base_url() ?>assets/img/Group 36.png" class="mini-img" alt="">
      <span class="text-alert">The email address you entered does not match with required format</span>
    </div>
  </div>
  <p class="w-50 text-center mt-4 test2 text-white">
    Be notified by email as soon as we go live
  </p>
  <a href="https://instagram.com/automotiva.co?igshid=YmMyMTA2M2Y=" class="test5"><img src="<?= base_url() ?>assets/img/Vector.png" alt="" class="instagram" />@Automotiva</a>
</div>

<!-- Button trigger modal -->
<button style="display:none" id="btn-trigger" type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
  Launch demo modal
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="exampleModalLongTitle"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-5">
        <img class="modal-img" src="<?= base_url() ?>assets/img/email2.png" alt="" />
        <h3 class="h3-modal">Thank You For Your Submitting!</h3>
        <small class="text-modal">You will be receiving a confirmation email to confirm your submittion. Come back to the Automotiva Co page in 1 hour if you don’t receive an email from us.</small>
      </div>
    </div>
  </div>
</div>