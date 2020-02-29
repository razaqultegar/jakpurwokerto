$(function(){
	$("#wizard").steps({
    headerTag: "h4",
    bodyTag: "section",
    transitionEffect: "fade",
    enableAllSteps: true,
    transitionEffectSpeed: 300,
    labels: {
      next: "Selanjutnya",
      previous: "Sebelumnya",
      finish: "Selesai"
    },
    onStepChanging: function (event, currentIndex, newIndex) { 
      if(newIndex === 1) {
        $('.steps ul').addClass('step-2');
      }else{
        $('.steps ul').removeClass('step-2');
      }

      if(newIndex === 2) {
        $('.steps ul').addClass('step-3');
        $('.actions ul').addClass('mt-7');
      }else{
        $('.steps ul').removeClass('step-3');
        $('.actions ul').removeClass('mt-7');
      }

      return true; 
    },
    onFinished: function (event, currentIndex) {
      var form = $(this);
      form.submit();
    }
  });
    
  // Custom Button Jquery Steps
  $('.forward').click(function(){
    $("#wizard").steps('next');
  })
  $('.backward').click(function(){
    $("#wizard").steps('previous');
  })

  // Grid 
  $('.grid .grid-item').click(function(){
    $('.grid .grid-item').removeClass('active');
    $(this).addClass('active');
  })

  // Date Picker
  var dp1 = $('#dp1').datepicker().data('datepicker');
  dp1.selectDate( new Date( ));
})
