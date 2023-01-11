@extends('layout.loginexam')
@section('pageTitle')
  <title>Crew Management</title>
  <link rel="stylesheet" href="/theme/css/about.css">
@endsection
@section('metaCSRF')
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('contents')
<section id="about-p1">
    <div class="container">
      <div class="row">
        <div id="" class="col-md-4"></div>
        <div id="" class="col-md-4" style="padding:20px;">
          <h3 class="text-center" style="font-weight:500;">Crew Management</h3>
          <h5 class="text-center" style="color:blue;">User Log In</h5>
          <div  class="card" style="margin-top:10px;padding:20px;">
            <form id="loginform">
              <div class="row">
                <input type="hidden" name="type" value="login" />
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" id="user"  name="username" value=""/>
                  </div>

                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" id="password" name="password" value="" />
                  </div>

                  <div class="form-group">
                    <button type="button" id="loginbtn">Log In</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div id="" class="col-md-4"></div>
      </div>
    </div>
</section>
@endsection
@section('js')
<script src="/theme/js/jquery/jquery.min.js"></script>
<script src="/theme/js/popper/popper.min.js"></script>
<script src="/theme/js/bootstrap/bootstrap.min.js"></script>
<script src="/theme/js/wow/wow.min.js"></script>
<script src="/theme/js/owl-carousel/owl.carousel.min.js"></script>
<!-- Plugin JavaScript -->
<script src="/theme/js/jquery-easing/jquery.easing.min.js"></script>
<script src="/theme/js/custom.js"></script>
  <script>
  $('#user').on('keyup',function(){
    var usr = $(this).val();
    var ulen = usr.length;
    var usrstr = usr.substring(0,ulen - 1);
    if (!/^[a-zA-Z]+$/.test(usr)) {
        $('#user').val(usrstr);
    }
    else if(usr.length > 10) $('#user').val(usrstr);
  });
  $('#password').on('keyup',function(){
    var usr = $(this).val();
    var ulen = usr.length;
    var usrstr = usr.substring(0,ulen - 1);
    if (!/^[a-zA-Z0-9]+$/.test(usr)) {
        $('#password').val(usrstr);
    }
    else if(usr.length > 20) $('#password').val(usrstr);
  });
  $('#loginbtn').on('click',function(event){
    $.ajax({
  			url: '/api-request',
  			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
  			method: 'POST',
  			data: $('#loginform').serialize(),
  			success:function(e){
  					if(e){
  						console.log(e);
              if(e.error > 0) alert("Invalid Credentials");
              else window.location.href = "/crew";
  					}
  			},
  	})
  });
  </script>
@endsection
