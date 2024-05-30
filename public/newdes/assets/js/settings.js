(function ($) {
  'use strict';

  $(".ms-settings-toggle").on('click', function (e) {
    $('body').toggleClass('ms-settings-open');
  });

  $("#dark-mode").on('click', function () {
    $('body').toggleClass('ms-dark-theme');
  });
  $("#remove-quickbar").on('click', function () {
    $('body').toggleClass('ms-has-quickbar');
  });
  $("#hide-aside-left").on('click', function () {
    $('body').toggleClass('ms-aside-left-open');
  });

  $('.myTable').DataTable({
    responsive: true,
    dom: 'Bfrtip',
    buttons: [
        'excelHtml5'
    ]

  });
  $('#myTable').DataTable({
    responsive: true,
    dom: 'Bfrtip',
    buttons: [
        'excelHtml5'
    ]
  });

//   $("#myTable").on('click', 'td:not(.dtr-control)', function() {
//     window.location = $(this).parent().data("href");
// });


  // Get references to the button and the span element
var excel_btn = document.querySelector('.buttons-excel span');
     excel_btn.innerHTML=`Export <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
     <path d="M4 16L4 17C4 18.6569 5.34315 20 7 20L17 20C18.6569 20 20 18.6569 20 17L20 16M16 8L12 4M12 4L8 8M12 4L12 16" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
     </svg>
     `
     var search_input = document.querySelectorAll('.dataTables_filter input');
      search_input.forEach(Element => {
        Element.placeholder = 'Search by name';
      })
     

})(jQuery);

