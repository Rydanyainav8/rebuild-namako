$.ajax({
  type: "GET",
  url: "{{ path('search') }}",
  dataType: "json",
  data: {search: input},
  cache: false,
  success: function (response) {
         $('.example-wrapper').replaceWith(response);
         //$('.example-wrapper').load("{{ path('search') }}?search="+ $search.val());
          console.log(response);
           },
  error: function (response) {
         console.log(response);
             }
});