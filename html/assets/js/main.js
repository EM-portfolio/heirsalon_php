$(function(){
    // mainpage nav
  $('.nav-list > li').on('click', function(){
    $('.nav-list > li').removeClass('color-on');
    $(this).addClass('color-on');
  })
  
  // loginpage nav
  $('.form-li').on('click', function(){
    $('.form-li').removeClass('active-tab');
    $('.tab-content').removeClass('active-content');
    $(this).addClass('active-tab');
    var index = $('.form-li').index(this);
    $('.tab-content').eq(index).addClass('active-content');
  })

  // dashbord
  $(document).ready(function () {
    $('.list').on('click', function () {
        $('.list').removeClass('active');
        $(this).addClass('active');

        var index = $('.list').index(this);
        $('.section').removeClass('active-section');
        $('.section').eq(index).addClass('active-section');

    });
  });


  var $menulist = $('#menulist');
  $('#menulist > h3').on('click',function(){
    $('#menulist').toggleClass('open');
    if($menulist.hasClass('open')){
      $menulist.stop(true).animate({
        bottom:'0px'
      }, 600, 'easeOutBack');
    }else{
      $menulist.stop(true).animate({
        bottom:'-420px'
      }, 600, 'easeOutBack');
    }
  });
  // Scroll処理
  $(window).scroll(function() {
    $('#menulist').toggleClass('open');
    if($menulist.hasClass('open')){
      $menulist.stop(true).animate({
        bottom:'0px'
      }, 600, 'easeOutBack');
    }
  });
  



}); // end