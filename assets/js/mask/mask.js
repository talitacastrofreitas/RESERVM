var $mask = jQuery.noConflict();
$mask(document).ready(function () {
  $mask(".date").mask("00/00/0000");
  $mask(".time").mask("00:00:00");
  $mask(".hora").mask("00:00");
  $mask(".date_time").mask("00/00/0000 00:00:00");
  $mask(".cep").mask("00000-000");
  $mask(".phone").mask("0000-0000");
  $mask(".phone_with_ddd").mask("(00) 0000-0000");
  $mask(".phone_us").mask("(000) 000-0000");
  $mask(".mixed").mask("AAA 000-S0S");
  $mask(".ip_address").mask("099.099.099.099");
  $mask(".percent").mask("##0,00%", {
    reverse: true,
  });
  $mask(".clear-if-not-match").mask("00/00/0000", {
    clearIfNotMatch: true,
  });
  $mask(".placeholder").mask("00/00/0000", {
    placeholder: "__/__/____",
  });
  $mask(".fallback").mask("00r00r0000", {
    translation: {
      r: {
        pattern: /[\/]/,
        fallback: "/",
      },
      placeholder: "__/__/____",
    },
  });

  $mask(".selectonfocus").mask("00/00/0000", {
    selectOnFocus: true,
  });

  $mask(".cep_with_callback").mask("00000-000", {
    onComplete: function (cep) {
      console.log("Mask is done!:", cep);
    },
    onKeyPress: function (cep, event, currentField, options) {
      console.log(
        "An key was pressed!:",
        cep,
        " event: ",
        event,
        "currentField: ",
        currentField.attr("class"),
        " options: ",
        options,
      );
    },
    onInvalid: function (val, e, field, invalid, options) {
      var error = invalid[0];
      console.log(
        "Digit: ",
        error.v,
        " is invalid for the position: ",
        error.p,
        ". We expect something like: ",
        error.e,
      );
    },
  });

  $mask(".crazy_cep").mask("00000-000", {
    onKeyPress: function (cep, e, field, options) {
      var masks = ["00000-000", "0-00-00-00"];
      mask = cep.length > 7 ? masks[1] : masks[0];
      $mask(".crazy_cep").mask(mask, options);
    },
  });

  $mask(".cnpj").mask("00.000.000/0000-00", {
    reverse: true,
  });
  $mask(".cpf").mask("000.000.000-00", {
    reverse: true,
  });
  $mask(".rg").mask("00.000.000-00", {
    reverse: true,
  });
  $mask(".money").mask("#.##0,00", {
    reverse: true,
  });

  var SPMaskBehavior = function (val) {
      return val.replace(/\D/g, "").length === 11
        ? "(00) 00000-0000"
        : "(00) 0000-00009";
    },
    spOptions = {
      onKeyPress: function (val, e, field, options) {
        field.mask(SPMaskBehavior.apply({}, arguments), options);
      },
    };

  $mask(".cel_tel").mask(SPMaskBehavior, spOptions);

  $mask(".bt-mask-it").click(function () {
    $mask(".mask-on-div").mask("000.000.000-00");
    $mask(".mask-on-div").fadeOut(500).fadeIn(500);
  });
});
