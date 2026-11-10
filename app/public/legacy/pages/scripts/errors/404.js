$(window).ready(function () {
  /* Slider height */
  resize();
  $(window).on("resize", function () {
    resize();
  });

  function resize() {
    var height = $(window).height();

    $("main").css("height", "calc(" + height + "px");
  }
});
