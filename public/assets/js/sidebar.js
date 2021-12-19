// list = document.querySelectorAll('.list');
// for (let i = 0; i < list.length; i++) {
//     list[i].onclick = function () {
//         let j = 0;
//         while (j < list.length) {
//             list[j++].className = 'list';
//         }
//         list[i].className = 'list active';
//     }
// }

$(function () {
    // this will get the full URL at the address bar
    var url = window.location.href;

    // passes on every "a" tag 
    $("#navigation a").each(function () {
        // checks if its the same on the address bar
        if (url == (this.href)) {
            $(this).closest("li").addClass("active");
        }
    });
});
