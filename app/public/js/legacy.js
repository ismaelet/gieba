$(document).ready(function () {
  $(".file-inp").on("change", function () {
    $(this).parents(".file-form").find(".file-submit").click();

    console.log(1);
  });

  $(".file-form").submit(function () {
    var self = $(this);

    self.find(".file-cancel").hide();
    self.find(".file-submit").hide();
    self.find(".file-load").show();
    self.find(".file-stick").show().css("opacity", 1);
  });
});

$(document).ready(function () {
  //clear error messages
  $(".clear-error").keypress(function () {
    $(".inp.err").hide();
  });
  $(".clear-error").click(function () {
    $(".inp.err").hide();
  });

  //No spance inputs
  $(".inp-nospace").keypress(function (e) {
    if (e.which === 32) return false;
  });

  //dependent selector
  $(".sel.affect").each(function () {
    selector_affect($(this));
  });
  $(".sel.affect").on("change", function () {
    selector_affect($(this));
  });

  function selector_affect(selector) {
    const affected = selector.parent().find('.sel[name="' + selector.data("affect") + '"]');
    const field_id = affected.data("id");
    const field_show = affected.data("show");

    affected.attr("disabled", false).empty();

    $.ajax({
      type: "POST",

      url: affected.data("root") + "/system/modules/ajax/search-selector.php",

      data: {
        tbl: affected.data("tbl"),
        filter: affected.data("filter"),
        filter_val: selector.val(),
        field_id: field_id,
        field_show: field_show,
      },

      success: function (data) {
        const options = $.parseJSON(data);

        for (var i = 0; i < options.length; i++) {
          const option = options[i];

          affected.append(
            '<option value="' + option[field_id] + '" selected>' + option[field_show] + "</option>",
          );
        }
      },
    });
  }

  //ajax search
  $(".ajax-search-txt").keyup(function () {
    //setup
    const self = $(this);
    const parent = self.parents(".ajax-search");
    const url = parent.data("url");
    const filter = parent.data("filter");
    const root = parent.data("root");

    $.ajax({
      type: "POST",

      url: root + "/app/modules/ajax/search-" + url + ".php",

      data: { search: self.val(), filter: filter },

      success: function (data) {
        if ($.trim(data).length) {
          parent.find(".ajax-search-ddown").show();
          parent.find(".ajax-search-box").html(data);
        } else {
          parent.find(".ajax-search-ddown").hide();
          parent.find(".ajax-search-box").html("");
        }
      },
    });
  });

  $(".ajax-search").on("click", ".ajax-search-item", function () {
    var self = $(this);
    var parent = self.parents(".ajax-search");

    parent.find(".ajax-search-id").val(self.data("id"));
    parent.find(".ajax-search-txt").val(self.data("txt"));

    parent.find(".ajax-search-ddown").hide();
  });

  $(".inp, .mbtn").focus(function () {
    $(".ajax-search").find(".ajax-search-ddown").hide();
    $(".ajax-search").find(".ajax-search-box").html("");
  });
});

$(document).ready(function () {
  //open
  $(".lbox-open").click(function () {
    const tar = $($(this).data("tar"));

    if ($(".lbox:visible").length) {
      $(".lbox").hide();

      tar.show();
    } else {
      $("body").addClass("fixed");

      tar.fadeIn();
    }
  });

  //close
  $(".lbox-close").click(function () {
    close($(this));
  });
  $(".lbox-bgr").click(function () {
    close($(this));
  });
  $(".lbox-close-icon").click(function () {
    close($(this));
  });

  function close(ele) {
    ele.parents(".lbox").fadeOut();

    $("body").removeClass("fixed");
  }
});

$(window).on("load", function () {
  if ($("#loading").length) {
    setTimeout(function () {
      $("#loading").fadeOut(400);
      update();
    }, 400);
  } else {
    update();
  }

  var winBot = 0;

  $(window).scroll(function () {
    update();
  });
  $(window).on("resize", function () {
    update();
  });

  function update() {
    winBot = $(window).scrollTop() + $(window).height();

    $(".load").each(function () {
      var elem = $(this);

      if (visible(elem)) {
        elem.removeClass("load");
      }
    });
  }

  function visible(elem) {
    var elemPos = elem.offset().top;
    //var elemPos = elem.offset().top + elem.outerHeight(true);
    return elemPos <= winBot;
  }
});

$(document).ready(function () {
  update();

  $(window).resize(function () {
    update();
  });

  function update() {
    $(".slide-pan").each(function () {
      var panel = $(this);
      var block = panel.find(".slide-block").outerWidth();
      var box = panel.find(".slide-box").outerWidth();

      var size = Math.round(block / box);

      panel.data("size", size);

      panel.find(".slide-row").addClass("no-trans");

      slide(panel);

      panel[0].offsetHeight;

      panel.find(".slide-row").removeClass("no-trans");
    });
  }

  //move panel
  function slide(panel) {
    //get variables
    var pos = parseInt(panel.data("pos"));
    var size = parseInt(panel.data("size"));

    //move the row
    var left = (-100 * (pos - 1)) / size;

    panel.find(".slide-row").css({ left: left + "%" });
  }
});

$(document).ready(function () {
  //document ready
  $(".slide-pan").each(function () {
    var panel = $(this);
    panel.find(".slide-row").addClass("no-trans");

    slide(panel);

    panel[0].offsetHeight;

    panel.find(".slide-row").removeClass("no-trans");
  });

  //on click
  $(".slide-arrow").click(function () {
    if (!$(this).hasClass("disabled")) {
      var arrow = $(this);
      var panel = $(arrow.data("tar"));

      update_pos(arrow, panel);
      slide(panel);
    }
  });

  //update position variable
  function update_pos(arrow, panel) {
    //get data
    var qty = Math.round(
      parseInt(panel.find(".slide-row").css("width")) /
        parseInt(panel.find(".slide-box").css("width")),
    );
    var size = Math.round(
      parseInt(panel.find(".slide-block").css("width")) /
        parseInt(panel.find(".slide-box").css("width")),
    );
    var loop = panel.data("loop");
    var leap = panel.data("leap");

    if (!leap) {
      leap = size;
    }

    //if is a fixed arrow
    if (arrow.data("pos")) {
      var pos = parseInt(arrow.data("pos"));

      //if is a dynamic arrow
    } else if (arrow.data("dir")) {
      var pos = parseInt(panel.data("pos"));
      var dir = arrow.data("dir");
      var posOld = pos;

      //get pos
      if (dir === "right") {
        pos = Math.floor(pos + leap);
        arrow.prev(".left.disabled").removeClass("disabled");
      } else if (dir === "left") {
        pos = Math.ceil(pos - leap);
        arrow.next(".right.disabled").removeClass("disabled");
      }

      //enable & disable arrows
      if (pos <= 1 && dir === "left") {
        if (loop && posOld === 1) {
          pos = qty - size + 1;
        } else {
          pos = 1;
        }

        if (!loop) {
          arrow.addClass("disabled");
        }
      }

      if (pos >= qty - (size - 1) && dir === "right") {
        if (loop && posOld === qty - size + 1) {
          pos = 1;
        } else {
          pos = qty - size + 1;
        }

        if (!loop) arrow.addClass("disabled");
      }
    }

    //save new pos
    panel.data("pos", pos);
  }

  //move panel
  function slide(panel) {
    //get variables
    var pos = parseInt(panel.data("pos"));
    var size = Math.round(
      parseInt(panel.find(".slide-block").css("width")) /
        parseInt(panel.find(".slide-box").css("width")),
    );

    //move the row
    var left = (-100 * (pos - 1)) / size;

    panel.find(".slide-row").css({ left: left + "%" });
  }
});

$(document).ready(function () {
  $(".svg").each(function () {
    var img = $(this);
    var imgURL = img.attr("src");
    var attributes = img.prop("attributes");

    $.get(
      imgURL,
      function (data) {
        // Get the SVG tag, ignore the rest
        var svg = jQuery(data).find("svg");

        // Loop through IMG attributes and apply on SVG
        $.each(attributes, function () {
          svg.attr(this.name, this.value);
        });

        // Replace IMG with SVG
        img.replaceWith(svg);
      },
      "xml",
    );
  });
});
