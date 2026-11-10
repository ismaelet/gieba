$(document).ready(function () {
  $(".h-linkM-txt").hover(
    function () {
      $(this).addClass("focus");
      $(".h-linkM-txt").not(this).addClass("blur");
    },
    function () {
      $(".h-linkM-txt").removeClass("focus").removeClass("blur");
    },
  );
});
